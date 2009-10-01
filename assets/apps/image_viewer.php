<?php

$id = $_GET['id'];
$size = $_GET['size'];

$ext = substr($id, strrpos($id, '.') + 1);

$ext = strtolower($ext);

if ($ext == "jpeg"){
	$ext = "jpg";
}

switch ($ext){
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

define('IN_AIKICMS', true);

require_once("system/index.php");
$aiki = new aiki();
require_once("system/database/index.php");



$default_photo_module = $aiki->setting['default_photo_module'];


//$id = mysql_real_escape_string($_GET['id']);

if ($id){

	if (!preg_match('/jpg|jpeg|gif|png|svg|JPG|JPEG|GIF|PNG|SVG/i', $id)){
		$image = $db->get_row("SELECT filename, full_path, available_sizes, no_watermark_under, watermark FROM $default_photo_module where  id='$id'");
		$id = $image->filename;
	}else{
		$file = $id;
		$file = str_replace(".svg", ".png", $file);

		$image = $db->get_row("SELECT id, full_path, available_sizes, no_watermark_under, watermark FROM $default_photo_module where filename='$file'");
	}



	if ($image){


		$get_root = str_replace("//", "/", $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF']);
		$get_root = str_replace("image_viewer.php", "", $get_root);

		$original_filename = $get_root.$image->full_path.$id;

		if ($aiki->setting['max_res'] and !$size){
			$size = $aiki->setting['max_res'];
		}

		if ($size == '_'){
			$size = '';
		}

		if ($ext == "svg"){
			$svgfile = implode(file($original_filename));
			if ($size){
				$size = str_replace('px', '', $size);

				$header = get_string_between($svgfile, "<svg", ">");
				$width = get_string_between($header, 'width="', '"');
				$hight = get_string_between($header, 'height="', '"');

				$newvalue = $size;
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

				$headerfixed = str_replace('width="'.$width.'"', 'width="'.$newwidth.'"', $header);
				$headerfixed = str_replace('height="'.$hight.'"', 'height="'.$newhight.'"', $headerfixed);

				$svgfile = str_replace($header, $headerfixed, $svgfile);

				echo $svgfile;

				$FileHandle = fopen($get_root.$image->full_path."$size"."px-".$id, 'w') or die("can't open file");
				fwrite($FileHandle, $svgfile);
				fclose($FileHandle);

				$image_processing->rsvg_convert_svg_png($get_root.$image->full_path."$size"."px-".$id);


			}else{
				echo $svgfile;
			}

		}else{
			$resize_filename = $get_root.$image->full_path.$size."px-".$id;



			if ($image->watermark and !$size and file_exists($original_filename)){

				$original_filename = display_watermarked_image($original_filename, $image->watermark, $image->no_watermark_under);

			}elseif ($image->watermark and $size and file_exists($resize_filename)){

				$original_filename = display_watermarked_image($resize_filename, $image->watermark, $image->no_watermark_under);

			}

			if ($size){
				$req_filename = $resize_filename;
			}else{
				$req_filename = $original_filename;
			}



			if (file_exists($req_filename)){

				switch ($ext){

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



			}elseif(file_exists($original_filename)){

				imageresize($get_root.$image->full_path,$id,$size,$size."px-");

				if (file_exists($req_filename)){

					$image->available_sizes = $image->available_sizes."$size".'px|';
					$update_sizes = $db->query("UPDATE $default_photo_module set available_sizes = '$image->available_sizes' where id = '$image->id'");


					switch ($ext){
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


//TODO Please don't dublicate functions
//find a way around

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

	if ($width < $newwidth or $hight < $newhight){
		$newhight = $hight;
		$newwidth = $width;
	}



	switch ($type){
		case "image/jpeg":
			$thumb = imagecreatetruecolor($newwidth, $newhight);

			$source = imagecreatefromjpeg($filename2);
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newhight, $width, $hight);
			imagejpeg($thumb,$path.$imageprefix.$filename);

			imagedestroy($thumb);
			imagedestroy($source);
			break;
		case "image/gif":
			$thumb = imagecreatetruecolor($newwidth, $newhight);

			$source = imagecreatefromgif($filename2);
			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newhight, $width, $hight);
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

			imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newhight, $width, $hight);
			imagepng($thumb,$path.$imageprefix.$filename);

			imagedestroy($thumb);
			imagedestroy($source);
			break;
	}


}

function display_watermarked_image($fimage, $watermark_file, $minValueWaterMark){
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


function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

?>