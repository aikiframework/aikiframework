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
 * @package     Apps
 * @filesource
 *
 * @todo		break this code into smaller pieces
 */

error_reporting(0);
//error_reporting(E_STRICT | E_ALL);

/**
 * @global string $id the name of the image
 */
$id = $_GET['id'];

/**
 * @global int $size grabs the size of the file
 */
$size = $_GET['size'];

if (isset($_GET['mode']))
	$mode = $_GET['mode'];
else
	$mode = '';

/**
 * @global string $ext isolates the extension of the filename
 */
$ext = substr($id, strrpos($id, '.') + 1);

/**
 * Converts extension to all lower case, to be read into switch case comparison
 */
$ext = strtolower($ext);

if ($ext == "jpeg")
	$ext = "jpg";

/**
 * Compares isolated file extension & generates appropriate content-type
 */
switch ($ext)
{
	case "svg":
		header('Content-Type: image/svg+xml');
		break;

	case "png":
		header('Content-Type: image/png');
		break;

	case "jpg":
		header('content-type: image/jpeg');
		break;

	default:
		header('content-type: image/jpeg');
		break;
}

/**
 * Case to check if file is displayable, by default, or if additional processing is needed
 */
switch ($mode)
{
	case "svg_to_png":
		$ext = "svg";
		break;

	default:
		if ($mode)
			$hard_full_path = $mode;
		break;
}


/**
 * @see bootstrap.php
 */
require_once("../../bootstrap.php");

/**
 * @see image.php
 */
$aiki->load("image");


$default_photo_module = $config['default_photo_module'];

/**
 * If a filename has been pulled, process it
 */
if ($id)
{
	if (!isset($hard_full_path))
	{
		/**
		 * Ensure the extension is a displayable file type
		 *
		 * @todo extract hardcoded default to the top of the document
		 */
		if (!preg_match('/jpg|jpeg|gif|png|svg|JPG|JPEG|GIF|PNG|SVG/i', $id))
		{

			$image = $db->get_row("SELECT filename, full_path, available_sizes, no_watermark_under, watermark FROM $default_photo_module where id='$id'");
			$id = $image->filename;

		} else {
			$file = $id;
			switch ($mode)
			{
				case "svg_to_png":
					$file = str_replace(".png", ".svg", $file);
					break;
			}

			$image = $db->get_row("SELECT id, full_path, available_sizes, no_watermark_under, watermark FROM $default_photo_module where filename='$file'");
		}
		
	} else {
		
		$image =  new stdClass();
		if (!isset($hard_full_path) or !$hard_full_path)
			$hard_full_path = '';

		$image->full_path = $hard_full_path."/";
		$image->filename = $id;
		$image->no_watermark_under = '';
		$image->watermark = '';
		$image->available_sizes = '';
		$hard_image = true;
	}
	
	/**
	 * If a row has been pulled from the db describing an image, handle it
	 */
	if ($image)
	{
		/**
		 * Begin constructing the URL
		 */
		$get_root = $AIKI_ROOT_DIR."/";
		
		$original_filename = $get_root.$image->full_path.$id;
		
		/**
		 * Sets default size, if no size specified
		 */
		if ($config['max_res'] and !$size)
			$size = $config['max_res'];

		if ($size == '_')
			$size = '';

		/**
		 * If file is svg, rename to png
		 */
		if ($ext == "svg")
		{
			switch ($mode)
			{
				case "svg_to_png":
					$original_filename = 
						str_replace(".png", ".svg", $original_filename );
					break;
			}

			if ($size)
			{
				$size = str_replace('px', '', $size);

				switch ($mode)
				{
					case "svg_to_png":

						if(file_exists($get_root.$image->full_path."$size"."px-".$id)){
							$final_image = imagecreatefrompng($get_root.$image->full_path."$size"."px-".$id);

							imagealphablending($final_image, false);
							imagesavealpha($final_image, true);

							imagepng($final_image);
							imagedestroy($final_image);

						}else{

							$svgfile = implode(file($original_filename));

							$header = $aiki->get_string_between($svgfile, "<svg", ">");

							$or_width = $aiki->get_string_between($header, 'width="', '"');
							$width = str_replace("px", "", $or_width );
							$width = str_replace("pt", "", $width );
							$width  = intval($width);

							$or_height = $aiki->get_string_between($header, 'height="', '"');
							$height  = str_replace("px", "", $or_height);
							$height  = str_replace("pt", "", $height);
							$height = intval($height);

							$newvalue = $size;
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

							$id_svg = str_replace(".png", ".svg", $id);
							$or_svg_file = $get_root.$image->full_path.$id_svg;

							if (file_exists($or_svg_file)){

								$aiki->image->rsvg_convert_svg_png($or_svg_file, $newwidth, $newhight);


								$final_image = imagecreatefrompng($get_root.$image->full_path.$size."px-".$id);

								//$aiki->image->imageresize($get_root.$image->full_path,$id,$size,$size."px-");

								imagealphablending($final_image, false);
								imagesavealpha($final_image, true);

								imagepng($final_image);
								imagedestroy($final_image);
							}
						}
						break;

					default:
						echo $svgfile;
						break;
				}

			}else{
				echo $svgfile;
			}

		} else {
			$resize_filename = $get_root.$image->full_path.$size."px-".$id;

			if ($image->watermark and 
				!$size and 
				file_exists($original_filename))
			{
				$original_filename = 
					$aiki->image->display_watermarked_image($original_filename,
						$image->watermark, $image->no_watermark_under);

			} elseif ($image->watermark and $size and 
					  file_exists($resize_filename))
			{
				$original_filename = 
					$aiki->image->display_watermarked_image($resize_filename, 
						$image->watermark, $image->no_watermark_under);
			}

			if ($size)
				$req_filename = $resize_filename;
			else
				$req_filename = $original_filename;

			if (file_exists($req_filename))
			{
				switch ($ext)
				{
					case "png":

						$final_image = imagecreatefrompng($req_filename);

						imagealphablending($final_image, false);
						imagesavealpha($final_image, true);
							
						imagepng($final_image);
						imagedestroy($final_image);

						break;

					case "jpg":
						$final_image = imagecreatefromjpeg($req_filename);
						imagejpeg($final_image);
						imagedestroy($final_image);
						break;
				}



			} elseif(file_exists($original_filename))
			{
				$aiki->image->imageresize($get_root.$image->full_path,$id,$size,$size."px-");

				if (file_exists($req_filename))
				{
					$image->available_sizes = $image->available_sizes."$size".'px|';
					if (!isset($hard_image))
					{
						$update_sizes = $db->query("UPDATE $default_photo_module set available_sizes = '$image->available_sizes' where id = '$image->id'");
					}

					switch ($ext)
					{
						case "png":
							$final_image = imagecreatefrompng($req_filename);
							imagealphablending($final_image, false);
							imagesavealpha($final_image, true);
							imagepng($final_image);
							imagedestroy($final_image);
							break;

						case "jpg":
							$final_image = imagecreatefromjpeg($req_filename);
							imagejpeg($final_image);
							imagedestroy($final_image);
							break;
					}

				}
			}
		}
	}
}
