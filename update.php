<?php
define('IN_AIKI', true);

require_once("aiki.php");


//set_time_limit('999999999999999999');

$files = $db->get_results("select id, filename from ocal_files");

foreach ($files as $file){
	$png = str_replace(".svg", '.png', $file->filename);
	$update = $db->query("update ocal_files set filename_png = '$png' where id = '$file->id'");
}

echo "nothing to do";
?>
