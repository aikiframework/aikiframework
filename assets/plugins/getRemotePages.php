<?php
if(!defined('IN_AIKI')){die('No direct script access allowed');}


class getRemotePages extends aiki
{
	function getRemotePages(){
		
	}
	
	function do_getRemotePages($text){

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