<?php

/** Aiki Framework (PHP)
 * 
 * This is the bootstrap file.
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
/** Aiki Framework Version
 * The number left or west of the dots indicates a MAJOR production release.
 * In between the dots indicates a significant change or MINOR changes.
 * The number right or east of the dots indicates a bug FIX or small change.
 * When the MINOR number changes, the FIX number should reset to zero.
 * When the MAJOR number changes, the MINOR number should reset to zero.
 * When the MAJOR number is zero, this indicates an alpha or beta type 
 * release. Each number can, but should probably not exceed 99 */
define('AIKI_VERSION','0.8.15');

/**
 * Used to test for script access
 */
define('IN_AIKI', true);

/**
 * Determine the full-server path.
 * @global string $system_folder
 */
$system_folder = realpath(dirname(__FILE__));

// append to the include path while preserving existing entries
set_include_path(
    get_include_path() .
    PATH_SEPARATOR .
    "$system_folder" .
    PATH_SEPARATOR .
    "$system_folder/system");

/** 
 * @todo these should be set in some class, and are scoped wrong
 */
if (isset($_GET['nogui'])){ $nogui = true; }
if (isset($_GET['noheaders'])){ $noheaders = true; }
if (isset($_GET['custom_output'])){$custom_output = true;	$noheaders = true; }

/** The existence of aiki-defs.php indicates an Automake installation.
 * @see aiki-defs.inc */
if (file_exists("$system_folder/configs/aiki-defs.php")) {
	/** @see config.php */
	require_once("$system_folder/configs/config.php");
}

/** ENABLE_RUNTIME_INSTALLER is defined by aiki-defs.php which
 * should NOT exist in a run-time installation distribution package.
 * It should be used to test for an Automake installed config and database 
 * versus a config and database which is created at run-time via a web page.
 * Basically, config is Automake or run-time generated.
 * When ENABLE_RUNTIME_INSTALLER is NOT defined or TRUE, we
 * use the run-time installer. Otherwise, we use the Automake installer. */
if (!defined('ENABLE_RUNTIME_INSTALLER') or ENABLE_RUNTIME_INSTALLER == TRUE)
{
	/* use run-time installer config */
	if (file_exists("$system_folder/config.php")) {
		/** @see config.php */
		require_once("$system_folder/config.php");
	}
	else {
		/** @see installer.php */
		require("$system_folder/system/libraries/installer.php");
		die();
	}
}
    
/* setting $config["log_level"] = "NONE" disables the log 
 * or "None" and "none". Also if the log_level is not valid
 * the log will default to disabled. */
/** @see Log.php */
require_once("$system_folder/system/libraries/Log.php");
    
/** Instantiate a new log for global use
 * @global Log $log */
$log = new Log($config["log_dir"],
            $config["log_file"],
            $config["log_level"]);

/* the following lines are usage examples:
$log->message("test message which defaults to debug level");
$log->message("test ERROR", Log::ERROR);
$log->message("test WARN", Log::WARN);
$log->message("test INFO", Log::INFO);
$log->message("test DEBUG", Log::DEBUG);*/

/**
 * Where $db is defined as a switch in this 3rd party library.
 * 
 * @see index.php
 */
require_once("$system_folder/system/database/index.php");

/**
 * @see core.php
 */
require_once ("$system_folder/system/core.php");

/**
 * Global creation of the aiki instance.
 * @global aiki $aiki
 */ 
$aiki = new aiki();

/**
 * Get and store the configuration options.
 * @global array $config
 */ 
$config = $aiki->get_config($config);

$aiki->load("message");

/**
 * Load membership class for global use.
 * @global membership $membership
 */ 
$membership = $aiki->load("membership");

/**
 * @todo: scoping is wrong here, need solution on these.
 * @see aiki.php
 */
if (isset($_GET['site']))
	$site=addslashes($_GET['site']);
else
	$site = $config['site'];

/**
 * Get the site information for determining installer setup.
 * @global array $site_info
 */ 
$site_info = $db->get_row("SELECT * from aiki_sites where site_shortcut='$site' limit 1");
if (!$site_info)
{
    /** 
     * @todo: this needs translation. Its a hardcoded string!
     */
	die("Fatal Error: Wrong site name provided. " .
	  (( ENABLE_RUNTIME_INSTALLER == FALSE ) ?
           "ENABLE_RUNTIME_INSTALLER is set to FALSE." : ""));
}

if ($site_info and $site_info->is_active != 1)
	die($site_info->if_closed_output);

// load rest of classes
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
/**
 * Global language object for use at runtime.
 * @global language $language
 */ 
$languages = $aiki->load("languages");
$aiki->load("image");

/**
 * Global error object for use in runtime.
 * @global errors $errors
 */ 
$errors = $aiki->load("errors");

/**
 * Global object for handling urls.
 * @global url $url 
 */ 
$url = $aiki->load("url");
