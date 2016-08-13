<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Roger Martin
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Library
 * @filesource
 *
 * @todo        Translation of output messages.
 * @toto        error as html page.
 */

if(!defined('IN_AIKI')) {
    die('No direct script access allowed');
}


class Site {

    private $site; // contains all information of site:

    private $languages; // a array like [0]=>'en',[1]=>'fr'...
    private $need_translation;
    private $default_language;
    private $widget_language;
    private $site_view;
    private $site_view_prefix;

    /**
     * return site view
     * @return string
     */

    function view() {
        return $this->site_view;
    }

     /**
     * return site view prefix
     * @return string
     */

    function view_prefix() {
        return $this->site_view_prefix;
    }


    /**
     * return site prefix
     * @return string
     */

    function prefix() {
        return $this->site->site_prefix;
    }


     /**
     * return the default language of a site.
     * @return string language
     */
    function language($new=NULL) {
        global $aiki;
        if (!is_null($new)) {
            if (in_array($new, $this->languages)) {
                $this->default_language = $new;
                $this->need_translation = ($new != $this->widget_language);
                return true;
            } else {
                return false;
            }
        }
        return $this->default_language;
    }


    /**
     * return list (array) of allowed languages in a site.
     * @return array languages
     */
    function languages() {
        return $this->languages;
    }

    /**
     * return site
     * @return string Return site short name
     */
    function get_site() {
        return $this->site->site_shortcut;
    }


    /**
     * return site name
     * @return string Return site long name
     */
    function site_name() {
        return $this->site->site_name;
    }


    /**
     * return site engine (aiki by default)
     * @return string
     */
    function engine() {
        if ( !isset($this->site->site_engine) ||  $this->site->site_engine=="" ){
            return "aiki";
        }
        return $this->site->site_engine;
    }


    /**
     * return site engine parameters
     * @return arrays
     */
    function engine_parameters() {
        if ( !isset($this->site->site_engine_parameters) || isset($this->site->site_engine_parameters)=="" ){
            return array();
        }

        // parameters is a string width pair of var=value or var, separated ";"
        // example: checkHtml;markup=c;debug=on;
        foreach ($data= explode(";",$this->site->site_engine_parameters) as $pair ){
            $npos=strpos($pair,"=");
            if ($npos){
                $ret[ substr($pair,0,$npos)] = substr($pair,$npos+1);
            } else {
                $ret[$pair]=1;
            }
        }

        return $ret;
    }


    /**
     * return true if site need to be translated
     * @return boolean
     */
    function need_translation() {
        return $this->need_translation;
    }

    /**
     * magic method for use object site as a string
     * @return string Return site long name
     */

    function __toString() {
        return $this->site->site_shortcut;
    }


    /**
     * set site, checking if is active or closed.
     * @param mixed Object (site) or a sitename
     */

    private function set_site($sitename) {
        global $db, $config, $aiki;
        if ( !is_string($sitename) ) {
            // $sitename is a object
            $this->site = $sitename;
            $config['site'] = $sitename->site_shortcut;
            return ;
        }

        // search site
        $site = $db->get_row("SELECT * from aiki_sites where site_shortcut='$sitename' limit 1");
        if ( is_null($site) ){
            // not found
            die($aiki->message->error( t("Fatal Error: Wrong site name provided.") ,NULL,false));
        } elseif ( $site->is_active != 1 ) {
            // not active or closed;
            die($aiki->message->error($site->if_closed_output ? $info->if_closed_output : t("Site $sitename is closed."),
                    NULL,
                    false));
        } else {
            $this->site = $site;
            $config['site'] = $sitename;
        }
    }


    /**
     * Constructor: set site name or die if is inexistent or inactive.
     * The site is established by (in this order), GET[site], $config[site]
     * and finaly Default.
     *
     * @global $db
     * @global $config
     */

    function __construct() {
        global $db, $config, $aiki;

        // determine site name

        // when aiki receive directly the wigdet to render, widget_site is set as site
        $widget= isset($_GET["widget"]) ? $aiki->widgets->get_widget($_GET['widget'],false) : NULL ;

        if ( !is_null($widget)){
            // 1. site is determind by widget
            $this->set_site($widget->widget_site);
        } elseif (isset($_GET['site'])) {
            // 2. site is given
            $this->set_site($_GET['site']);
        } else {

            // 3. try determine site by url (for multi-site and apps)
            $this->site_prefix = "";
            $path = $aiki->url->first_url();
            $site = ($path == "homepage" ? NULL : $db->get_row("SELECT * FROM aiki_sites WHERE is_active!=0 AND site_prefix='$path'"));
            if ( is_null($site) ) {
                // 4 by config[site] else default.
                $this->set_site ( ( isset($config['site']) ? $config['site'] : 'default' ) );
            } else {
                $path = $aiki->url->shift_url();
                $this->set_site($site);
            }

        }

        // determine view
        if (isset($_GET['view'])) {
            $this->site_view= addslashes($_GET['view']);
        } else {
            $prefix = $aiki->url->first_url();
            $view = $db->get_var("SELECT view_name, view_prefix FROM aiki_views " .
                    " WHERE view_site='{$config['site']}' AND view_active='1' AND view_prefix='$prefix'");
            if ($view) {
                $aiki->url->shift_url();
                $this->site_view = $view;
                $this->site_view_prefix = $prefix;
            }
        }

        // define default language, list of allowed languages
        $this->default_language = ( $this->site->site_default_language ?
            $this->siteo->site_default_language :
            "en" );
        $this->languages = ( $this->site->site_languages ?
            explode(",", $this->site->site_languages) :
            array($this->default_language) );

        $this->widget_language  = ( $this->site->widget_language ?
            $this->site->widget_language :
            $this->default_language );

        if (!in_array($this->default_language, $this->languages)) {
            // correction: include default in allowed languages.
            $this->languages[]= $this->default_language;
        }

        // determine language
        if (isset($_GET['language'])) {
            $this->language(addslashes($_GET['language']));
        } elseif ($this->language($aiki->url->first_url())) {
            $aiki->url->shift_url();
        }
        $this->need_translation = ($this->default_language != $this->widget_language);

        //  the site manages dictionaries
        if ( $this->default_language != "en" ) {
            $aiki->dictionary->add("core", new dictionaryTable($this->default_language));
            $aiki->dictionary->translateTo($this->default_language);
        }

        if ( $this->widget_language != "en" ) {
            $aiki->dictionary->translateFrom($this->widget_language);
            $aiki->dictionary->add($config['site'], new dictionaryTable($this->default_language,$this->widget_language));
        }

        // language
        $aiki->languages->set( $this->language());

    }
}

?>
