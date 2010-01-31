<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2009-2010 Aikilab inc
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


//TODO: Image Manipulation Library (cropping, resizing, rotating, etc.). Supports GD, ImageMagick, and NetPBM

class aiki_image
{
	function rsvg_convert_svg_png($file, $newwidth, $newhight){

		exec("rsvg -v", $checkversion);//check if rsvg is exists and the eninge can access it and get me the version

		if ($checkversion[0]){
			$filenamepng = str_replace(".svg", ".png", $file);

			exec("rsvg --width $newwidth --height $newhight $file $filenamepng", $output);
		}else{
			$output = "<b>Fatal Error: </b>Can't find (rsvg)";
		}

		return $filenamepng;
	}

	function inkscape_convert_svg_png($path_to_inkscape, $svg_default_background, $images_path, $filename){
		$filenamepng = str_replace(".svg", ".png", $filename);
		//check if inkscape is exists and aikicms can access
		exec("".$path_to_inkscape."inkscape -V", $checkversion);
		if ($checkversion[0]){
			exec("".$path_to_inkscape."inkscape -e ".$images_path.$filenamepng." ".$images_path.$filename." --export-background=".$svg_default_background."", $output);
		}else{
			$output = "<b>Fatal Error: </b>Can't find (inkscape)";
		}
		return $output;
	}


	function imageresize($path,$filename,$newvalue,$imageprefix)
	{
		$filename2 =$path.$filename;
		$size = getimagesize($filename2);
		$width = $size["0"];
		$hight = $size["1"];
		$type = $size["mime"];
		if ($width < $hight){
			$newhight = $newvalue;
			$newwidth = round(($newvalue * $width)/$hight);
		}elseif ($width == $hight) {
			$newhight = $newvalue;
			$newwidth = $newvalue;
		}else{
			$newwidth = $newvalue;
			$newhight = round(($newvalue * $hight)/$width);
		}
		$thumb = imagecreatetruecolor($newwidth, $newhight);

		switch ($type){
			case "image/jpeg":
				$source = imagecreatefromjpeg($filename2);
				imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newhight, $width, $hight);
				imagejpeg($thumb,$path.$imageprefix.$filename);
				break;
			case "image/gif":
				$source = imagecreatefromgif($filename2);
				imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newhight, $width, $hight);
				imagegif($thumb,$path.$imageprefix.$filename);
				break;
			case "image/png":
				$source = imagecreatefrompng($filename2);
				imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newhight, $width, $hight);
				imagepng($thumb,$path.$imageprefix.$filename);
				break;
		}


	}



	function extract_exif($image){

		//http://ca.php.net/exif_read_data
		$exif = exif_read_data($image, 'IFD0');
		echo $exif===false ? "No header data found.<br />\n" : "Image contains headers<br />\n";

		$exif = exif_read_data($image, 0, true);
		echo "$image<br />\n";
		foreach ($exif as $key => $section) {
			foreach ($section as $name => $val) {
				echo "$key.$name: $val<br />\n";
			}
		}
	}


}
?>