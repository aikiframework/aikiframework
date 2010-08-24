<?php

/**
 * Aiki framework (PHP)
 *
 * @author		Aikilab http://www.aikilab.com
 * @copyright  (c) 2008-2010 Aikilab
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */


error_reporting(0);

define('IN_AIKI', true);

require_once("../../../aiki.php");

if ($membership->permissions != "SystemGOD"){
	die("You do not have permissions to access this file");
}

if (!isset($_GET['widget'])){

	$current_events = $db->get_results("select * from aiki_events order by id DESC");
	foreach ($current_events as $event){
		$event->timestarted = strtotime($event->timestarted);
		$difference = (time() - $event->timestarted) / 60;
		$difference = (int)$difference;
		echo $difference." minutes ago - ".$event->event."<br />";
	}

}else{

	$widget_name = $db->get_var("select widget_name from aiki_widgets where id = ".$_GET['widget']."");

	$add_event = $db->query("insert into aiki_events(id, event, username, widgetid) VALUES (NULL, '".$aiki->membership->username." is editing widget ".$_GET['widget']." - ".$widget_name."', '".$aiki->membership->username."', '".$_GET['widget']."')");

	$latest_action = $db->get_var("select id from aiki_events where username = '".$aiki->membership->username."' order by id DESC limit 1");

	$delete_older_events = $db->query("delete from aiki_events where username = '".$aiki->membership->username."' and id != '$latest_action'");
}
?>