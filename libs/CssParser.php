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

if (!defined('IN_AIKI')) {
    die('No direct script access allowed');
}

/**
 * Parses CSS inserting variables, evaluating expressions and
 * filtering conditional block.
 *
 *
 */

class CssParser {

    /**
     * Complete parse of a text.
     *
     * @param string $style Text to be filter
     * @param array  $array initial variables.
     *
     * @return string. Filtered css.
     *
     */
    function parse($style, $vars) {
        $style = $this->style_parse_conditional_css($style, $vars);
        $style = $this->style_parse_declarations($style, $vars);
        $style = $this->style_parse_vars($style, $vars);
        return $style;
    }

    /**
     * Parse conditional css.
     * Filter all "(css( )css)": if not match delete block else clean
     * begin and end delimiters
     *
     * @param string $style Text to be filter
     * @param array  $array Contain site,language and view variable (as keys)
     *
     */
    function style_parse_conditional_css($style, $vars) {
        global $aiki;

        $view = $aiki->site->view();
        $language= $aiki->site->language();

        $position = array();
        while ($aiki->inner_markup($style, "(css(", ")css)", $position)) {
            $length = $position[1]+5-$position[0]; // 5 = len of ")css)"
            $condition = explode(":", substr($style, $position[0]+5, $length-10), 2); // 5=(css( 10=(css(+)css)
            if (isset($condition[1]) && $aiki->match_pair($condition[0], $view, $language) ) {
               $content = $condition[1];
            } else {
               $content = "";
            }
            $style = substr_replace($style, $content, $position[0], $length);
      }
      return $style;
    }

    /**
     * Parse variable block in style.
     *
     * @param string $style text (plain css) to be filter
     * @param byref array $array var
     *
     * @return string $style filtered
     */

    function style_parse_declarations($style, &$var) {
        global $aiki;

        $view = $aiki->site->view();
        $language = $aiki->site->language();

        if (preg_match_all("#\(declarations\((.*)\)declarations\)#Us", $style, $matches)) {

            foreach ($matches[1] as $i => $declarations) {
                    $declarations = preg_replace ("#/\*.*\*/#Us", "", $declarations);   // supress comentaries
                    $lines = explode ("\n", $declarations);
                    $firstline = true;

                    foreach ($lines as $line) {
                        $line= trim($line);  // clean line.

                        // test first conditional line. It's optional
                        if ($firstline) {
                            if ( $line == "" || $aiki->match_pair(substr($line, 0, -1), $view, $language) ) {
                                $firstline = false;
                                continue;
                            } else {
                                break;
                            }
                        }

                        if ( !$line || strpos($line, "=") === false ) {
                            continue;
                        }
                        list($key, $value) = explode("=", $line, 2);

                        $key = trim($key);
                        $value = trim($value);

                        if ( substr($key, 0, 1) != '$' ) {
                            continue;
                        }
                        $key = substr($key, 1);

                        if ( $value && substr($value,0,1) == "(" && substr($value, -1, 1) == ")" ) {

                            $value= substr($value,1,-1);
                            $value= meval($value, $var);
                        }

                        $var[$key] = $value;

                    }
            }
            $style= preg_replace("#\(declarations\(.*\)declarations\)#Us", "", $style);
        }
        return $style;
    }


    /**
     * Insert variable values in text style
     * begin and end delimiters
     *
     * @param string $style text (plain css) to be filter
     * @param array $var variables.
     *
     * @return string $style parsed.
     */

    function style_parse_vars(&$style, &$vars) {
        global $aiki;
        // variables
        krsort($vars); // important! $username must be replace first than $user
        foreach ($vars as $var=>$value) {
            $style= str_replace("\$$var", $value, $style);
        }

        // expressions
        $empty = array();
        if (preg_match_all('~\(([0-9' . preg_quote("+- /\*()%") .  ']*)\)~', $style, $matches)) {
            foreach ($matches[1] as $i=>$match) {
                $match = preg_replace('~\s~', '', $match); //remove space necesary only for human ;-)
                $expresions[$matches[0][$i]] = $aiki->eval_expression($match, $empty);
            }
            $style= strtr($style,$expresions);
        }

        return $style;

    }


}

?>