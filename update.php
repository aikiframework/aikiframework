<?php
define('IN_AIKI', true);

require_once("aiki.php");

$update = $db->query("UPDATE aiki_widgets SET `if_authorized` = '(#(form:delete:(!(2)!):(!(3)!))#)' where id = 13");

echo "done :)";
?>
