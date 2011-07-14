<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Roger Martin 
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Library
 * @filesource
 *
 * @todo        Translation of output messages.
 * @toto        error as html page.
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class Site {
 
    private $site ; 
    private $site_name; 

    /**
     * return site
     * @return string Return site short name
     */
    function get_site(){
        return $this->site;
    }   


    /**
     * return site name
     * @return string Return site long name
     */
    function site_name(){
        return $this->site_name;
    }

    /**
     * magic method for use object site as a string
     * @return string Return site long name
     */

    function __toString(){
        return $this->site;
    }

    /** 
     * Constructor: set site name or die if is inexistent or inactive.
     * The site is established by (in this order), GET[site], $config[site]
     * and finaly Default.
     * 
     * @global $db
     * @global $config 
     */

    function __construct(){
        global $db, $config, $aiki;
        
        // determine site name
        if (isset($_GET['site'])) {
            $config['site'] = addslashes($_GET['site']);
        } elseif ( !isset( $config['site'] )) {
            $config['site'] = 'default';
        }  
        
        // try read site information and test if is_active.
        $info = $db->get_row("SELECT * from aiki_sites where site_shortcut='{$config['site']}' limit 1");
        $error = false;
        if ( is_null($info) ) {        
            $error =  "Fatal Error: Wrong site name provided. " .
                      (defined('ENABLE_RUNTIME_INSTALLER') && ENABLE_RUNTIME_INSTALLER == FALSE ?
                      "ENABLE_RUNTIME_INSTALLER is set to FALSE." : "");      
        } elseif ( $info->is_active != 1) {
            $error = $info->if_closed_output ? 
                $info->if_closed_output : 
                "Site {$config['site']} is closed.";
            
        }
        if ( $error ){
            die( $aiki->message->error( $error, NULL, false) ); 
        }    
        
        $this->site      = $config['site'];
        $this->site_name = $info->site_name;
    }
}
