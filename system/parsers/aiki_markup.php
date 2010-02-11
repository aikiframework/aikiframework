<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class aiki_aiki_markup
{

	function datetime($text, $widget_value){
		global $aiki;

		$datetimes = preg_match_all('/\(\#\(datetime\:(.*)\)\#\)/Us', $text, $matchs);
		if ($datetimes > 0){
			foreach ($matchs[1] as $datetime){

				if (preg_match('/[0-9]{10}/', $widget_value->$datetime)){ //Check if valid unix timestamp

					$widget_value->$datetime = date($aiki->setting['default_time_format'], $widget_value->$datetime);

				}else{
					$widget_value->$datetime = '';
				}
				//TODO: Custom Time output formats inserted by user
				$text = str_replace("(#(datetime:$datetime)#)", $widget_value->$datetime , $text);

			}
		}
		return $text;
	}


	function aikiTemplates($widget){
		global $db, $aiki;

		$numMatches = preg_match_all( '/\{\{/', $widget, $matches);


		for ($i=0; $i<$numMatches; $i++){

			$templateFullText = $aiki->get_string_between($widget, "{{", "}}");
			$templateText = str_replace("| ", "|", $templateFullText);

			$templateText = str_replace("<br>", "", $templateText); //fix for after nl2br function is used
			$templateText = str_replace("<br />", "", $templateText);

			$templateElement = explode("|", $templateText);
			$templateName = trim($templateElement[0]);


			if ($templateName){


				$template_output = $db->get_var("SELECT template_output from aiki_template where template_name = '$templateName'");

			}

			foreach ($templateElement as $element){
				$element = trim($element);
				$elementSides = explode("=", $element);
				$elementSides[0] = trim($elementSides[0]);
				$elementSides[1] = trim($elementSides[1]);

				$template_output = str_replace("($elementSides[0])", $elementSides[1], $template_output);

			}
			$widget = str_replace ("{{".$templateFullText."}}", $template_output, $widget);

		}

		return $widget;

	}



}
?>