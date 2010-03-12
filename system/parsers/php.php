<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class aiki_php
{

	function parser($text){
		global $aiki;

		$php_matchs = preg_match_all('/\<php (.*) php\>/Us', $text, $matchs);

		if ($php_matchs > 0){

			foreach ($matchs[1] as $php_function){

				if (preg_match('/str_replace((.*));/Us', $php_function)){
					$php_output = $this->aiki_str_replace($php_function);
				}

				if (preg_match('/if(.*) then (.*)/Us', $php_function)){
					$php_output = $this->aiki_if_then($php_function);
				}

				if (preg_match('/htmlspecialchars\((.*)\)/Us', $php_function)){
					$php_output = $this->aiki_htmlspecialchars($php_function);
				}

				if (preg_match('/\$aiki\-\>(.*)\-\>(.*)\((.*)\)\;/Us', $php_function)){
					$php_output = $this->aiki_function($php_function);
				}

				$text = str_replace("<php $php_function php>", $php_output , $text);
			}
		}

		return $text;
	}

	function aiki_function($text){
		global $aiki;

		//function does not have vars
		if (preg_match('/\$aiki\-\>(.*)\-\>(.*)\(\)\;/Us', $text)){

			$class = $aiki->get_string_between($text, '$aiki->', '->');
			$function = $aiki->get_string_between($text, '$aiki->'.$class.'->', '();');

			if (isset($aiki->$class)){
				$output = $aiki->$class->$function();
			}else{
				$output = '';
			}

			//function has vars
		}elseif (preg_match('/\$aiki\-\>(.*)\-\>(.*)\((.*)\)\;/Us', $text)){

			$class = $aiki->get_string_between($text, '$aiki->', '->');
			$function = $aiki->get_string_between($text, '$aiki->'.$class.'->', '(');
			$vars_array = $aiki->get_string_between($text, '(', ');');
			
			if (isset($aiki->$class)){
				$output = $aiki->$class->$function($vars_array);

			}else{
				$output = '';
			}

		}

		return $output;
	}

	function aiki_htmlspecialchars($text){
		global $aiki;

		$original_string = $aiki->get_string_between($text, "htmlspecialchars(", ")");

		$string = htmlspecialchars($original_string);

		$text = str_replace("htmlspecialchars($original_string)", $string, $text);
			
		return $text;
	}


	function aiki_if_then($text){
		global $aiki;

		$string = explode(" then ", $text);
		$if_cond = explode('=', $string[0]);

		$if_cond[0] = str_replace("if ", "", $if_cond[0]);
		$if_cond[0] = trim($if_cond[0]);

		if (!isset($if_cond[1])){
			if ($if_cond[0]){
				$output = trim($string[1]);
			}else{
				$output = '';
			}
		}else{

			$if_cond[1] = trim($if_cond[1]);

			if ($if_cond[0] == $if_cond[1]){
				$output = trim($string[1]);
			}else{
				$output = '';
			}
		}

		return $output;
	}

	function aiki_str_replace($text){
		global $aiki;

		$string = $aiki->get_string_between($text, "str_replace(", ");");
		$string = explode(",", $string );

		$output = str_replace($string[0], $string[1] , $string[2]);
			
		return $output;
	}

}

?>