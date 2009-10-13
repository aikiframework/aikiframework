<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2008-2009 Bassel Khartabil.
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */

header('Content-type: text/css');

define('IN_AIKI', true);

require_once("aiki.php");

$site = $_GET['site'];
$widgets = $_GET['widgets'];

if (!$site){
	$site = "aiki_shared";
}


$styles = $db->get_results("SELECT css_name, style_sheet FROM aiki_css where is_active=1 and (css_group='$site' or css_group='aiki_shared') order by id");
if ($styles){
	foreach ( $styles as $style )
	{
		echo $style->css_name."{\n".$style->style_sheet."\n}\n";
	}
}

if (isset($widgets) and $widgets != ''){

	$widgets = eregi_replace('_$', '', $widgets);

	if (preg_match('/\_/', $widgets)){
		$widgets = str_replace('_', " or id = ", $widgets);
	}

	$get_widgets_css = $db->get_results("SELECT css from aiki_widgets where id = $widgets and is_active = 1 order by id");

	foreach ( $get_widgets_css as $widget_css )
	{
		echo $widget_css->css;
	}
}

?>