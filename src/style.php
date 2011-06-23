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
$site = isset($_GET['site']) ? addslashes($_GET['site']) : 'default';
/**
 * @global string $widgets
 */
$widgets_list = isset($_GET['widgets']) ? addslashes($_GET['widgets']) : '';



if ( $widgets_list != ''){
	
	$where = "id='" . str_replace('_', "' or id = '", $widgets_list). "'";
	$sql = 
		"SELECT id, css, widget_name" . 
        " FROM aiki_widgets".
        " WHERE " . $where . 
        "  AND is_active = 1".
        " ORDER BY id";
	$get_widgets = $db->get_results($sql);

	if ($get_widgets)
    {
		foreach ( $get_widgets as $widget )
		{
            /**
             * @todo need to be able to disable all output, if not in debug
             */
			echo "\n/*CSS for the widget {$widget->widget_name}({$widget->id}) */\n";
			echo stripcslashes($aiki->languages->L10n($widget->css));
		}
	}
}
