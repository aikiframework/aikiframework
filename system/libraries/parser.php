<?php

/**
 * Aiki framework (PHP)
 *
 * @author		Aikilab http://www.aikilab.com
 * @copyright  (c) 2008-2010 Aikilab
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class parser extends aiki
{

	//curl timeout. set to zero for no timeout
	//TODO add timeout here and other places to config editor
	private $timeout = 2;

	public function process($text){

		$text = $this->markup_ajax($text);
		$text = $this->images($text);
		$text = $this->inline($text);
		$text = $this->rss_parser($text);

		return $text;
	}


	public function rss_parser($text){
		global $aiki;

		$rss_matchs = preg_match_all('/\<rss\>(.*)\<\/rss\>/Us', $text, $matchs);

		if ($rss_matchs > 0){

			foreach ($matchs[1] as $rss){

				$rss_url = $aiki->get_string_between($rss , "<url>", "</url>");
				$rss_url = trim($rss_url);

				$limit = $aiki->get_string_between($rss , "<limit>", "</limit>");
				$limit = trim($limit);

				$output = $aiki->get_string_between($rss , "<output>", "</output>");

				$type = $aiki->get_string_between($rss , "<type>", "</type>");
				if (!$type){
					$type = "rss";
				}

				if(!$output){

					$output = "<div class='news'>
						<h4>[[title]]</h4>
						<p>[[pubDate]]</p>
						<p><a href='[[link]]'>[[guid]]</a></p>
						<div class='description'>[[description]]</div>
						</div>";
				}

				$ch = curl_init();
				curl_setopt ($ch, CURLOPT_URL, $rss_url);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

				ob_start();
				curl_exec($ch);
				curl_close($ch);
				$content = ob_get_contents();
				ob_end_clean();


				if ($content !== false) {

					$xml = @simplexml_load_string($content);

					$i = 1;

					$html_output = '';
					if ($xml){

						switch ($type){
							case "atom":
								$xml_items = $xml->entry;
								break;

							case "rss":
								$xml_items = $xml->channel->item;
								break;
						}

						foreach ($xml_items as $item) {

							$items_matchs = preg_match_all('/\[\[(.*)\]\]/Us', $output, $elements);

							if ($items_matchs > 0){

								$processed_output = $output;

								foreach ($elements[1] as $element){

									$element = trim($element);


									if (preg_match('/\-\>/', $element)){

										$element = explode("->", $element);
										$element_sides = $item->$element[0]->$element[1];

										$processed_output = str_replace("[[".$element[0]."->".$element[1]."]]", $element_sides, $processed_output);

									}elseif (preg_match('/\:/', $element)){
										
										$element = explode(":", $element);
										$element_sides = $item->$element[0]->attributes()->$element[1];
										

										$processed_output = str_replace("[[".$element[0].":".$element[1]."]]", $element_sides, $processed_output);

									}else{

										$processed_output = str_replace("[[".$element."]]", $item->$element, $processed_output);

									}

								}

								$html_output .= $processed_output;
								$processed_output = '';
							}



							if (isset($limit) and $limit == $i){
								break;
							}
							$i++;
						}

					}else{


					}
				}

				$text = str_replace("<rss>$rss</rss>", $html_output , $text);
			}
		}

		return $text;
	}


	public function tags($text, $widget_value){
		global $db, $aiki;

		$tags = $aiki->get_string_between($text, "(#(tags:", ")#)");
		if ($tags){
			$tagsides = explode("||", $tags);

			/*$tag_cloud = "[[relatedKeywords]]:
			 <br />
			 <ul>";*/
			$tags_links = explode(",", $widget_value->$tagsides[0]);
			$tag_cloud = '';
			foreach ($tags_links as $tag_link){
				if ($tag_link){
					$tag_link = trim($tag_link);
					$tag_cloud .= ' , <a href="[root]/'.$tagsides[1].'" rel="tag">'.$tag_link.'</a>';
					$tag_cloud = str_replace("_self", $tag_link, $tag_cloud);
				}
			}
			//$tag_cloud .= "</ul>";
			$text = str_replace("(#(tags:$tags)#)", $tag_cloud , $text);
		}

		return $text;

	}

	public function images($text){
		global $db, $aiki, $config;
		$numMatches = preg_match_all( '/\{\+\{/', $text, $matches);

		for ($i=0; $i<$numMatches; $i++){

			$get_photo_info = $this->get_string_between($text, "{+{", "}+}");
			$photo_info_array = explode("|", $get_photo_info);
			$html_photo = "";

			if (!isset($photo_info_array[7])){
				$html_photo .= "<a href='".$config['url']."file/image|".$photo_info_array[0]."'>";
			}

			$html_photo .= "<img ";

			//display image by calling it's id {+{213}+}


			$html_photo .= "src='".$config['url']."image/";

			if ($photo_info_array[5] and $photo_info_array[5] != "px" ){
				$html_photo .= "$photo_info_array[5]/"; //add spesific size virtual folder
			}
			$html_photo .= "$photo_info_array[0]'";

			/*}elseif (preg_match('/([0-9])/',$photo_info_array[0])) {
				$photo_file = $db->get_row("SELECT filename FROM modules_photo_archive where id='$photo_info_array[0]'");
				if ($photo_file){
				if ($photo_file->filename){
				$html_photo .= "src='aikicore->setting[url]/image/";
				if ($photo_info_array[5] and $photo_info_array[5] != "px" ){
				$html_photo .= "$photo_info_array[5]/"; //add spesific size virtual folder
				}
				$html_photo .= "$photo_file->filename'";
				}
				}
				}
				*/


			//this will overwrite the alt value from the database
			if (isset($photo_info_array[1]) and $photo_info_array[1] != "0" ){
				$html_photo .= "alt='$photo_info_array[1]' ";
			}

			if (isset($photo_info_array[2]) and $photo_info_array[2] != "0" and !$photo_info_array[6]){//no need to align if it's contained in aligned div
				$html_photo .= "align='$photo_info_array[2]' ";
			}

			if (isset($photo_info_array[3]) and $photo_info_array[3] != "v:" ){
				$photo_info_array[3] = str_replace("v:", "", $photo_info_array[3]);
				$html_photo .= "vspace='$photo_info_array[3]' ";
			}

			if ($photo_info_array[4] and $photo_info_array[4] != "h:" ){
				$photo_info_array[4] = str_replace("h:", "", $photo_info_array[4]);
				$html_photo .= "hspace='$photo_info_array[4]' ";
			}
			$html_photo .= "/ >";
			if (!isset($photo_info_array[7])){
				$html_photo .= "</a>";
			}
			if (isset($photo_info_array[6]) and $photo_info_array[6] != "0" ){
				$html_photo .= "<br />$photo_info_array[6]";
			}


			if ($photo_info_array[6] and $photo_info_array[6] != "0" ){
				$html_photo = "<div id='img_container' style='z-index: 9; clear: ".$photo_info_array[2]."; float: ".$photo_info_array[2]."; border-width: .5em 0 .8em 1.4em; padding: 10px'>
				<div style='z-index: 10; border: 1px solid #ccc;	padding: 3px; background-color: #f9f9f9;font-size: 80%;text-align: center;overflow: hidden;'>$html_photo</div></div>";
			}


			$text = str_replace("{+{".$get_photo_info."}+}", $html_photo, $text);
		}

		return $text;

	}


	public function inline($text){

		$inline = preg_match_all('/\(\#\(inline\:(.*)\)\#\)/U', $text, $matchs);

		if ($inline > 0){

			foreach ($matchs[1] as $inline_per){

				$content = file_get_contents($inline_per);

				$text = str_replace("(#(inline:$inline_per)#)", $content, $text);


			}
		}

		return $text;
	}


	public function markup_ajax($text){
		global $db;

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

	public function datetime($text, $widget_value){
		global $aiki, $config;

		$datetimes = preg_match_all('/\(\#\(datetime\:(.*)\)\#\)/Us', $text, $matchs);
		if ($datetimes > 0){
			foreach ($matchs[1] as $datetime){

				if (preg_match('/[0-9]{10}/', $widget_value->$datetime)){ //Check if valid unix timestamp

					$widget_value->$datetime = date($config['default_time_format'], $widget_value->$datetime);

				}else{
					$widget_value->$datetime = '';
				}
				//TODO: Custom Time output formats inserted by user
				$text = str_replace("(#(datetime:$datetime)#)", $widget_value->$datetime , $text);

			}
		}
		return $text;
	}



}
?>