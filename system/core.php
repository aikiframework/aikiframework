<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}

class aiki
{

	private $aikiarray;
	public $title = "";
	private $array_fields;
	private $array_keys;
	private $stop_sql_loops;
	public $output;
	private $sql_tree_output;


	public function load($class){
		global $system_folder;

		static $objects = array();

		if (isset($objects[$class]))
		{
			return $objects[$class];
		}

		if (file_exists($system_folder.'/system/libraries/'.$class.'.php')){
			require($system_folder.'/system/libraries/'.$class.'.php');
		}
		
		$name = 'aiki_'.$class;

		$objects[$class] =& new $name();

		$this->$class = $objects[$class];

		return $objects[$class];
	}


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


	public function escape($text){

		$text = stripcslashes($text);
		$text = str_replace('\"', '"', $text);
		$text = str_replace("\'", "'", $text);
		$text = str_replace('"', '\"', $text);
		$text = str_replace("'", "\'", $text);

		return $text;
	}


	public function get_string_between($string, $start, $end){
		$string = " ".$string;
		$ini = strpos($string,$start);
		if ($ini == 0) return "";
		$ini += strlen($start);
		$len = strpos($string,$end,$ini) - $ini;
		return substr($string,$ini,$len);
	}


	public function processVars($text){
		global $aiki, $page, $membership, $config;


		$text = str_replace("[username]", $membership->username, $text);
		$text = str_replace("[userid]", $membership->userid, $text);
		$text = str_replace("[full_name]", $membership->full_name, $text );
		$text = str_replace("[language]", $config['default_language'], $text);
		$text = str_replace("[page]", $page, $text);
		$text = str_replace('[site]', $config['site'], $text);

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