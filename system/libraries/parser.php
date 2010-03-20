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

	private $timeout = 5; // set to zero for no timeout

	public function process($text){

		$text = $this->markup_ajax($text);
		$text = $this->images($text);
		$text = $this->inline($text);
		$text = $this->intlinks($text);
		$text = $this->extlinks($text);
		$text = $this->aikiTemplates($text);
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
						foreach ($xml->channel->item as $item) {

							$items_matchs = preg_match_all('/\[\[(.*)\]\]/Us', $output, $elements);

							if ($items_matchs > 0){

								$processed_output = $output;

								foreach ($elements[1] as $element){

									$element = trim($element);

									$processed_output = str_replace("[[".$element."]]", $item->$element, $processed_output);

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

	public function intlinks($text){
		global $aiki, $membership, $db, $config;


		if (preg_match('/\(\+\((.*)\)\+\)/', $text)){

			$link_tags = $db->get_results("SELECT * FROM apps_wiki_links");
			$query = '';
			if ($link_tags){

				foreach ($link_tags as $tag)
				{
					$query = '';


					$count = preg_match_all( '/'.preg_quote($tag->tagstart, '/').preg_quote($tag->parlset, '/').'(.*)'.preg_quote($tag->tagend, '/').'/U', $text, $match );

					if ($count > 0){

						if ($tag->linkexample){

							$query = "SELECT $tag->idcolumn, $tag->namecolumn FROM $tag->dbtable WHERE ";
							$tagidcolumn = $tag->idcolumn;
							$tagnamecolumn = $tag->namecolumn;
							$is_extrasql_loop = $tag->is_extrasql_loop;

							$i = 1;

							if ($tag->extrasql){
								$extrasql = "$tag->extrasql";
							}else{
								$extrasql = "";
							}

							$tagnamecolumn = $tag->namecolumn;

							foreach ($match[1] as $tag_match){

								$tag_match_array = explode('=', $tag_match);

								if (!isset($tag_match_array[1])){

									$tag_text = $tag_match_array[0];
									$tag_equivalent = $tag_match_array[0];

								}else{

									$tag_text = $tag_match_array[0];
									$tag_equivalent = $tag_match_array[1];

								}

								$query .= "$tagnamecolumn LIKE '$tag_equivalent'";


								if ($extrasql and $is_extrasql_loop){
									$extrasql = str_replace('[tag_equivalent]', $tag_equivalent, $extrasql);
									$query .= " $extrasql ";
								}


								if ($count != $i){
									$add_or = "or";
								}else{
									$add_or = "";
								}

								$query .= " $add_or ";


								$i++;
							}

							if ($extrasql and !$is_extrasql_loop){
								$extrasql = str_replace('[tag_equivalent]', $tag_equivalent, $extrasql);
								$query .= " $extrasql ";
							}

							$result = $db->get_results($query);
							if ($result){

								foreach($result as $replacment){

									$tagname = $replacment->$tagnamecolumn;
									$tagid = $replacment->$tagidcolumn;

									foreach ($match[1] as $tag_output){
										$tag_output = explode('=', $tag_output);
										if ($tag_output[1]){

											$tag_output_side = $tag_output[1];

											if ($tag_output_side == $tagname){

												$text = str_replace($tag->tagstart.$tag->parlset.$tag_output[0].'='.$tag_output[1].$tag->tagend, "<a href=\"".$config['url']."$tag->linkexample/$tagid\">$tag_output[0]</a>", $text);

											}

										}else{
											$tag_output = $tag_output[0];

											if ($tag_output == $tagname){

												$text = str_replace($tag->tagstart.$tag->parlset.$tag_output.$tag->tagend, "<a href=\"".$config['url']."$tag->linkexample/$tagid\">$tag_output</a>", $text);

											}

										}



									}

								}


							}

						}


					}
					if ($membership->permissions == "SystemGOD"){
						$text = preg_replace( '/'.preg_quote($tag->tagstart, '/').preg_quote($tag->parlset, '/').'(.*)(\=.*)?'.preg_quote($tag->tagend, '/').'/U', "<a style='color:#FF0000' target=\"_blank\" href=\"".$config['url']."$tag->linkexample/new\"><b>\\1</b></a>", $text );
						//$text = preg_replace( '/'.preg_quote($tag->tagstart, '/').preg_quote($tag->parlset, '/').'[\x0627-\x0649](\=.*)?'.preg_quote($tag->tagend, '/').'/U', "<a style='color:#FF0000' target=\"_blank\" href=\"aikicore->setting[url]/$tag->linkexample/new\"><b>\\1</b></a>", $text );
						//'/\(\+\(tag:(.*?)[^)]*\)\+\)/';
						//$text = preg_replace( '/'.preg_quote($tag->tagstart, '/').preg_quote($tag->parlset, '/').'(.*)(\=[^)].*)?'.preg_quote($tag->tagend, '/').'/U', "<a style='color:#FF0000' target=\"_blank\" href=\"aikicore->setting[url]/$tag->linkexample/new\"><b>\\1</b></a>", $text );

					}else{
						$text = preg_replace( '/'.preg_quote($tag->tagstart, '/').preg_quote($tag->parlset, '/').'(.*)(\=.*)?'.preg_quote($tag->tagend, '/').'/U', '\\1', $text );
					}

				}
			}


		}
		return $text;

	}


	public function extlinks($text){
		global $aiki, $config;

		$tags_output = array();

		$count = preg_match_all( '/'."\(\+\(".'(.*)'."\)\+\)".'/U', $text, $match );

		if ($count > 0){

			foreach ($match[1] as $tag_match){

				$tag_match_array = explode('=', $tag_match);

				if (!isset($tag_match_array[1])){

					$tag_text = $tag_match_array[0];
					$tag_equivalent = $tag_match_array[0];

				}else{

					$tag_text = $tag_match_array[0];
					$tag_equivalent = $tag_match_array[1];

				}


				//TODO: make sure it's correct link and if not correct it, and check for email addresses
				$processed_tag = "<a target=\"_blank\" href=\"$tag_equivalent\" style=\"background:transparent url(".$config['url']."assets/images/external.png) no-repeat scroll left center; padding-left:13px;\">".$tag_text.'</a>';


				$tags_output[] .= $processed_tag;

			}
			$text = str_replace($match[0], $tags_output, $text);

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


	public function aikiTemplates($widget){
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


				$template_output = $db->get_var("SELECT template_output from apps_wiki_templates where template_name = '$templateName'");

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