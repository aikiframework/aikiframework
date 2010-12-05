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
 * @package     Aiki
 * @filesource
 */

error_reporting(0);

header('Content-type: text/css');

/**
 * Used to test for script access
 * @ignore
 */
define('IN_AIKI', true);

/**
 * @see aiki.php
 */
require_once("aiki.php");

$site = addslashes($_GET['site']);
$widgets = addslashes($_GET['widgets']);

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
			echo stripcslashes($aiki->languages->L10n($widget_css->css));
		}
	}
}