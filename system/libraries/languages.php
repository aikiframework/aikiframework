<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

class aiki_languages
{


	function L10n($string){
		global $db, $config;

		$count = preg_match_all( '/\[\[(.*)\]\]/', $string, $matches );
		if ($count >0){
			$default_language = "lang_".$config['default_language'];

			foreach ($matches[1] as $parsed)
			{
				$parsed = trim($parsed);
				$short_term = $db->get_row("SELECT $default_language FROM aiki_dictionary WHERE short_term = '$parsed'");
				if ($short_term){
					$string = str_replace("[[$parsed]]", $short_term->$default_language, $string);
				}

			}
		}
		return $string;
	}


}
?>