<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

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

				$text = str_replace("<php $php_function php>", $php_output , $text);
			}
		}

		return $text;
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