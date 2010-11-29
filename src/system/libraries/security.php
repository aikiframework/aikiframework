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
class security
{

	public function remove_markup($text){

		$text = preg_replace("/\(\#\(form(.*)\)\#\)/Us", "", $text);

		$text = preg_replace("/\<edit\>(.*)\<\/edit\>/Us", "", $text);

		$text = preg_replace("/\<script(.*)\>(.*)\<\/script\>/Ui", "", $text);

		$text = preg_replace("/\<iframe(.*)\>(.*)\<\/iframe\>/Ui", "", $text);

		return $text;
	}


	public function inlinePermissions($text){
		global $membership, $db;
			
		$inline = preg_match_all('/\(\#\(permissions\:(.*)\)\#\)/Us', $text, $matchs);
		if ($inline > 0){
			foreach ($matchs[1] as $inline_per){
				$get_sides = explode("||", $inline_per);

				$get_group_level = $db->get_var ("SELECT group_level from aiki_users_groups where group_permissions='$get_sides[0]'");

				if ($get_sides[0] == $membership->permissions or $membership->group_level < $get_group_level){
					$text = str_replace("(#(permissions:$get_sides[0]||$get_sides[1])#)", $get_sides[1], $text);
				}else{
					$text = str_replace("(#(permissions:$get_sides[0]||$get_sides[1])#)", '', $text);
				}

			}
		}

		return $text;

	}


}