<?php
/**
 * Translation system based on .PO files
 *
 * It translates always from english to another language.
 * Translations is stored in .po files and loaded in memory, so think before
 * use it with great files.
 *
 * @author      Roger Martin
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     free (at least Afero GPL)
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     utility
 *
 *
 */


class TranslateUsingPo {
    private $domains;       // Domains or dictionaries
    private $translations;  // Available translation for domains
    private $translateTo;   // iso code for language.
    private $defaultDomain; // first added domain or false.

    /**
     *
     * Create the translator.
     *
     * @param $translatTo string to translate by default (default "en").
     * @param $checkGet mixed $_GET key to seek language
     * @param $checkSession mixed $_SESSION key to seek / store language
     */

    function __construct( $translateTo="en", $checkGet=false, $checkSession=false){
        $this->translateTo($translateTo);
        if ( $checkGet || $checkSession) {
            $this->checkLanguage($checkGet, $checkSession);
        }
    }


    /**
     *
     * Change / set the language to translate, unloading all domains
     *
     * @param $newValeu Language to translate (use ISO code, please)
     */

    function translateTo($newValue=NULL){
        if ( !is_null( $newValue) ){
            $this->translateTo  = $newValue;
            $this->domains = array();
            $this->translations= array();
            $this->defaultDomain = false;

        }
        return $this->translateTo;
    }

    /**
     *
     * Try to get language to translate
     * Check $_GET and $_SESSION (in this order) to set language.
     *
     * @param $getVar $_GET key or variable to exam
     * @param $sessionVar $_SESSION key or variable to exam and store the value.
     */

    function checkLanguage( $getVar ="_language", $sessionVar="language"){

        //  set language by Get,session or "en"
        if ( $getVar && isset($_GET[$getVar]) ){
           $found= $_GET[$getVar];
        } elseif ( $sessionVar && isset($_SESSION[$sessionVar]) ){
           $found= $_SESSION[$sessionVar];
        } else {
           $found= false;
        }

        // check language. Only ISO is allowed: en, es, or en_GB.
        if ( $found && preg_match('/^[a-z]{2}(\_[A-Z]{2})?$/', $found) ){
            $this->translateTo= $found;
            if ( $sessionVar ){
                $_SESSION[$sessionVar]= $found;
            }
        }
    }

    /**
     *
     * Add a new domain loading the po file
     * @param $domain name of new domain
     * @param $poFileDir directory with all po files. Only poFileDir/LANGUAGE.po will be loaded.
     * @return true if domain have been created or is not necesary because language is english.
     */

    function addDomain ($domain, $poFileDir ){

        if ( !is_dir($poFileDir) ){
            return false;
        }


        // load po file if necesary
        if ( $this->translateTo!='en' && $text = file_get_contents($poFileDir ."/". $this->translateTo . ".po" )){

            //create array of term=>translation
            $dictionary= array();
            $matchs  = "";
            $pattern = '/msgid "(.*)"\s*msgstr "(.*)"/U';
            if ( preg_match_all ( $pattern, $text, $matchs) ){
                foreach ($matchs[1] as $i => $word ){
                    if ($matchs[2][$i]){
                        $dictionary[$word] = $matchs[2][$i];
                    }
                }
                $this->domains[$domain] = $dictionary;
            }
        }

        // search all availabled languages
        $founded = array();
        $start   = strlen($poFileDir)+1;
        foreach ( glob($poFileDir ."/*.po") as $poFile ) {
            // extract path and extension of file name.
            $founded []= substr($poFile,$start,-3);
        }
        $this->translations[$domain]= $founded;

        // set defaultDomain
        if ( $this->defaultDomain === false ) {
           $this->defaultDomain = $domain;
        }


        return ($this->translateTo=='en' || isset($this->domains[$domain]) );
    }

    function translations($domain=NULL ){
        if ( is_null($domain) ){
            if ( $this->defaultDomain=== false ){
                return false;
            }
            $domain= $this->defaultDomain;
        }
        return isset ( $this->translations[$domain] ) ? $this->translations[$domain] : false;
     }



    /**
     *
     * translate string
     *
     * @param string $string to translate (in english)
     * @return string translated string or original if can't translate
     *
     */

    function t($string, $domain=NULL ){

        if ( is_array($string) ){ // translation make by preg_replace_callback
            $string = $string[1];
        }

        if ( !$string || $this->translateTo=="en" || $this->defaultDomain=== false ){
            return $string;
        }
        if ( is_null($domain) ) {
            $domain = $this->defaultDomain;
        }

        return  (
           isset($this->domains[$domain]) &&
           isset($this->domains[$domain][$string]) ) ? $this->domains[$domain][$string] : $string;
    }


}
