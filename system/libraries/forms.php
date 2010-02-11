<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}

class aiki_forms
{

	var $submit_button;

	function displayForms($text ){
		global $db, $aiki;

		$forms_count = preg_match_all("/\(\#\(form\:(.*)\)\#\)/U", $text, $forms);

		if ($forms_count >0){

			foreach ($forms[1] as $form_data){

				if ($form_data){

					$form_output = '';

					$form_sides = explode(":", $form_data);

					$form = $db->get_row("SELECT * from aiki_forms where id='$form_sides[1]' limit 1");

					if ($form){
						$form_array = unserialize($form->form_array);
					}

					switch ($form_sides[0]){

						case "add":

							$form_output = $this->create_insert_form($form_array, $form->form_html, $form->id);

							break;

						case "edit":

							$form_output = $this->create_update_form($form_array, $form->form_html, $form->id, $form_sides[2]);

							break;

						case "process":

							break;

						case "auto_generate":

							if ($form_sides['1']){
								$this->auto_generate($form_sides['1']);
							}

							break;


						case "delete":
							$form_output = $aiki->records->delete_record($form_array['tablename'], $form_sides[2],  $form_sides[3], $form_array['pkey']);
							break;

					}


					if (isset ($form_sides[3])){
					 $form_static_values = explode("|", $form_sides[3]);
					 foreach($form_static_values as $static_vaule){
					 	$static_value_sides = explode("=", $static_vaule);

					 	$form_output = preg_replace("/name\=\"$static_value_sides[0]\"/U", "name='$static_value_sides[0]' value='$static_value_sides[1]'", $form_output);

					 }
					}

				}

				$text = str_replace("(#(form:$form_data)#)", $form_output, $text);
			}

		}


		return $text;
	}



	function createForm ($form_array, $form_id, $record_id){
		global $db, $membership, $aiki, $config;

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


		$form = "<form method=\"post\" enctype=\"multipart/form-data\" id=\""; if (isset($form_data)){$form .= 'edit_form';}else{$form .= 'new_record_form';}$form .= "\" name=\""; if (isset($form_data)){$form .= 'edit_form';}else{$form .= 'new_record_form';}$form .= "\">
		";

		$i = 0;

		foreach($form_array as $field)
		{

			//$field = $aiki->url->apply_url_on_query($field);

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

						$form .= "<h2>$intwalker[1]</h2>";
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

						break;

					case "selection":
						$form .= '<h2>'.$intwalker['1'].'</h2>
							<select name="'.$intwalker['0'].'" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '">
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
						$form .= ("</select>");
						break;

					case "textinput":
						$form .= '<h2>'.$intwalker['1'].'</h2><input type="text" id="'.$intwalker['0'].'" name="'.$intwalker['0'].'" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '" value="'; if (isset($form_data)){$form .= $form_data->$intwalker[0] ;} $form .= '">';
						break;

					case "imagenoupload":
						$form .= ("<h2>$intwalker[1]</h2><input type=\"text\" name=\"$intwalker[0]\" dir=\"$get_permission_and_man_info[3]\" value=\""); $form .= $_POST[$intwalker[0]]; $form .= ("\">");
						break;

					case "image":
						$form .= ("<h2>$intwalker[1]</h2><input type=\"text\" name=\"$intwalker[0]\" dir=\"$get_permission_and_man_info[3]\" value=\""); $form .= $_POST[$intwalker[0]]; $form .= ("\">");
						$form .= ("<h2>$intwalker[1]</h2><input type=\"file\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\">");
						break;

					case "unique_textinput":
						$form .= '<h2>'.$intwalker['1'].'</h2><input type="text" name="'.$intwalker['0'].'" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '" value="'; if (isset($form_data)){$form .= $form_data->$intwalker[0] ;} $form .= '">';
						break;

					case "password":
						$form .= '<h2>'.$intwalker['1'].'</h2><input type="password" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '" name="'.$intwalker['0'].'" ">';
						break;

					case "submit":
						$this->submit_button = $intwalker[0];
						break;

					case "verify_password":
						//$form .= ("<h2>لا تظهر تأكيد كلمة المرور</h2>");
						break;


					case "bigtextblock":
						$form .= '<h2>'.$intwalker[1].'</h2><textarea id="bigfont" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '" style="height: 500px; width: 600px; display: block;" name="'.$intwalker['0'].'">'; if (isset($form_data)){$form .= $form_data->$intwalker[0] ;} $form .= '</textarea>';
						break;


					case "bigwikiblock":

						$form .= ("<h2>$intwalker[1]</h2>
							<div id=\"toolbar\">
							<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('\'\'\'','\'\'\'','$intwalker[0]');\"><b>Bold</b></span>
							<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('==','==','$intwalker[0]');\">سطر</span>
							<span class='button' onmouseover=\"mouseover(this);\"  onmouseout=\"mouseout(this);\"  onmousedown=\"mousedown(this);\"  onmouseup=\"mouseup(this);\" onclick=\"FormatSelection('{+{','|0|left|v:10|h:10|300px|0}+}','$intwalker[0]');\">Image</span>				
							</div>
							<textarea id=\"bigfont\" dir=\"$get_permission_and_man_info[3]\" style=\"height: 500px; width: 600px; display: block;\" name=\"$intwalker[0]\">"); $form .= $_POST[$intwalker['0']]; $form .=("</textarea>");
						break;

					case "textblock":
						$form .= '<h2>'.$intwalker['1'].'</h2><div id="'.$intwalker['0'].'_container"><textarea rows="7" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '" cols="50" id="'.$intwalker['0'].'" name="'.$intwalker['0'].'">'; if (isset($form_data)){$form .= $form_data->$intwalker[0] ;} $form .= '</textarea></div>';
						break;

					case "upload":
						if (isset($form_data)){

							$form .= ("<h2>$intwalker[1]</h2>");
							$site_path = $config['url'];
							$img = "<img src=\"";
							$img .= substr($intwalker[6], 1);
							$img .= $form_data->$intwalker[0]."\"/><br />";
							$img .= $form_data->$intwalker[0];
							$img = str_replace("//", "/", $img);

							$form .=("$img");
						}else{
							$form .= '<h2>'.$intwalker['1'].'</h2><input type="file" dir="'; if (isset ($get_permission_and_man_info['3'])){$form .= $get_permission_and_man_info['3'];} $form .= '" name="'.$intwalker['0'].'">';
						}
						break;

					case "imagefolderupload":
						$form .= ("<h2>$intwalker[1]</h2><input type=\"text\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\">");
						break;

					case "datetime":
						$form .= ("<h2>$intwalker[1]</h2>$intwalker[0]</h2>");
						break;

					case "hidden":
						$form .= ("<input type=\"hidden\" name=\"$intwalker[0]\">");
						break;

					case "static_input":
						$form .= '<h2>'.$intwalker[1].'</h2><input type="text" dir="'.$get_permission_and_man_info[3].'" name="'.$intwalker[0].'" value="'; if (isset($form_data)){$form .= $form_data->$intwalker[0] ;} $form .= '">';
						break;



					case "filemanager":
						switch ($intwalker[2]){

							case "unique_filename":
								$form .= ("<h2>$intwalker[1]</h2><input dir=\"$get_permission_and_man_info[3]\" type=\"file\" name=\"$intwalker[0]\">");
								$form .= ("<input type=\"hidden\" name=\"unique_filename\" value=\"unique_filename\">");
								break;

						}
						break;



							case "autofiled":
								switch ($intwalker[2]){
									case "publishdate":
										$form .= ("<h2>$intwalker[1]</h2><input type=\"text\" dir=\"$get_permission_and_man_info[3]\" name=\"$intwalker[0]\" value=\""); $form .= $_POST[$intwalker[0]]; $form .= ("\">");
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

									case "insertedby_id":
										if (isset ($form_data)){
											$insertedby_userid = $form_data->$intwalker['0'];
										}else{
											$insertedby_userid = $membership->userid;
										}

										$form .= ("<input type=\"hidden\" name=\"$intwalker[0]\" value=\"$insertedby_userid\">");
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
		$form .= ('<p class="form-buttons">');
		$form .= ("<input type=\"hidden\" value=\"$form_id\" name=\"form_id\">");

		if (!isset ($this->submit_button)){
			$this->submit_button = 'Ok';
		}

		if (isset($form_data)){
			$record_id = $form_data->$pkey;
			$form .= ("<input type=\"hidden\" value=\"$record_id\" name=\"record_id\">");
			$form .= ("<input type=\"submit\" value=\"$this->submit_button\" name=\"edit_form\">");
		}else{
			$form .= ("<input type=\"submit\" value=\"$this->submit_button\" name=\"add_to_form\">");
		}
		$form .= ("</p></form>");

		return $form;

	}

	function create_insert_form($form_array, $form_html, $form_id){
		global $db, $aiki, $membership;

		$form = '';


		if (isset($form_html) and $form_html){

			$form = $aiki->sql_markup->sql($form_html);
			$form = $aiki->processVars($form_html);

			$form = str_replace('$form_id', $form_id, $form);
			$form = str_replace('$form_type', 'new_record_form', $form);
			$form = str_replace('$submit', 'add_to_form', $form);



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

			$form = str_replace('$form_id', $form_id, $form);
			$form = str_replace('$record_id', $record_id, $form);
			$form = str_replace('$form_type', 'edit_form', $form);
			$form = str_replace('$submit', 'edit_form', $form);

			$arraykeys = array_keys($form_array);

			if (in_array("tablename", $arraykeys))
			$tablename = $form_array["tablename"];

			if (in_array("pkey", $arraykeys)) {
				$pkey = $form_array["pkey"];
			}else{
				$pkey = 'id';
			}

			$sql = "select * from $tablename where $pkey='$record_id' limit 1";
			$form = $this->fill_form($form, $sql);


		}else{

			$form = $this->createForm ($form_array, $form_id, $record_id);

		}

		return $form;


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

	function auto_generate($table){
		global $aiki, $db;

		$table = mysql_escape_string($table);

		$table_exists = $db->get_var("SELECT id FROM aiki_forms where form_table = '$table'");

		if ($table_exists){
			die("Form for db table: <b>$table</b> already exists");
		}

		$form_array = array();

		$table_info = $db->get_results("SELECT * FROM $table limit 1");

		if ($table_info){
			
			$form_array["tablename"] = $table;

			$i = 0;

			foreach ($db->col_info as $column){

				$column = $aiki->array->object2array($column);
					
					
				if ($column['primary_key'] == 1){
					$form_array["pkey"] = $column['name'];
				}else{

					$i++;

					switch ($column['type']){

						case "int":
							$column['type'] = 'textinput';
							break;

						case "string":
							$column['type'] = 'textinput';
							break;

						case "blob":
							$column['type'] = 'textblock';
							break;
					}

					$column_display_name = str_replace('_', ' ', $column['name']);
					$column_display_name = str_replace('-', ' ', $column_display_name);

					$form_array[$column['type'].$i] = $column['name']."|SystemGOD:$column_display_name";
				}

			}

			$form_array = serialize($form_array);

			$insert_form = $db->query("insert into aiki_forms (form_name, form_array, form_table) values ('$table', '$form_array', '$table')");
			if (isset ($insert_form)){
				echo "Form for db table: <b>$table</b> created successfully";
			}
			
		}else{
			echo "Sorry db table: <b>$table</b> doesn't exists or unable to create form for it";
		}
	}

}
?>