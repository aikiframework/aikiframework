<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2009-2010 Aikilab inc
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */

class aiki_javascript
{

	function call_javascripts($widget, $widget_id){
		global $db;

		$numMatches = preg_match_all( '/\(\#\(javascript\:(.*)\)\#\)/', $widget, $matches);
		if ($numMatches > 0){
			foreach ($matches[1] as $script){

				$this->CallJavaScript[$widget_id] = $script;


			}

			$widget = preg_replace('/\(\#\(javascript\:(.*)\)\#\)/', '', $widget);


		}
		return $widget;
	}



}


?>