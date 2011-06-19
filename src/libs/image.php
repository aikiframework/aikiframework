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
 * A utility class to manipulate images.
 *
 * @category    Aiki
 * @package     Library
 *
 * @todo        rename class to Image
 */
class image
{
	/**
	 * Converts an svg file to png
	 * 
	 * @link http://librsvg.sourceforge.net/
	 *
	 * @param   string	$file       filename with fullpath
	 * @param   int		$newwidth   png width
	 * @param   int		$newheight	png height
	 *  
	 * @return  string
	 */
	public function rsvg_convert_svg_png($file, $newwidth, $newheight)
	{
		$file = str_replace(" ", "\ ",  $file);
		$file = str_replace("(", "\(",  $file);
		$file = str_replace(")", "\)",  $file);
		//check if rsvg exists
		exec("rsvg -v", $checkversion);

		if ($newwidth < $newheight)
			$size = $newheight;
		else
			$size = $newwidth;

		if ($checkversion[0])
		{
			$filenopath = explode("/", $file);
			$filenopath = array_reverse($filenopath);

			$fileno = $filenopath[0];
			$fileno = str_replace(".svg", ".png", $fileno);
			$fileno = $size."px-".$fileno;

			$filenamepng = str_replace($filenopath[0], $fileno, $file);

			exec("rsvg --width $newwidth --height $newheight ".
				 "$file $filenamepng", $output);

		} else {
			/**
			 * @todo rip out this error, a user should never seen this.
			 */
			$output = "<b>Fatal Error: </b>Can't find (rsvg)";
		}
		return $filenamepng;
	}

	/**
	 * Outputs an image with a watermark over it.
	 *
	 * @param	string	$fimage			path to an image
	 * @param	string	$watermark_file	path to the watermark to overlay image
	 * @param	integer	$minValueWaterMark	
	 * 
	 */
	public function display_watermarked_image($fimage, 
											  $watermark_file, 
											  $minValueWaterMark)
	{
		$size = getimagesize($fimage);

		if ($minValueWaterMark and 
			$size["0"] < $minValueWaterMark and 
			$size["1"] < $minValueWaterMark)
		{
			// nothing?
		} else 
		{
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
			imagecopy($image, $watermark, $dest_x, $dest_y, 
					  0, 0, $watermark_width, $watermark_height);
			imagejpeg($image);
			imagedestroy($image);
			imagedestroy($watermark);
		}
	} // end of display_watermarked_image function


	/**
	 * Resizes and iamge
	 * @param	string	$path			path to image to resize
	 * @param	string	$filename		name of file
	 * @param	integer	$newvalue		get maximum new size
	 * @param	string	$imageprefix	prefix to the new image
	 *
	 * @todo	should really allow one to specify new height or width
	 */
	public function imageresize($path,$filename,$newvalue,$imageprefix)
	{
		$filename2 =$path.$filename;
		$size = getimagesize($filename2);
		$width = $size["0"];
		$height = $size["1"];
		$type = $size["mime"];

		if ($width < $height)
		{
			$newheight = $newvalue;
			$newwidth = round(($newvalue * $width)/$height);
		} elseif ($width == $height) {
			$newheight = $newvalue;
			$newwidth = $newvalue;
		} else {
			$newwidth = $newvalue;
			$newheight = round(($newvalue * $height)/$width);
		}

		if ($width < $newwidth or $height < $newheight)
		{
			$newheight = $height;
			$newwidth = $width;
		}

		switch ($type)
		{
			case "image/jpeg":
				$thumb = imagecreatetruecolor($newwidth, $newheight);
				$source = imagecreatefromjpeg($filename2);
				imagecopyresampled($thumb, $source, 0, 0, 0, 0, 
								   $newwidth, $newheight, $width, $height);
				imagejpeg($thumb,$path.$imageprefix.$filename);
				imagedestroy($thumb);
				imagedestroy($source);
				break;

			case "image/gif":
				$thumb = imagecreatetruecolor($newwidth, $newheight);
				$source = imagecreatefromgif($filename2);
				imagecopyresampled($thumb, $source, 0, 0, 0, 0, 
								   $newwidth, $newheight, $width, $height);
				imagegif($thumb,$path.$imageprefix.$filename);
				imagedestroy($thumb);
				imagedestroy($source);
				break;

			case "image/png":
				$thumb = imagecreatetruecolor($newwidth, $newheight);
				$source = imagecreatefrompng($filename2);

				imagealphablending($source, false);
				imagesavealpha($source, true);
				imagealphablending($thumb, false);
				imagesavealpha($thumb, true);
				imagecopyresampled($thumb, $source, 0, 0, 0, 0, 
								   $newwidth, $newheight, $width, $height);
				imagepng($thumb,$path.$imageprefix.$filename);
				imagedestroy($thumb);
				imagedestroy($source);
				break;
		}

	} // end of imageresize

} // end of Image class
