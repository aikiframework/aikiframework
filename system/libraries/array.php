<?php
if(!defined('IN_AIKI')){die('No direct script access allowed');}

class aiki_array
{
	var $createdArray;
	var $insertQuery;
	var $new_array_field;

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

		$setting = array();

		$settings = $db->get_results("SELECT $id, $data, $name FROM $table $where");
		$i=0;
		$y=0;

		$domain = $_SERVER['HTTP_HOST'];
		$path = $_SERVER['SCRIPT_NAME'];
		$queryString = $_SERVER['QUERY_STRING'];
		$thisurl = "http://" . $domain . $path . "?" . $queryString;

		$html_form = "
		<div id='stylized' class='myform'>
		<form method='post'>";
		foreach ( $settings as $setting_group )
		{

			$setting_group->$data = unserialize($setting_group->$data);

			$arrykeys = array_keys($setting_group->$data);

			$output_array = array();

			$html_form .= "<h1>".$setting_group->$name.":</h1>";
			foreach($setting_group->$data as $field)
			{

				if ($_POST['submit']){

					$output_array[$arrykeys[$i]] = $_POST[$y.$arrykeys[$i]];

					$field = $_POST[$y.$arrykeys[$i]];
				}
				//$toreplace = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
				$display = str_replace($toreplace, "", $arrykeys[$i]);
				$html_form .= "<label><span class='title'>$display</span><input type='text' name=\"".$y.$arrykeys[$i]."\" value=\"".$field."\" size='35'></label><br />";
				$i++;
			}
			$newfield = $setting_group->$name;

			//$html_form .= "<input type='hidden' name='$newfield' value='$newfield'>";
			$html_form .= "<label><input type='text' name=\"left_$newfield\" value=\"\" size='20'></label><input type='text' name=\"right_$newfield\" value=\"\" size='35'></td></tr>";

			$y++;
			$i=0;
			if (isset($_POST['submit'])){


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
		
		<input type=\"hidden\" value=\"$thisurl\" name=\"httpreferer\">
		<button type='submit' name='submit' value='submit'>Save</button>
		</form></div>";

		if ($this->new_array_field){
			Header("Location: $_POST[httpreferer]");
		}else{
			return $html_form;
		}
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