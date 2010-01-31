<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2009-2010 Aikilab inc
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */

/*
 Page History
 [##KeepCopies##]
 [##KeepCopies|public##]
 [##KeepCopies|admins##]
 [##KeepCopies|user group##]



 1- (+(bank:Canada#Ottawa)+)
 2- (+(bank:Canda=Canada#Ottawa)+)

 in the same Page:
 3- (+(#Ottawa)+)
 4- (+(Otawa=#Ottawa)+)


 7- (+(wikipedia:Canada)+)
 8- (+(wikipedia:Canda=Canada)+)

 9- (+(GeoCo:33,11222|24,22111)+)
 10- (+(GeoAdd:address goes here)+)

insert current date and time:
 ~~~~~


catagorize the article, like in wiki style some who:
 {{Cities}} {{Canada}}

Related Links:
 [##related:photos##]
 [##related:news##]
 [##related:photos=Canada##]


 [##sound:filename.flv##]
 [##sound:filename.mp3|player:some player##]

 [##flv:filename.flv##]
 [##mov:filename.mov##]
 [##flv:http://www.google.com/filename.flv##]



 L10n:
 [[some_string]]
 */

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
			//$widget = preg_replace("", "bassel", $widget);
		}

		return $widget;

	}



}
?>