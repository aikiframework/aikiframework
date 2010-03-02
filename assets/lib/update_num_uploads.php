<?php

require("../../aiki.php");

$user = mysql_escape_string($_GET['user']);

$get_num_uploads = $db->get_var("select count(id) from ocal_files where user_name = '$user'");
if ($get_num_uploads > 0){
	$update = $db->query("update aiki_users set num_uploads='$get_num_uploads' where username='$user'");
	echo "updated number of uploaded files for user $user - total: $get_num_uploads<br>";
}


?>