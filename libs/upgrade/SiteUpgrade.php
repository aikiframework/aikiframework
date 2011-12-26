<?php

/** Aiki Framework (PHP)
 *
 * SiteUpgrade utility library.
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
 * @package     Library
 * @filesource */

// disable php script access
if(!defined("IN_AIKI")) { die("No direct script access allowed"); }

/** @see UpgradeInterface.php */
require_once("libs/upgrade/UpgradeInterface.php");

class SiteUpgrade implements UpgradeInterface {
    
    /** Constructs a new SiteUpgrade */
    public function __construct() {
        
    }
    
    /** Install an Upgrade */
    public function install() {
    	
    }
}