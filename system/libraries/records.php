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


//include("plugins/fckeditor2.6.4/fckeditor.php") ;


class records_libs
{

	var $stop;
	var $edit_done;
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

	function dbUpdateDelete($module_link, $op, $postedpkey, $extras, $orderby, $normalarray, $serializedarray){
		global $layout, $db, $aiki, $global_index, $operators_key, $operators, $page, $membership, $image_processing, $dir;


		$pagefromaiki = $page;

		if (isset($_REQUEST['orderby']))
		$orderby = $db->escape($_REQUEST['orderby']);

		$module_form = $this->get_module_form($module_link);

		if (!$normalarray and !$serializedarray and $module_link){
			$module_array = $this->get_module_array($module_link);
			$viewArray = unserialize($module_array);
		}elseif(!$normalarray and $serializedarray){
			$viewArray = unserialize($serializedarray);
		}elseif (!$serializedarray and $normalarray){
			$viewArray = $normalarray;
		}

		$arraykeys = array_keys($viewArray);

		if (in_array("tablename", $arraykeys)) {
			$tablename = $viewArray["tablename"];
		}else{
			die("Fatel Error: No Table Name specified in array <br />");
		}
		if (in_array("pkey", $arraykeys)) {
			$pkey = $viewArray["pkey"];
		}else{
			die("Fatel Error: No primary key specified in array <br />");
		}
		//echo $op;

		switch ($op){

			case "del":
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

				break;

			case "edit":

				if (!isset($_REQUEST['edit'])){

					if (!$module_form){

						//$layout->forms .= $this->wikieditor('editor');

						$layout->forms .= ("<form method=\"post\" enctype=\"multipart/form-data\" name='editor'><table width=\"100%\" dir=\"$dir\" border=\"0\">");

						$viewQuery = "select * from $tablename where $pkey='$postedpkey' limit 1";
						$viewResult = mysql_query($viewQuery);
						$Viewrow = mysql_fetch_array($viewResult);

						$dolock = $this->lockdocument($pkey, $postedpkey, $tablename);
						$layout->forms .= $dolock;


						$i = 0;
						foreach($viewArray as $field)
						{
							if ($field != $tablename and $field != $pkey){

								$intwalker = explode(":", $field);

								$get_permission_and_man_info = explode("|", $intwalker[0]);
								$intwalker[0] = $get_permission_and_man_info[0];

								$toreplace = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
								$switcher = str_replace($toreplace, '', $arraykeys[$i]);


								if ($get_permission_and_man_info[2] == "true"){
									$intwalker[1] = "<font color='#FF0000'>".$intwalker[1]."</font>";
								}

								if ($get_permission_and_man_info[1]){
									$get_group_level = $db->get_var ("SELECT group_level from aiki_users_groups where group_permissions='$get_permission_and_man_info[1]'");
								}



								if (!$get_permission_and_man_info[1] or $get_permission_and_man_info[1] == $membership->permissions or $membership->group_level < $get_group_level){
									switch ($switcher){

										case "autofiled":
											$thefild = stripslashes($Viewrow["$intwalker[0]"]);

											switch ($intwalker[2]){
												case "publishdate":
													$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\" value=\""); $layout->forms .= $thefild; $layout->forms .= ("\"></td></tr>");
													break;

												case "uploaddate":
													//$currentdatetime = time();
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"$thefild\">");
													break;

												case "orderby":
													if ($intwalker[3]){
														$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"\">");
													}
													break;

												case "insertedby":
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"$membership->full_name\">");
													break;

												case "hitscounter":
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"0\">");
													break;

												case "EditingHistory":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);
													$currentdatetime = time();
													//TODO: Custom Time output formats inserted by user
													$datetime = date('m/d/y g:ia', $currentdatetime);

													$thefild = $thefild."- Edit By $membership->full_name on $datetime <br />";
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"\">");
													break;


											}
											break;

												case "staticselect":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);

													$layout->forms .= "<tr><td>$intwalker[1]</td><td>";
													if ($intwalker[2] == "custome" and $intwalker[3]){
														$layout->forms .= '<select name="'.$intwalker[0].'" dir="'.$get_permission_and_man_info[3].'">';
														$explodeStaticSelect = explode("&", $intwalker[3]);
														foreach ($explodeStaticSelect as $option){
															$optionsieds = explode(">", $option);

															$layout->forms .= '<option value="'.$optionsieds['1'].'"';
															if ($thefild == $optionsieds['1']){
																$layout->forms .=' selected';
															}
															$layout->forms .= '>'.$optionsieds['0'].'</option>';
														}
														$layout->forms .= '</select>';
													}
													//staticselect($intwalker[0], $intwalker[2]);
													$layout->forms .= "</td></tr>";
													break;

												case "selection":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);
													$layout->forms .= ("<tr><td>$intwalker[1]</td>
													<td><select name=\"$intwalker[0]\" dir=\"$get_permission_and_man_info[3]\">
													<option value=\"0\">Please Select</option>");
													//$aquery = "select $intwalker[3], $intwalker[4] from $intwalker[2] order by $intwalker[3]";
													$aquery = "select $intwalker[3], $intwalker[4] from $intwalker[2] order by BINARY $intwalker[4]";
													$aresult = mysql_query($aquery);
													$num_results = mysql_num_rows($aresult);
													for ($k=0; $k <$num_results; $k++)
													{
														$row = mysql_fetch_array($aresult);
														$name = stripslashes($row["$intwalker[4]"]);
														$id = stripslashes($row["$intwalker[3]"]);
														$layout->forms .= "<option value='$id' "; if ($thefild  == $id){$layout->forms .= "selected";} $layout->forms .= ">$name</option>";
													}
													$layout->forms .= ("</select></td></tr>");
													break;

												case "textinput":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);

													//fix the double quote bug by converting " into DOM quotes
													$thefild = str_replace('"', '&#34;', $thefild);

													$layout->forms .= '<tr><td>'.$intwalker[1].'</td><td><input type="text" name="'.$intwalker[0].'" dir="'.$get_permission_and_man_info[3].'" value="'.$thefild.'"></td></tr>';
													break;


												case "imagenoupload":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);

													//fix the double quote bug by converting " into DOM quotes
													$thefild = str_replace('"', '&#34;', $thefild);

													$layout->forms .= '<tr><td>'.$intwalker[1].'</td><td><input type="text" name="'.$intwalker[0].'" dir="'.$get_permission_and_man_info[3].'" value="'.$thefild.'"><br /><img src="'.$aiki->setting[url].'image/201px/'.$thefild.'" /></td></tr>';
													break;


												case "bigtextblock":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);

													$thefild = str_replace('<','&lt;',$thefild);
													$thefild = str_replace('>','&gt;',$thefild);

													$layout->forms .= ("<tr><td>$intwalker[1]</td><td><textarea id=\"bigfont\" dir=\"$get_permission_and_man_info[3]\" style=\"height: 500px; width: 600px; display: block;\" name=\"$intwalker[0]\">$thefild</textarea></td></tr>");
													break;

												case "ckeditor":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);

													$layout->forms .= ("<tr><td>$intwalker[1]</td><td><textarea id=\"$intwalker[0]\" name=\"$intwalker[0]\" dir=\"$get_permission_and_man_info[3]\">$thefild</textarea>");

													$layout->forms .= "<script type=\"text/javascript\">
													CKEDITOR.replace( '$intwalker[0]' );
													</script>
													</td></tr>
												";

													break;

												case "bigwikiblock":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);

													$thefild = str_replace('<','&lt;',$thefild);
													$thefild = str_replace('>','&gt;',$thefild);

													$layout->forms .= ("<tr><td>$intwalker[1]</td><td>
													<div id=\"toolbar\">
													<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('\'\'\'','\'\'\'','$intwalker[0]');\"><b>Bold</b></span>
													<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('==','==','$intwalker[0]');\">سطر</span>
													<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('{+{','|0|left|v:10|h:10|300px|0}+}','$intwalker[0]');\">صورة</span>												
													</div>
													<textarea id=\"bigfont\" dir=\"$get_permission_and_man_info[3]\"  dir=\"$get_permission_and_man_info[3]\"  style=\"height: 500px; width: 600px; display: block;\" name=\"$intwalker[0]\">"); $layout->forms .= $thefild; $layout->forms .=("</textarea></td></tr>");
													break;

												case "normaltextblock":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);

													$layout->forms .= ("<tr><td>$intwalker[1]</td><td><textarea rows=\"7\" dir=\"$get_permission_and_man_info[3]\" cols=\"50\" name=\"$intwalker[0]\">$thefild</textarea></td></tr>");
													break;

												case "textblock":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);
													$layout->forms .= ("<tr><td>$intwalker[1]</td><td>");
													$oFCKeditor = new FCKeditor($intwalker[0]) ;
													$oFCKeditor->BasePath = 'assets/plugins/fckeditor2.6.4/' ;
													$oFCKeditor->Height		=  400;
													$oFCKeditor->Value		=  $thefild;
													$oFCKeditor->Create() ;
													("</td></tr>");
													//$layout->forms .= ("<tr><td>$intwalker[1]</td><td><textarea name=\"$intwalker[0]\">"); $layout->forms .= $thefild; $layout->forms .= ("</textarea></td></tr>");
													break;

												case "static_input":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);
													$layout->forms .= '<tr><td>'.$intwalker[1].'</td><td><input type="text" dir="'.$get_permission_and_man_info[3].'" name="'.$intwalker[0].'" value="'.$thefild.'"></td></tr>';
													break;

												case "upload":
													$layout->forms .= ("<tr><td>$intwalker[1]</td><td>");
													$site_path = $aiki->setting['url'];
													$img = "<img src=\"";
													$img .= substr($intwalker[6], 1);
													$img .= $Viewrow["$intwalker[0]"]."\"/><br />";
													$img .= $Viewrow["$intwalker[0]"];
													$img = str_replace("//", "/", $img);

													$layout->forms .=("$img</td></tr>");

													break;

												case "imagefolderupload":
													$layout->forms .= ("<tr><td>$intwalker[1]</td><td>");
													$imagepath = "$layout->forms .= \"$intwalker[6]\";";
													$layout->forms .= "<img src=\"$aiki->setting[url]/";
													eval($imagepath);
													$layout->forms .= $Viewrow["$intwalker[0]"]."\"/><br />";
													$layout->forms .= $Viewrow["$intwalker[0]"];
													$layout->forms .=("</td></tr>");
													break;

												case "datetime":
													$layout->forms .= ("<tr><td>$intwalker[1]</td><td>$intwalker[0]</td></tr>");
													break;

												case "hiddenfiled":
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\">");
													break;

												case "filemanager":
													$thefild = stripslashes($Viewrow["$intwalker[0]"]);
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"$thefild\">");

													break;
									}
								}
							}
							$i++;
						}


						$layout->forms .= ("</table>

						<input type=\"hidden\" value=\"$_SERVER[HTTP_REFERER]\" name=\"httpreferer\">
						<input type=\"hidden\" value=\"$Viewrow[$pkey]\" name=\"editpkey\">
						<input type=\"hidden\" value=\"edit\" name=\"edit\">
						<input type=\"submit\" name=\"edit\" value=\"edit\">
						</form>");

							

					}else{


						$layout->forms = $aiki->sql_markup->sql($module_form);
						$layout->forms = $this->fill_form($layout->forms, "select * from $tablename where $pkey='$postedpkey' limit 1");

					}

				}else{

					$editQuery = "update $tablename set ";
					$i = 0;
					$viewCount = count($viewArray);
					foreach($viewArray as $field)
					{
						if ($field != $tablename and $field != $pkey and $field != $viewArray['upload']){
							$intwalker = explode(":", $field);

							$get_permission_and_man_info = explode("|", $intwalker[0]);
							$intwalker[0] = $get_permission_and_man_info[0];



							switch ($intwalker[2]){


								case "orderby":
									//$_POST[$intwalker[0]] = $_POST['editpkey'];
									$_POST[$intwalker[0]] = ($_POST['publish_date'] * 1000)+$_POST['editpkey'];
									break;


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

					$editQuery .= " where ".$pkey."=".$postedpkey;
					$editQuery = str_replace("set ,", "set", $editQuery);

					$editResult = mysql_query($editQuery);
					if ($editResult){
						$layout->forms .= "<b>تم تعديل السجل: $_POST[editpkey]</b><br />";
						$this->unlockdocument($pkey, $postedpkey, $tablename);

						$this->edit_done = true;
						if ($_POST['httpreferer']){
							Header("Location: $_POST[httpreferer]");
						}
					}

				}
				break;
		}



		return $layout->forms;
	}

	function fill_form($html, $sql){
		global $db, $aiki;

		$viewrow = $db->get_row($sql);

		$viewrow = $aiki->array->object2array($viewrow);

		if (!is_array($viewrow)){return;}

		$arraykeys = array_keys($viewrow);


		$get_input_fields = preg_match_all("|<input[^>]+>|Us",$html, $input_matchs );

		foreach($input_matchs[0] as $input){

			$name = $aiki->get_string_between($input, 'name="', '"');

			if (in_array($name, $arraykeys)){

				$mod_input = preg_replace('/value\=\"(.*)\"/', "", $input);
				$mod_input = str_replace('>','value="'.$viewrow["$name"].'">', $mod_input);

				$html = str_replace($input, $mod_input, $html);
			}

		}




		$get_text_areas = preg_match_all("|<textarea[^>]+>(.*)</textarea+>|Us",$html, $input_matchs );

		foreach($input_matchs[0] as $input){

			$name = $aiki->get_string_between($input, 'name="', '"');

			if (in_array($name, $arraykeys)){

				$html = preg_replace('|<textarea[^>](.*)name\=\"'.$name.'\"(.*)+>(.*)</textarea+>|Us', "<textarea \\1 name=\"$name\">".$viewrow["$name"]."</textarea>", $html);
			}

		}

		return $html;
	}

	function edit_all(){
		if (!$this->edit_done){
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

	}


	function createForm ($insertArray, $arraykeys, $tablename, $pkey){
		global $layout, $db, $membership, $dir;

		//$layout->forms .= $this->wikieditor('insertform');

		$layout->forms .= "<form method=\"post\" enctype=\"multipart/form-data\" name=\"insertform\">
		<table dir=\"$dir\" border=\"0\" width=\"100%\">";
		$i = 0;
		foreach($insertArray as $field)
		{
			if ($field != $tablename and $field != $pkey){

				$intwalker = explode(":", $field);
				$toreplace = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
				$switcher = str_replace($toreplace, '', $arraykeys[$i]);

				$get_permission_and_man_info = explode("|", $intwalker[0]);
				$intwalker[0] = $get_permission_and_man_info[0];

				if (isset($get_permission_and_man_info[2]) and $get_permission_and_man_info[2] == "true"){
					$intwalker[1] = "<font color='#FF0000'>".$intwalker[1]."</font>";
				}

				if (isset($get_permission_and_man_info[1])){
					$get_group_level = $db->get_var ("SELECT group_level from aiki_users_groups where group_permissions='$get_permission_and_man_info[1]'");
				}



				if (!isset($get_permission_and_man_info[1]) or $get_permission_and_man_info[1] == $membership->permissions or $membership->group_level < $get_group_level){

					if (!isset($_POST[$intwalker[0]])){
						$_POST[$intwalker[0]] = "";
					}


					switch ($switcher){

						case "ExplodeConvertEach":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" name=\"$intwalker[0]\" value=\""); $layout->forms .= $_POST[$intwalker[0]]; $layout->forms .= ("\"></td></tr>");
							break;

						case "staticselect":
							$thefild = stripslashes($Viewrow["$intwalker[0]"]);
							$layout->forms .= "<tr><td>$intwalker[1]</td><td>";
							if ($intwalker[2] == "custome" and $intwalker[3]){
								$layout->forms .= '<select name="'.$intwalker[0].'" dir="'.$get_permission_and_man_info[3].'">';
								$explodeStaticSelect = explode("&", $intwalker[3]);
								foreach ($explodeStaticSelect as $option){
									$optionsieds = explode(">", $option);
									$layout->forms .= '<option value='.$optionsieds['1'].'>'.$optionsieds['0'].'</option>';
								}
								$layout->forms .= '</select>';
							}
							$layout->forms .= "</td></tr>";
							break;

						case "selection":
							$layout->forms .= "<tr><td>$intwalker[1]</td>
							<td><select name=\"$intwalker[0]\" dir=\"$get_permission_and_man_info[3]\">
							<option value=\"0\">Please Select</option>";

							$aquery = $db->get_results("select $intwalker[3], $intwalker[4] from $intwalker[2] order by BINARY $intwalker[4]");
							if ($aquery){
								foreach ( $aquery as $mini_selection )
								{
									$name = $mini_selection->$intwalker[4];
									$id = $mini_selection->$intwalker[3];

									$layout->forms .= "<option value=\"$id\" "; if ($_POST[$intwalker[0]] == $id){$layout->forms .= "selected";} $layout->forms .= ">$name</option>";
								}
							}
							$layout->forms .= ("</select></td></tr>");
							break;

						case "textinput":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" name=\"$intwalker[0]\" dir=\"$get_permission_and_man_info[3]\" value=\""); $layout->forms .= $_POST[$intwalker[0]]; $layout->forms .= ("\"></td></tr>");
							break;

						case "imagenoupload":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" name=\"$intwalker[0]\" dir=\"$get_permission_and_man_info[3]\" value=\""); $layout->forms .= $_POST[$intwalker[0]]; $layout->forms .= ("\"></td></tr>");
							break;

						case "image":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" name=\"$intwalker[0]\" dir=\"$get_permission_and_man_info[3]\" value=\""); $layout->forms .= $_POST[$intwalker[0]]; $layout->forms .= ("\"></td></tr>");
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"file\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\"></td></tr>");
							break;

						case "unique_textinput":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" name=\"$intwalker[0]\" dir=\"$get_permission_and_man_info[3]\" value=\""); $layout->forms .= $_POST[$intwalker[0]]; $layout->forms .= ("\"></td></tr>");
							break;

						case "password":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"password\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\" \"></td></tr>");
							break;

						case "verify_password":
							//$layout->forms .= ("<tr><td>لا تظهر تأكيد كلمة المرور</td></tr>");
							break;

						case "textblock":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td>");
							$oFCKeditor = new FCKeditor($intwalker[0]) ;
							$oFCKeditor->BasePath = 'assets/plugins/fckeditor2.6.4/' ;
							$oFCKeditor->Value		=  $_POST[$intwalker[0]];
							$oFCKeditor->Height		=  400;
							$oFCKeditor->Create() ;
							$layout->forms .= ("</td></tr>");
							break;


						case "bigtextblock":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td><textarea id=\"bigfont\" dir=\"$get_permission_and_man_info[3]\" style=\"height: 500px; width: 600px; display: block;\" name=\"$intwalker[0]\">"); $layout->forms .= $_POST[$intwalker['0']]; $layout->forms .=("</textarea></td></tr>");
							break;

						case "ckeditor":

							$layout->forms .= "<tr><td>$intwalker[1]</td><td><textarea dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\">".$_POST[$intwalker['0']]."</textarea>";

							$layout->forms .= "<script type=\"text/javascript\">
							CKEDITOR.replace( '$intwalker[0]' );
							</script>
							</td></tr>";
							break;

						case "bigwikiblock":

							$layout->forms .= ("<tr><td>$intwalker[1]</td><td>
							<div id=\"toolbar\">
							<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('\'\'\'','\'\'\'','$intwalker[0]');\"><b>Bold</b></span>
							<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('==','==','$intwalker[0]');\">سطر</span>
							<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('{+{','|0|left|v:10|h:10|300px|0}+}','$intwalker[0]');\">صورة</span>				
							</div>
							<textarea id=\"bigfont\" dir=\"$get_permission_and_man_info[3]\" style=\"height: 500px; width: 600px; display: block;\" name=\"$intwalker[0]\">"); $layout->forms .= $_POST[$intwalker['0']]; $layout->forms .=("</textarea></td></tr>");
							break;

						case "normaltextblock":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td><textarea rows=\"7\" dir=\"$get_permission_and_man_info[3]\" cols=\"50\" name=\"$intwalker[0]\">"); $layout->forms .= $_POST[$intwalker['0']]; $layout->forms .=("</textarea></td></tr>");
							break;

						case "upload":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"file\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\"></td></tr>");
							break;

						case "imagefolderupload":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\"></td></tr>");
							break;

						case "datetime":
							$layout->forms .= ("<tr><td>$intwalker[1]</td><td>$intwalker[0]</td></tr>");
							break;

						case "hidden":
							$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\">");
							break;


						case "textinput_if_valid":
							switch ($intwalker[2]){

								case "email":
									$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\" value=\""); $layout->forms .= $_POST[$intwalker[0]]; $layout->forms .= ("\"></td></tr>");
									break;

								case "url":
									$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\" value=\""); $layout->forms .= $_POST[$intwalker[0]]; $layout->forms .= ("\"></td></tr>");
									break;

							}
							break;


								case "filemanager":
									switch ($intwalker[2]){

										case "unique_filename":
											$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input dir=\"$get_permission_and_man_info[3]\" type=\"file\" name=\"$intwalker[0]\"></td></tr>");
											$layout->forms .= ("<input type=\"hidden\" name=\"unique_filename\" value=\"unique_filename\">");
											break;


									}
									break;



										case "autofiled":
											switch ($intwalker[2]){
												case "publishdate":
													$layout->forms .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\" value=\""); $layout->forms .= $_POST[$intwalker[0]]; $layout->forms .= ("\"></td></tr>");
													break;

												case "uploaddate":
													$currentdatetime = time();
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"$currentdatetime\">");
													break;

												case "orderby":
													if ($intwalker[3]){
														$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"\">");
													}
													break;

												case "insertedby":
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"$membership->full_name\">");
													break;

												case "insertedby_username":
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"$membership->username\">");
													break;

												case "hitscounter":
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"0\">");
													break;

												case "EditingHistory":
													$currentdatetime = time();
													//TODO: Custom Time output formats inserted by user
													$datetime = date('m/d/y g:ia', $currentdatetime);
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"- Inserted By $membership->full_name on $datetime <br />\">");
													break;

												case "is_editable":
													$layout->forms .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"\">");
													break;

											}

											break;
					}
				}
			}
			$i++;
		}
		$layout->forms .= ("<tr><td colspan=\"2\">");
		$layout->forms .= ("<input type=\"submit\" value=\"Ok\" name=\"add\">");
		$layout->forms .= ("</td></tr>");
		$layout->forms .= ("</table></form>");

	}


	function dbCreateInsert($module_link, $module_array){
		global $layout, $db, $aiki, $membership, $image_processing, $aiki_text_engine;

		if (!$module_array and $module_link){
			$module_array = $this->get_module_array($module_link);
		}

		$insertArray = unserialize($module_array);
		//print_r($insertArray);
		$module_form = $this->get_module_form($module_link);

		$arraykeys = array_keys($insertArray);

		if (in_array("tablename", $arraykeys)) {
			$tablename = $insertArray["tablename"];
		}else{
			$layout->forms .= "Fatal Error: No Table Name specified in array <br />";
		}

		if (in_array("send_email", $arraykeys)) {
			$send_email = $insertArray["send_email"];
		}

		if (in_array("pkey", $arraykeys)) {
			$pkey = $insertArray["pkey"];
		}else{
			$layout->forms .= "Fatal Error: No primary key specified in array <br />";
		}
		if (in_array("upload", $arraykeys)) {
			$upload = $insertArray["upload"];
		}
		if (in_array("imagefolderupload", $arraykeys)) {
			$imagefolderupload = $insertArray["imagefolderupload"];
		}
		if (in_array("ExplodeConvertEach", $arraykeys)) {
			$ExplodeConvertEach = $insertArray["ExplodeConvertEach"];
		}

		if (!isset($_POST['add'])){


			if ($module_form){

				$layout->forms = $aiki->sql($module_form);
				$layout->forms = $aiki->processVars($layout->forms);

			}else{
				$this->createForm ($insertArray,$arraykeys, $tablename, $pkey);
			}

		}else{



			if (isset($_REQUEST['unique_filename'])){
				$unique_filename = $_REQUEST['unique_filename'];
			}


			$insertQuery = "insert into $tablename ";
			$i = 0;
			$insertCount = count($insertArray);
			foreach($insertArray as $field)
			{


				$intwalker = explode(":", $field);


				$get_permission_and_man_info = explode("|", $intwalker[0]);

				if (isset($get_permission_and_man_info[1])){
					$get_group_level = $db->get_var ("SELECT group_level from aiki_users_groups where group_permissions='$get_permission_and_man_info[1]'");
				}

				$intwalker[0] = $get_permission_and_man_info[0];


				//Security Check to remove unauthorized POST data
				if (isset($get_permission_and_man_info[1]) and $get_permission_and_man_info[1] == $membership->permissions or $membership->group_level < $get_group_level){

				}elseif (isset($get_permission_and_man_info[1])){
					$_POST[$intwalker[0]] = '';
				}

				if (isset($get_permission_and_man_info[2]) and $get_permission_and_man_info[2] == "true" and !$_POST[$intwalker[0]]){
					$layout->forms .= "<b>تنبيه:</b> الرجاء ادخال $intwalker[1]<br />";
					$this->stop = true;
				}



				if ($insertCount == $i+1){
					$insert_values .= "'".$intwalker[0]."'";
				}else{
					$insert_values .= "'".$intwalker[0]."', ";
				}

				//echo $intwalker[2]."<br>";

				switch ($intwalker[2]){
					case "full_path":
						$full_path = $_POST[$intwalker[0]]; //get full path dir value
						if (!$full_path){
							$full_path = "upload/bank/"; //TODO Custome default upload folder
							$_POST[$intwalker[0]] = $full_path;
						}
						break;

					case "password":
						if (!$_POST[$intwalker[0]]){
							$layout->forms .= "<b>تنبيه: الرجاء ادخال كلمة المرور</b><br />";
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
							$layout->forms .= "<b>تنبيه: عنوان البريد الإلكتروني الذي أدخلته غير صحيح</b><br />";
							$this->stop = true;
						}

						break;

					case "unique":

						if ($this->record_exists($_POST[$intwalker[0]], $tablename, $intwalker[0])){

							$layout->forms .= "<b>عذراً هذا الاسم مستخدم الرجاء اختيار اسم آخر.</b><br />";
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
									$layout->forms .= "new directory created: $path";
									@$result = move_uploaded_file($tmp_filename,$newfile);
								}else{
									$layout->forms .= ("folder not found<br />");
								}
							}


							//TODO: keep original file name for insert into original_filename field
							if ($type == "svg"){
								//TODO: make option to choose converting engine
								//$layout->forms .= "<p dir='ltr' align='left'>";
								//$layout->forms .= $image_processing->rsvg_convert_svg_png($newfile);
								//$layout->forms .= "</p>";

								$image_processing->rsvg_convert_svg_png($newfile);

								$name = str_replace(".svg", ".png", $name);

							}

						} else {
							$layout->forms .=( "Sorry, but that file '$newfile' already exists.");
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
									$layout->forms .= (_error1);
								} else {
								}

							} else {
								$layout->forms .=( "Sorry, but that file '$newfile' already exists.");
							}
						} else {
							$layout->forms .=("Uploaded 0 files<br />");
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
							$layout->forms .= "$file <br>";
							mysql_query ("insert into aiki_photographers_photos values ('', '39', '', '', '800', '800', '', '$file')");
							if (!copy($_POST[$intwalker[0]]."/".$file, $aiki->setting[top_folder]."/".$uploadexploded[5]."/$file")) {
								$layout->forms .= "failed to copy $file\n";
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

				if ($field != $tablename and $field != $send_email){
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

			if (!$imagefolderupload and !$this->stop){

				$insertQuery .= "($tableFields) values ($preinsertQuery)";
				//echo $insertQuery;
				$insertResult = mysql_query($insertQuery);
				//$layout->forms .= "<b>SQL Query:</b> ".$insertQuery;
				if ($insertResult){
					//$layout->forms .= "<b>SQL Query:</b> ".$insertQuery;

					//$layout->forms .= ("<br /><a href=\"?\">New Record</a>");
					$layout->forms .= "Added successfully<br />";

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


					if ($filename){
						//$filename[size]; حجم ملف الصور
						$layout->forms .= "ملف الصورة:<br />";
						$layout->forms .= "<p dir='ltr'>".$name."</p>";
					}
				}else{
					//echo "error";
				}
			}else{
				$this->createForm ($insertArray,$arraykeys, $tablename, $pkey);

			}
		}
		//$layout->forms .= $module_array;
		return $layout->forms;
	}

	function get_module_array($module_link){
		global $db;
		$module_array = $db->get_var("SELECT module_array from aiki_modules where module_link='$module_link'");

		return $module_array;

	}

	function get_module_form($module_link){
		global $db, $aiki;
		$module_form = $db->get_var("SELECT module_form  from aiki_modules where module_link='$module_link'");
		$module_form = $aiki->processVars($module_form);
		return $module_form;

	}



}


?>