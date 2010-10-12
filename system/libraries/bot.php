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


class bot
{
	public  $timeout = 20;
	public  $url;


	public function import_mockup($url, $theme, $display_url){
		global $aiki, $db, $config;

		$this->url = $url;

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



			$head = $aiki->get_string_between($content , "<head>", "</head>");

			if (isset($head)){
				$head = trim($head);
				$css = $this->import_css($head);
				$css = addslashes($css);
			}else{
				$css = '';
			}


			$body = $aiki->get_string_between($content , "<body>", "</body>");

			if (isset($body)){

				$body = trim($body);
				$body = str_replace('"', "'", $body);

				$doc = new DOMDocument();
				@$doc->loadHTML($body);
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

						$do = $db->query("INSERT INTO aiki_widgets (`id` ,`widget_name` ,`widget_site` ,`widget_target` ,`widget_type` ,`display_order` ,`style_id` ,`is_father` ,`father_widget` ,`display_urls` ,`widget` ,`is_active`, `css`) VALUES (NULL, '$father_name', 'default', 'body', 'div', '$i', '$father_class', '1', '', '$display_url', '', '1', '$css')");
						$css = '';

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

	public function import_css($head){

		$css_matchs = preg_match_all('/\<link href\=\"(.*)\" type\=\"text\/css\" rel=\"stylesheet\" \/\>/Us', $head, $matchs);

		if ($css_matchs > 0){
			$css = '';
			foreach ($matchs[1] as $css_link){

				if (preg_match('/http/', $css_link)){
					$link = $css_link;
				}else{
					$link = preg_replace('/(.*)\/(.*)/', '\\1'.'/'.$css_link, $this->url);
				}

				$ch = curl_init();
				curl_setopt ($ch, CURLOPT_URL, $link);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

				ob_start();
				curl_exec($ch);
				curl_close($ch);
				$content = ob_get_contents();
				ob_end_clean();

				if ($content !== false) {
					$css .= $content;
				}

			}
		}

		return $css;
	}


	public function import_javascript(){


	}

	public function import_image(){


	}

	public function create_mockup_from_psd(){


	}

	public function create_mockup_from_svg(){


	}

	public function rename_files_give_timestamp($path){

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


	public function create_photos_archive_meta($tablename){
		global $config;

		$photos = $db->get_results("SELECT * FROM $tablename where checksum_sha1 =''");
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

				$db->query("update $tablename set checksum_sha1='$sha1', checksum_md5='$md5', upload_file_size='$filesize', width='$width', height='$hight', is_missing='0' where id='$photo->id'");

			}else{
				$db->query("update $tablename set is_missing='1' where id='$photo->id'");
			}
			echo $photo->id."<br>";
		}

	}



	//dump all
	//dump app
	//dump aiki installtion with all apps and no data
	//dump user data or not
	function mysql_dump($database) {

		$query = '';

		$tables = @mysql_list_tables($database);
		while ($row = @mysql_fetch_row($tables)) {
			$table_list[] = $row[0];
		}

		for ($i = 0; $i < @count($table_list); $i++) {

			$results = mysql_query('DESCRIBE ' . $database . '.' . $table_list[$i]);

			$query .='CREATE TABLE `' . $table_list[$i] . '` (' . "\r";

			$tmp = '';

			while ($row = @mysql_fetch_assoc($results)) {

				$query .= '`' . $row['Field'] . '` ' . $row['Type'];

				if ($row['Null'] != 'YES') { $query .= ' NOT NULL'; }
				if ($row['Default'] != '') { $query .= ' DEFAULT \'' . $row['Default'] . '\''; }
				if ($row['Extra']) { $query .= ' ' . strtoupper($row['Extra']); }
				if ($row['Key'] == 'PRI') { $tmp = 'primary key(' . $row['Field'] . ')'; }

				$query .= ','. "\r";

			}

			$query .= $tmp . "\r" . ');' . str_repeat("\r", 1);

			$query .= '--------------------------------------------------------'."\r";

			$results = mysql_query('SELECT * FROM ' . $database . '.' . $table_list[$i]);

			while ($row = @mysql_fetch_assoc($results)) {

				$query .= 'INSERT INTO `' . $table_list[$i] .'` (';

				$data = Array();

				while (list($key, $value) = @each($row)) {
					$data['keys'][] = $key; $data['values'][] = addslashes($value);
				}

				$query .= join($data['keys'], ', ') . ')' . "\r" . 'VALUES (\'' . join($data['values'], '\', \'') . '\');' . "\r";
					
				$query .= '--------------------------------------------------------'."\r";

			}

		}

		$query = str_replace("'CURRENT_TIMESTAMP'", 'CURRENT_TIMESTAMP', $query );

		return $query;

	}


}

?>