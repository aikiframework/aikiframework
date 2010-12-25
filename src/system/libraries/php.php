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

					case "str_replace":
						preg_match("/str_replace\($para,$para,$para\);/Us", $php_function, $partial);
						$php_output = str_replace($partial[1],$partial[2],$partial[3]);
						break;

					case "substr":
						preg_match("/substr\($para,$para(,(.*))?\);/s", $php_function, $partial);
						$php_output = ( $partial[4] ? substr($partial[1], $partial[2],$partial[4])
						: substr($partial[1], $partial[2] ));
						break;

					case "if":
						if (preg_match('/if (.*)\=\=(.*) then (.*) else (.*)/s', $php_function, $partial)){
							$php_output=  ( $partial[1] == $partial[2] ? $partial[3]: $partial[4]);
						} elseif (preg_match('/if (.*)\=\=(.*) then (.*)/s', $php_function, $partial)){
							$php_output=  ( $partial[1] == $partial[2] ? $partial[3]:"");
						}elseif (preg_match('/if (.*)\=(.*) then (.*) else (.*)/s', $php_function, $partial)){
							$php_output=  ( $partial[1] == $partial[2] ? $partial[3]: $partial[4]);
						} elseif (preg_match('/if (.*)\=(.*) then (.*)/s', $php_function, $partial)){
							$php_output=  ( $partial[1] == $partial[2] ? $partial[3]:"");
						}
						break;

					case "htmlspecialchars":
						preg_match('/htmlspecialchars\((.*)\);/s', $php_function, $partial);
						$php_output = htmlspecialchars($partial[1]);
						break;

					case '$aiki':
						preg_match('/\$aiki\-\>(.*)\-\>(.*)\((.*)\)\;/s', $php_function,$partial);
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