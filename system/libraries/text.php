<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2009-2010 Aikilab inc
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */

/*
 * metaphone($str);
 * nl2br("Welcome\r\nThis is my HTML document", false);

 * Allow <p> and <a>
 * strip_tags($text, '<p><a>');
 *
 *
 *
 *
 */
class aiki_text
{



	/**
	 * strip numbers from given text
	 * @author Bassel Khartabil <b@ssel.me>
	 * @copyright Copyright (c) 2009, Bassel Khartabil
	 * @version 1
	 */
	function strip_numbers($value){
		$toreplace = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
		$value = str_replace($toreplace, '', $value);
		return $value;
	}

	function text_to_req_filed($text){

		$text = strip_tags($text);
		$text = stripslashes($text);
		$text = strtolower($text);
		$replace = array("!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "/", "}", "{", "?");
		$text = str_replace($replace, "", $text);
		$text = trim($text);
		$text = str_replace(" ", "_", $text);
		return $text;
	}



	function createnormaldate($pubdate){
		if ($pubdate> 0){
			$year = substr($pubdate, 0, 4);
			$month = substr($pubdate, 4, 2);
			$dayin = substr($pubdate, 6, 2);
		}
		switch ($month){
			case 1:
				$month = ("كانون الثاني");
				break;
			case 2:
				$month = ("شباط");
				break;
			case 3:
				$month = ("آذار");
				break;
			case 4:
				$month = ("نيسان");
				break;
			case 5:
				$month = ("أيار");
				break;
			case 6:
				$month = ("حزيران");
				break;
			case 7:
				$month = ("تموز");
				break;
			case 8:
				$month = ("آب");
				break;
			case 9:
				$month = ("أيلول");
				break;
			case 10:
				$month = ("تشرين الأول");
				break;
			case 11:
				$month = ("تشرين الثاني");
				break;
			case 12:
				$month = ("كانون الأول");
				break;

		}
			
		$pubdate = "<small><font color=\"#a0a0a0\">$dayin/$month/$year</font></small>";
		return $pubdate;
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

	//a dot lyskawa at pp-layouts dot com
	//http://ca.php.net/manual/en/function.str-ireplace.php
	/**
	* Case insensitive multi-byte safe str_ireplace
	* @param string $search
	* @param string $replace
	* @param string $subject
	* @param int $count
	* @param string $encoding
	* @return string
	*/
	function mb_str_ireplace($search, $replace, $subject, $count = null, $encoding = u8) {
		$l1 = mb_strlen($search, $encoding);
		$l2 = mb_strlen($replace, $encoding);
		$rc = 0;
		$offset = 0;
		while(ok($p = mb_stripos($subject, $search, $offset, $encoding)) && (is_null($count) || $rc <= $count)) {
			if (ok($p))
			$subject = mb_substr($subject, 0, $p, $encoding) . $replace . mb_substr($subject, $p + $l1, mb_strlen($subject, $encoding), $encoding);
			$offset = $p + $l2;
			$rc++;
		}
		return $subject;
	}

	//dmitry dot polushkin at gmail dot com
	//http://ca.php.net/manual/en/function.similar-text.php
	// returns the percentage of the string "similarity"
	function str_compare($str1, $str2) {
		$count = 0;

		$str1 = ereg_replace("[^a-z]", ' ', strtolower($str1));
		while(strstr($str1, '  ')) {
			$str1 = str_replace('  ', ' ', $str1);
		}
		$str1 = explode(' ', $str1);

		$str2 = ereg_replace("[^a-z]", ' ', strtolower($str2));
		while(strstr($str2, '  ')) {
			$str2 = str_replace('  ', ' ', $str2);
		}
		$str2 = explode(' ', $str2);

		if(count($str1)<count($str2)) {
			$tmp = $str1;
			$str1 = $str2;
			$str2 = $tmp;
			unset($tmp);
		}

		for($i=0; $i<count($str1); $i++) {
			if(in_array($str1[$i], $str2)) {
				$count++;
			}
		}

		return $count/count($str2)*100;
	}


	//bmorel at ssi dot fr
	//http://www.php.net/utf8_encode
	function seems_utf8($Str) {
		for ($i=0; $i<strlen($Str); $i++) {
			if (ord($Str[$i]) < 0x80) $n=0; # 0bbbbbbb
			elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif ((ord($Str[$i]) & 0xF0) == 0xF0) $n=3; # 1111bbbb
			else return false; # Does not match any model
			for ($j=0; $j<$n; $j++) { # n octets that match 10bbbbbb follow ?
				if ((++$i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80)) return false;
			}
		}
		return true;
	}




	//phpC2007
	//http://ca.php.net/manual/en/function.count-chars.php
	function utf8_count_strings($stringChar)
	{
		$num = -1;
		$lenStringChar = strlen($stringChar);

		for ($lastPosition = 0;
		$lastPosition !== false;
		$lastPosition = strpos($textSnippet, $stringChar, $lastPosition + $lenStringChar))
		{
			$num++;
		}

		return $num;
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