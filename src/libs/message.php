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
 * @category	Aiki
 * @package     Library
 * @filesource
 *
 * A class to handle formatting and output of messages.
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
 * if (!something_info) {
 *	die( message("Fatal Error: Wrong site name provided",NULL, false) );
 * }
 * $aiki->message->warning("No clipart's found");
 * $aiki->message->error("No file upload", array("class"=>"fatal")); class will be 'message-error fatal'
 * $aiki->message->general("Hi, Roger", array("id"=>"welcome"));
 *
 */

if (!defined('IN_AIKI')) {
	die('No direct script access allowed');
}

/**
 *  Show or return a message.
 *
 * @param   string  $text text to show. Will be translated if there is a
 *				  t (translate) function or is working $aiki->languages
 * @param	array	$attribs (optional=NULL) array of html atributes i
 *					(id,class, style,onmouse)..
 * @param	bool	$echo ($option=false) true, echo the result, and false 
 *					return it.
 * @global	aiki	$aiki	global aiki instance
 */
function message($text, $attribs=NULL, $echo = true) {
	global $aiki;

	// when there isn't aiki object and not attributes are provides, 
	// assumes error.
	if ( !isset($aiki) and !isset($attribs) ) {
		$attribs['style'] = 
		"background-color:#F8F8FF;color:#c00;font-weight:bold;padding:4px 6px;";
	}

	$tag  = ( isset($attribs["tag"]) ? $attribs["tag"] : "div" );
	$cRet = "<$tag";
	if (is_array($attribs)) {
		foreach ( $attribs as $k=>$v ) {
			if( $k != "tag" )
				$cRet .= " $k='$v' ";
		}
	}
	
	if (isset($aiki->languages)) {
		$text= $aiki->languages->L10n($text);
	}
	$cRet .= ">". $text. "</$tag>";

	if (!$echo) {
		return $cRet;
	}
	echo $cRet;
}

/**
 * Class for handling messages storage, unstorage and some simple courtesies
 */
class message {
	/** 
	 * @var	string
	 */
	private $stored;

	/**
	 * Constructor setup
	 */
	function __construct() {
		$this->stored=array();
	}

	/**
	 * Store a message.
	 *
	 * @param	string	$message	message for output
	 * @param	string	$key		key for storing
	 * @return	string
	 */
	function store($message, $key="", $add=true) {
		if ( $key == "" ) {
			$this->stored[]= array($message);
			return end($this->stored);
		} elseif ( $add===false ) {
			$this->stored[$key] = array($message);
			return $key;
		} elseif ( $add===true ) {
			$this->stored[$key][] = $message;
			return $key;
		} else {
			$this->stored[$key][$add] = $message;
		}
	}

	
	/**
	 * Unstore a message by echoing it or returning.
	 * 
	 * @param	string	$key	key for storage
	 * @param	bool	$echo	echo or do not echo
	 * @param	bool	$clear	clear out of storage or keep stored
	 * @param	string	$glue	glue for end of line output 
	 * @return	mixed
	 */
	function unstore($key="*", $echo = false, $clear= true, $glue="\n") {
		$ret = "";
		if ( $key == "*" ) {
		   $ret = "";
		   foreach ( $this->stored as $k=>$v ) {
				$ret.= implode($glue,$v);
		   }
		   if ($clear) {
			   $this->stored= array();
		   }
		} else {
			if (isset($this->stored[$key])) {
				$ret = implode($glue, $this->stored[$key]);
				if ($clear) {
				   unset($this->stored[$key]);
				}
			}
		}
		if ($echo) {
			echo $ret;
		}
		return $ret;
	}

	
	/**
	 * Order the stored messages and let us know if successful or failed.
	 * 
	 * @param	string	$key	key for stored message
	 * @param	string	$order	the type of sort order
	 * @return	bool
	 */
	function order($key, $order="asc") {
		if (!isset($this->stored[$key])) {
			return false;
		}
		switch (strtolower($order)) {
			case "asc":
				ksort($this->stored[$key]);
				break;
			case "desc":
				krsort($this->stored[$key]);
				break;
			case "asc-values":
				asort($this->stored[$key]);
				break;
			case "desc-values":
				arsort($this->stored[$key]);
				break;
			case "natural":
				natsort($this->stored[$key]);
				break;
			default:
				return false;
		}
		return true;
	}

	/**
	 * Set and store a message
	 * 
	 * @param	string	$key
	 * @param	string	$message
	 */
	function set($key,$message) {
		return $this->store($message,$key, false);
	}

	/**
	 * Add a message to the stack
	 * 
	 * @param	string	$key		key for storage
	 * @param	string	$message	message
	 * @param	bool	$order		order or don't order the entry
	 */
	function add($key,$message,$order=false) {
		return $this->store($message,$key, ( $order === false ? true : $order ));
	}

	/**
	 * Get a message by key.
	 *
	 * @param	string	$key	key to get message out of storage
	 * @param	string	$glue	glue for formatting the message
	 * @return	string
	 */
	function get($key, $glue="ul") {
		if (!isset($this->stored[$key])) {
		  return ( $glue == "as array" ? array() : "" );
		}

		$tag = array();;
		if (preg_match('#^<([^ />]*) ?[^>]*>$#s', $glue, $tag)) {
			// it's a HTML tag
			$tag = $tag[1];
			echo "*** $tag ***";
			switch ($tag) {
				case "ul":
				case "ol":
					return "$glue<li>" . implode("</li><li>", 
						$this->stored[$key]) . "</li></$tag>";
				case "br":
					return implode($glue, $this->stored[$key]);
				default:
					return "$glue" . implode("</$tag>$glue", 
						$this->stored[$key]) . "</$tag>";
			}
		}

		switch ($glue) {
			case "as array": return $this->stored[$key];
			case "comment-list": 
				return "<!-- " . implode(", ", $this->stored[$key]) . "-->";
			case "li": return "<li>" . implode("</li><li>", 
					   $this->stored[$key]) . "</li>";
			case "ol":
			case "ul": return "<$glue><li>" . implode("</li><li>", 
					   $this->stored[$key]) . "</li></$glue>";
			default  : return implode($glue, $this->stored[$key]);
		}
		return "";
	}

	/**
	 * Echo a stored message.
	 *
	 * @param	string	$key	key for storage
	 * @param	string	$glue	your own glue
	 */
	function show($key, $glue) {
		echo $this->get($key,$glue);
	}

	/**
	 * Return the last stored message on the stack or return false.
	 * @param	string	$key	key for stored message
	 * @return	mixed
	 */
	function last($key) {
		if (isset($this->stored[$key])) {
			return end($this->stored[$key]);
		}
		return false;
	}

	/**
	 * Return the first stored message on the stack or return false.
	 *
	 * @param	string	$key	key for stored message
	 * @return	mixed
	 */
	function first($key) {
		if (isset($this->stored[$key])) {
			return reset($this->stored[$key]);
		}
		return false;
	}


	// shortcuts for login error

	/**
	 * Set a login error
	 * 
	 * @param	string	$error
	 */
	function set_login_error($error) {
		$this->store($this->error($error, array("id"=>"login-wrong-username"),
			false), "error-in-login");
	}

	/**
	 * Return a login error.
	 * 
	 * @return	mixed
	 */
	function get_login_error() {
		return $this->unstore("error-in-login", false, false);
	}

	/**
	 * Add a class to the message
	 *
	 * @param	string	$default	default class name
	 * @param	array	$attribs	the attributes array
	 * @return	string
	 */
	private function Addclass($default, &$attribs) {
		return $default . 
			( isset($attribs['class']) ? " " . $attribs['class'] : "" );
	}

	/**
	 * Return an error message or output it.
	 * 
	 * @param	string	$text		the text of the message
	 * @param	array	$attribs	attributes for the message
	 * @param	bool	$echo		echo or don't echo
	 * @return	mixed
	 */
	function error($text, $attribs=NULL, $echo = true) {
		$attribs['class'] = $this->addclass('message-error', $attribs);
		return message($text, $attribs, $echo);
	}

	/**
	 * Return a warning message or output it.
	 *
	 * @param	string	$text		the text of the message
	 * @param	array	$attribs	attributes for the message
	 * @param	bool	$echo		echo or don't echo
	 * @return	mixed
	 */
	function warning($text, $attribs=NULL, $echo = true) {
		$attribs['class'] = $this->addclass('message-warning', $attribs);
		return message($text, $attribs, $echo);
	}

	/**
	 * Return an ok message or output it.
	 *
	 * @param	string	$text		the text of the message
	 * @param	array	$attribs	attributes for the message
	 * @param	bool	$echo		echo or don't echo
	 * @return	mixed
	 */
	function ok($text, $attribs=NULL, $echo = true) {
	   $attribs['class'] = $this->addclass('message-ok', $attribs );
	   return message($text, $attribs, $echo);
	}

	/**
	 * Return a general message or output it.
	 *
	 * @param	string	$text		the text of the message
	 * @param	array	$attribs	attributes for the message
	 * @param	bool	$echo		echo or don't echo
	 * @return	mixed
	 */
	function general($text,$attribs=NULL, $echo = true) {
	   $attribs['class'] = $this->addclass('message', $attribs);
	   return message($text, $attribs, $echo);
	}

} // end of message class

?>