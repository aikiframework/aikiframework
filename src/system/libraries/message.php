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
 * @copyright   (c) 2008-2010 Aikilab
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
 * draft horse of module. Show o return a message.
 * 
 * @param $text : text to show. Will be translated if there is a
 *                 t (translate) function or is workin $aiki->languages
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
