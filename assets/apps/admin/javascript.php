<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2009-2010 Aikilab inc
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */


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
	echo '<item parent_id="0" id="'.$javascript->id.'" ><content><name icon="'.$config['url'].'assets/images/icons/page_gear.png"><![CDATA['.$javascript->script_name.']]></name></content></item>';
}

echo "</root>";

?>