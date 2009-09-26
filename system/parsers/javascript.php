<?php

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