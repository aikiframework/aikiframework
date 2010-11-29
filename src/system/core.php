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
 * @copyright   (c) 2008-2010 Aikilab
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
	 * @param   string class name
	 * @return  mixed
	 */
	public function load($class){
		global $system_folder;

		if (isset($this->$class)){
			return $this->$class;
		}

		if (file_exists($system_folder.'/system/libraries/'.$class.'.php')){

			require_once($system_folder.'/system/libraries/'.$class.'.php');

		}elseif(file_exists($system_folder.'/assets/extensions/'.$class.'.php')){

			require_once($system_folder.'/assets/extensions/'.$class.'.php');

		}else{

			return false;

		}

		$object = new $class();

		$this->$class = $object;
			
		return $object;
	}



	/**
	 * Add db.table aiki_config data to config array
	 *
	 * @param  array   config array
	 * @return  array
	 */
	public function get_config($config){
		global $db;

		$settings = $db->get_results("SELECT config_data FROM aiki_config");
		foreach ( $settings as $setting_group ){
			$temp = @unserialize($setting_group->config_data);
			if (is_array($temp)){
				$config= array_merge($config,$temp );
			}
		}

		return $config;
	}

	/**
	 * Get a String that is between two delimiters.
	 *
	 * @param  string   the full string
	 * @param  string   first delimiter
	 * @param  string   second delimiter
	 * @return  string
	 */
	public function get_string_between($string, $start, $end){
		$ini = strpos($string,$start);
		if ($ini ===false) return "";
		$ini += strlen($start);
		$len = strpos($string,$end,$ini) - $ini;
		return substr($string,$ini,$len);
	}


	/**
	 *  Works with HTML special chars. and few other special chars that PHP does not normally convert.
	 *
	 * @param   string   text
	 * @return  string
	 */
	public function convert_to_specialchars($text){

		$text = htmlspecialchars($text);

		$html_chars = array(")", "(", "[", "]", "{", "|", "}", "<", ">", "_");
		$html_entities = array("&#41;", "&#40;", "&#91;", "&#93;", "&#123;", "&#124;", "&#125;", "&#60;", "&#62;", "&#95;");

		$text = str_replace($html_chars, $html_entities,$text);

		return $text;
	}


	public function convert_to_html($text){

		return $text;
	}


	/**
	 * Replace Aiki vars with their assigned values.
	 * Use normal urls if mod_rewrite is not enabled.
	 *
	 * @param  string   text before processing
	 * @return  string
	 */
	public function processVars($text){
		global $aiki, $page, $membership, $config, $dir;

		$current_month = date("n");
		$current_year = date("Y");
		$current_day = date("j");

		$aReplace = array (
		"[userid]"      => $membership->userid,
        "[full_name]" => $membership->full_name,
        "[language]" => $config['default_language'],
		"[username]" => $membership->username,
		"[page]" => $page,
		"[site]" => $config['site'],
		"[direction]" => $dir,
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