<?php

/**
 * Aiki framework (PHP)
 *
 * @author		Aikilab http://www.aikilab.com
 * @copyright  (c) 2008-2010 Aikilab
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}

class aiki
{

	//stores the config data after getting them from config.php and db.table aiki_config
	private $aikiarray;

	//stores the keys of config data after getting them from config.php and db.table aiki_config
	private $array_keys;


	/**
	 * Loads an aiki library
	 *
	 * @param   string   class name
	 * @return  mixed
	 */
	public function load($class){
		global $system_folder;

		$objects = array();

		if (isset($objects[$class]))
		{
			return $objects[$class];
		}

		if (file_exists($system_folder.'/system/libraries/'.$class.'.php')){

			require($system_folder.'/system/libraries/'.$class.'.php');

			$objects[$class] = new $class();

			$this->$class = $objects[$class];

			return $objects[$class];

		}else{

			return false;
		}
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
		$i=0;
		foreach ( $settings as $setting_group )
		{
			$this->aikiarray = unserialize($setting_group->config_data);
			if (is_array($this->aikiarray)){
				$arrykeys = array_keys($this->aikiarray);
				foreach($this->aikiarray as $field)
				{
					$field = explode("|", $field);
					$field = $field[0];
					$config[$arrykeys[$i]] = $field;
					$i++;
				}
			}
			$i=0;
		}
		return $config;
	}


	/**
	 * Get String between two delimiters
	 *
	 * @param  string   the full string
	 * @param  string   first delimiter
	 * @param  string   second delimiter
	 * @return  string
	 */
	public function get_string_between($string, $start, $end){
		$string = " ".$string;
		$ini = strpos($string,$start);
		if ($ini == 0) return "";
		$ini += strlen($start);
		$len = strpos($string,$end,$ini) - $ini;
		return substr($string,$ini,$len);
	}



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
	 * Replace aiki vars with there values
	 * and fail to normal urls if mod_rewrite is not enabled
	 *
	 * @param  string   text before processing
	 * @return  string
	 */
	public function processVars($text){
		global $aiki, $page, $membership, $config, $dir;


		$text = str_replace("[username]", $membership->username, $text);
		$text = str_replace("[userid]", $membership->userid, $text);
		$text = str_replace("[full_name]", $membership->full_name, $text );
		$text = str_replace("[language]", $config['default_language'], $text);
		$text = str_replace("[page]", $page, $text);
		$text = str_replace('[site]', $config['site'], $text);
		$text = str_replace("[direction]", $dir, $text);

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
?>