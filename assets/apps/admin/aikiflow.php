<?php

/**
 * Aiki framework (PHP)
 *
 * @author		Aikilab http://www.aikilab.com
 * @copyright  (c) 2008-2010 Aikilab
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */


error_reporting(0);

define('IN_AIKI', true);

require_once("../../../aiki.php");

if ($membership->permissions != "SystemGOD"){
	die("You do not have permissions to access this file");
}

if (isset($_POST['widget']) and isset($_POST['input'])){

}

?>