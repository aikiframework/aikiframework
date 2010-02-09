<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
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