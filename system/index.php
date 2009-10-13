<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2008-2009 Bassel Khartabil.
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}

class aiki
{




	var $aikiarray;
	var $title = "";
	var $array_fields;
	var $array_keys;
	var $stop_sql_loops;
	var $output;
	var $sql_tree_output;


	function aiki(){

	}

	function load($class){
		global $system_folder;
		
		static $objects = array();

		if (isset($objects[$class]))
		{
			return $objects[$class];
		}

		if (file_exists($system_folder.'/system/libraries/'.$class.'.php'))
		{
			require($system_folder.'/system/libraries/'.$class.'.php');
		}elseif(file_exists($system_folder.'/system/handlers/'.$class.'.php')){
			require($system_folder.'/system/handlers/'.$class.'.php');
		}elseif(file_exists($system_folder.'/system/parsers/'.$class.'.php')){
			require($system_folder.'/system/parsers/'.$class.'.php');
		}


		$name = 'aiki_'.$class;

		$objects[$class] =& new $name();

		$this->$class = $objects[$class];

		return $objects[$class];
	}


	function get_config($config){
		global $db;

		$settings = $db->get_results("SELECT config_data FROM aiki_config");
		$i=0;
		foreach ( $settings as $setting_group )
		{
			$this->processSettingsArray($setting_group->config_data);
			$arrykeys = array_keys($this->aikiarray);
			foreach($this->aikiarray as $field)
			{
				$field = explode("|", $field);
				$field = $field[0];
				$config[$arrykeys[$i]] = $field;
				$i++;
			}
			$i=0;
		}
		return $config;
	}




	function aikiswitch($text){


	}



	function get_string_between($string, $start, $end){
		$string = " ".$string;
		$ini = strpos($string,$start);
		if ($ini == 0) return "";
		$ini += strlen($start);
		$len = strpos($string,$end,$ini) - $ini;
		return substr($string,$ini,$len);
	}




	function processSettingsArray($array){
		$this->aikiarray = unserialize($array);
	}




	function processVars($text){
		global $aiki, $page, $membership, $config;

		$text = str_replace("[username]", $membership->username, $text);
		$text = str_replace("[full_name]", $membership->full_name, $text );
		$text = str_replace("[language]", $config['default_language'], $text);
		$text = str_replace("[page]", $page, $text);
		$text = str_replace('[site]', $config['site'], $text);
		$text = str_replace('[root]', $config['url'], $text);
		$text = str_replace($config['url'].'/', $config['url'], $text);

		return $text;
	}	




}
?>