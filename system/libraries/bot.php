<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class aiki_bot
{
	var $timeout = 5;

	function improt_mockup($url, $theme, $display_url){
		global $aiki, $db, $config;

		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

		ob_start();
		curl_exec($ch);
		curl_close($ch);
		$content = ob_get_contents();
		ob_end_clean();

		if ($content !== false) {

			if ( extension_loaded('tidy' ) and function_exists('tidy_parse_string')) {

				$tidy = new tidy();
				$tidy->parseString($content, $config["html_tidy_config"], 'utf8');
				$tidy->cleanRepair();

				$content = tidy_get_output($tidy);

			}

			$body = $aiki->get_string_between($content , "<body>", "</body>");

			if (isset($body)){

				$body = trim($body);
				$body = str_replace('"', "'", $body);

				$doc = new DOMDocument();
				$doc->loadHTML($body);
				$divs = $doc->getElementsByTagName('div');

				$i = 0;
				foreach($divs as $div) {
					$i++;
					$match = "/\<$div->nodeName";
					if ($div->getAttribute('id')){
						$widgetname = $div->getAttribute('id');
						$match .= " id\='".$div->getAttribute('id')."'";
					}else{
						$widgetname = 'nonamne';
					}

					if ($div->getAttribute('class')){
						$styleid = $div->getAttribute('class');
						$match .= " class\='".$div->getAttribute('class')."'";
					}else{
						$styleid = 'nostyle';
					}

					$match .= "\>(.*)\<\/$div->nodeName\>/Us";

					$item = preg_match($match, $body, $match);


					if (!preg_match('/\<div/', $match[1])){

						$match[1] = addslashes($match[1]);

						$do = $db->query("INSERT INTO aiki_widgets (`id` ,`widget_name` ,`widget_site` ,`widget_target` ,`widget_type` ,`display_order` ,`style_id` ,`is_father` ,`father_widget` ,`display_urls` ,`widget` ,`is_active`) VALUES (NULL, '$widgetname', 'default', 'body', 'div', '$i', '$styleid', '0', '', '$display_url', '$match[1]', '1')");


					}else{


						$father_dev = $aiki->get_string_between($match[0] , "<div", ">");
						$father_name = $aiki->get_string_between($father_dev, "id='", "'");
						$father_class = $aiki->get_string_between($father_dev, "class='", "'");

						$do = $db->query("INSERT INTO aiki_widgets (`id` ,`widget_name` ,`widget_site` ,`widget_target` ,`widget_type` ,`display_order` ,`style_id` ,`is_father` ,`father_widget` ,`display_urls` ,`widget` ,`is_active`) VALUES (NULL, '$father_name', 'default', 'body', 'div', '$i', '$father_class', '1', '', '$display_url', '', '1')");

					}

				}

				//set fathers:
				$widgets = $db->get_results("select id, is_father from aiki_widgets where display_urls='$display_url' order by display_order");
				if ($widgets){
						
					foreach ($widgets as $widget){

						if (isset($next_is_son) and $next_is_son != 0){
							$update = $db->query("update aiki_widgets set father_widget='$next_is_son' where id = '$widget->id'");
						}

						if ($widget->is_father == '1'){
							$next_is_son = $widget->id;
						}else{
							$next_is_son = 0;
						}

					}

					echo "Imported the mockup successfully";
				}else{
					echo "Faild to import the mockup";
				}

			}else{
				echo "Faild to locate the body of the document";
			}

		}else{
			echo "Faild to load contents form file";
		}


	}

	function import_css(){


	}


	function import_javascript(){


	}

	function import_image(){


	}

	function create_mockup_from_psd(){


	}

	function create_mockup_from_svg(){


	}

	function rename_files_give_timestamp($path){

		if (!isset($path)){return;}

		$handle = opendir($path);
		$path = str_replace(" ", "\ ", $path);
		while (($file = readdir($handle))!==false) {
			if ($file != "." and $file != ".."){

				$file = str_replace(" ", "\ ", $file);
				$file = str_replace("(", "\(", $file);
				$file = str_replace(")", "\)", $file);

				$or_file = $file;

				$file = time().".jpg";



				echo $or_file."<br>";
				sleep(1);
				exec("mv -v $path/$or_file $path/$file", $output);
				print_r($output);
				sleep(1);
			}
		}
		closedir($handle);
	}


	function create_photos_archive_meta(){
		global $config;

		$photos = $db->get_results("SELECT * FROM apps_photo_archive where checksum_sha1 =''");
		foreach ( $photos as $photo )
		{
			$path = $photo->full_path;

			if (file_exists($config['top_folder'].'/'.$path.$photo->filename)){
				$sha1 = sha1_file($config['top_folder'].'/'.$path.$photo->filename);
				$md5 = md5_file($config['top_folder'].'/'.$path.$photo->filename);
				$filesize = filesize($config['top_folder'].'/'.$path.$photo->filename);

				$size = getimagesize($config['top_folder'].'/'.$path.$photo->filename);
				$width = $size["0"];
				$hight = $size["1"];

				$db->query("update apps_photo_archive set checksum_sha1='$sha1', checksum_md5='$md5', upload_file_size='$filesize', width='$width', height='$hight', is_missing='0' where id='$photo->id'");

			}else{
				$db->query("update apps_photo_archive set is_missing='1' where id='$photo->id'");
			}
			echo $photo->id."<br>";
		}

	}


}

?>