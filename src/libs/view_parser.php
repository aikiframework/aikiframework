<?php
/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Roger Martin
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


/*
$vars = array("view" => "bluemarine", "lang" => "this is", "language" => "eu", "vie" => "aa");


$style = "body { background: url(\$site/body.png); }
(css(bluemarine:
(css(es: esto es para es)css)
(css(eu: hau euskera da)css)
)css)

(css(mobil:
 hau no tiene que salir.<br>
)css)

(css(eu:
 hau bai <br>
)css)


view: \$view
saludo: \$hola
que: \$que vacio?
color: \$red
(declarations(
red= #fff
)declarations)

vista: \$view

(declarations(
hola= 10
que=
)declarations)
"; */


/**
 * viwe
 *
 *
 */


class view_parser {

	/**
	 * Parse conditional css. 
	 * Filter all "(css( )css)": if not match delete block else clean 
	 * begin and end delimiters
	 * 
	 * @param string $style Text to be filter
	 * @param array  $array Contain site,language and view variable (as keys) 
	 * 
	 */

	function parse($text, $view, $language) {
		global $aiki;	
						
		$position = array();
		while ($aiki->inner_markup($text, "(view(", ")view)", $position)) {
			$length = $position[1]+6-$position[0]; // 6 = len of ")view)" 
			$condition = explode ( ":",  substr( $text, $position[0]+6,$length-12),2); // 6=(view( 12
			if (isset($condition[1]) && $aiki->match_pair($condition[0],$view,$language)) {
				$content= $condition[1];
			} else {
				$content="";
			}
			$text= substr_replace($text, $content, $position[0], $length);	 
		} 
		return $text;
	}

}
