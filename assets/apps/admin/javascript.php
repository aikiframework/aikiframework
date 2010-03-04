<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

error_reporting(0);

header('Content-type: text/xml');

define('IN_AIKI', true);

require_once("../../../aiki.php");


if ($membership->permissions != "SystemGOD"){
	die("You do not have permissions to access this file");
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<root>';


$get_java = $db->get_results("select id, script_name from aiki_javascript order by script_name,id");

foreach ($get_java as $javascript){
	echo '<item parent_id="0" id="'.$javascript->id.'" ><content><name icon="'.$config['url'].'assets/apps/admin/images/icons/page_gear.png"><![CDATA['.$javascript->script_name.']]></name></content></item>';
}

echo "</root>";

?>