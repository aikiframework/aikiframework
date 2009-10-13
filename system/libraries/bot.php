<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2008-2009 Bassel Khartabil.
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */

if ($membership->permissions != "SystemGOD"){
	die("You do not have permissions to access this file");
}

?>
<style type="text/css">
input {
	-moz-background-clip: border;
	-moz-background-inline-policy: continuous;
	-moz-background-origin: padding;
	background: GhostWhite none repeat scroll 0 0;
	border: 2px solid #C3C3C3;
	color: #555753;
	font-family: "Courier New";
	font-size: 120%;
	margin: 0 4px 12px;
	padding: 3px;
}

label {
	margin-top: 12px;
}
</style>

<?php

class aiki_bot
{

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