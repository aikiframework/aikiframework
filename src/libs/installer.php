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
 * @package     Library
 * @filesource
 *
 * @todo        look at modularizing the installer for maintainability
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}

/** @see defs.inc */
require_once("$AIKI_ROOT_DIR/configs/defs.php");
    
/* setting $config["log_level"] = "NONE" disables the log 
 * or "None" and "none". Also if the log_level is not valid
 * the log will default to disabled. */
/** @see Log.php */
require_once("$AIKI_ROOT_DIR/libs/Log.php");

/** @see File.php */
require_once("$AIKI_ROOT_DIR/libs/File.php");
    
/** Instantiate a new log for installer use
 * Log $log */
$log = new Log(AIKI_LOG_DIR,
			AIKI_LOG_FILE,
			AIKI_LOG_LEVEL,
			$AIKI_ROOT_DIR);
$log->message("Starting run-time installation", Log::INFO);

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>Aiki Framework Installer</title>

<style type="text/css">
* {
	padding: 0;
	margin: 0;
}

body {
	background-color: #E5E5E5;
}

legend {
	padding: 20px;
}

#content {
	float: left;
	width: 520px;
	padding: 6px 10px 0px 6px;
}

#content img {
	margin-right: 16px;
	margin-left: 12px;
	margin-top: 6px;
	margin-bottom: 6px;
	padding: 2px;
	float: left;
}

#content p {
	font-family: verdana, "Microsoft Sans Serif", Times, serif;
	font-size: 8pt;
	margin-top: 0px;
	margin-bottom: 10px;
	padding: 0px 10px;
	text-align: justify;
	line-height: 12pt;
}

#content h1 {
	font-family: Baskerville, Georgia, Times, serif;
	font-size: 15pt;
	font-style: normal;
	font-weight: normal;
	margin-top: 5px;
	padding: 10px 0 10px 0;
}

.myform {
	margin: 10px;
	padding: 14px;
}

#stylized {
	border: solid 2px #999;
	background: #F7F7F7;
}

#stylized h1 {
	font-size: 14px;
	font-weight: bold;
	margin-bottom: 0px;
}

#stylized p {
	font-size: 11px;
	color: #666666;
	margin-bottom: 20px;
	border-bottom: solid 1px #999;
	padding-bottom: 10px;
}

#stylized label {
	display: block;
	font-weight: bold;
	text-align: right;
	width: 200px;
	float: left;
}

#stylized .small {
	color: #666666;
	display: block;
	font-size: 11px;
	font-weight: normal;
	text-align: right;
	width: 140px;
}

#stylized input {
	float: left;
	font-size: 12px;
	padding: 4px 2px;
	border: solid 1px #999;
	width: 200px;
	margin: 2px 0 20px 10px;
}

#stylized select {
	float: left;
	font-size: 12px;
	padding: 4px 2px;
	border: solid 1px #999;
	width: 200px;
	margin: 2px 0 20px 10px;
}

#stylized button {
	clear: both;
	margin-left: 150px;
	width: 128px;
	height: 35px;
	background: #666666;
	text-align: center;
	line-height: 31px;
	color: #FFFFFF;
	font-size: 11px;
	font-weight: bold;
}
</style>

</head>

<body>

<div class="" id="content">
<h1>Aiki Framework Installer</h1>

<div id="stylized" class="myform">';
if (!isset($_POST['db_type']) or !isset($_POST['db_host']) or !isset($_POST['db_name']) or !isset($_POST['db_user'])){
	echo '
<p>ONE STEP Installer Guide
<br />
Before we start you need the following:
<br />
<br />
1- An empty database, with collation set to utf8_general_ci.
<br />
<br />
2- PHP 5.2 or above and apache2.
<br />
<br />
3- mod_rewrite must be enabled inside apache2 httpd.conf  
</p>	
	
<form method="post" id="form">
<fieldset><legend> Database Settings</legend> <label>Database type</label>
<select name="db_type">
<option name="mysql" selected>mysql</option>
<option name="mssql">mssql</option>
<option name="oracle">oracle 8 or higher</option>
<option name="pdo">pdo</option>
<option name="postgresql">postgresql</option>
<option name="sqlite">sqlite</option>
</select>
<label>Host Name</label><input
	type="text" name="db_host" value="localhost" /> <label>Database name</label><input
	type="text" name="db_name" value="" /> <label>Database username</label><input
	type="text" name="db_user" value="" /> <label>Database password</label><input
	type="text" name="db_pass" value="" /> <label>Database encoding</label><input
	type="text" name="db_encoding" value="utf8" />
	</fieldset>
	
<fieldset><legend>Admin Settings</legend>
<label>Username</label><input type="text" name="username" value="" /></label>
<label>Full Name</label><input type="text" name="full_name" value="" /></label>
<label>Email</label><input	type="text" name="email" value="" /></label>
	</fieldset>	

<button type="submit">Next..</button>
</form>';

}else{

	if ($_POST['username']){
		$username = $_POST['username'];
	}else{
		$username = "admin";
	}

	if ($_POST['full_name']){
		$full_name = $_POST['full_name'];
	}else{
		$full_name = "System Admin";
	}

	if ($_POST['email']){
		if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $_POST['email'])){
			$email = $_POST['email'];
		}
	}
	if (!isset($email)){
		$email = '';
	}

	$pageURL = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	$page_strlen =  strlen($pageURL);
	$AIKI_ROOT_DIR_strlen =  strlen($AIKI_ROOT_DIR);
	$_SERVER["REQUEST_URI"] = str_replace("index.php", '', $_SERVER["REQUEST_URI"]);

	/* Read config from file. This way the configurations can be shared with the
	 * other installers and it's much easier to maintain one PHP configuration file */
	$config_file = file_get_contents("$AIKI_ROOT_DIR/configs/config.php");
	if (false == $config_file){
		die("<br />FATAL: failed to read file -> $AIKI_ROOT_DIR/configs/config.php<br />");
	}
	$config_file = str_replace("DB_TYPE","\"".$_POST['db_type']."\"",$config_file);
	$config_file = str_replace("DB_NAME","\"".$_POST['db_name']."\"",$config_file);
	$config_file = str_replace("DB_USER","\"".$_POST['db_user']."\"",$config_file);
	$config_file = str_replace("DB_PASS","\"".$_POST['db_pass']."\"",$config_file);
	$config_file = str_replace("DB_HOST","\"".$_POST['db_host']."\"",$config_file);
	$config_file = str_replace("DB_ENCODE","\"".$_POST['db_encoding']."\"",$config_file);
	$config_file = str_replace("@AIKI_SITE_URL@",$pageURL,$config_file);
	$config_file = str_replace("@AIKI_REWRITE_BASE@",$_SERVER["REQUEST_URI"],$config_file);

	$config_file_html = htmlspecialchars($config_file);
	$config_file_html = nl2br($config_file_html);

	$conn = @mysql_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_pass']) or die ('Error connecting to mysql');
	$select_db = @mysql_select_db($_POST['db_name']);

	if (!$select_db){
		echo "An existing database named $_POST[db_name] is not found.<br />Attempting to create a database named $_POST[db_name]...";
		$create_db = mysql_query("CREATE DATABASE `$_POST[db_name]` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$select_db = @mysql_select_db($_POST['db_name']);

		if (!$create_db and !$select_db){
			die ("<br />Failed to create a database named $_POST[db_name].");
		}else{
			echo "<br />Successfully created a database named $_POST[db_name].";
		}
	}

	$htaccess_file_path = "$AIKI_ROOT_DIR/configs/htaccess.inc";
	$htaccess_file = file_get_contents($htaccess_file_path);
	if ( false == $htaccess_file )
		die("<br />WARN: failed to read file $htaccess_file_path<br />");

	// $rewrite_base = ( AIKI_REWRITE_BASE != $_SERVER["REQUEST_URI"] ) ? 
	//				  $_SERVER["REQUEST_URI"] : AIKI_REWRITE_BASE;
	$htaccess_file = str_replace("@AIKI_REWRITE_BASE@", 
								 $_SERVER["REQUEST_URI"], 
					     		$htaccess_file);

	$htaccess_file_html = nl2br($htaccess_file);

	$config_file_name = "config.php";
	$FileHandle = fopen($config_file_name, 'w') or die("<br />Sorry, no permissions to create config.php, please create it in <b>$AIKI_ROOT_DIR</b> with the following: <br /><br />$config_file_html<hr /><br />also please add the following to .htaccess to enable pretty urls:<br /><br /><small>".$htaccess_file_html."</small>");
	fwrite($FileHandle, $config_file);
	fclose($FileHandle);
    /* This is needed in the case where the 
     * default file mode is too restrictive. */
    chmod($config_file_name, 0644);

	$admin_password = substr(md5(uniqid(rand(),true)),1,8);
	$admin_password_md5_md5 = md5(md5($admin_password));

	/* Read SQL from files. This way the SQL statements can be shared with the
	 * other installers and it's much easier to maintain SQL scripts separately
	 * using a SQL supported editor rather than the PHP escaped SQL statements */
	$sql_create_tables = file_get_contents("$AIKI_ROOT_DIR/sql/CreateTables.sql");
	if (false == $sql_create_tables){
		die("<br />FATAL: failed to read file -> $AIKI_ROOT_DIR/sql/CreateTables.sql<br />");
	}
	$sql_insert_defaults = file_get_contents("$AIKI_ROOT_DIR/sql/InsertDefaults.sql");
	if (false == $sql_insert_defaults){
		die("<br />FATAL: failed to read file -> $AIKI_ROOT_DIR/sql/InsertDefaults.sql<br />");
	}
	$sql_insert_variable = file_get_contents("$AIKI_ROOT_DIR/sql/InsertVariable-in.sql");
	if (false == $sql_insert_variable){
		die("<br />FATAL: failed to read file -> $AIKI_ROOT_DIR/sql/InsertVariable-in.sql<br />");
	}
	$sql_insert_variable = str_replace("@AIKI_SITE_URL_LEN@",$page_strlen,$sql_insert_variable);
	$sql_insert_variable = str_replace("@AIKI_SITE_URL@",$pageURL,$sql_insert_variable);
	$sql_insert_variable = str_replace("@PKG_DATA_DIR_LEN@",$AIKI_ROOT_DIR_strlen,$sql_insert_variable);
	$sql_insert_variable = str_replace("@PKG_DATA_DIR@",$AIKI_ROOT_DIR,$sql_insert_variable);
	$sql_insert_variable = str_replace("@ADMIN_USER@",$username,$sql_insert_variable);
	$sql_insert_variable = str_replace("@ADMIN_NAME@",$full_name,$sql_insert_variable);
	$sql_insert_variable = str_replace("@ADMIN_PASS@",$admin_password_md5_md5,$sql_insert_variable);
	$sql_insert_variable = str_replace("@ADMIN_MAIL@",$email,$sql_insert_variable);
	$sql_insert_variable = str_replace("@VERSION@",AIKI_VERSION,$sql_insert_variable);
	$sql_insert_variable = str_replace("@REVISION@",AIKI_REVISION,$sql_insert_variable);
	$sql_insert_variable = str_replace("@AUTHORS@",AIKI_AUTHORS,$sql_insert_variable);
	
	/* In MySQL, the “-- ” (double-dash) comment style requires the second
	 * dash to be followed by at least one whitespace or control character.
	 * SEE: http://dev.mysql.com/doc/refman/5.1/en/comments.html 
	 * The single space ('-- ') is REQUIRED for the SQL files */
	define("SQL_DELIMIT",'-- ------------------------------------------------------');
	$sql = $sql_create_tables.SQL_DELIMIT.$sql_insert_defaults.SQL_DELIMIT.$sql_insert_variable;

	$sql = explode(SQL_DELIMIT, $sql);

	foreach($sql as $sql_statment)
	{
	 mysql_query($sql_statment);
	}


	echo '<h1>Great success '.$full_name.'! Aiki Framework is installed.</h1>';
	echo '<a href="admin/">Click here to login and start creating a CMS</a>.';
	echo '<br />';
	echo 'Username: '.$username;
	echo '<br />';
	echo 'Password: '.$admin_password;

	if ($email){

		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		$headers .= "From: noreply@aikiframework.org\r\n";

		$message = "Hello $full_name <br /> your new Aiki installation is ready to be used <br />
			Go to: ".$pageURL."admin <br />
Username: $username <br />
Password: $admin_password
";

		mail($email,'Your new Aiki installation',$message,$headers);
	}
    
	$htaccess_file_name = ".htaccess";
	$FileHandle = fopen($htaccess_file_name, 'w') or die("<br />Sorry, no permissions to create .htaccess file<br /> please add the following to .htaccess to enable pretty urls:<br /><br /><small>".$htaccess_file_html."</small>");
	fwrite($FileHandle, $htaccess_file);
	fclose($FileHandle);
	/* This is needed in the case where the 
	 * default file mode is too restrictive. */
    chmod($htaccess_file_name, 0644);
}
echo '</div>
</div>
</body>
</html>';
