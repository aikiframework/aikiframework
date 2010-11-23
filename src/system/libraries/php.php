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
 * @package     Library
 * @filesource
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


/**
 * BriefDescription
 *
 * @category    Aiki
 * @package     Library
 */
class php
{

	public function parser($text){
		global $aiki;

		if (!preg_match ("/\<form(.*)\<php (.*) php\>(.*)\<\/form\>/Us", $text)){

			$php_matchs = preg_match_all('/\<php (.*) php\>/Us', $text, $matchs);

		}else{

			$php_matchs = 0;
		}

		if ($php_matchs > 0){

			foreach ($matchs[1] as $php_function){

				if (preg_match('/eval((.*));/Us', $php_function)){
					$php_output = $this->aiki_eval($php_function);
				}

				if (preg_match('/str_replace((.*));/Us', $php_function)){
					$php_output = $this->aiki_str_replace($php_function);
				}

				if (preg_match('/substr((.*));/Us', $php_function)){
					$php_output = $this->aiki_substr($php_function);
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

	/**
	 * Evaluate a string as PHP code.
	 * 
	 * @param   string $text The complete PHP statement such as: eval(echo "hello";);
	 * @return  mixed $result Returns NULL unless return is called in the evaluated code, in which case the value passed to return is returned. If there is a parse error in the evaluated code, eval() returns FALSE and execution of the following code continues normally
	 */
	public function aiki_eval($text){
		global $aiki;

		$code = $aiki->get_string_between($text, "eval(", ")");

		$result = eval($code);
			
		return $result;
	}

	public function aiki_function($text){
		global $aiki;

		//function does not have vars
		if (preg_match('/\$aiki\-\>(.*)\-\>(.*)\(\)\;/Us', $text)){

			$class = $aiki->get_string_between($text, '$aiki->', '->');
			$function = $aiki->get_string_between($text, '$aiki->'.$class.'->', '();');

			if (isset($aiki->$class)){
				$output = $aiki->$class->$function();
			}else{
				//try to load the library
				$aiki->load($class);

				if (isset($aiki->$class)){
					$output = $aiki->$class->$function();
				}else{
					$output = '';
				}
			}

			//function has vars
		}elseif (preg_match('/\$aiki\-\>(.*)\-\>(.*)\((.*)\)\;/Us', $text)){

			$class = $aiki->get_string_between($text, '$aiki->', '->');
			$function = $aiki->get_string_between($text, '$aiki->'.$class.'->', '(');

			$vars_array = preg_match('/'.$function.'\((.*)\)\;$/Us', $text, $vars_match);
			if ($vars_match[1]){
				$vars_array = $vars_match[1];
			}else{
				$vars_array = '';
			}

			if (isset($aiki->$class)){
				$output = $aiki->$class->$function($vars_array);

			}else{
				//try to load the library
				$aiki->load($class);

				if (isset($aiki->$class)){
					$output = $aiki->$class->$function($vars_array);
				}else{
					$output = '';
				}

			}

		}

		return $output;
	}

	public function aiki_htmlspecialchars($text){
		global $aiki;

		$original_string = $aiki->get_string_between($text, "htmlspecialchars(", ")");

		$string = htmlspecialchars($original_string);

		$text = str_replace("htmlspecialchars($original_string)", $string, $text);
			
		return $text;
	}


	public function aiki_if_then($text){
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

	public function aiki_str_replace($text){
		global $aiki;

		$string = $aiki->get_string_between($text, "str_replace(", ");");
		$string = explode(",", $string );

		$output = str_replace($string[0], $string[1] , $string[2]);
			
		return $output;
	}

	public function aiki_substr($text){
		global $aiki;

		$string = $aiki->get_string_between($text, "substr(", ");");
		$string = explode(",", $string );

		if ($string[2]){
			$output = substr($string[0], $string[1] , $string[2]);
		}else{
			$output = substr($string[0], $string[1]);
		}
			
		return $output;
	}

}

?>