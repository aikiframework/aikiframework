<?php

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class ajax extends aiki
{

	function ajax(){

	}

	function do_ajax($text){
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
		 	$callback = $this->get_string_between($value['2'], "'", "'");

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
}
?>