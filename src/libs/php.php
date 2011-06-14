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

if(!defined('IN_AIKI')){die('No direct script access allowed');}


/**
 * parser language that can be used in widget.
 * 
 * These are parsed PHP functions, and not pure PHP functions, so they 
 * would work even if the aiki core is the ruby or C++ version. 
 * The full PHP language is not supported, and only some functions.
 *
 * @category	Aiki
 * @package	 Library
 */

class php
{

// vars user by odd, counter, adn mod functions.
private 
	$odd=0,
	$mod=0,
	$counters=array(),
	$increments=array(),
	$initialized=array();


/*
 * Parser a text
 * @param string $text. Input text.
 * @return string. Output text.
 */
	public function parser($text){
		global $aiki;

		//$text = htmlspecialchars_decode($text);
		$text = stripslashes($text);
		
		if (preg_match ("/\<form(.*)\<php (.*) php\>(.*)\<\/form\>/Us", $text)){
			return $text;
		}

		// now, we will divided the text. The array is always
		// out of marker, in of markers, out of markers, in of markers
		$tokens= preg_split("/(<php)|(php>)/", $text);
	   $parsed = "";
	   		
	   do {
	   					
	   	$parsed .= current($tokens);
			
			$php_function = trim(next($tokens));
			$php_output="";

			// obtain first word..
			$len  = strcspn($php_function," -(");
			$word = ( $len ? substr($php_function,0,$len): "");													
					
			$rest = preg_replace('/;$/',"", trim( substr($php_function,$len+1)));
			$rest = preg_replace('/\)$/',"", $rest);
			
			//evaluate each case..
			switch ($word) {				
				case "";
					break;
				// 'if' is a very special case
				case "if";			
					$php_output= $this->php_ifelse($php_function);					
					break;					
				// counters and odds
				case "setcounter": $this->setcounter( $this->mtoken($rest)); break;
				case "counter"   : $php_output = $this->counter($rest);   break;
				case "odd"	     : $php_output = $this->odd(); break;
				case "mod"	     : $php_output = $this->mod($rest); break;

				// getinfo
				case "getinfo"   : 
					$php_output = $this->getinfo($rest); 
					break;
					
				// string functionts
				case "replace":
				case "str_replace":
					$partial = $this->mtoken($rest);
					if ( isset($partial[2]) ){
						$php_output = str_replace($partial[0],$partial[1],$partial[2]);						
					}
					break;

				case "substr":
					$partial = $this->mtoken($rest);
					if ( isset($partial[2])) {
						$php_output = substr($partial[0], $partial[1],$partial[2]);
					} else {
						$php_output = substr($partial[0], $partial[1]);
					}
					break;

				case "htmlspecialchars":
					$temp= preg_replace( array('/^"(.*)"$/', '/^\'(.*)\'$/'),'$1',$rest);
					$php_output = htmlspecialchars($temp);
					break;
	
				case '$aiki':										   
					if ( preg_match('/\$aiki\-\>(.*)\-\>(.*)\((.*)\)\;?/Us', $php_function,$partial) ){
						$php_output = $this->aiki_function($partial[1],$partial[2],$partial[3]);
					}
					break;
				  
				case "sql":													  
					$php_output= $this->sql($rest);
					break;

				default :
					if ( isset($config['debug']) ){
						$php_output = "<php $php_function php>";
					}
			}
			$parsed .= $php_output;
		} while ( next($tokens)!== false ) ;
		
		return $parsed;

	}

/**
 * Internal function to parser argument
 */

function mtoken ( $text, $separator=',' ){
	$max  = strlen($text);
	$state= 0 ; /* state 0: waiting a token
							  1: over ' delimited string
							  2  over " delimited string
							  3  over a no delimited string,
							  4  waiting coma */
	$word = "";
	$resul= array();

	for($i=0;$i<$max;$i++){
		$char = $text[$i];

		// continue over white space
		if ( ( $state==0 || $state==4) && ( $char==" " || $char=="\n" || $char=="\r" || $char=="\t" )) {
			 $continue;
		} elseif ( ($i+1)==$max && ($state>0 && $state<4) ) { //last character must be added
			 if ($char==$separator) { //last character is a separator
				$resul[]= $word;
				$resul[]= "";
			 } else {
				$resul[]= $word . $char;
			 }				 
			 $word="";
		} elseif ( ( $char=="'" && $state==1) ||  //anotate string ends.
					  ( $char=='"' && $state==2) ||
					  ( $char==$separator && $state==3) ) {
			 $state= ( $state==3 ? 0 : 4);
			 $resul[]= $word ;
			 $word="";
		} elseif ( $char==$separator && $state==4 ) { //found separator when waiting
			 $state=0 ;
		} elseif ( $char==$separator && $state==0 ) { //found separator when waiting a token.
			 $resul[]="";
			 $state=0 ;	 
		} elseif ( ($char=="'" || $char=='"' ) && $state==0) { //initiate a string
			 $state= ( $char=="'" ? 1: 2) ;
		} elseif ( $char=='\\'  ) {
			 $i++;
			 $word .= $text[$i];
		} elseif ( $state==0){
			 $state=3;
			 $word = $char;
		} else {
			 $word .= $char;
		}
	} // for
	if ( $word ) {
	  $resul[]=$word;
	}

	return $resul;
 }



function evalNeg($text){
	  $text= trim($text);
	  if ( preg_match ( '/^\$_(GET|POST|SESSION|REQUEST|COOKIE)\[(.*)\]$/', $text, $cap )){
			$key= preg_replace ( "/^['\"]|['\"]$/",'',$cap[2]);
			switch ( $cap[1] ) {
				 case "POST"   : $text= isset($_POST[$key])	? $_POST[$key] : "" ; break;
				 case "GET"	: $text= isset($_GET[$key])	 ? $_GET[$key] : "" ; break;
				 case "REQUEST": $text= isset($_REQUEST[$key]) ? $_REQUEST[$key] : "" ; break;
				 case "SESSION": $text= isset($_SESSION[$key]) ? $_SESSION[$key] : "" ; break;
				 case "COOKIE" : $text= isset($_COOKIE[$key])  ? $_COOKIE[$key] : "" ; break;
				 default:
					  $text="";
			 }
	  }
	  $text =  preg_replace ( "/^(!?)\s?['\"]|['\"]$/",'\\1', $text );
	  return ( $text && $text[0]=='!' ? !substr($text,1) : $text );
 }


function php_ifelse($text){

		//divide the text
		// @TODO improve this.
		$partial= preg_split('/if |then |else /s', $text);

		if ( !isset($partial[3])) {
			$partial[3]="";
		}

		if ( preg_match ( '/([^<>=]+)(=|==|>|<|>=|<=|<>| in | not in )([^<>=]+)/is', $partial[1],$evaluation)){
			$first = $this->evalNeg($evaluation[1]);
			$second= $this->evalNeg($evaluation[3]);

			switch ( $evaluation[2]){
				case "<" : $condition= $first  < $second ; break;
				case ">" : $condition= $first  > $second ; break;
				case "<=": $condition= $first <= $second ; break;
				case ">=": $condition= $first >= $second ; break;
				case "<>": $condition= $first <> $second ; break;
				case "=":
				case "==": $condition= $first == $second ; break;
				case " in "	: $condition = stripos($second,$first)!==false;break;
				case " not in ": $condition = stripos($second,$first)===false;break;
			}

		} else {
			$condition = $this->evalNeg($partial[1]);
		}

		return (bool) $condition ? $partial[2]: $partial[3];
	}


public function aiki_function($class,$function, $para){
		global $aiki;

		// load class if not exists..
		if (!isset($aiki->$class)){
			$aiki->load($class);
			if (!isset($aiki->$class)) {
				return "Sorry, [$class] doesn't exist";
			}
		}

		if ($para){
			return  $aiki->$class->$function($para);
		} else {
			return $aiki->$class->$function();
		}

}


/*
 * Implementation of odd, counter and mods.
 * 
 */



function odd(){
	$this->odd= ( $this->odd==0 ? 1:0);
	return ( $this->odd ? "odd" : "even");
}


function setcounter($para){		
	$this->counters[$para[0]]   = (isset($para[1]) ? $para[1] : 1);
	$this->increments[$para[0]] = (isset($para[2]) ? (int) $para[2] :1);
	$this->initialized[$para[0]]= false;
}


function counter($counter){
	if ( !isset( $this->counters[$counter]) )  {
		$this->counters[$counter]=0;
		$this->increments[$counter]=1;
		$this->initialized[$counter]=true;
	} elseif ( ! $this->initialized[$counter] ) {
		$this->initialized[$counter]=true;
	} else {
		$this->counters[$counter]+= $this->increments[$counter];
	}
	return $this->counters[$counter];
}


function mod($factor){
	$factor= (int)$factor;
	$cRet =  ( $factor != 0 ? $this->mod % $factor: 0);
	$this->mod++;
	return ( $cRet);
}


/**
 * return information about aiki version, and runtime (queries and time)
 * 
 * @param string $what.  can be version, hidden-version, queries,hidden-queries,time,hidden-time.
 * @return string.
 */
function getinfo($what) {
	switch ($what) {
		case "version":
		case "hidden-version":
			return $what=="version" ? AIKI_VERSION : "\n<!-- aikiframework version: ". AIKI_VERSION . "." . AIKI_REVISION . " -->\n";
		case "queries" :
		case "hidden-queries" :
			global $db;
			return $what=="queries" ? $db->num_queries : "\n<!-- queries: ". $db->num_queries ."-->\n";
		case "time":
		case "hidden-time":
			global $start_time;
			$end = (float) array_sum(explode(' ',microtime()));
			$end_time = sprintf("%.4f", ($end-$start_time));
			return $what=="time" ? $end_time : "\n <!-- Time: ".$end_time." seconds -->\n";
		default:
			return $what;
	}
}
	
/**
 * Determine is a string is a SQL statement and only one.
 * 
 * @param string $sql Sql to be tested.
 * @return boolean. 
 */		 

private function is_sql_select_statement( $sql) {
	// with this two step all delimited string was substitute with a q
	$sql= strtr( $sql, array("\\'"=>"", "\\" . '"' =>""));
	$sql= preg_replace("/('[^']*')|(\"[^\"]*\")/"," q ",$sql);
	return preg_match( '/^SELECT [^;]*;?$/i',$sql);
}
	
	
/**
 * Return the first field of first row of a query.
 * 
 * @param string $sql. Must be a select statement.
 * @return mixed
 *
 */

private function sql($sql){
	global $db;
	
	$sql= preg_replace( array('/^"(.*)"$/', '/^\'(.*)\'$/'),'$1',$sql);
	if (!$this->is_sql_select_statement($sql) ) {
		return "invalid SQL SELECT";
	}		
	return $db->get_var($sql,0,0);
}

} // end of class
