<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class text
{


	public function aiki_nl2br($text){
		global $aiki;

		$nl2br = $aiki->get_string_between($text, "[br[", "]br]");
		if ($nl2br){
			$nl2br_processed = nl2br($nl2br);
			$text = str_replace("[br[".$nl2br."]br]", $nl2br_processed, $text);
		}

		return $text;
	}

	public function aiki_nl2p($text){
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
	public function is_valid($type,$var) {
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

}
?>