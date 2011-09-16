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
        
        $style="";
		foreach ( $get_widgets as $widget )
		{
            /**
             * @todo need to be able to disable all output, if not in debug
             */
            if ( $widget->css != "" ) {
                $style .="\n/*CSS for the widget {$widget->widget_name} (id {$widget->id}) */\n".
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
        
	}
}
