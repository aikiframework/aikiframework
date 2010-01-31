<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2009-2010 Aikilab inc
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class markup_extlinks extends aiki
{


	function markup_extlinks($text){
		global $aiki;

		$tags_output = array();

		$count = preg_match_all( '/'."\(\+\(".'(.*)'."\)\+\)".'/U', $text, $match );

		if ($count > 0){

			foreach ($match[1] as $tag_match){

				$tag_match_array = explode('=', $tag_match);

				if (!isset($tag_match_array[1])){

					$tag_text = $tag_match_array[0];
					$tag_equivalent = $tag_match_array[0];

				}else{

					$tag_text = $tag_match_array[0];
					$tag_equivalent = $tag_match_array[1];

				}


				//TODO: make sure it's correct link and if not correct it, and check for email addresses
				$processed_tag = "<a target=\"_blank\" href=\"$tag_equivalent\" style=\"background:transparent url(".$aiki->setting['url']."/images/skins/assets/external.png) no-repeat scroll left center; padding-left:13px;\">".$tag_text.'</a>';


				$tags_output[] .= $processed_tag;

			}
			$text = str_replace($match[0], $tags_output, $text);

		}


		return $text;

	}

}
?>