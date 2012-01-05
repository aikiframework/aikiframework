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

require_once("dictionary.php");

if (!defined('IN_AIKI')) {
	die('No direct script access allowed');
}

/**
 * Aiki class for parsing string with L10, basicaly replacing ocurrences of
 * __something__ with results searched in aiki_dictionary
 *
 * The markup is __STRING__, where STRING can't cointains space or '.
 * For example: __next_page__ , __read_more__ __año_siguiente__ are valid,
 * but __ not found__ or __joe's_tabern__ is invalid.
 * Length of string must be between 1 and 255.
 *
 * If string is not founded in aiki_dictionary, it will remain untouched.
 *
 * Note: 
 * The code of language is irrelevant for this class, but L10 only
 * will work fine, if code used in aiki_languages, aiki_dictionary, 
 * and $config are coherent.
 * 
 */

class languages {

	public $language;
	public $dir;

	/**
	 * construct the clase
	 * 
	 * Try to set the language using $aiki->site
	 */

	function __construct() {		
		$this->language = "en" ;
		$this->dir      = "ltr";
	}


	/**
	 * set the language.
	 *
	 * The language is set if it is defined in aiki_languages table..
	 * @param string $lang Default_language
	 * @return boolean true if the language is valid (exist in aiki_languages) else, false.
	 */
	function set($lang) {
		global $db, $config;

		$is_real_language = $db->get_row("SELECT sys_name,dir, short_name FROM aiki_languages WHERE short_name='$lang'");
		if (isset($is_real_language->sys_name)) {
			$this->language = $lang;
			$config['default_language'] = $lang;
			$this->dir = $is_real_language->dir;
			return true;
		}

		return false;
	}

   
	/**
	 * Parses a string replacing __Something__ 
	 *
	 * 1. searchs all ocurrences of __something__, where something is a string of characters, except space and '.
	 * for example: __encoding__  __Error_adding_database__. 
	 * not valid: __error during__ (contains space) __Joe's_netbook__ (contains ')
	 * 2. Searchs ocurrence in aiki_dictionary, and replace that founded.
	 * A not founded string will not replaced.
	 * 
	 * @param string $string Input string.
	 * @retun string Parsed string. All ocurrences of __Something__ are replaced, in found in aiki_dictionary.
	 */
	public function L10n($string) {
		global $db, $aiki;

		if (preg_match_all("/__([a-z_]{1,255})__/U", $string, $matches)){
		
			$default_language = "lang_". $aiki->site->language();
			$founded= array_unique($matches[1]);
			unset($matches);

			$query= " SELECT short_term, $default_language FROM aiki_dictionary ".
					" WHERE short_term='" . implode("' or short_term='", $founded ) . "'";
			unset($founded);		
			
			$terms = $db->get_results($query);
			
			// if not terms was found or the first terms doesn't have a $default_language column
			// is not necessary make replacements.
			if ( $terms && isset(reset($terms)->$default_language) ) {
				foreach ( $terms as $term ) {
					if ($term->$default_language) {
						// only founded terms will be replaced 
						$replace[ "__{$term->short_term}__" ] = $term->$default_language;
					}
				}
				
				if (count($replace)) {
					$string = strtr($string, $replace) ;
				}
			}
				
		}
		return $string;
	}

}

?>
