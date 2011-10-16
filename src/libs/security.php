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
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Library
 * @filesource
 */

if(!defined('IN_AIKI')) {
	die('No direct script access allowed');
}


/**
 * This class handles some security issues for aiki internals.
 *
 * @category	Aiki
 * @package	 Library
 * 
 * @todo rename this class to Security across codebase
 */
class security {
	/**
	 * Comb through and remove some scary markup from the fields.
	 * 
	 * Tag (including child) form, edit, script and iframe will be removed.
	 * 
	 * @param   string  $text   text for processing
	 * @return  string
	 */
	public function remove_markup($text) {
		$text = preg_replace("/\(\#\(form(.*)\)\#\)/Us", "", $text);

		$text = preg_replace("/\<edit\>(.*)\<\/edit\>/Us", "", $text);

		$text = preg_replace("/\<script(.*)\>(.*)\<\/script\>/Ui", "", $text);

		$text = preg_replace("/\<iframe(.*)\>(.*)\<\/iframe\>/Ui", "", $text);

		return $text;
	}

	/**
	 * Handle inline permission.
	 *
	 * @param     string      $text	        text for processing
	 * @return    string      text parsed.
	 * @global    aiki        $aiki   	
	 * @global    array       $db			
	 */
	public function inlinePermissions($text) {
		global $aiki, $db;
			
		if (preg_match_all('/\(\#\(permissions\:(.*)\)\#\)/Us', $text, $matchs)) {
			foreach ( $matchs[1] as $i => $inline_per ) {							
				if ( strpos ( $inline_per, "||") !== false )  {
					$get_sides = explode("||", $inline_per, 2);
									
					$sql = "SELECT group_level" .
						   " FROM  aiki_users_groups".
						   " WHERE group_permissions='". addslashes($get_sides[0]) ."'";
									
					$get_group_level = $db->get_var($sql);

					if ( $get_sides[0] == $aiki->membership->permissions ||
						$aiki->membership->group_level < $get_group_level ) {
						$replace = $get_sides[1];
					} else {			
						$replace = "";						
					}
					
					$text = str_replace($matchs[0][$i],$replace, $text);
				}
			}
		}
		return $text;
	} // end of inlinePermissions function

} // end of class

?>
