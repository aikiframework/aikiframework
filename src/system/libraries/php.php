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
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


/**
 * BriefDescription
 *
 * @category    Aiki
 * @package     Library
 */

class php
{

	public function parser($text){
		global $aiki;

		$para="([^,]*)"; // for comodity

		if (preg_match ("/\<form(.*)\<php (.*) php\>(.*)\<\/form\>/Us", $text)){
			return $text;
		}

		if (preg_match_all('/\<php (.*) php\>/Us', $text, $matchs)){

			foreach ($matchs[1] as $php_function){

				$php_output="";

				// obtain first word..
				$len= strcspn($php_function," -(");
				$word = ( $len ? substr($php_function,0,$len): "");
				$rest= trim(substr($php_function,$len+1,-1));

				//evaluate each case..
				switch ($word) {
					/*case "dump":
						preg_match('/dump (.*);/s', $php_function, $partial);
						ob_start();
						eval( "return var_dump({$partial[1]});");
						$php_output= ob_get_clean();
						break;

						case "echo":
						preg_match('/echo (.*);/s', $php_function, $partial);
						$php_output = eval( "return {$partial[1]};") ;
						break;

						case "eval":
						preg_match('/eval\((.*)\);/s', $php_function, $partial);
						$php_output = eval($partial[1] . ( substr($partial[1],-1)!=";" ? ";" :"")) ;
						break;*/

					case "replace":
					case "str_replace":
						$partial = $this->mtoken($rest);
						$php_output = str_replace($partial[0],$partial[1],$partial[2]);
						break;

					case "substr":
						$partial = $this->mtoken($rest);
						if ( isset($partial[2])) {
							$php_output = substr($partial[0], $partial[1],$partial[2]);
						} else {
							$php_output = substr($partial[0], $partial[1]);
						}
						break;

					case "if":
						$php_output= $this->php_ifelse($php_function);
						break;

					case "htmlspecialchars":
						preg_match('/htmlspecialchars\((.*)\);/s', $php_function, $partial);
						$php_output = htmlspecialchars($partial[1]);
						break;

					case '$aiki':
						preg_match('/\$aiki\-\>(.*)\-\>(.*)\((.*)\)\;/Us', $php_function,$partial);
						$php_output = $this->aiki_function($partial[1],$partial[2],$partial[3]);
						break;

					default :
						if ( isset($config['debug']) or true){
							$php_output = "<php $php_function php>";
						}

				}

				$text = str_replace("<php $php_function php>", $php_output , $text);
			}
		}
		return $text;

	}

	/**
	 * Internal function to parser argument
	 */

	function mtoken ( $text, $separator=',' ){
		$max  = strlen($text)-1;
		/**
		 *  state 0: waiting a token
		 * 1: over ' delimited string
		 * 2  over " delimited string
		 * 3  over a no delimited string,
		 * 4  waiting coma
		 */
		$state= 0 ;
		$word = "";
		$resul= array();

		for($i=0;$i<=$max;$i++){
			$char = $text[$i];

			// continue over white space
			if ( ( $state==0 || $state==4) && ( $char==" " || $char=="\n" || $char=="\r" || $char=="\t" )) {
				$continue;
			} elseif ( ( $char=="'" && $state==1) || ( $char=='"' && $state==2) ||	( $char==$separator && $state==3) || $i==$max ) {
				$state= ( $state==3 ? 0 : 4);
				$resul[]= $word ;
				$word="";
			} elseif ( $char==$separator && $state==4 ) { //found a ' when waiting
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

		}

		return $resul;
	}

	function evalNeg($text){
		$text= trim($text);
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
				case " in "    : $condition = stripos($second,$first)!==false;break;
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
				return "Sorry, [$class] don't exists";
			}
		}

		if ($para){
			return  $aiki->$class->$function($para);
		} else {
			return $aiki->$class->$function();
		}


	}


}