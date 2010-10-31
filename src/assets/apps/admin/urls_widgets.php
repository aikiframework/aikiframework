<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Aikilab http://www.aikilab.com
 * @copyright   (c) 2008-2010 Aikilab
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Admin
 * @filesource
 */

error_reporting(0);

header('Content-type: text/xml');

/**
 * Used to test for script access
 */
define('IN_AIKI', true);

/**
 * @see aiki.php
 */
require_once("../../../aiki.php");


if ($membership->permissions != "SystemGOD"){
	die("You do not have permissions to access this file");
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<root>';


$get_urls = $db->get_results("select distinct display_urls from aiki_widgets where display_urls NOT REGEXP 'admin' and display_urls != '' order by BINARY display_urls");
$used = array();
$used_url = array();
foreach ($get_urls as $url){

	if ($url->display_urls == "*"){
		$url->display_urls = "Global";
	}
	$multi_url = explode("|", $url->display_urls);

	if (isset($multi_url['1'])){
		$url->display_urls = $multi_url[0];
	}

	if (!in_array($url->display_urls, $used_url)){
		echo '<item parent_id="0" id="'.$url->display_urls.'" ><content><name icon="'.$config['url'].'assets/apps/admin/images/icons/link.png"><![CDATA['.$url->display_urls.']]></name></content></item>';
	}

	$used_url[$url->display_urls] = $url->display_urls;

	$get_widgets = $db->get_results("select id, widget_name, father_widget, is_father, display_urls from aiki_widgets where display_urls = '$url->display_urls' or display_urls LIKE '$url->display_urls|%' or display_urls LIKE '$url->display_urls/%' or display_urls = '*' order by display_order,id");
	if($get_widgets){
		foreach ($get_widgets as $widget){

			if (!in_array($widget->id, $used)){
				
				if ($widget->father_widget == 0 or $widget->display_urls == $url->display_urls){
					$used[$widget->id] = $widget->id;
					echo '<item parent_id="'.$url->display_urls.'" id="'.$widget->id.'" ><content><name icon="'.$config['url'].'assets/apps/admin/images/icons/layout_content.png"><![CDATA['.$widget->widget_name.']]></name></content></item>';
				}else{
					$used[$widget->id] = $widget->id;
					echo '<item parent_id="'.$widget->father_widget.'" id="'.$widget->id.'" ><content><name icon="'.$config['url'].'assets/apps/admin/images/icons/layout_content.png"><![CDATA['.$widget->widget_name.']]></name></content></item>';
				}
			}

		}
	}

}

echo '<item parent_id="0" id="view_all_widgets" ><content><name icon="'.$config['url'].'assets/apps/admin/images/icons/link.png"><![CDATA[All Widgets]]></name></content></item>';
$get_widgets = $db->get_results("select id, widget_name, father_widget, is_father from aiki_widgets where app_id != 1 order by id");
if($get_widgets){
	foreach ($get_widgets as $widget){
		echo '<item parent_id="view_all_widgets" id="'.$widget->id.'" ><content><name icon="'.$config['url'].'assets/apps/admin/images/icons/layout_content.png"><![CDATA['.$widget->id.' - '.$widget->widget_name.']]></name></content></item>';
	}
}

echo "</root>";

?>