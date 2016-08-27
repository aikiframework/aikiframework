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


if(!defined('IN_AIKI')){
    die('No direct script access allowed');
}


/**
 * Handles urls in the widget system, fixes, them, and grants creation
 * of widgets depending upon the url path set for a widget.
 *
 * @category    Aiki
 * @package     Library
 * @todo fix the class name to be Url
 */
class url {
    /**
     * Array in url parts (/user/details/212 will be [0]=>user,[1]=>details and [2]=>212
     *
     * @var string
     * @access public
     */

    public $url;
    public $pretty;

    /**
     * @var integer
     * @access public
     */
    public $url_count;


    /**
     * return first route of url
     * example: with foo/bar  -> return foo. In index return "homepage"
     * @return string First part of url, or "homepage".
     *
     */
    public function first_url(){
        return current($this->url);
    }

    /**
     * remove first route of url ( /foo/bar to /bar) .
     */
    public function shift_url(){
        if ( count($this->url) > 1 ) {
             $this->pretty = substr($this->pretty, strlen($this->url[0])+1) ;
             array_shift($this->url);
        } else {
            $this->url = array("homepage");
            $this->pretty = "";
        }
    }

    /**
     * Sets up the url for further processing.
     */
    public function __construct() {
        /**
         *
         * url procces requests transformed by .htaccess by this rule:
         * RewriteRule ^(.*)$ index.php?pretty=$1 [L,QSA]
         *
         * So, in homepage (direct index.php)) 'pretty' doesn't exist,
         *
         */

        if (isset($_GET['pretty'])) {
            $this->pretty= $_GET['pretty'];
            $this->url = explode("/", str_replace("|", "/", $this->pretty));
        } else {
            $this->url[0]="homepage";
            $this->pretty="";
        }

        $this->url_count = count($this->url);
    }


    /**
     * Apply a url on a query.
     *
     * @param array $query a constructed query
     * @return arry
     */
    public function apply_url_on_query($query)    {
        if (preg_match_all('/\(\!\((.*)\)\!\)/U', $query, $matches)) {
            foreach ($matches[1] as $parsed) {
                $query = @str_replace("(!($parsed)!)", $this->url[$parsed],
                                      $query);
            }
        }
        return $query;
    }


    /**
     * Clean up a url.
     * @param string $text
     * @return string
     */
    public function fix_url($text)
    {
        $text = trim($text);
        $text = strtr($text, array(
                      " " =>"_",
                      "'" =>"" ,
                      '"' =>""));
        $text = strtolower($text);

        return $text;
    }


    /**
     * match a list of displays_url (routes) against actual url.
     * @return boolean
     */

    public function match($displayString) {
        if ($displayString) {

            // a regular expression can containe a | character, that must be
            // escaped, so in this case use preg_split.
            if ( strpos($displayString,"#") !== false ) {
                $displayUrls= preg_split('#(?<!\\\)\|#', $displayString);
            } else {
                $displayUrls= explode("|", $displayString);
            }

            foreach ($displayUrls as $displayUrl) {

                if (!$displayUrl) {
                    continue;
                }

                // exact match. Example: news$ matchs news but not news/latest
                if ( substr($displayUrl, -1) == '$' ) {
                    if (substr($displayUrl,0,-1) == $this->pretty) {
                        return true;
                    } else {
                        continue;
                    }

                }

                // easy option
                if ( $displayUrl == "*"
                     || ( $this->pretty == '' && $displayUrl == 'homepage' )
                     || strpos($this->pretty,$displayUrl) === 0 ) {
                    return true;
                }

                //regular expression?
                if ( strpos($displayUrl, "#") === 0 &&
                        preg_match('/^#.+#[Uims]*$/', $displayUrl) ) {
                    $displayUrl = str_replace( "\|", "|", $displayUrl);  //unescaped
                    //it's a regex, so or match or continue.
                    if (preg_match($displayUrl, $this->pretty)) {
                        return true;
                    } else {
                        continue;
                    }
                }

                // can be /foo/bar/*..
                if ( strpos($displayUrl, "*") !== false ) {
                    // now the hard work user/details/1 must match user/details/*
                    $temp = str_replace("*","[^/]*", $displayUrl);
                    if (preg_match("#^" . $temp . '$#ui', $this->pretty)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }





} // end of url class

?>
