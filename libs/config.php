<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Roger martin  - Aikilab http://www.aikilab.com
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Library
 * @filesource
 *
 * @todo admin and edit options.
 *
 */

if (!defined('IN_AIKI')) {
    die('No direct script access allowed');
}


/**
 * A class to handle settings and configuration.
 *
 * This class provide support for multisite,multiview and multilangague
 * configuration.
 *
 * @category    Aiki
 * @package     Library
 *
 */


class config {

    /**
     * cache for site,view and language
     * @access private
     */
    private $site, $view, $language;


    /**
     * set timezone calling date_default_timezone_set
     *
     * @return void
     *
     */

    function set_timezone() {
        if ( function_exists('date_default_timezone_set') &&
            function_exists('date_default_timezone_get') ) {
            $timezone = $this->get("timezone", false);
            if ($timezone) {
                date_default_timezone_set($timezone);
            } else {
                // see http://php.net/manual/en/function.date-default-timezone-get.php to understand this
                date_default_timezone_set( @date_default_timezone_get());
                    }
        }

    }


    /**
     * Load configuration for actual site/view/language
     *
     * The constructor calls this methods, so use this method
     * only when you want reload configuration.
     *
     * @return integer number of setting loaded.
     *
     * @global $aiki
     */
    function load_configuration() {
        global $aiki, $config, $db;

        // cache site,view, language
        $this->site = $aiki->site->get_site();
        $this->view = $aiki->site->view();
        $this->language = $aiki->site->language();

        if (count($config)) {
            $checkPrevious = " OR config_name IN ('" . implode("','", array_keys($config)) . "')";
        } else {
            $checkPrevious = "";
        }

        // search autoload config setting and previuos config.
        $sql = "SELECT config_selector, config_name, config_value ".
            " FROM aiki_settings RIGHT JOIN aiki_configs ON setting_name=config_name".
            " WHERE setting_autoload=1". $checkPrevious.
            " ORDER by config_important DESC, config_weight DESC";

        $values = $db->get_results($sql);
        if (!is_array($values)) {
            return 0;
        }
        $prevName="";
        $count = 0;

        foreach ($values as $value) {
            // only store first value than match selector.
            if ( $prevName != $value->config_name  &&
                    $aiki->match_pair($value->config_selector, $this->site, $this->view, $this->language) ) {
                $config[$value->config_name] = unserialize($value->config_value);
                $prevName = $value->config_name;
                $count++;
            }
        }

        // special config values
        $this->set_timezone();

        return $count;
    }


    /**
     * Constructor.
     *
     * Cache site, language and view and load configuration.
     *
     */

    function __construct(){
        $this->load_configuration();
    }

    /**
     * parse selector
     *
     * @return array Site,view,language as array or string.
     * @access private
     */

     function selector($selector, $returnString=false) {
        $ret = array($this->site, $this->view, $this->language);

        switch ($selector) {
            case "CURRENT":
                break;
            case "CURRENT_SITE_VIEW":
                $ret[2] = "*";
                break;
            case "CURRENT_SITE":
                $ret[1] = "*";
                $ret[2] = "*";
                break;
            case "CURRENT_VIEW":
                $ret[0] = "*";
                $ret[2] = "*";
                break;
            case "CURRENT_LANGUAGE":
                $ret[0] = "*";
                $ret[1] = "*";
                break;
            default:
                $parts = explode("/", $selector. "/*/*/*", 4); // be sure three will be extracted
                $ret = array(
                    ( $parts[0] ? $parts[0] : "*" ),
                    ( $parts[1] ? $parts[1] : "*" ),
                    ( $parts[2] ? $parts[2] : "*" ));
        }

        return $returnString ? implode("/", $ret) : $ret;
    }



    /**
     * unserialize a value only if it seems like a serialized value
     **/

    private function _unserialize( $what ){
        return  $what=="N;" || preg_match ("/^[abidsO]:/", $what ) ? unserialize($what) : $what;
    }

    /**
     * get a setting
     *
     * You can define a default value, used when setting, and selector than can be
     * a trio of site/view/languages (you can use *), or string like
     * CURRENT (current site/view/language), CURRENT_SITE (only current site),
     * CURRENT_VIEW, CURRENT_SITE_VIEW (without language ), CURRENT_LANGUAGE
     * @param string $setting
     * @param string $default Value to return if not found (false)
     * @param string $selector (CURRENT is default)
     * @return setting if foundm else $defaut.
     * @global $db
     * @global $config
     * @global $aiki
     */


    function get($setting, $default=false, $selector="CURRENT", $site="default") {
        global $db, $config, $aiki;
        if (isset($config['sites'][$site][$setting])) {
            return $config['sites'][$site][$setting];
        }
        // check if setting is in cache.
        if (isset($config[$setting])) {
            return $config[$setting];
        }

        //else, obtain list of values for for setting.
        $sql=
            "SELECT config_value, config_selector" .
            " FROM aiki_configs" .
            " WHERE config_name='" . addslashes($setting) . "'" .
            " ORDER BY config_important DESC,config_weight DESC";

         // if any value is valid only seek first.
         // note: apps that don't have site/view/language use this way.
         if ($selector == "*" || $selector == "*/*/*") {
            $value = $db->get_var($sql);
            if (is_null($value)) {
                return $default;
            }
            $ret = $this->_unserialize($value);
            $config[$setting]= $ret;
            return $ret;
        }

        // firt obtain all values
        $values = $db->get_results($sql);
        list($site,$view,$language) = $this->selector($selector);

        // and filter the first that match selector
        if (is_array($values)) {
            foreach ($values as $value) {
                if ($aiki->match_pair($value->config_selector, $site, $view, $language)) {
                    $ret = $this->_unserialize($value->config_value);
                    $config[$setting]= $ret;
                    return $ret;
                }
            }
        }
        return $default;
    }

    /**
     * store a setting in memory, so it lives only on actual page.
     *
     * this functions overwrittes previous value of setting.
     *
     * @param string $setting
     * @param string $value
     *
     * @global $config
     */

    function remember($setting, $value) {
        global $config;
        $config[$setting] = $value;
    }

    /**
     * delete values for a setting
     *
     * Always deletes database and (optional) config.
     *
     * @param string $setting
     * @param boolen $deleteConfig, if true, unsets $config[$setting]
     *
     * @global $config
     * @globla $db
     */
    function delete_value($setting, $deleteConfig=false) {
        if ( $deleteConfig && isset($config[$setting]) ) {
            unset( $config[$setting]);
        }
        $db->query("DELETE FROM aiki_configs WHERE config_name='" . addslashes($setting) . "'");
    }

    /**
     * set a setting permantely and for actual page if selector match.
     *
     * Selector have to habitual syntax (site/view/language) with all
     * elements optional * ). You can add the literal '!important' if you
     * want set rule as important.%
     * Example: admin !important %
     *
     * @param string $setting
     * @param string $value
     * @param string $selector
     * @return boolean true is setting has been saved in database
     *
     * @global $config
     * @global $db
     * @global $aiki
     */

    function set($setting, $value, $givenSelector="*"){
        global $db, $config, $aiki;

        if ( substr($givenSelector,-10) == "!important" ){
            $important = 1;
            $selector = trim(substr($givenSelector, 0, -10));
        } else {
            $important= 0;
            $selector = $givenSelector;
        }

        if ( $givenSelector == "CURRENT" ||
             $givenSelector=="*" || $givenSelector =="*/*/*" ||
             $aiki->match_pair($givenSelector, $this->site, $this->view, $this->language) ) {
            // value will only store in config, in selector is current, * (all) or match against
            // current enviroment.
            $config[$setting] = $value ;
        }

        $parts   = $this->selector($selector);

        // Rules: a given site value 100, a view 10, a language only 1, * cero.
        $weight .=
            ( $parts[0] != "*" && $parts[0] != "" ? 100 : 00 ) +
            ( $parts[1] != "*" && $parts[1] != "" ?  10 : 00 ) +
            ( $parts[2] != "*" && $parts[2] != "" ?   1 : 00 );

        $name      = addslashes($setting);
        $selector = addslashes("{$parts[0]}/{$parts[1]}/{$parts[2]}");
        $where    = " WHERE config_name='$name' AND config_selector='$selector'";

        if ($db->get_var("SELECT config_id FROM aiki_configs $where")) {
            $SQL =
                "UPDATE aiki_configs SET" .
                " config_name='%s',config_value='%s'," .
                " config_important=%d, config_weight=%d, config_selector='%s'" .
                $where;
        } else {
            $SQL = "INSERT INTO aiki_configs".
                  " (config_name,config_value, config_important, config_weight, config_selector)" .
                  " VALUES ( '%s','%s',%d,%d,'%s') ";
        }

        $SQL = sprintf($SQL,
               $name,
               addslashes(serialize($value)),
               $important,
               $weight,
               $selector);
        return $db->query($SQL);

    }

}


/**
 * Shortcut for aiki->config->get
 *
 * You can define a default value, used when setting, and selector than can be
 * a trio of site/view/languages (you can use *), or string like
 * CURRENT (current site/view/language), CURRENT_SITE (only current site),
 * CURRENT_VIEW, CURRENT_SITE_VIEW (without language ), CURRENT_LANGUAGE
 * @param string $setting
 * @param string $default Value to return if not faound (false)
 * @param string $selector (CURRENT is default)
 * @return setting if found.
 *
 * @global $aiki
 */

function config($setting, $default=false, $selector="CURRENT") {
    global $aiki;
    return $aiki->config->get($setting, $default, $selector);
}

/**
 * Shortcut for aiki->config->set
 *
 * set a setting permantely and for actual page if selector match.
 *
 * Selector have to habitual syntax (site/view/language) with all
 * elements optional * ). You can add the literal '!important' if you
 * want set rule as important.%
 * Example: admin !important %
 *
 * @param string $setting
 * @param string $value to set
 * @param string $selector (CURRENT is default)
 * @return boolean true is setting has been saved in database
 *
 * @global $aiki
 */

function config_set($setting, $value, $selector="*") {
    global $aiki;
    return $aiki->config->set($setting, $value, $selector );
}



/**
 * Set error level depending of config debug options
 *
 * Call this function after aiki load "config" and "membership" class.
 *
 * @param integer $newLevel (optional) the new level if debug if off.
 * @return integer previous error level
 *
 * @global $aiki
 */

function config_error_reporting($newLevel=false) {
    global $aiki;
    if (is_debug_on()) {
        $oldLevel = error_reporting( E_ALL | E_STRICT );
    } else {
        if ( $newLevel === false ) {
            $newLevel = $aiki->config->get("error_level", E_ALL | E_STRICT );
        }
        $oldLevel = error_reporting($newLevel);
    }
    return $oldLevel;
}


/**
 * return debug mode.
 *
 * config["debug"] can be have this values:
 * false / true  (depreceated)
 * 0 : show no debug)
 * 1 : show debug always)
 * 2 : show debug if current user is SystemGOD or ModulesGod
 *
 * @return boolean true is setting has been saved in database
 *
 * @global $aiki
 */

function is_debug_on() {
    global $aiki;
    $debug = $aiki->config->get("debug", false, "CURRENT", $_GET['site']);

    if ( $debug === false || $debug === true ) {
        // boolean. Backwark compatibility
        return $debug;
    }

    if ( $debug == 1 ||
        ( $debug == 2 && isset($aiki->membership) &&
        ( $aiki->membership->permissions =="SystemGOD" ||
        $aiki->membership->permissions =="ModulesGOD" ) ) ) {
        return true;
    }
    return false;

}

?>
