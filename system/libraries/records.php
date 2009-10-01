<?php

/***************************************************************************
 *   Copyright (C) 2008-2009 by Bassel Khartabil                           *
 *   http://www.aikicms.org                                                *
 *   bassel@aikicms.org                                                    *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 *   This program is distributed in the hope that it will be useful,       *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 *   GNU General Public License for more details.                          *
 *                                                                         *
 *   GNU General Public License Version 3 or later (the "GPL")             *
 *    http://www.gnu.org/licenses/                                         *
 ***************************************************************************/



if(!defined('IN_AIKI')){die('No direct script access allowed');}



class aiki_records
{

	var $stop;
	var $file_name;
	var $file_size;
	var $checksum_sha1;
	var $checksum_md5;
	var $width;
	var $height;
	var $rand;

	function record_exists($value, $tablename, $field){
		global $db;

		$get_value = $db->get_var("SELECT $field from $tablename where $field='$value'");

		if ($get_value and $get_value == $value){
			return true;
		}else{
			return false;
		}

	}

	function lockdocument($pkeyname, $pkeybalue, $tablename){
		global $db, $membership;
		if ($pkeyname and $tablename){

			$not_editable = $db->get_var("select is_editable from $tablename where $pkeyname='$pkeybalue' limit 1");

			if (!$not_editable){
				$currentdatetime = time();

				//TODO Date output control:
				$datetime = date('m/d/y g:ia', $currentdatetime);

				$is_editable = "<font color=\"#FF0000\"><b>تحذير: </b></font>يقوم المستخدم <b>$membership->full_name</b> بالعمل على هذا السجل في هذه الأثناء وقد باشر العمل في: $datetime";


				$lockdocument = $db->query("update $tablename set is_editable = '$is_editable' where $pkeyname='$pkeybalue'");
			}else{

				return "<br />".$not_editable."<br /><br />";

			}
		}
	}

	function unlockdocument($pkeyname, $pkeybalue, $tablename){
		global $db, $membership;
		if ($pkeyname and $tablename){
			$lockdocument = $db->query("update $tablename set is_editable = null where $pkeyname='$pkeybalue'");
		}
	}

	function wikieditor($formname){

		$wikieditor = "
		<script language=\"Javascript\">
		<!--
		function FormatSelection( aTag, eTag , fieldname ) {
		var aTag =  aTag;
		var eTag =  eTag;
		var input = document.forms['$formname'].elements[fieldname];
		input.focus();

		//FOR IE
		if	(typeof document.selection != 'undefined') {
		var range = document.selection.createRange();
		var insText = range.text;
		range.text = aTag + insText + eTag;
		range = document.selection.createRange();
		if	(insText.length == 0) {
		range.move('character', -eTag.length);
	}	else {
	range.moveStart('character', aTag.length + insText.length + eTag.length);
	}
	range.select();

	}   else  {

	// FOR Gecko Browser

	if	(typeof input.selectionStart != 'undefined') {

	var start = input.selectionStart;
	var end = input.selectionEnd;
	var insText = input.value.substring(start, end);
	input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);

	var pos;
	if	(insText.length == 0) {
	pos = start + aTag.length;
	} else {
	pos = start + aTag.length + insText.length + eTag.length;
	}
	input.selectionStart = pos;
	input.selectionEnd = pos;

	}
	}

	};
	//-->
	</script>


	<script type=\"text/javascript\">

	function mouseover(el) {
	el.className = \"raised\";
	}

	function mouseout(el) {
	el.className = \"button\";
	}

	function mousedown(el) {
	el.className = \"pressed\";
	}

	function mouseup(el) {
	el.className = \"raised\";
	}

	</script>


	<style type=\"text/css\">

	#toolbar 	{
-moz-background-clip:border;
-moz-background-inline-policy:continuous;
-moz-background-origin:padding;
background:buttonface none repeat scroll 0 0;
border-color:buttonhighlight buttonshadow buttonshadow buttonhighlight;
border-style:solid;
border-width:1px;
cursor:pointer;
height:19px;
margin:0;
padding:6px;
text-align:left;
width:591px;
	}

	.button 	{
	background: buttonface;
	border: 1px solid buttonface;
	padding:4px;

	}

	.raised		{
	border-top: 1px solid buttonhighlight;
	border-left: 1px solid buttonhighlight;
	border-bottom: 1px solid buttonshadow;
	border-right: 1px solid buttonshadow;
	background: buttonface;
	padding:4px;

	}

	.pressed	{
	border-top: 1px solid buttonshadow;
	border-left: 1px solid buttonshadow;
	border-bottom: 1px solid buttonhighlight;
	border-right: 1px solid buttonhighlight;
	background: buttonface;
	padding:4px;

	}

	</style>
	";		

		return $wikieditor;
	}



	function edit_all(){

		$pagekey = explode(":", $global_index);
		if (isset($pagekey[1])){
			$page = $pagekey[1];
		}
		if (!isset($_GET['pkey']) or isset($_POST['editpkey']) or $_GET['do'] == "delete"){

			$layout->forms .= ("
							<form method=\"POST\" name='abc'>
							<table width=\"200\">
							<tr>
							<td>Search: </td>
							<td><input type=\"text\" name=\"keyword\" size=\"30\"></td>
							<td><select name=\"wheresearch\">");
			foreach($viewArray as $field)
			{
				if ($field != $tablename){
					$intwalker = explode(":", $field);

					if (!$intwalker[1]){$intwalker[1] = $intwalker[0];}
					$layout->forms .= "<option value=\"$intwalker[0]\">".$intwalker[1]."</option>";
				}
			}
			$layout->forms .= ("</select></td>
							<td><input type=\"submit\" value=\"Go\" name=\"search\"></td>
							</tr>
							</table>
							</form>
							");

			if ($_POST['wheresearch'] and $_POST['keyword']){
				$term = $_POST['wheresearch'].":".$_POST['keyword'];
			}
			if (!$term and $_GET['term'] and $_GET['term'] != ":"){
				$term = $_GET['term'];
			}
			$layout->forms .= ("<table dir=\"$dir\" border=\"1\">");
			if ($term){
				$term = explode(":", $term);
				$zxquery1 = "select $pkey from $tablename where $term[0] RLIKE '$term[1]'";
			}else{
				$zxquery1 = "select $pkey from $tablename";
			}
			$zxresult1 = mysql_query($zxquery1);
			$num_resultsf = mysql_num_rows($zxresult1);
			if ($num_resultsf != 30){
				$numpages = $num_resultsf / 30;
				$numpages = (int)($numpages+1);
			}else{
				$numpages = 1;
			}
			$fnumre = $page * 30;
			if ($orderby){
				$orderbykey = $orderby;
				$orderby = "order by ".$orderby;

			}else{
				$orderbykey = $pkey;
				$orderby = "order by ".$pkey;
			}

			if ($term){

				if ($term[0] == $pkey){
					$viewQuery = "select * from $tablename where $term[0] = '$term[1]' $orderby LIMIT $fnumre,30";
				}else{
					$viewQuery = "select * from $tablename where $term[0] RLIKE '$term[1]' $orderby LIMIT $fnumre,30";
				}
			}else{
				$viewQuery = "select * from $tablename $orderby LIMIT $fnumre,30";
			}
			$viewResult = mysql_query($viewQuery);
			$num_results = mysql_num_rows($viewResult);
			$i = 0;
			$layout->forms .= ("<td colspan=\"2\">Tools</td>");

			foreach($viewArray as $field)
			{
				if ($field != $tablename){
					$intwalker = explode(":", $field);

					if (!$intwalker[1]){$intwalker[1] = $intwalker[0];}
					$get_permission_and_man_info = explode("|", $intwalker[0]);
					$intwalker[0] = $get_permission_and_man_info[0];

					$intwalker[0] = $intwalker[0]." DESC&desc=true&port=$intwalker[1]";

					if ($_GET[desc]){
						if ($_GET[port] == $intwalker[1]){
							$intwalker[1] = $intwalker[1]." ⇈";
						}
						$intwalker[0] = str_replace("DESC&desc=true", "ASC&asc=true", $intwalker[0]);
					}elseif ($_GET[asc]){
						if ($_GET[port] == $intwalker[1]){
							$intwalker[1] = $intwalker[1]." ⇊";
						}

					}
					$layout->forms .= "<td><b><a href=\"index.php?language=arabic&module=admin&operators=$operators_key|$operators|$pagefromaiki&orderby=$intwalker[0]\">".$intwalker[1]."</a></b></td>";
				}
				$i++;
			}


			for ($k=0; $k <$num_results; $k++)
			{

				$layout->forms .= ("<tr>");
				$viewRow = mysql_fetch_array($viewResult);
				$i = 0;

				foreach($viewArray as $field)
				{
					if ($field == $pkey){
						$intwalker = explode(":", $field);

						$intwalker[0] = stripslashes($viewRow[$intwalker[0]]);
						$layout->forms .= ("<td><a href=\"index.php?module=admin&operators=$_GET[operators]&op=edit&do=edit$module_link&pkey=$intwalker[0]&extras=$extras\"><img border=\"0\" src=\"images/skins/admin/b_edit.png\" /></a></td>
							<td><a href=\"index.php?module=admin&operators=$_GET[operators]&op=del&do=del&pkey=$intwalker[0]:$tablename&extras=$extras\"><img border=\"0\" src=\"images/skins/admin/b_drop.png\" /></a></td>");
					}


					if ($field != $tablename){
						$intwalker = explode(":", $field);

						$get_permission_and_man_info = explode("|", $intwalker[0]);
						$intwalker[0] = $get_permission_and_man_info[0];

						$intwalker[0] = stripslashes($viewRow[$intwalker[0]]);
						$intwalker[0] = htmlspecialchars($intwalker[0]);



						if (!$intwalker[0]){
							$intwalker[0] = " ";
							$intsub  = " ";
						}else{

							$intsub = substr($intwalker[0], 0, 200);
							if ($intsub != $intwalker[0]){
								$intsub = $intsub." ......";
							}

						}
						//$intsub = mb_convert_encoding($intsub, "utf8", "auto");


						//TODO not working:
						//$intsub = iconv('UTF-8', 'UTF-8', $intsub);

						//this is a special case for paintings in www.discover-syria.com:
						//any way you may find it usefull
						//you can use the var $savedint in path for upload
						//you need to change the $viewArray['selection1'] thing to mach your needs
						if ($field == $viewArray['selection1']){
							$savedint = $intwalker[0];
						}
						//////////////////////
						//Selection display
						$toreplace = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
						$findselection = str_replace($toreplace, '', $arraykeys[$i]);
						if ($findselection == "selection"){
							$aquery = "select $intwalker[3], $intwalker[4] from $intwalker[2] where $intwalker[3] = $intsub";
							$aresult = mysql_query($aquery);
							if ($aresult){
								$row = mysql_fetch_array($aresult);
								$name = stripslashes($row["$intwalker[4]"]);
								$id = stripslashes($row["$intwalker[3]"]);
								$intsub = "$id > $name";
							}
						}
						//////////////////////
						//Check for displaying Images:
						if ($intwalker[6]){

							$imagepath = "$layout->forms .= \"$intwalker[6]\";";
							$path = $aiki->setting['url'];

							$layout->forms .= "<td><img src='". $path. substr($intwalker[6].$intsub, 1) ."'><br />$intsub</td>";
							//eval($imagepath);

						}else{

							//TODO: change this please (it's for unique images only) and custome min size

							if ($intwalker[2] == "unique_filename"){
								//$intsub = $aiki->setting[url]."/".$full_path.$intwalker[0];
								//$intsub = str_replace("//", "/", $intsub);
								$url = $aiki->setting['url'];
								$intsub = "<img src=\""."$url"."image/150px/$intsub\"><br>$intsub";
							}

							$layout->forms .= "<td>".$intsub."</td>";
						}
						/////////////////
					}

					$i++;

				}

				$layout->forms .= "</tr>";
			}
			$layout->forms .= "</table>";
		}


		if (isset($numpages) and $numpages > 1){
			$page2 = $page + 1;
			$layout->forms .= "<br /><p><b>Move To Page:</b></p>";
			for ($i=0; $i <$numpages; $i++)
			{
				$y = $i + 1;
				if ($i == $page){
					$layout->forms .= "&nbsp;$y&nbsp; ";
				}else{

					$layout->forms .= "&nbsp;<a href=\"index.php?language=arabic&module=admin&operators=$operators_key|$operators|$pagefromaiki|page:$i&orderby=$orderbykey\">$y</a>&nbsp; ";
				}
			}
		}


	}



	function insert_from_form_to_db($post, $form_id){
		global $db, $aiki, $membership;

		$insert_values = "";
		$tableFields = "";
		$preinsertQuery = "";

		$form = $db->get_row("SELECT * from aiki_forms where id='$form_id' limit 1");

		$form_array = unserialize($form->form_array);

		$arraykeys = array_keys($form_array);

		if (in_array("tablename", $arraykeys))
		$tablename = $form_array["tablename"];


		if (in_array("send_email", $arraykeys))
		$send_email = $form_array["send_email"];

		if (in_array("pkey", $arraykeys)) {
			$pkey = $form_array["pkey"];
		}else{
			$pkey = 'id';
		}

		if (in_array("upload", $arraykeys))
		$upload = $form_array["upload"];

		if (in_array("imagefolderupload", $arraykeys))
		$imagefolderupload = $form_array["imagefolderupload"];

		if (isset($post['unique_filename']))
		$unique_filename = $_REQUEST['unique_filename'];



		$insertQuery = "insert into $tablename ";
		$i = 0;
		$insertCount = count($form_array);
		foreach($form_array as $field)
		{


			$intwalker = explode(":", $field);


			$get_permission_and_man_info = explode("|", $intwalker[0]);

			if (isset($get_permission_and_man_info[1])){
				$get_group_level = $db->get_var ("SELECT group_level from aiki_users_groups where group_permissions='$get_permission_and_man_info[1]'");
			}

			$intwalker[0] = $get_permission_and_man_info[0];


			//Security Check to remove unauthorized POST data
			if (isset($get_group_level)){
				if (isset($get_permission_and_man_info['1']) and $get_permission_and_man_info[1] == $membership->permissions or $membership->group_level < $get_group_level){

				}elseif (isset($get_permission_and_man_info[1])){
					$_POST[$intwalker[0]] = '';
				}
			}

			if (isset($get_permission_and_man_info[2]) and $get_permission_and_man_info[2] == "true" and !$_POST[$intwalker[0]]){
				$form .= "<b>تنبيه:</b> الرجاء ادخال $intwalker[1]<br />";
				$this->stop = true;
			}



			if ($insertCount == $i+1){
				$insert_values .= "'".$intwalker[0]."'";
			}else{
				$insert_values .= "'".$intwalker[0]."', ";
			}

			//echo $intwalker[2]."<br>";

			if (isset($intwalker['2'])){
				switch ($intwalker['2']){
					case "full_path":
						$full_path = $_POST[$intwalker[0]]; //get full path dir value
						if (!$full_path){
							$full_path = "upload/bank/"; //TODO Custome default upload folder
							$_POST[$intwalker[0]] = $full_path;
						}
						break;

					case "password":
						if (!$_POST[$intwalker[0]]){
							$form .= "<b>تنبيه: الرجاء ادخال كلمة المرور</b><br />";
							$this->stop = true;
						}

						if ($intwalker[3] and $_POST[$intwalker[0]]){
							$num_levels = explode("|", $intwalker[3]);
							foreach ($num_levels as $crypt_level){

								$_POST[$intwalker[0]] = md5(stripcslashes($_POST[$intwalker[0]]));
							}
						}
						break;

					case "value":
						$_POST[$intwalker[0]] = $intwalker[3];

						break;

					case "rand":

						$_POST[$intwalker[0]] = substr(md5(uniqid(rand(),true)),1,15);
						$this->rand = $_POST[$intwalker[0]];
						break;

					case "email":

						if (!$aiki_text_engine->is_valid("email",$_POST[$intwalker[0]])){
							$form .= "<b>تنبيه: عنوان البريد الإلكتروني الذي أدخلته غير صحيح</b><br />";
							$this->stop = true;
						}

						break;

					case "url":
							
						break;
							

					case "unique":

						if ($this->record_exists($_POST[$intwalker[0]], $tablename, $intwalker[0])){

							$form .= "<b>عذراً هذا الاسم مستخدم الرجاء اختيار اسم آخر.</b><br />";
							$this->stop = true;
						}
						break;

					case "orderby":

						break;

					case "mime_type":

						break;

					case "upload_file_name":

						if (isset($this->file_name)){
							$_POST[$intwalker[0]] = $this->file_name;
						}

						break;

					case "upload_file_size":
						if (isset($this->file_size)){
							$_POST[$intwalker[0]] = $this->file_size;
						}
						break;

					case "width":
						if (isset($this->width)){
							$_POST[$intwalker[0]] = $this->width;
						}
						break;

					case "height":
						if (isset($this->hight)){
							$_POST[$intwalker[0]] = $this->hight;
						}
						break;

					case "checksum_sha1":
						if (isset($this->checksum_sha1)){
							$_POST[$intwalker[0]] = $this->checksum_sha1;
						}
						break;

					case "checksum_md5":
						if (isset($this->checksum_md5)){
							$_POST[$intwalker[0]] = $this->checksum_md5;
						}
						break;

				}
			}






			if (isset($unique_filename) and $unique_filename == $intwalker[2] and $full_path){ //unique_filename processing

				$uploadexploded = explode(":", $intwalker[0]);
				$filename = $_FILES[$uploadexploded[0]];
				$name = $filename['name'];
				$this->file_name = $name;


				$path = $aiki->setting[top_folder]."/".$full_path."";

				$tmp_filename = $filename['tmp_name'];

				$this->file_size = filesize($tmp_filename);

				$this->checksum_sha1 = sha1_file($tmp_filename);
				$this->checksum_md5 = md5_file($tmp_filename);
				$size = getimagesize($tmp_filename);
				$this->width = $size["0"];
				$this->hight = $size["1"];


				if ($tmp_filename) {
					$filename_array = explode(".",$name);
					//TODO: DOn't allow all extinsions
					$type= $filename_array[1];


					//TODO: check also if file exists for renaming
					if (!$this->record_exists($name, $tablename, $intwalker[0])){ //check if filename already exists
						$newfile = $path.$name;

					}else{

						$current_time = time();
						$name = $current_time.".".$type;

						$newfile = $path.$name;
					}



					if (!file_exists($newfile)) {
						@$result = move_uploaded_file($tmp_filename,$newfile);
						if (!$result) {

							if (@mkdir($path,0777)){
								$form .= "new directory created: $path";
								@$result = move_uploaded_file($tmp_filename,$newfile);
							}else{
								$form .= ("folder not found<br />");
							}
						}


						//TODO: keep original file name for insert into original_filename field
						if ($type == "svg"){
							//TODO: make option to choose converting engine
							//$form .= "<p dir='ltr' align='left'>";
							//$form .= $image_processing->rsvg_convert_svg_png($newfile);
							//$form .= "</p>";

							$image_processing->rsvg_convert_svg_png($newfile);

							$name = str_replace(".svg", ".png", $name);

						}

					} else {
						$form .=( "Sorry, but that file '$newfile' already exists.");
					}
				}


				//$insertQuery .= "'".$_POST[$intwalker[0]]."', ";
				//check if file checksum match another file
				$_POST[$intwalker[0]] = $name;
			}


			if (isset($insertArray['upload']) and $field == $insertArray['upload']){

				////////////////////
				if (isset($upload)){
					$uploadexploded = explode(":", $upload);
					$path = $aiki->setting[top_folder]."/".$uploadexploded[5]."/";
					$filename = $_FILES[$uploadexploded[0]];
					$name = $filename['name'];
					$tmp_filename = $filename['tmp_name'];
					if ($tmp_filename) {
						$filename_array = explode(".",$name);
						$type= $filename_array[1];
						$newfile = $path.$name;
						if (!file_exists($newfile)) {
							@$result = move_uploaded_file($tmp_filename,$newfile);
							if (!$result) {
								$form .= (_error1);
							} else {
							}

						} else {
							$form .=( "Sorry, but that file '$newfile' already exists.");
						}
					} else {
						$form .=("Uploaded 0 files<br />");
					}
					if ($filename){
						$imageresize = explode("|", $intwalker[4]);
						foreach($imageresize as $resizeop){
							$oldprefix = $imageprefix;
							if (is_numeric($resizeop)){
								$sizenum = $resizeop;
							}else{
								$imageprefix = $resizeop;
							}
							if ($imageprefix and $sizenum and $oldprefix != $imageprefix){
								$this->imageresize($path, $filename['name'], $sizenum, $imageprefix);
							}
						}

					}
				}

				/////////////////////////
				if ($insertCount == $i+1){
					$preinsertQuery .= "'".$filename['name']."'";
				}else{
					$preinsertQuery .= "'".$filename['name']."', ";
				}
				//print_r($filename);
			}

			if (isset($imagefolderupload) and $field == $insertArray['imagefolderupload']){
				$uploadexploded = explode(":", $imagefolderupload);
				$path = $aiki->setting[url]."/".$uploadexploded[5]."/";
				$handle=opendir($_POST[$intwalker[0]]);
				while (($file = readdir($handle))!==false) {
					if ($file != "." and $file != ".."){
						$form .= "$file <br>";
						mysql_query ("insert into aiki_photographers_photos values ('', '39', '', '', '800', '800', '', '$file')");
						if (!copy($_POST[$intwalker[0]]."/".$file, $aiki->setting[top_folder]."/".$uploadexploded[5]."/$file")) {
							$form .= "failed to copy $file\n";
						}else{
							$filename = $file;
						}


					}

					if ($filename){
						$imageresize = explode("|", $intwalker[4]);

						foreach($imageresize as $resizeop){
							$oldprefix = $imageprefix;
							if (is_numeric($resizeop)){
								$sizenum = $resizeop;
							}else{
								$imageprefix = $resizeop;
							}
							if ($imageprefix and $sizenum and $oldprefix != $imageprefix){
								$this->imageresize($path, $filename, $sizenum, $imageprefix);
							}
						}

					}

				}
				closedir($handle);
			}

			if (!isset($send_email)){
				$send_email = '';
			}

			if ($field != $tablename and $field != $send_email and isset($_POST[$intwalker[0]]) and $_POST[$intwalker[0]]){
				//$_POST[$intwalker[0]] = mysql_real_escape_string($_POST[$intwalker[0]]);

				if ($insertCount == $i+1){
					$tableFields .=$intwalker[0];
					$preinsertQuery .= "'".$_POST[$intwalker[0]]."'";
				}else{
					$tableFields .= $intwalker[0].", ";
					$preinsertQuery .= "'".$_POST[$intwalker[0]]."', ";
				}

			}
			$i++;

		}

		if (!isset($imagefolderupload) and !$this->stop){

			$insertQuery .= "($tableFields) values ($preinsertQuery)";

			$insertResult = $db->query($insertQuery);

			if ($insertResult){

				$output_result = "Added successfully<br />";

				if ($send_email){

					$send_email = explode("|", $send_email);

					$get_email = $aiki->get_string_between($send_email[0], '[', ']');
					if ($get_email){
						$send_email[0] = $_POST[$get_email];
					}

					$get_from = $aiki->get_string_between($send_email[1], '[', ']');
					if ($get_from){
						$send_email[1] = $_POST[$get_from];
					}

					$to = $send_email[0];
					$subject = $send_email[2];
					$message = $send_email[3];
					$count = preg_match_all( '/\[(.*)\]/U', $message, $matches );
					foreach ($matches[1] as $parsed){
						$message = str_replace("[$parsed]", $_POST[$parsed], $message);
					}

					$from = $send_email[1];
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=utf-8\r\n";
					$headers .= "From: $from\r\n";

					$message = nl2br($message);

					mail($to,$subject,$message,$headers);


				}


				if (isset($filename)){
					$output_result .= "Filename:<br />";
					$output_result .= "<p dir='ltr'>".$name."</p>";
				}
			}else{
				$output_result = "Error inserting into database";
			}
		}
		return $output_result;
	}


	function delete_record(){

		if (!$postedpkey){
			die("Fatel Error: No primary key, nothing to do");
		}
		$pkeyexploded = explode(":", $postedpkey);
		if (!$pkeyexploded[2] or $pkeyexploded[2] != "yes"){
			$layout->forms .= ("Delete record #");
			$layout->forms .= ("<b>$pkeyexploded[0]</b>");
			$layout->forms .= (" From: ");
			$layout->forms .= ("<b>$pkeyexploded[1]</b> ?");
			$layout->forms .= ("<br />");
			$layout->forms .= ("<a href=\"index.php?language=$_GET[language]&module=admin&operators=$_GET[operators]&op=del&do=del&pkey=$pkeyexploded[0]:$pkeyexploded[1]:yes&extras=$extras\">Yes</a> | <a href=\"\">No</a>");
		}else{
			$deletequery = "delete from $pkeyexploded[1] where $pkey=".$pkeyexploded[0];
			$deleteresult = mysql_query($deletequery);
			if ($deleteresult){
				$layout->forms .= ("Record <b>#$pkeyexploded[0]</b> Deleted from <b>$pkeyexploded[1]</b>");
			}
		}

	}

	//$layout->forms = $aiki->sql_markup->sql($module_form);
	//$layout->forms = $this->fill_form($layout->forms, "select * from $tablename where $pkey='$postedpkey' limit 1");
	//$dolock = $this->lockdocument($pkey, $postedpkey, $tablename);
	//$layout->forms .= $dolock;

	function edit_db_record_by_form_post($post, $form_id, $record_id){
		global $db, $aiki, $membership;

		$form = $db->get_row("SELECT * from aiki_forms where id='$form_id' limit 1");

		$form_array = unserialize($form->form_array);

		$arraykeys = array_keys($form_array);

		if (in_array("tablename", $arraykeys))
		$tablename = $form_array["tablename"];


		if (in_array("send_email", $arraykeys))
		$send_email = $form_array["send_email"];

		if (in_array("pkey", $arraykeys)) {
			$pkey = $form_array["pkey"];
		}else{
			$pkey = 'id';
		}

		if (in_array("upload", $arraykeys)){
			$upload = $form_array["upload"];
		}else{
			$upload = '';
		}

		if (in_array("imagefolderupload", $arraykeys))
		$imagefolderupload = $form_array["imagefolderupload"];

		if (isset($post['unique_filename']))
		$unique_filename = $_REQUEST['unique_filename'];



		if (isset($_REQUEST['orderby']))
		$orderby = $_REQUEST['orderby'];



		$editQuery = "update $tablename set ";
		$i = 0;
		$viewCount = count($form_array);
		foreach($form_array as $field)
		{
			if ($field != $tablename and $field != $pkey and $field != $upload){
				$intwalker = explode(":", $field);

				$get_permission_and_man_info = explode("|", $intwalker[0]);
				$intwalker[0] = $get_permission_and_man_info[0];


				if (isset($intwalker['2'])){
					switch ($intwalker['2']){


						case "orderby":
							//$_POST[$intwalker[0]] = $_POST['editpkey'];
							$_POST[$intwalker[0]] = ($_POST['publish_date'] * 1000)+$_POST['editpkey'];
							break;


					}
				}


				if ($get_permission_and_man_info[1]){
					$get_group_level = $db->get_var ("SELECT group_level from aiki_users_groups where group_permissions='$get_permission_and_man_info[1]'");
				}

				if (!$get_permission_and_man_info[1] or $get_permission_and_man_info[1] == $membership->permissions or $membership->group_level < $get_group_level){

					$_POST[$intwalker[0]] = str_replace('&lt;', '<' , $_POST[$intwalker[0]]);
					$_POST[$intwalker[0]] = str_replace('&gt;', '>' , $_POST[$intwalker[0]]);

					$editQuery .= ", ".$intwalker[0]."='".$_POST[$intwalker[0]]."'";
				}

			}
			$i++;
		}

		$editQuery .= " where ".$pkey."=".$record_id;
		$editQuery = str_replace("set ,", "set", $editQuery);

		$editResult = $db->query($editQuery);
		if ($editResult){
			$output_result = "Edited record $record_id in $tablename successfully";
			//$this->unlockdocument($pkey, $postedpkey, $tablename);

		}

		return $output_result;
	}

}


?>