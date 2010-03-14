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


$db->select($config['db_name']);


foreach ( $db->get_col("SHOW TABLES",0) as $table_name )

{

		echo '<item parent_id="0" id="'.$table_name.'" ><content><name icon="'.$config['url'].'assets/apps/admin/images/icons/database.png"><![CDATA['.$table_name.']]></name></content></item>';

		$get_forms = $db->get_results("select id, form_name from aiki_forms where form_table like '$table_name' order by id");

		if (isset($get_forms)){
			foreach ($get_forms as $form){

				echo '<item parent_id="'.$table_name.'" id="'.$form->id.'" ><content><name icon="'.$config['url'].'assets/apps/admin/images/icons/application_form.png"><![CDATA['.$form->id.' - '.$form->form_name.']]></name></content></item>';
			}
		}

}


echo "</root>";

?>