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
 * @package     Admin
 * @filesource
 */


error_reporting(0);

/**
 * Used to test for script access
 * @ignore
 */
define('IN_AIKI', true);

/**
 * @see bootstrap.php
 */
require_once("../../../bootstrap.php");
config_error_reporting(0);

if ($membership->permissions != "SystemGOD"){
	die("You do not have permissions to access this file");
}

if (isset($_POST['widget']) and isset($_POST['input'])){

}
