<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}

class aiki_array
{
	var $createdArray;
	var $insertQuery;
	var $new_array_field;

	
	function __construct(){

	}

	function displayArrayEditor($text){
		global $db, $aiki;

		$arrays_count = preg_match_all("/\(\#\(array\:(.*)\)\#\)/U", $text, $arrays);
		//id:form_name:form_array:aiki_forms:3)#)
		if ($arrays_count >0){

			foreach ($arrays[1] as $array_data){

				if ($array_data){

					$arrayEditor = explode(":", $array_data);

					$output = $aiki->array->editor($arrayEditor['1'], $arrayEditor['2'], $arrayEditor['3'], $arrayEditor['4'], "where ".$arrayEditor['1']."=".$arrayEditor['5']."");

					$text = preg_replace("/\(\#\(array\:$array_data\)\#\)/U", $output, $text);

				}
			}


		}

		return $text;
	}


	public function CreateArrayByExploding ($data, $explodefactor){
		if (isset($data)){
			$this->createdArray = explode($explodefactor, $data);
			return $this->createdArray;
		}

	}


	//http://www.jonasjohn.de/snippets/php/array2object.htm
	function array2object($arrGiven){
		//create empty class
		$objResult=new stdClass();

		foreach ($arrLinklist as $key => $value){
			//recursive call for multidimensional arrays
			if(is_array($value)) $value=$this->array2object($value);


			$objResult->{$key}=$value;
		}
		return $objResult;
	}

	function object2array($object) {
		if (is_object($object) || is_array($object)) {
			foreach ($object as $key => $value) {
				//print "$key\r\n";
				$array[$key] = $this->object2array($value);
			}
		}else {
			$array = $object;
		}
		return $array;
	}



	function editor($id, $name, $data, $table, $where){
		global $db;

		$pageURL = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

		$setting = array();

		$settings = $db->get_results("SELECT $id, $data, $name FROM $table $where");
		$i=0;
		$y=0;

		$html_form = "
		<form method='post' id='edit_form' name='edit_form' action='$pageURL'>";
		foreach ( $settings as $setting_group )
		{

			$setting_group->$data = unserialize($setting_group->$data);

			$arrykeys = array_keys($setting_group->$data);

			$output_array = array();

			$html_form .= "<h2>".$setting_group->$name."</h2>";
			foreach($setting_group->$data as $field)
			{

				if (isset($_POST['edit_array'])){

					$outp_key = $_POST[$y.$arrykeys[$i]."_type"];
					if ($outp_key != 'tablename' and $outp_key != 'pkey' and $outp_key != 'send_email' and $outp_key != 'unique_filename' and $outp_key != 'permission' and $outp_key != 'events'){
						$outp_key = $outp_key.$i;
					}

					$output_array[$outp_key] = $_POST[$y.$arrykeys[$i]];

					$field = $_POST[$y.$arrykeys[$i]];
				}
				$toreplace = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
				$display = str_replace($toreplace, "", $arrykeys[$i]);

				//TODO insert new field and consider other arrays like config
				$html_form .= '<select name="'.$y.$arrykeys[$i]."_type".'" >
				<option value="'.$display.'" selected="selected">'.$display.'</option>
				<option value="selection" >selection</option>
				<option value="upload" >upload</option>
				<option value="staticselect" >staticselect</option>
				<option value="unique_textinput" >unique_textinput</option>
				<option value="password" >password</option>
				<option value="textblock" >textblock</option>
				<option value="bigtextblock" >bigtextblock</option>
				<option value="textinput" >textinput</option>
				<option value="hidden" >hidden</option>
				<option value="static_input" >static_input</option>
				<option value="autofield" >autofield</option>
				<option value="submit" >submit</option>
				<option value="pkey" >pkey</option>
				<option value="send_email" >send_email</option>
				<option value="tablename" >tablename</option>
				<option value="unique_filename" >unique_filename</option>
				<option value="filemanager" >filemanager</option>
				<option value="permission" >permission</option>
				<option value="events" >events</option>
				</select>';
				$html_form .= "<input type='text' name=\"".$y.$arrykeys[$i]."\" value=\"".$field."\" size='35'><br /><br />";
				$i++;
			}
			$newfield = $setting_group->$name;

			$html_form .= '<select name="left_'.$newfield.'" >
				<option value="selection" >selection</option>
				<option value="upload" >upload</option>
				<option value="staticselect" >staticselect</option>
				<option value="unique_textinput" >unique_textinput</option>
				<option value="password" >password</option>
				<option value="textblock" >textblock</option>
				<option value="bigtextblock" >bigtextblock</option>
				<option value="textinput" selected="selected">textinput</option>
				<option value="hidden" >hidden</option>
				<option value="static_input" >static_input</option>
				<option value="autofield" >autofield</option>	
				<option value="submit" >submit</option>			
				<option value="pkey" >pkey</option>
				<option value="send_email" >send_email</option>
				<option value="tablename" >tablename</option>
				<option value="unique_filename" >unique_filename</option>
				<option value="filemanager" >filemanager</option>
				<option value="permission" >permission</option>
				<option value="events" >events</option>
				</select>';			
			$html_form .= "<input type='text' name=\"right_$newfield\" value=\"\" size='35'></td></tr>";

			$y++;
			$i=0;
			if (isset($_POST['edit_array'])){


				if ($_POST['left_'.$newfield] and $_POST['right_'.$newfield]){
					$output_array[$_POST['left_'.$newfield]] = $_POST['right_'.$newfield];
					$this->new_array_field = true;
				}

				$output_array = serialize($output_array);

				$output_id = $setting_group->$id;

				$update = $db->query("UPDATE $table set $data = '$output_array' where $id='$output_id'");

				$output_array = '';

			}


		}
		$html_form .= "
		<p class=\"form-buttons\">
		<input class=\"button\" type=\"submit\" value=\"Save\" name=\"edit_array\">
		</p>
		</form>";

		return $html_form;

	}

	public function CreateInsertsFromArray ($array, $inArrayExplodeFactor, $tablename, $QueryExample){
		global $db;
		if (isset($array)){
			$this->insertQuery = "insert into $tablename VALUES";
			foreach ($array as $value){

				$value = trim($value);
				if (isset($inArrayExplodeFactor)){
					$value = explode($inArrayExplodeFactor, $value);
					$value[0] = trim($value[0]);
					$value[1] = trim($value[1]);
					$innerQuery = str_replace('$value[0]', $value[0], $QueryExample);
					$innerQuery = str_replace('$value[1]', $value[1], $innerQuery);

				}

				$this->insertQuery .= "(".$innerQuery ."), ";
			}
			$this->insertQuery = substr($this->insertQuery,0,(strLen($this->insertQuery)-2));
			$db->query("$this->insertQuery");
		}

	}
}
?>