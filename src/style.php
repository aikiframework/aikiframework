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
 * @see bootstrap.php
 */
require_once("bootstrap.php");


// if a previous valid css file, exists, cache_file, outputs it, and aiki dies.
$cached_file = $aiki->output->cache_file("css");

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
        $debugOn = is_debug_on();
        $style="";
		foreach ( $get_widgets as $widget )
		{
            if (  $widget->css != "" ) {
                $style .= ( $debugOn ? "\n/*CSS for the widget {$widget->widget_name} (id {$widget->id}) */\n" : "") .
                          stripcslashes($aiki->languages->L10n($widget->css));
            }                                  
		}
        if ( $style ){            
            // predefined vars.
            $vars  = array (
                "view"     => $aiki->site->view(),
                "language" => $aiki->site->language(),
                "site"     => $aiki->site->get_site());
                            
            $style = $aiki->css_parser->parse( $style, $vars );            
        }           
        echo $style;
        
        if ($cached_file){            
            //write the cache file
            error_log ( $style, 3, $cached_file);
        }
        
	} 
}
