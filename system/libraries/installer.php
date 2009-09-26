<?php  
if(!defined('IN_AIKI')){die('No direct script access allowed');}

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>Aiki framework installer</title>

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
	padding: 10px 10px;
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
<h1>Welcome to Aiki framework installer</h1>

<div id="stylized" class="myform">';
if (!isset($_POST['db_type']) or !isset($_POST['db_host']) or !isset($_POST['db_name']) or !isset($_POST['db_user']) or !isset($_POST['db_pass'])){
	echo '
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
	type="text" name="db_encoding" value="utf8" /></fieldset>

<button type="submit">Next..</button>
</form>';

}else{

	$config_file = '<?php

$db_type = "'.$_POST['db_type'].'";
$db_name = "'.$_POST['db_name'].'";
$db_user = "'.$_POST['db_user'].'";
$db_pass = "'.$_POST['db_pass'].'";
$db_host = "'.$_POST['db_host'].'";
$db_encoding = "'.$_POST['db_encoding'].'";

$db_path = ""; //SQLite and sqlite PDO
$db_dsn = ""; //sqlite PDO

$db_cache_timeout = 24;
$cache_dir = "";//db cacheing
$enable_query_cache = false;

$html_tidy = false;

$tidy_compress = false;


$widget_cache = false;
$widget_cache_dir = "widgets";

$css_cache = false;
$css_cache_timeout = 24;
$css_cache_file = "";

$javascript_cache = false;
$javascript_cache_timeout = 24;
$javascript_cache_file = "";

?>';	


	$config_file_name = "config_alpha.php";
	$FileHandle = fopen($config_file_name, 'w') or die("can't create file");
	fwrite($FileHandle, $config_file);
	fclose($FileHandle);


	/*
	 $fp = fopen('somefile.sql', 'r');
	 while($fp != feof())
	 {
	 $line = fread($fp, 2048);
	 $line = mysql_real_escape_string($db, $line);
	 mysql_query($line);
	 }
	 fclose($fp);
	 */

	echo '<h1>Great success, aiki framework installed</h1>';
	echo '<a href="admin/">Click here to login and start creating a cms</a>';
	echo '<br />';
	echo 'Username: admin';
	echo '<br />';
	echo 'Password: admin';


}
echo '</div>
</div>
</body>
</html>';
?>