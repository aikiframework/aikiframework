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
 *
 * BriefDescription
 *
 * This small module wants to be an attempt to unify all outbound
 * messages from the application. Provides a function called 'message'
 * charge of doing all the work, and a class called 'message' that
 * can be added to object 'aiki'.
 *
 * No CSS is added except in two cases:
 * - object aiki don't exist ( a error in init process?)
 * - the user specifies the css (a practice whose use is not recommended)
 * For all other cases it is assumed there is a css file who definid the styles
 * of messages.
 *
 * The module redefined three types of messages:
 * error, warning, ok, that generate messages of the
 * class 'message-error', 'message-warning', 'message-ok', respectively.
 *
 *
 * Examples:
 * if (!$site_info){
 *	die( message("Fatal Error: Wrong site name provided",NULL, false) );
 *
 * $aiki->message->warning("No clipart's found");
 * $aiki->message->error("No file upload", array("class"=>"fatal")); class will be 'message-error fatal'
 * $aiki->message->general("Hi, Roger", array("id"=>"welcome"));
 *
 */


if(!defined('IN_AIKI')){die('No direct script access allowed');}

/*
 * function message
 *
 * draft horse of module. Show or return a message.
 *
 * @param $text : text to show. Will be translated if there is a
 *                 t (translate) function or is working $aiki->languages
 * @param $attribs (optional=NULL): array of html atributes (id,class, style,onmouse)..
 * @param echo  ($option=false) true, echo the result, and false return it.
 */


function message($text,$attribs=NULL, $echo = true) {
        global $aiki;

        if ( !isset($aiki) and !isset($attribs) ){
            // when there isn't aiki object and not attributes are provides, assumes error.
            $attribs['style']="background-color:#F8F8FF;color:#c00;font-weight:bold;padding:4px 6px;";
        }

        $tag  = ( isset($attribs["tag"]) ? $attribs["tag"] : "div");
        $cRet = "<$tag";
        if (is_array($attribs)) {
           foreach ( $attribs as $k=>$v) {
                if( $k !="tag"){
                    $cRet .= " $k='$v' ";
                }
           }
        }
        if ( function_exists("t")) {
           $text= t($text);
        } elseif ( isset( $aiki->languages) ){
           $text= $aiki->languages->L10n($text);
        }
        $cRet .= ">". $text. "</$tag>";
        if ( !$echo ) {
           return $cRet;
        }
        echo $cRet;
    }


class message {
    private $stored;

    function __construct(){
        $this->stored=array();
    }

    function store($message, $key="", $add=true){
        if ( $key==""){
            $this->stored[]= array($message);
            return end($this->stored);
        } elseif ( $add===false ) {
            $this->stored[$key]= array($message);
            return $key;
        } elseif ( $add===true ) {
            $this->stored[$key][]= $message;
            return $key;
         } else {
            $this->stored[$key][$add]= $message;
        }
    }

    function unstore( $key="*", $echo = false, $clear= true, $glue="\n"){
        $ret="";
        if ( $key=="*" ) {
           $ret="";
           foreach ( $this->stored as $k=>$v){
                $ret.= implode($glue,$v);
           }
           if ( $clear) {
               $this->stored= array();
           }
        } else {
            if ( isset($this->stored[$key]) ) {
                $ret= implode($glue,$this->stored[$key]);
                if ( $clear ){
                   unset($this->stored[$key]);
                }
            }
        }
        if ($echo){
           echo $ret;
        }
        return $ret;
    }


    function order($key, $order="asc"){
        if ( !isset($this->stored[$key]) ) {
            return false;
        }
        switch ( strtolower($order) ){
            case "asc"         : ksort($this->stored[$key])  ; break;
            case "desc"        : krsort($this->stored[$key]) ; break;
            case "asc-values"  : asort($this->stored[$key])  ; break;
            case "desc-values" : arsort($this->stored[$key]) ; break;
            case "natural"     : natsort($this->stored[$key]); break;
            default : return false;
        }
        return true;
    }

    function set($key,$message){
        return $this->store($message,$key, false);
    }

    function add($key,$message,$order=false){
        return $this->store($message,$key, ( $order===false ? true: $order) );
    }

    function get($key, $glue="ul" ){
        if ( !isset($this->stored[$key]) ){
          return ( $glue=="as array"? array() : "" );
        }

        $tag= array();;
        if ( preg_match('#^<([^ />]*) ?[^>]*>$#s',$glue,$tag) ){
            // it's a HTML tag
            $tag= $tag[1];
            echo "*** $tag ***";
            switch ( $tag ){
                case "ul":
                case "ol": return "$glue<li>". implode("</li><li>", $this->stored[$key]) ."</li></$tag>";
                case "br": return implode($glue, $this->stored[$key]);
                default  : return "$glue". implode("</$tag>$glue", $this->stored[$key]) ."</$tag>";
            }
        }

        switch ($glue){
            case "as array": return $this->stored[$key];
            case "comented-list": return "<!-- ". implode(", ", $this->stored[$key]) ."-->";
            case "li": return "<li>". implode("</li><li>", $this->stored[$key]) ."</li>";
            case "ol":
            case "ul": return "<$glue><li>". implode("</li><li>", $this->stored[$key]) ."</li></$glue>";
            default  : return implode($glue, $this->stored[$key]);
        }
        return "";
    }

    function show($key, $glue){
        echo $this->get($key,$glue);
    }

    function last($key){
        if ( isset($this->stored[$key]) ){
            return end($this->stored[$key]);
        }
        return false;
    }

    function first($key){
        if ( isset($this->stored[$key]) ){
            return reset($this->stored[$key]);
        }
        return false;
    }


    //shortcuts for login error
    function set_login_error($error){
        $this->store( $this->error($error, array("id"=>"login-wrong-username"),false),"error-in-login");
    }

    function get_login_error(){
        return $this->unstore( "error-in-login",false,false);
    }

    private function Addclass ( $default, &$attribs ){
        return $default . (isset($attribs['class']) ? " ". $attribs['class'] : "" );
    }

    function error( $text, $attribs=NULL, $echo = true){
       $attribs['class']=  $this->addclass('message-error', $attribs );
       return message( $text,$attribs,$echo);
    }

    function warning( $text, $attribs=NULL, $echo = true){
       $attribs['class']=  $this->addclass('message-warning', $attribs );
       return message( $text,$attribs,$echo);
    }

    function ok( $text, $attribs=NULL, $echo = true){
       $attribs['class']=  $this->addclass('message-ok', $attribs );
       return message( $text,$attribs,$echo);
    }

    function general($text,$attribs=NULL, $echo = true) {
       $attribs['class']=  $this->addclass('message', $attribs );
       return message( $text,$attribs,$echo);
    }

}
