<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class aiki_aiki_markup extends aiki
{
	
	function aiki_parser($text){
		
		$text = $this->markup_ajax($text);
		//$text = $this->datetime($text);
		$text = $this->aikiTemplates($text);
		
		return $text;
	}

	function markup_ajax($text){
		global $db;
		/*
		 <script type="text/javascript">
		 $(document).ready(function(){
		 function ajaxSOMETHING(file, targetwidget){
		 $.get('http://www.aikiframework.org/aikidev/admin_tools/edit/widgets/'+file,function(data) {
		 $(targetwidget).html(data);
		 });
		 }

		 $("a").click(function(event){
		 globalajaxify($(this).attr("rel"), $(this).attr("href"));
		 return false;

		 });
		 });
		 </script>
		 */

		$count_links = preg_match_all('/\(ajax\_a\((.*)\)ajax\_a\)/Us', $text, $links);

		if ($count_links > 0){


			foreach ($links[1] as $set_of_requests)
			{
				$output = '';

				$array = explode(';', $set_of_requests);

				$array_of_values = $array;

				unset($array_of_values[0]);

				$function_name = str_replace('-', '', $array[0]);

				$output .= " <script type=\"text/javascript\">
				$(document).ready(function(){
				function $function_name(file, targetwidget, callback){

				$(targetwidget).load(file, {limit: 25}, function(){
				eval(callback);
			});
			}
		 $(\"#$array[0]\").click(function(event){
		 ";

				foreach ($array_of_values as $value){

					$value = $this->get_string_between($value, "[", "]");

					$value = explode(',', $value);

					$url = $this->get_string_between($value['0'], "'", "'");
					$target = $this->get_string_between($value['1'], "'", "'");
						
					if (isset ($value['2'])){
						$callback = $this->get_string_between($value['2'], "'", "'");
					}

					$output .= "$function_name('$url', '$target'";

					if ($callback){
						$output .= ", '$callback;'";
					}

					$output .= ");"."\n";

				}



				$output .= "return false;

		 });
		 });
		 </script>";

				$text = preg_replace('/\(ajax\_a\('.preg_quote($set_of_requests, '/').'\)ajax\_a\)/Us', $output, $text);

			}

		}

		return $text;
	}

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