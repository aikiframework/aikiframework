<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class aiki_text
{


	function __construct(){

	}

	function aiki_nl2br($text){
		global $aiki;

		$nl2br = $aiki->get_string_between($text, "[br[", "]br]");
		if ($nl2br){
			$nl2br_processed = nl2br($nl2br);
			$text = str_replace("[br[".$nl2br."]br]", $nl2br_processed, $text);
		}

		return $text;
	}

	function aiki_nl2p($text){
		global $aiki;

		$nl2p = $aiki->get_string_between($text, "[p[", "]p]");

		if ($nl2p){

			$nl2p_text = str_replace("\n\r", "</p><p>", $nl2p);

			$nl2p_text = "<p>".$nl2p_text."</p>";
				
			$nl2p_text = nl2br($nl2p_text);
				
			$nl2p_text = str_replace("<br />\r</p>", "</p>", $nl2p_text);
			$nl2p_text = str_replace("<p><br />", "<p>", $nl2p_text);
			$nl2p_text = str_replace("<p></p>", "<br />", $nl2p_text);
				

			$text = str_replace("[p[".$nl2p."]p]", $nl2p_text, $text);

		}

		return $text;
	}


	//pascalaschwandenPLEASENOSPAM at gmail dot com
	//http://ca.php.net/manual/en/function.ereg.php
	/*Example:
	$phone = "789-1234";
	if (is_valid("Phone",$phone)) {
	echo "Valid Phone Number";
	} else {
	echo "Invalid Phone Number";
	}
	*/
	//added email check by Bassel Khartabil on 14-9-2008
	function is_valid($type,$var) {
		$valid = false;
		switch ($type) {
			case "email":
				if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $var)) {
					$valid = true;
				}
				break;
			case "IP":
				if (ereg('^([0-9]{1,3}\.){3}[0-9]{1,3}$',$var)) {
					$valid = true;
				}
				break;
			case "url":
				if (ereg("^[a-zA-Z0-9\-\.]+\.(com|org|net|mil|edu|ws|biz|info)$",$var)) {
					$valid = true;
				}
				break;
			case "SSN":
				if (ereg("^[0-9]{3}[- ][0-9]{2}[- ][0-9]{4}|[0-9]{9}$",$var)) {
					$valid = true;
				}
				break;
			case "CC":
				if (ereg("^([0-9]{4}[- ]){3}[0-9]{4}|[0-9]{16}$",$var)) {
					$valid = true;
				}
				break;
			case "ISBN":
				if (ereg("^[0-9]{9}[[0-9]|X|x]$",$var)) {
					$valid = true;
				}
				break;
			case "Date":
				if (ereg("^([0-9][0-2]|[0-9])\/([0-2][0-9]|3[01]|[0-9])\/[0-9]{4}|([0-9][0-2]|[0-9])-([0-2][0-9]|3[01]|[0-9])-[0-9]{4}$",$var)) {
					$valid = true;
				}
				break;
			case "Zip":
				if (ereg("^[0-9]{5}(-[0-9]{4})?$",$var)) {
					$valid = true;
				}
				break;
			case "Phone":
				if (ereg("^((\([0-9]{3}\) ?)|([0-9]{3}-))?[0-9]{3}-[0-9]{4}$",$var)) {
					$valid = true;
				}
				break;
			case "HexColor":
				if (ereg('^#?([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$',$var)) {
					$valid = true;
				}
				break;
			case "User":
				if (ereg("^[a-zA-Z0-9_]{3,16}$",$var)) {
					$valid = true;
				}
				break;
		}
		return $valid;
	}




	//Modified version of:
	//jgriggs777 at yahoo dot com
	//http://ca.php.net/manual/en/function.str-ireplace.php

	function highlight_this($text, $words) {
		$words = trim($words);
		$the_count = 0;
		$wordsArray = explode(' ', $words);
		foreach($wordsArray as $word) {
			if(strlen(trim($word)) != 0)

			//exclude these words from being replaced
			//$exclude_list = array("word1", "word2", "word3");
			// Check if it's excluded
			//if ( in_array( strtolower($word), $exclude_list ) ) {

			//} else {

			//http://www.webdeveloper.com/forum/showpost.php?p=381088&postcount=6
			$text = preg_replace('/'.$word.'(?![^<]*>)/', "<span class=\"highlight\">".$word."</span>", $text);

			//$text = str_ireplace($word, "<span class=\"highlight\">".strtoupper($word)."</span>", $text, $count);
			$the_count = $count + $the_count;
			//}

		}

		return $text;
	}


	function highlight_and_crop_this($text, $words) {
		$words = trim($words);
		$the_count = 0;
		$wordsArray = explode(' ', $words);
		foreach($wordsArray as $word) {
			if(strlen(trim($word)) != 0)

			//exclude these words from being replaced
			$exclude_list = array("word1", "word2", "word3");
			// Check if it's excluded
			if ( in_array( strtolower($word), $exclude_list ) ) {

			} else {
				$text = "<textstarthere>".$text."</textstarthere>";
				$text = str_ireplace($word, "<span class=\"highlight\">$word</span>[[\\//]]", $text, $count);
			}

		}
		//$text = explode("[[\\//]]", $text);
		$is_big_text = mb_substr($text, 0, 1000);

		if ($text != $is_big_text){
			$count = preg_match("/". preg_quote('<textstarthere>', "/").'(.*)'.preg_quote('[[\\//]]', "/") ."/U",$text,$result);
			$count_rest = preg_match("/". preg_quote('[[\\//]]', "/").'(.*)'.preg_quote('</textstarthere>', "/") ."/U",$text,$result_rest);
			$text = $result[1]."......".$result_rest[1]."...";
		}
		$text = str_replace("<textstarthere>", "", $text);
		$text = str_replace("[[\\//]]", "", $text);
		$text = str_replace("[[\//]]", "", $text);
		$text = str_replace("</textstarthere>", "", $text);
		//$text = $text[0].$text[1];
		return $text;
	}


	/**
	 * @return string
	 * @param int $i
	 * @desc Returns 'white' for even numbers and 'yellow' for odd numbers
	 */
	//usage:  print "<tr bgcolor=\"".row_color($i)."\">\n";
	function row_color($i)
	{
		$bgcolor1 = "white";
		$bgcolor2 = "yellow";

		if ( ($i % 2) == 0 ) {
			return $bgcolor1;
		} else {
			return $bgcolor2;
		}
	}

}
?>