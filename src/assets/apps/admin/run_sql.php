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
 * @see aiki.php
 */
require_once("../../../aiki.php");

/**
 * Checks to ensure user has appropriate permissions
 */
if ($membership->permissions != "SystemGOD"){
	die("You do not have permissions to access this file");
}

/**
 * @see /src/libs/database/index.php
 */
if (isset($_POST['sql_query'])){
	$query = stripslashes($_POST['sql_query']);
	if ($query){
		$result = $db->query($query);
		$db->debug();		 
	}else{
		echo "Empty Query";
	}
}
