<?php

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class doTables extends aiki
{

	function doTables(){


	}

	function do_doTables($text){
		
		global $aiki;
		//TODO colspan and cellspan and table header and footer and colored columns

		$numMatches = preg_match_all( '/\{\|/', $text, $matches);
		for ($i=0; $i<$numMatches; $i++){

			$get_table_contents = $this->get_string_between($text, "{|", "|}");

			$table_atr = $aiki->get_string_between($text, "{|", "|-");
			$table_atr = trim($table_atr);
			$table_atr = str_replace("<br>", "", $table_atr);
			$table_atr = str_replace("<br />", "", $table_atr);

			$table = "<table $table_atr >";


			$get_rows = explode("|-", $get_table_contents);
			unset($get_rows[0]); //fix for the first element being empty
			foreach ($get_rows as $row){
				$table .= "<tr>";

				$get_cells = explode("|", $row);
				unset($get_cells[0]); //fix for the first element being empty


				foreach ($get_cells as $cell){
					$table .= "<td>$cell</td>";

				}

				$table .= "</tr>";
			}


			$table .= "</table>";

			$text = str_replace ("{|".$get_table_contents."|}", $table, $text);
		}


		return $text;

	}
}
?>