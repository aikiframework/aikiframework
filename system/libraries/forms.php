<?php
if(!defined('IN_AIKI')){die('No direct script access allowed');}

class aiki_forms
{


	function displayForms($text){
		global $db, $records_libs;

		$forms_count = preg_match_all("/\(\#\(form\:(.*)\)\#\)/U", $text, $forms);

		if ($forms_count >0){

			foreach ($forms[1] as $form_data){

				if ($form_data){

					$form_sides = explode(":", $form_data);

					$form = $db->get_row("SELECT * from aiki_forms where id='$form_sides[1]' limit 1");

					$form_array = unserialize($form->form_array);

					switch ($form_sides[0]){

						case "add":

							$form_output = $this->create_insert_form($form_array, $form->form_html, $form->id);

							break;

						case "edit":

							$form_output = $this->create_update_form($form_array, $form->form_html, $form->id, $form_sides[2]);

							break;

						case "process":

							break;

					}




					/*if ($form_sides[3]){
					 $form_static_values = explode("|", $form_sides[1]);
					 $form_inner_data = "";
					 foreach($form_static_values as $static_vaule){
					 $static_value_sides = explode("=", $static_vaule);

					 $form = preg_replace("/name\=\"$static_value_sides[0]\"/U", "name='$static_value_sides[0]' value='$static_value_sides[1]'", $form);

					 $form_inner_data .= $static_vaule;
					 }
						}*/

				}
				if (isset($form_inner_data)){
					$text = preg_replace("/\(\#\(form\:$form_data:$form_inner_data\)\#\)/U", $form, $text);

				}else{
					$text = preg_replace("/\(\#\(form\:$form_data\)\#\)/U", $form_output, $text);
				}
			}

		}

		return $text;
	}



	function createForm ($form_array, $form_id, $record_id){
		global $db, $membership, $aiki, $config;

		//$_POST[$intwalker[0]]

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

		if (isset ($record_id)){
			$form_data = $db->get_row("select * from $tablename where $pkey='$record_id' limit 1");
		}


		$form = "<form method=\"post\" enctype=\"multipart/form-data\" id=\"new_record_form\" name=\"new_record_form\">
		<table border=\"0\" width=\"100%\">";

		$i = 0;

		foreach($form_array as $field)
		{

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

					case "staticselect":

						$form .= "<tr><td>$intwalker[1]</td><td>";
						if ($intwalker[2] == "custome" and $intwalker[3]){
							$form .= '<select name="'.$intwalker[0].'" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '">';
							$explodeStaticSelect = explode("&", $intwalker[3]);
							foreach ($explodeStaticSelect as $option){
								$optionsieds = explode(">", $option);
								$form .= '<option value="'.$optionsieds['1'].'"';
								if (isset($form_data) and $form_data->$intwalker[0] ==  $optionsieds['1']){
									$form .=' selected';
								}
								$form .= '>'.$optionsieds['0'].'</option>';
							}
							$form .= '</select>';
						}
						$form .= "</td></tr>";
						break;

					case "selection":
						$form .= '<tr><td>'.$intwalker['1'].'</td>
							<td><select name="'.$intwalker['0'].'" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '">
							<option value="0">Please Select</option>';

						$aquery = $db->get_results("select $intwalker[3], $intwalker[4] from $intwalker[2] order by BINARY $intwalker[4]");
						if ($aquery){
							foreach ( $aquery as $mini_selection )
							{
								$name = $mini_selection->$intwalker[4];
								$id = $mini_selection->$intwalker[3];

								$form .= "<option value=\"$id\" ";
								if (isset($form_data) and $form_data->$intwalker[0] ==  $id){
									$form .=' selected';
								}
								$form .= ">$name</option>";
							}
						}
						$form .= ("</select></td></tr>");
						break;

					case "textinput":
						$form .= '<tr><td>'.$intwalker['1'].'</td><td><input type="text" name="'.$intwalker['0'].'" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '" value="'; if (isset($form_data)){$form .= $form_data->$intwalker[0] ;} $form .= '"></td></tr>';
						break;

					case "imagenoupload":
						$form .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" name=\"$intwalker[0]\" dir=\"$get_permission_and_man_info[3]\" value=\""); $form .= $_POST[$intwalker[0]]; $form .= ("\"></td></tr>");
						break;

					case "image":
						$form .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" name=\"$intwalker[0]\" dir=\"$get_permission_and_man_info[3]\" value=\""); $form .= $_POST[$intwalker[0]]; $form .= ("\"></td></tr>");
						$form .= ("<tr><td>$intwalker[1]</td><td><input type=\"file\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\"></td></tr>");
						break;

					case "unique_textinput":
						$form .= '<tr><td>'.$intwalker['1'].'</td><td><input type="text" name="'.$intwalker['0'].'" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '" value="'; if (isset($form_data)){$form .= $form_data->$intwalker[0] ;} $form .= '"></td></tr>';
						break;

					case "password":
						$form .= ("<tr><td>$intwalker[1]</td><td><input type=\"password\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\" \"></td></tr>");
						break;

					case "verify_password":
						//$form .= ("<tr><td>لا تظهر تأكيد كلمة المرور</td></tr>");
						break;


					case "bigtextblock":
						$form .= '<tr><td>'.$intwalker[1].'</td><td><textarea id="bigfont" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '" style="height: 500px; width: 600px; display: block;" name="'.$intwalker['0'].'">'; if (isset($form_data)){$form .= $form_data->$intwalker[0] ;} $form .= '</textarea></td></tr>';
						break;


					case "bigwikiblock":

						$form .= ("<tr><td>$intwalker[1]</td><td>
							<div id=\"toolbar\">
							<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('\'\'\'','\'\'\'','$intwalker[0]');\"><b>Bold</b></span>
							<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('==','==','$intwalker[0]');\">سطر</span>
							<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('{+{','|0|left|v:10|h:10|300px|0}+}','$intwalker[0]');\">Image</span>				
							</div>
							<textarea id=\"bigfont\" dir=\"$get_permission_and_man_info[3]\" style=\"height: 500px; width: 600px; display: block;\" name=\"$intwalker[0]\">"); $form .= $_POST[$intwalker['0']]; $form .=("</textarea></td></tr>");
						break;

					case "textblock":
						$form .= '<tr><td>'.$intwalker['1'].'</td><td><textarea rows="7" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '" cols="50" name="'.$intwalker['0'].'">'; if (isset($form_data)){$form .= $form_data->$intwalker[0] ;} $form .= '</textarea></td></tr>';
						break;

					case "upload":
						if (isset($form_data)){
								
							$form .= ("<tr><td>$intwalker[1]</td><td>");
							$site_path = $config['url'];
							$img = "<img src=\"";
							$img .= substr($intwalker[6], 1);
							$img .= $form_data->$intwalker[0]."\"/><br />";
							$img .= $form_data->$intwalker[0];
							$img = str_replace("//", "/", $img);

							$form .=("$img</td></tr>");
						}else{
							$form .= ("<tr><td>$intwalker[1]</td><td><input type=\"file\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\"></td></tr>");
						}
						break;

					case "imagefolderupload":
						$form .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\"></td></tr>");
						break;

					case "datetime":
						$form .= ("<tr><td>$intwalker[1]</td><td>$intwalker[0]</td></tr>");
						break;

					case "hidden":
						$form .= ("<input type=\"hidden\" name=\"$intwalker[0]\">");
						break;

					case "static_input":
						$form .= '<tr><td>'.$intwalker[1].'</td><td><input type="text" dir="'.$get_permission_and_man_info[3].'" name="'.$intwalker[0].'" value="'; if (isset($form_data)){$form .= $form_data->$intwalker[0] ;} $form .= '"></td></tr>';
						break;



					case "filemanager":
						switch ($intwalker[2]){

							case "unique_filename":
								$form .= ("<tr><td>$intwalker[1]</td><td><input dir=\"$get_permission_and_man_info[3]\" type=\"file\" name=\"$intwalker[0]\"></td></tr>");
								$form .= ("<input type=\"hidden\" name=\"unique_filename\" value=\"unique_filename\">");
								break;

						}
						break;



							case "autofiled":
								switch ($intwalker[2]){
									case "publishdate":
										$form .= ("<tr><td>$intwalker[1]</td><td><input type=\"text\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\" value=\""); $form .= $_POST[$intwalker[0]]; $form .= ("\"></td></tr>");
										break;

									case "uploaddate":
										if (isset ($form_data)){
											$currentdatetime = $form_data->$intwalker['0'];
										}else{
											$currentdatetime = time();
										}
										$form .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"$currentdatetime\">");
										break;

									case "orderby":
										if ($intwalker[3]){
											$form .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"\">");
										}
										break;

									case "insertedby":
										if (isset ($form_data)){
											$insertedby_value = $form_data->$intwalker['0'];
										}else{
											$insertedby_value = $membership->full_name;
										}
										$form .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"$insertedby_value\">");
										break;

									case "insertedby_username":
										if (isset ($form_data)){
											$insertedby_username = $form_data->$intwalker['0'];
										}else{
											$insertedby_username = $membership->username;
										}

										$form .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"$insertedby_username\">");
										break;

									case "hitscounter":
										$form .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"0\">");
										break;

									case "EditingHistory":
										$currentdatetime = time();
										//TODO: Custom Time output formats inserted by user
										$datetime = date('m/d/y g:ia', $currentdatetime);

										if (isset ($form_data)){
											$EditingHistory = $form_data->$intwalker['0'];
											$EditingHistory .= "- Edit By $membership->full_name on $datetime <br />";
										}else{
											$EditingHistory = "- Inserted By $membership->full_name on $datetime <br />";
										}

										$form .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"$EditingHistory\">");
										break;

									case "is_editable":
										$form .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"\">");
										break;

								}

								break;
				}
			}

			$i++;
		}
		$form .= ("<tr><td colspan=\"2\">");
		$form .= ("<input type=\"hidden\" value=\"$form_id\" name=\"form_id\">");
		if (isset($form_data)){
			$record_id = $form_data->$pkey;
			$form .= ("<input type=\"hidden\" value=\"$record_id\" name=\"record_id\">");
			$form .= ("<input type=\"submit\" value=\"Ok\" name=\"edit_form\">");
		}else{
			$form .= ("<input type=\"submit\" value=\"Ok\" name=\"add_to_form\">");
		}
		$form .= ("</td></tr>");
		$form .= ("</table></form>");

		return $form;

	}

	function create_insert_form($form_array, $form_html, $form_id){
		global $db, $aiki, $membership;

		$form = '';


		if (isset($form_html) and $form_html){

			$form = $aiki->sql_markup->sql($form_html);
			$form = $aiki->processVars($form_html);


		}else{

			$form = $this->createForm ($form_array, $form_id, '');

		}

		return $form;

	}

	function create_update_form($form_array, $form_html, $form_id, $record_id){
		global $aiki;
		
		$form = '';
			
		if (isset($form_html) and $form_html){

			$form = $aiki->sql_markup->sql($form_html);
			$form = $aiki->processVars($form_html);


		}else{

			$form = $this->createForm ($form_array, $form_id, $record_id);

		}

		return $form;


	}

}
?>