<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Aikilab http://www.aikilab.com 
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Library
 * @filesource
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


/**
 * BriefDescription
 *
 * @category    Aiki
 * @package     Library
 */
class image
{


	/**
	 * Converts an svg file to png
	 *
	 * @param   string filename with fullpath
	 * @param   int png width
	 * @param   int png height
	 *  
	 * @return  string
	 */
	public function rsvg_convert_svg_png($file, $newwidth, $newhight){

		$file = str_replace(" ", "\ ",  $file);
		$file = str_replace("(", "\(",  $file);
		$file = str_replace(")", "\)",  $file);
		//check if rsvg exists
		exec("rsvg -v", $checkversion);

		if ($newwidth < $newhight){
			$size = $newhight;
		}else{
			$size = $newwidth;
		}

		if ($checkversion[0]){

			$filenopath = explode("/", $file);
			$filenopath = array_reverse($filenopath);

			$fileno = $filenopath[0];
			$fileno = str_replace(".svg", ".png", $fileno);
			$fileno = $size."px-".$fileno;

			$filenamepng = str_replace($filenopath[0], $fileno, $file);


			exec("rsvg --width $newwidth --height $newhight $file $filenamepng", $output);

		}else{
			$output = "<b>Fatal Error: </b>Can't find (rsvg)";
		}

		return $filenamepng;
	}


	public function display_watermarked_image($fimage, $watermark_file, $minValueWaterMark){
		$size = getimagesize($fimage);

		if ($minValueWaterMark and $size["0"] < $minValueWaterMark and $size["1"] < $minValueWaterMark){

		}else{

			$watermark_file_size = getimagesize($watermark_file);

			$watermark_width = $watermark_file_size["0"];
			$watermark_height = $watermark_file_size["1"];

			$watermark = imagecreatefrompng($watermark_file);


			imagealphablending($watermark, false);
			imagesavealpha($watermark, true);

			$image = imagecreatetruecolor($watermark_width, $watermark_height);
			$image = imagecreatefromjpeg($fimage);

			$dest_x = 5;
			$dest_y = $size[1] - $watermark_height - 5;
			imagecopy($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
			imagejpeg($image);
			imagedestroy($image);
			imagedestroy($watermark);
		}
	}


	public function imageresize($path,$filename,$newvalue,$imageprefix)
	{
		$filename2 =$path.$filename;
		$size = getimagesize($filename2);
		$width = $size["0"];
		$height = $size["1"];
		$type = $size["mime"];
		if ($width < $height){
			$newhight = $newvalue;
			$newwidth = round(($newvalue * $width)/$height);
		}elseif ($width == $height) {
			$newhight = $newvalue;
			$newwidth = $newvalue;
		}else{
			$newwidth = $newvalue;
			$newhight = round(($newvalue * $height)/$width);
		}

		if ($width < $newwidth or $height < $newhight){
			$newhight = $height;
			$newwidth = $width;
		}



		switch ($type){
			case "image/jpeg":
				$thumb = imagecreatetruecolor($newwidth, $newhight);

				$source = imagecreatefromjpeg($filename2);
				imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newhight, $width, $height);
				imagejpeg($thumb,$path.$imageprefix.$filename);

				imagedestroy($thumb);
				imagedestroy($source);
				break;
			case "image/gif":
				$thumb = imagecreatetruecolor($newwidth, $newhight);

				$source = imagecreatefromgif($filename2);
				imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newhight, $width, $height);
				imagegif($thumb,$path.$imageprefix.$filename);

				imagedestroy($thumb);
				imagedestroy($source);
				break;

			case "image/png":

				$thumb = imagecreatetruecolor($newwidth, $newhight);

				$source = imagecreatefrompng($filename2);


				imagealphablending($source, false);
				imagesavealpha($source, true);

				imagealphablending($thumb, false);
				imagesavealpha($thumb, true);

				imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newhight, $width, $height);
				imagepng($thumb,$path.$imageprefix.$filename);

				imagedestroy($thumb);
				imagedestroy($source);
				break;
		}


	}


}
