<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2009-2010 Aikilab inc
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */



class aiki_xml
{
	function rss_parser($text){
		global $aiki;

		$rss_matchs = preg_match_all('/\<rss (.*) rss\>/Us', $text, $matchs);

		if ($rss_matchs > 0){

			foreach ($matchs[1] as $rss){

				$rss_url = trim($rss);
				$content = file_get_contents($rss_url );

				if ($content !== false) {
					$output = '';
					$xml = new SimpleXMLElement($content);

					foreach ($xml->channel->item as $item) {
						$output .= "<a href='$item->link'>".$item->title.'</a><br />';
						$output .= $item->description.'<br />';
					}
				}

				$text = str_replace("<rss $rss rss>", $output , $text);
			}
		}

		return $text;
	}

}


?>