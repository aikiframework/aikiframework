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
 *
 * @todo rename class Php
 */

class php
{

/**
 * Variables user by odd, counter, adn mod functions.
 */
private 
	$odd=0,
	$mod=0,
	$counters=array(),
	$increments=array(),
	$initialized=array();


/*
 * Parse over text
 * @param	string	$text	Input text
 * @global	aiki	$aiki	global aiki instance
 * @return	string. Output text.
 */
	public function parser($text)
	{
		global $aiki;

		/**
		 * @todo why is this not just deleted? kill or keep?
		 */
		//$text = htmlspecialchars_decode($text);
		$text = stripslashes($text);
		
		if (preg_match ("/\<form(.*)\<php (.*) php\>(.*)\<\/form\>/Us", $text))
			return $text;

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
					
			$rest = preg_replace('/;$/',"",trim( substr($php_function,$len+1)));
			$rest = preg_replace('/\)$/',"", $rest);
			
			//evaluate each case..
			switch ($word) 
			{				
				case "":
					break;
				// 'if' is a very special case
				case "if":			
					$php_output= $this->php_ifelse($php_function);					
					break;					
				// counters and odds
				case "setcounter": $this->setcounter( $this->mtoken($rest)); 
					break;
				case "counter"   : $php_output = $this->counter($rest);   
					break;
				case "odd"	     : $php_output = $this->odd(); 
					break;
				case "mod"	     : $php_output = $this->mod($rest); 
					break;
				// getinfo
				case "getinfo"   : 
					$php_output = $this->getinfo($rest); 
					break;
					
				// string functionts
				case "replace":
				case "str_replace":
					$partial = $this->mtoken($rest);
					if ( isset($partial[2]) )
						$php_output = str_replace($partial[0],$partial[1],
												  $partial[2]);						
					break;

				case "substr":
					$partial = $this->mtoken($rest);
					if ( isset($partial[2])) {
						$php_output = 
							substr($partial[0], $partial[1],$partial[2]);
					} else {
						$php_output = substr($partial[0], $partial[1]);
					}
					break;

				case "htmlspecialchars":
					$temp = preg_replace(array(
							'/^"(.*)"$/', '/^\'(.*)\'$/'),'$1',$rest);
					$php_output = htmlspecialchars($temp);
					break;
	
				case '$aiki':										   
					if ( preg_match('/\$aiki\-\>(.*)\-\>(.*)\((.*)\)\;?/Us', 
						$php_function,$partial) )
					{
						$php_output = $this->aiki_function($partial[1],
									  $partial[2],$partial[3]);
					}
					break;
				  
				case "sql":													  
					$php_output= $this->sql($rest);
					break;
				case "sql_rows":													  
					$php_output= $this->sql_rows($rest);
					break;    
                case "echo".
                	$php_output= $this->evalNeg($rest);

				default :
					if ( isset($config['debug']) )
						$php_output = "<php $php_function php>";
			}
			$parsed .= $php_output;
		} while ( next($tokens)!== false ) ;

		return $parsed;

	} // end of parser function


/**
 * Internal function to parse an argument
 *
 * @param	string		$text		text for processing
 * @param	string		$separator	separating character
 * @return	string
 */
function mtoken ( $text, $separator=',' )
{
	$max  = strlen($text);
	$state= 0 ; /* state 0: waiting a token
							  1: over ' delimited string
							  2  over " delimited string
							  3  over a no delimited string,
							  4  waiting coma */
	$word	= "";
	$result = array();

	for($i=0;$i<$max;$i++)
	{
		$char = $text[$i];

		// continue over white space
		if ( ( $state==0 || $state==4) && 
			( $char==" " || $char=="\n" || $char=="\r" || $char=="\t" )) 
		{
			 $continue;
		} elseif ( ($i+1)==$max && ($state>0 && $state<4) ) 
		{ 
			//last character must be added
			 if ($char==$separator) { //last character is a separator
				$result[]= $word;
				$result[]= "";
			 } else {
				$result[]= $word . $char;
			 }				 
			 $word="";
		} elseif ( ( $char=="'" && $state==1) ||  //anotate string ends.
					  ( $char=='"' && $state==2) ||
					  ( $char==$separator && $state==3) ) 
		{
			 $state= ( $state==3 ? 0 : 4);
			 $result[]= $word ;
			 $word="";
		} elseif ( $char==$separator && $state==4 ) 
		{ 
			//found separator when waiting
			 $state=0 ;
		} elseif ( $char==$separator && $state==0 ) 
		{ 
			//found separator when waiting a token.
			 $result[]="";
			 $state=0 ;	 
		} elseif ( ($char=="'" || $char=='"' ) && $state==0) 
		{ 
			 //initiate a string
			 $state= ( $char=="'" ? 1: 2) ;
		} elseif ( $char=='\\'  ) 
		{
			 $i++;
			 $word .= $text[$i];
		} elseif ( $state==0)
		{
			 $state=3;
			 $word = $char;
		} else {
			 $word .= $char;
		}
	} // end of for loop

	if ( $word )
	  $result[]=$word;

	return $result;
} // end of mtoken function


/**
 * Evaluates and extracts value or not from a string
 *
 * @param	string	$text	text for processing
 * @return	string
 * @todo	this function has a bad name
 */
function evalNeg($text)
{
	$text= trim($text);
	/**
	 * @todo $cap is an out of scope variable, needs fixing
	 */
	if ( preg_match ( '/^\$_(GET|POST|SESSION|REQUEST|COOKIE)\[(.*)\]$/', 
		  $text, $cap ))
	{
		$key= preg_replace ( "/^['\"]|['\"]$/",'',$cap[2]);
		switch ( $cap[1] ) {
			 case "POST"	: $text= isset($_POST[$key])	? $_POST[$key] : "";
				break;
			 case "GET"		: $text= isset($_GET[$key])		? $_GET[$key] : "";
				break;
			 case "REQUEST"	: $text= isset($_REQUEST[$key]) ? 
							  $_REQUEST[$key] : "" ; break;
			 case "SESSION"	: $text= isset($_SESSION[$key]) ? 
							  $_SESSION[$key] : "" ; break;
			 case "COOKIE"	: $text= isset($_COOKIE[$key])  ? 
							  $_COOKIE[$key] : "" ; break;
			 default:
				  $text="";
		}
	}
	$text =  preg_replace ( "/^(!?)\s?['\"]|['\"]$/",'\\1', $text );
	return ( $text && $text[0]=='!' ? !substr($text,1) : $text );

} // end of evalNeg function


/**
 * Parse a string an evaluate like a php if else statemetn
 *
 * @param	string	$text	text for processing
 * @return	bool
 */
function php_ifelse($text)
{
	//divide the text
	// @TODO improve this.
	$partial= preg_split('/if |then |else /s', $text);

	if ( !isset($partial[3])) {
		$partial[3]="";
	}

	if ( preg_match (
		'/([^<>=]+)(=|==|>|<|>=|<=|<>| in | not in )([^<>=]+)/is', 
			$partial[1],$evaluation))
	{
		$first = $this->evalNeg($evaluation[1]);
		$second= $this->evalNeg($evaluation[3]);

		switch ( $evaluation[2])
		{
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
} // end of php_ifelse function


/**
 * Implements a parsed php like function class.
 * 
 * @param	string	$class		classname
 * @param	string	$function	function name
 * @param	string	$para		some text for processing
 * @global	aiki	$aiki		global aiki instance
 * @return	mixed
 */
public function aiki_function($class,$function, $para)
{
	global $aiki;

	// load class if not exists..
	if (!isset($aiki->$class))
	{
		$aiki->load($class);
		if (!isset($aiki->$class))
			return "Sorry, [$class] doesn't exist";
	}

	if ($para)
		return  $aiki->$class->$function($para);
	else
		return $aiki->$class->$function();
} // end of aiki_function


/*
 * Implementation of odd, counter and mods.
 * 
 */


/**
 * If number is odd or even, then outputs string for odd or even, respectively.
 * @return	string
 */
function odd()
{
	$this->odd= ( $this->odd==0 ? 1:0);
	return ( $this->odd ? "odd" : "even");
}


/**
 * Sets a counter
 *
 * @param	array	$para	array items for parsing into a counter setup
 */
function setcounter($para)
{
	$this->counters[$para[0]]   = (isset($para[1]) ? $para[1] : 1);
	$this->increments[$para[0]] = (isset($para[2]) ? (int) $para[2] :1);
	$this->initialized[$para[0]]= false;
}


/**
 * Handles a counter
 *
 * @param	string	$counter	array key name
 * @return	integer
 */
function counter($counter)
{
	if ( !isset( $this->counters[$counter]) )
	{
		$this->counters[$counter]=0;
		$this->increments[$counter]=1;
		$this->initialized[$counter]=true;
	} elseif ( ! $this->initialized[$counter] )
	{
		$this->initialized[$counter]=true;
	} else {
		$this->counters[$counter]+= $this->increments[$counter];
	}
	return $this->counters[$counter];
}


/**
 * Standard mod function
 *
 * @param	integer		$factor
 * @return	integer
 */
function mod($factor)
{
	$factor= (int)$factor;
	$cRet =  ( $factor != 0 ? $this->mod % $factor: 0);
	$this->mod++;
	return ( $cRet);
}


/**
 * Returns	information about aiki version, and runtime (queries and time)
 * 
 * @param	string $what.  can be version, hidden-version, queries, 
 * hidden-queries, time,hidden-time.
 * @global	array	$db	global db instance
 * @return	string
 */
function getinfo($what)
{
	switch ($what) 
	{
		case "version":
		case "hidden-version":
			return $what=="version" ? 
				AIKI_VERSION : "\n<!-- aikiframework version: ". AIKI_VERSION .
					"." . AIKI_REVISION . " -->\n";
		case "queries" :
		case "hidden-queries" :
			global $db;
			return $what=="queries" ? 
			$db->num_queries : "\n<!-- queries: ". $db->num_queries ."-->\n";
		case "time":
		case "hidden-time":
			global $start_time;
			$end = (float) array_sum(explode(' ',microtime()));
			$end_time = sprintf("%.4f", ($end-$start_time));
			return $what=="time" ? 
				$end_time : "\n <!-- Time: ".$end_time." seconds -->\n";
		default:
			return $what;
	}
} // end of getinfo
	

/**
 * Determine is a string is a SQL statement and only one.
 * 
 * @param	string	$sql Sql to be tested.
 * @return	boolean 
 */		 

private function is_sql_select_statement( $sql)
{
	// with this two step all delimited string was substitute with a q
	$sql= strtr( $sql, array("\\'"=>"", "\\" . '"' =>""));
	$sql= preg_replace("/('[^']*')|(\"[^\"]*\")/"," q ",$sql);
	return preg_match( '/^SELECT [^;]*;?$/i',$sql);
}
	
	
/**
 * Return the first field of first row of a query.
 * 
 * @param	string	$sql.	Must be a select statement.
 * @global	array	$db		global db instance
 * @return	mixed
 *
 */
private function sql($sql)
{
	global $db;
	
    // remove, if exists initial and final ' "
	$sql= preg_replace( array('/^"(.*)"$/', '/^\'(.*)\'$/'),'$1',$sql);
	
	if (!$this->is_sql_select_statement($sql) ) {
		return "invalid SQL SELECT";
	}		
	return $db->get_var($sql,0,0);
}


private function sql_rows($sql)
{
	global $db;
	
    // remove, if exists initial and final ' "
	$sql= preg_replace( array('/^"(.*)"$/', '/^\'(.*)\'$/'),'$1',$sql);
	if (!$this->is_sql_select_statement($sql) ) {
		return "invalid SQL SELECT";
	}		
	$rows = $db->get_results($sql);
    if ( is_null($rows)) {
        return "ERROR in SQL statements";
    }
    
    $output="";
    $cont=1;
    $max =3;

    foreach ($rows as $i=>$row) {
        if ($cont ==1 ){
            $output="<thead>\n</tr>";
            foreach($row as $field=>$value) {
                $output .= "<th>$field</th>";
            }
            $output .= "</tr>\n</thead>";
        }    
                
        $output .="\n<tr class='". ( $cont %2 ? 'even' : 'odd' ) . "'>";
        foreach($row as $field) {
            $output .= "<td>$field</td>";
        }
        $output .="</tr>";
        
        // to avoid a unlimited query
        $cont++ ;
        if ( $cont>$max ) {
            break;
        }
    }
    return "<table class='sql-select-result'>$output</table>";

}


} // end of class
// need the close php tag in this class
?>
