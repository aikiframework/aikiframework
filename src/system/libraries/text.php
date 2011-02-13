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
class text
{


	public function aiki_nl2br($text){

		if ( preg_match_all( "#\[br\[(.*)\]br\]#sUi", $text, $captured) ){
			foreach ($captured[1] as $i=>$match){
				$replace[ $captured[0][$i]]= nl2br($match);
			}
			$text= strtr($text, $replace);
		}
		return $text;
	}

	public function aiki_nl2p($text){
		global $aiki;

		if ( preg_match_all( "#\[p\[(.*)\]p\]#sUi", $text, $captured) ){

			foreach ($captured[1] as $i=>$match){

				$nl2p_text = str_replace("\n\r", "</p><p>", $match);

				$nl2p_text = "<p>".$nl2p_text."</p>";

				$nl2p_text = nl2br($nl2p_text);

				$nl2p_text = str_replace("<br />\r</p>", "</p>", $nl2p_text);
				$nl2p_text = str_replace("<p><br />", "<p>", $nl2p_text);
				$replace[ $captured[0][$i]] = str_replace("<p></p>", "<br />", $nl2p_text);
			}
			$text= strtr($text, $replace);

		}

		return $text;
	}

	public function is_valid($type,$var) {
		$valid = false;
		switch ($type) {
			case "email":
				if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $var)) {
					$valid = true;
				}
				break;
			case "IP":
				if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',$var)) {
					$valid = true;
				}
				break;
			case "url":
				if (preg_match("/^[a-zA-Z0-9\-\.]+\.(com|org|net|mil|edu|ws|biz|info)$/",$var)) {
					$valid = true;
				}
				break;
			case "SSN":
				if (preg_match("/^[0-9]{3}[- ][0-9]{2}[- ][0-9]{4}|[0-9]{9}$/",$var)) {
					$valid = true;
				}
				break;
			case "CC":
				if (preg_match("/^([0-9]{4}[- ]){3}[0-9]{4}|[0-9]{16}$/",$var)) {
					$valid = true;
				}
				break;
			case "ISBN":
				if (preg_match("/^[0-9]{9}[[0-9]|X|x]$/",$var)) {
					$valid = true;
				}
				break;
			case "Date":
				if (preg_match("/^([0-9][0-2]|[0-9])\/([0-2][0-9]|3[01]|[0-9])\/[0-9]{4}|([0-9][0-2]|[0-9])-([0-2][0-9]|3[01]|[0-9])-[0-9]{4}$/",$var)) {
					$valid = true;
				}
				break;
			case "Zip":
				if (preg_match("/^[0-9]{5}(-[0-9]{4})?$/",$var)) {
					$valid = true;
				}
				break;
			case "Phone":
				if (preg_match("/^((\([0-9]{3}\) ?)|([0-9]{3}-))?[0-9]{3}-[0-9]{4}$/",$var)) {
					$valid = true;
				}
				break;
			case "HexColor":
				if (preg_match('/^#?([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/',$var)) {
					$valid = true;
				}
				break;
			case "User":
				if (preg_match("/^[a-zA-Z0-9_]{3,16}$/",$var)) {
					$valid = true;
				}
				break;
		}
		return $valid;
	}

}