<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

error_reporting(0);

header('Content-type: text/css');

define('IN_AIKI', true);

require_once("aiki.php");

$site = mysql_real_escape_string($_GET['site']);
$widgets = mysql_real_escape_string($_GET['widgets']);

if (!$site){
	$site = "default";
}


if (isset($widgets) and $widgets != ''){


	if (preg_match('/\_/', $widgets)){
		$widgets = str_replace('_', " or id = ", $widgets);
	}

	$get_widgets_css = $db->get_results("SELECT css from aiki_widgets where id = $widgets and is_active = 1 order by id");
	if ($get_widgets_css){
		foreach ( $get_widgets_css as $widget_css )
		{
			echo $widget_css->css;
		}
	}
}

?>