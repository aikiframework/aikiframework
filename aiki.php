<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */


// Framework Version
define('AIKI_VERSION',	'1.0.0');

define('IN_AIKI', true);

//try to determine the full-server path
if (function_exists('realpath') AND @realpath(dirname(__FILE__)) !== FALSE)
{
	$system_folder = realpath(dirname(__FILE__));

}

if (!isset($system_folder)){

	if (!isset($full_path)){

		if (!isset($system_folder) and !isset($_POST['fullpath']) or !file_exists($_POST['fullpath']."/system/libraries/installer.php")){

			echo ("<b>Aiki Fatal error: can't find the full path to your current installation</b><br /><br />
		please insert the full path on disk to this page <br />
		example: /var/www/aiki <br />
		or: c:/htdocs/aiki <br /><br />
		make sure there is no slash at the end of the full path
		<br />
		<form method='post'>
		<label>Full path:<input type='textinput' name='fullpath' value=''></label>
		<input type='submit' name='submit' value='Submit'>
		</form><br />
		this error will continue to happen if you entered a wrong path
		");

		}

		if (!isset($system_folder) and isset($_POST['fullpath'])){

			$system_folder = $_POST['fullpath'];

		}
	}else{
		$system_folder = $full_path;
	}
}


if (isset($_GET['nogui'])){ $nogui = true; }
if (isset($_GET['noheaders'])){ $noheaders = true; }
if (isset($_GET['custome_output'])){$custome_output = true;	$noheaders = true; }


if (file_exists("$system_folder/config.php")){
	require_once("$system_folder/config.php");
}else{
	require ("$system_folder/system/libraries/installer.php");
	die();
}

require_once("$system_folder/system/database/index.php");

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
if ($site_info->is_active != 1){ die($site_info->if_closed_output); }



$aiki->load("text");
$aiki->load("records");
$aiki->load("input");
$aiki->load("output");
$aiki->load("forms");
$aiki->load("upload");
$aiki->load("array");
$aiki->load("cronjobs");
$aiki->load("email");
$aiki->load("ftp");
$aiki->load("image");
$aiki->load("security");
$aiki->load("aiki_markup");
$aiki->load("html");
$aiki->load("php");
$aiki->load("xml");
$aiki->load("sql_markup");
$aiki->load("wiki_markup");
$aiki->load("languages");

$errors = $aiki->load("errors");


$url = $aiki->load("url");



if(isset($_GET['language'])){
	$language=$_GET['language'];
	$is_real_language = $db->get_row("SELECT sys_name, dir, short_name, align from aiki_languages where sys_name='$language'");
	if (isset($is_real_language->sys_name)){
		$config['default_language'] = $is_real_language->sys_name;
		$dir = $is_real_language->dir;
		$align = $is_real_language->align;
		$language_short_name = $is_real_language->short_name;
	}
}else{
	$default_language = $config['default_language'];
	$dir = $config['site_dir'];
	$language_short_name =$config['language_short_name'];
}


?>