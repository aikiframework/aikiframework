<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class markup_inline extends aiki
{
	
	function markup_inline($text){

		$inline = preg_match_all('/\(\#\(inline\:(.*)\)\#\)/U', $text, $matchs);

		if ($inline > 0){

			foreach ($matchs[1] as $inline_per){

				$content = file_get_contents($inline_per);

				$text = str_replace("(#(inline:$inline_per)#)", $content, $text);


			}
		}

		return $text;
	}

}
?>