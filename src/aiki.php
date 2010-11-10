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
 * @version     0.6.0
 * @filesource
 */

/**
 * Used to test for script access
 */
define('IN_AIKI', true);

//try to determine the full-server path
if (function_exists('realpath') AND @realpath(dirname(__FILE__)) !== FALSE)
{
	$system_folder = realpath(dirname(__FILE__));

}

if (isset($_GET['nogui'])){ $nogui = true; }
if (isset($_GET['noheaders'])){ $noheaders = true; }
if (isset($_GET['custome_output'])){$custome_output = true;	$noheaders = true; }

/**
 * @see aiki-defs.php
 */
if (file_exists("$system_folder/configs/aiki-defs.php")){
	/**
	 * @see config.php
	 */
	require_once("$system_folder/configs/config.php");
}else{
	/**
	 * Aiki Framework Version
	 */
	define('AIKI_VERSION',	'0.6.0');
}

/*
 * ENABLE_RUNTIME_INSTALLER is defined by aiki-defs.php and should be
 * used to test for an Automake installed config and database versus
 * a config and database which is created at run-time via a web page.
 * Basically, config is install-time or run-time generated.
 * When ENABLE_RUNTIME_INSTALLER is NOT defined or TRUE, we
 * use the run-time installer logic. Otherwise, we use the install-time logic.
 */
if (!defined('ENABLE_RUNTIME_INSTALLER') or ENABLE_RUNTIME_INSTALLER == TRUE){
	/* use run-time installer logic */
	if (file_exists("$system_folder/config.php")){
		/**
		 * @see config.php
		 */
		require_once("$system_folder/config.php");
	}else{
		/**
		 * @see installer.php
		 */
		require("$system_folder/system/libraries/installer.php");
		die();
	}
}

/**
 * @see index.php
 */
require_once("$system_folder/system/database/index.php");

/**
 * @see core.php
 */
require_once ("$system_folder/system/core.php");


$aiki = new aiki();

$config = $aiki->get_config($config);

$membership = $aiki->load("membership");


if(isset($_GET['site'])){
	$site=$_GET['site'];
}else{
	$site = $config['site'];
}


$site_info = $db->get_row("SELECT * from aiki_sites where site_shortcut='$site' limit 1");
if (!$site_info){
	die("Fatal Error: Wrong site name provided");
}
if ($site_info and $site_info->is_active != 1){
	die($site_info->if_closed_output);
}


$aiki->load("text");
$aiki->load("records");
$aiki->load("input");
$aiki->load("output");
$aiki->load("forms");
$aiki->load("aiki_array");
$aiki->load("security");
$aiki->load("parser");
$aiki->load("php");
$aiki->load("sql_markup");
$aiki->load("languages");

$errors = $aiki->load("errors");

$url = $aiki->load("url");

if(isset($_GET['language'])){
	$language=$_GET['language'];
	$is_real_language = $db->get_row("SELECT sys_name, dir, short_name from aiki_languages where sys_name='$language'");
	if (isset($is_real_language->sys_name)){
		$config['default_language'] = $is_real_language->sys_name;
		$dir = $is_real_language->dir;
		$language_short_name = $is_real_language->short_name;
	}
}else{
	$default_language = $config['default_language'];
	$dir = $config['site_dir'];
	$language_short_name =$config['language_short_name'];
}

?>