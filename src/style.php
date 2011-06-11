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
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Aiki
 * @filesource
 */

/**
 * File for outputting CSS for a rendered page.
 */

error_reporting(0);

header('Content-type: text/css');


/**
 * @see aiki.php
 */
require_once("aiki.php");

/**
 * @global string $site
 * @todo the site var looks useless here, should trace and remove
 */
$site = addslashes($_GET['site']);
/**
 * @global string $widgets
 */
$widgets = addslashes($_GET['widgets']);

if (!$site)
	$site = "default";

if (isset($widgets) and $widgets != '')
{
	$widgets = str_replace('_', "' or id = '", $widgets);

	$get_widgets_css = $db->get_results("SELECT css, widget_name from " . 
        "aiki_widgets where id = '$widgets' and is_active = 1 order by id");

	if ($get_widgets_css)
    {
		foreach ( $get_widgets_css as $widget_css )
		{
            /**
             * @todo need to be able to disable all output, if not in debug
             */
			echo "\n/*Css for the widget $widgets - $widget_css->widget_name */\n";
			echo stripcslashes($aiki->languages->L10n($widget_css->css));
		}
	}
}
