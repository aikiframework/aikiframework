<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Aikilab http://www.aikilab.com
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     System
 * @filesource
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}

/**
 * Stores config data and contains additional functions to process text,
 * output HTML, and generate clean URLs.
 * @category    Aiki
 * @package     System
 */
class aiki
{

    /**
     * Loads an aiki library.
     * 
     * Attempts to load from class first *.php, then tries to load *.php 
     * from extensions, then finally  tries classname/classname.php.
     *
     * @param   string $class name of class to be loaded
     * @global  string $system_folder the system folder
     * @return  mixed
     */
    public function load($class) {
        global $system_folder;

        if (isset($this->$class))
            return $this->$class;


        if (file_exists($system_folder.'/system/libraries/'.$class.'.php'))
        {
            require_once($system_folder.'/system/libraries/'.$class.'.php');
        }
        elseif(file_exists($system_folder.'/assets/extensions/'.$class.'.php'))
        {
            require_once($system_folder.'/assets/extensions/'.$class.'.php');
        } 
        elseif(file_exists(
                $system_folder.'/assets/extensions/'.$class.'/'.$class.'.php'))
        {
            require_once(
                $system_folder.'/assets/extensions/'.$class.'/'.$class.'.php');
        } 
        else {
            return false;
        }

        $object = new $class();
        $this->$class = $object;
        return $object;
    }


	/**
     * Get configuration options for use.
     *
	 * Get configuration items stored in the database
	 * and append those items which are NOT set in
	 * the configuration file. The configuration file
	 * items take precedence and should NOT be overwriten.
	 *
	 * @param array $config The global configuration array
     * @global array $db The global database object
	 * @return array     The global configuration array
	 */
	public function get_config($config) {
		global $db;

		// get an array of all the existing keys in the config array
		$preserve_keys = array_keys($config);

		// get the config data stored in the database
		$settings = $db->get_results("SELECT config_data FROM aiki_config");

		// go through every config record. if the config item
		// is not already set then use the database record
		foreach($settings as $setting_group) {

			// unserialize array key => value pairs stored
			// in this config group. Every row should be an array
			// of config items pertaining to a config group
			$temp = @unserialize($setting_group->config_data);

			if (is_array($temp)) {

				// remove/unset all the duplicate config
				// elements we want to preserve
				foreach ($preserve_keys as $duplicate) {
					unset($temp[$duplicate]);
				}

				// merging the arrays overwrites the first parameter/array with
				// the values of the second parameter/array when the keys match
				$config = array_merge($config, $temp);
			}
		}
		return $config;
	}

    /**
     * Get a String that is between two delimiters.
     *
     * @param  string   $string  the full string
     * @param  string   $start   first delimiter
     * @param  string   $end     second delimiter
     * @return string
     */
    public function get_string_between($string, $start, $end) {
        $ini = strpos($string,$start);
        if ($ini ===false) return "";
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        return substr($string,$ini,$len);
    }


    /**
     * Converts text to special characters.
     *
     * Works with HTML special characters and few other special characters 
     * that PHP does not normally convert.
     *
     * @param   string   $text text to convert to special characters
     * @return  string
     */
    public function convert_to_specialchars($text) {

        $text = htmlspecialchars($text);

        $html_chars = array(")", "(", "[", "]", "{", "|", "}", "<", ">", "_");
        $html_entities = array("&#41;", "&#40;", "&#91;", "&#93;", "&#123;", 
                               "&#124;", "&#125;", "&#60;", "&#62;", "&#95;");

        $text = str_replace($html_chars, $html_entities,$text);

        return $text;
    }


    /**
     * Replace Aiki vars with their assigned values.
     *
     * Use normal urls if mod_rewrite is not enabled.
     *
     * @param  string   $text before processing
     * @return  string
     * @todo this function is seriously overloaded and needs to be rethough
     */
    public function processVars($text) {
        global $aiki, $page, $membership, $config, $languages, $site_info;

        /**
         * @todo Setting variables really doesn't have a place in this function
         */
        if ( function_exists('date_default_timezone_set') and
             function_exists('date_default_timezone_get') ) 
        {
            if ( isset($config['timezone']) and !empty($config['timezone']) )
                date_default_timezone_set($config['timezone']); 
            else
                date_default_timezone_set(@date_default_timezone_get()); 
        }
        
        $current_month = date("n");
        $current_year = date("Y");
        $current_day = date("j");

        $aReplace = array (
        "[userid]"      => $membership->userid,
        "[full_name]" => $membership->full_name,
        "[language]" => $languages->language,
        "[username]" => $membership->username,
        "[page]" => $page,
        "[site_name]" => $site_info->site_name,
        "[site]" => $config['site'],
        "[direction]" => $languages->dir,
        "insertedby_username" => $membership->username,
        "insertedby_userid" => $membership->userid,
        "current_month" => $current_month,
        "current_year" => $current_year,
        "current_day" => $current_day
        );

        $text= strtr ( $text, $aReplace );

        if ($config['pretty_urls'] == 0){
            $text = preg_replace('/href\=\"\[root\](.*)\"/U', 'href="[root]?pretty=\\1"', $text);
            $text = str_replace('[root]', $config['url'], $text);
            $text = str_replace('=/', '=', $text);
        }else{
            $text = str_replace('[root]', $config['url'], $text);
        }

        $text = str_replace($config['url'].'/', $config['url'], $text);

        return $text;
    }
}
