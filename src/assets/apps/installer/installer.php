<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Roger Martin, Aikilab http://www.aikilab.com 
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki apps
 * @package     installaer
 * @filesource
 *
 * @TODO Translate
 
 * @TODO VERSION. REVISION, AUTHORS* 
 * AIKI_LOG_DIR, AIKI_LOG_FILE,AIKI_LOG_PROFILE, AIKI_LOG_LEVEL;
 *
 */


// Steps
// 0 Checks if neccesary file exists, and there is not a previous config.php file
// 1 Ask for setting
// 2 Create database. If can't do, go  step 1.
// 3 Create config. If can't do, show config file.
// 4 Create apache. If can't do, show htaccess and continue.

define ("SQLS_DELIMITER", "-- ------------------------------------------------------");

$AIKI_ROOT_DIR = realpath( dirname(__FILE__ ). "/../../..");
$AIKI_SITE_URL = clean_url("http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);

// Vars
$config = array(
    "db_type"        => "mysql",
	"db_host"        => "localhost",
	"db_name"        => "aiki",
	"db_user"        => "",
	"db_pass"        => "",
	"db_encoding"    => "UTF-8",
	"ADMIN_EMAIL"    => "",
	"ADMIN_USER"     => "admin",
	"ADMIN_FULLNAME" => "adminstrator");

// request $POST vars
foreach ( $config as $key => $value) {
    if ( isset( $_REQUEST[$key] ) &&  $_REQUEST[$key] ) {
		$config[$key] = addslashes($_REQUEST[$key]);
	}
}

$selectType="<select name='db_type' id='db_type' class='user-input'>\n";
$options = array (
	"mysql" =>"MySQL",
	"mssql" =>"mssql",
	"oracle" =>"oracle 8 or higher",
	"pdo"=>"PDO",
	"postgresql" =>"postgresql",
	"sqlite" =>"sqlite");
foreach ( $options as $value=>$option){
	$selected= ($value==$config['db_type'] ? " selected ": "" );
	$selectType .= "\t<option value='$value'$selected>$option</option>\n";
} 
$selectType .="</select>\n";


// templates for each step
$template[0]="<div class='error'>Installation aborted</div><div class='error'>%s</div>";
$template[1]= "%s<form method='post'>
	<fieldset class='db'><legend>Database</legend>
	<p><label for='db_type'>SQL</label>$selectType<span class='required'>*</span></p>
	<p><label for='db_host'>Host</label><input type='text' name='db_host' id='db_host' class='user-input' value='{$config['db_host']}'><span class='required'>*</span></p>
	<p><label for='db_name'>Database name</label><input type='text' name='db_name' id='db_name' class='user-input' value='{$config['db_name']}'><span class='required'>*</span></p>
	<p><label for='db_user'>User</label><input type='text' name='db_user' id='db_user' class='user-input' value='{$config['db_user']}'><span class='required'>*</span></p>
	<p><label for='db_pass'>Password</label><input type='password' name='db_pass' id='db_pass' class='user-input' value='{$config['db_pass']}'><span class='required'>*</span></p>
	<p><label for='db_encoding'>Encoding</label><input type='text' name='db_encoding' id='db_encoding' class='user-input' value='{$config['db_encoding']}'><span class='required'>*</span></p>
	</fieldset>

    <fieldset class='other'><legend>Admin user  / Site</legend>
    <p><label for='ADMIN_USER'>login</label><input type='text' name='ADMIN_USER'  id='ADMIN_USER'  class='user-input' value='{$config['ADMIN_USER']}'></p>
	<p><label for='ADMIN_FULLNAME'>Full name</label> <input type='text' name='SITE_TITLE'  id='SITE_TITLE'  class='user-input' value='{$config['ADMIN_FULLNAME']}'></p>
	<p><label for='ADMIN_EMAIL'>Email</label> <input type='text' name='ADMIN_EMAIL' id='ADMIN_EMAIL' class='user-input' value='{$config['ADMIN_EMAIL']}'></p>
	<p class='note'>Aiki will send login and password using this email.</p>    
    </fieldset>

    <input type='hidden' name='step' value='2'>
	<p class='required'><span class='required'>*</span> Required Fields</p>

    <div class='actions'>
    <input type='submit' value='Test connection' class='button' name='testDB'>
	<input type='submit' value='Next: Create database' class='button next'>
	</div>
	</form>";
// removed: <p><label for='SITE_URL'>Site url</label> <input type='text' name='SITE_URL' id='SITE_URL' class='user-input' value='{$config['SITE_URL']}'></p>

$template[2]= "%s " . form_hidden(3, "<input type='submit' class='button next' value='Next: write configuration'>");
$template[3]= "%s " . form_hidden(4, "<input type='submit' class='button next' value='Next: pretty url'>");
$template[4]= "%s <div class='actions'>%s<a href='{$AIKI_SITE_URL}' class='button'>Test my site!!</a></div>";

$steps = array (
    0=>"Required files",
	1=>"Setting",
    2=>"Create database",
	3=>"Config file",
    4=>"Pretty urls");



/*
 * Installer function library
 *
 ***********************************************************************/

function form_hidden ( $step , $buttons) {
	global $config;
	$form_hidden = "";
	foreach ( $config as $name => $value) {
		$form_hidden .= "\n<input type='hidden' name='$name' value='$value'>";
	}
	$form_hidden = "\n<div class='actions'><form method='post'>%s<input type='hidden' name='step' value='$step'>$form_hidden$buttons</form></div>\n";
	return $form_hidden;
}


function check_step($step) {
	global $AIKI_ROOT_DIR, $config;

	switch ($step){
		case 3:
		case 4:
			if ( !@mysql_connect ($config['db_host'],  $config['db_user'], $config['db_pass']) ) {
				return  "Error: no connection --{$config['db_pass']}--" ;
			} elseif ( !@mysql_selectdb ($config['db_name']) ){
				return  "Error: no database selected";
			}
			if ( $step==4 && !file_exists($AIKI_ROOT_DIR ."/config.php") ){
				$step=3;
			}

		case 1:
		default:
			if ( file_exists($AIKI_ROOT_DIR ."/config.php" )  && $step!=4 ) {
				return  "There is a existing configuration file<em>Please remove file to continue installation<br>".
				        "$AIKI_ROOT_DIR/config.php".
						"</em>";
			}

			$testFiles = array (
				"/sql/CreateTables.sql",
				"/sql/InsertDefaults.sql",
				"/sql/InsertVariable-in.sql",
				"/configs/htaccess.inc",
				"/configs/config.php");
			$message ="";
			foreach ( $testFiles as $name){
				$file = $AIKI_ROOT_DIR . $name ;
				if ( !file_exists($file) ){
					$message .= $file . "<br>";
					$step=0;
				}
			}
			if ($message!="") {
				$message ="Essential files missing:<em>$message</em>";
		    }
			return $message;
	}
}


function clean_url($url){
	$top= strpos( $url, "/assets/apps/installer");
	return ( $top ? substr($url,0,$top) : $url );
}


function send_data_by_email(){
	global $config, $AIKI_SITE_URL;
	
	if (!$config['ADMIN_EMAIL'] ||
	    !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $config['ADMIN_EMAIL'])){
			return false;
	}
	
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=utf-8\r\n";
	$headers .= "From: noreply@aikiframework.org\r\n";

	$message = "Hello {$config['ADMIN_FULLNAME']} your new Aiki installation is ready to be used <br>\n".
			   "Go to: " . $AIKI_SITE_URL . "/admin <br>\n".
			   "Username: {$config['ADMIN_USER']} <br>\n".
			   "Password: {$config['ADMIN_PASSWORD']}<br>\n".
			   " <br>\n".
			   "Have a nice day<br>\n";

	mail($config['ADMIN_EMAIL'],' Your new Aiki installation',$message,$headers);	
	
	echo "<div style='background:#fff;color:#000;'>$message</div>";
	return true;	
	
}


function sqls(){
	global $config, $AIKI_ROOT_DIR, $AIKI_SITE_URL ;

	$config["ADMIN_PASSWORD"]        = substr(md5(uniqid(rand(),true)),1,8);
	$config["ADMIN_PASSWORD_MD5_MD5"]= md5(md5($config["ADMIN_PASSWORD"]));

	$sql_create_tables   = @file_get_contents("$AIKI_ROOT_DIR/sql/CreateTables.sql");
	$sql_insert_defaults = @file_get_contents("$AIKI_ROOT_DIR/sql/InsertDefaults.sql");
	$sql_insert_variable = @file_get_contents("$AIKI_ROOT_DIR/sql/InsertVariable-in.sql");

    $replace = array ( 
		"@AIKI_SITE_URL_LEN@"=> strlen($AIKI_SITE_URL),
		"@AIKI_SITE_URL@"    => $AIKI_SITE_URL,
		"@PKG_DATA_DIR_LEN@" => strlen($AIKI_ROOT_DIR),
		"@PKG_DATA_DIR@"     => $AIKI_ROOT_DIR, 
		"@ADMIN_USER@"=> $config["ADMIN_USER"],
		"@ADMIN_NAME@"=> $config["ADMIN_FULLNAME"],
		"@ADMIN_PASS@"=> $config["ADMIN_PASSWORD_MD5_MD5"],
		"@ADMIN_MAIL@"=> $config["ADMIN_EMAIL"]);
	
	/* @TODO insert this variables.
		"@VERSION@"=> AIKI_VERSION;
		"@REVISION@"=> AIKI_REVISION;
		"@AUTHORS@"=> AIKI_AUTHORS;*/

	return $sql_create_tables . SQLS_DELIMITER . $sql_insert_defaults . SQLS_DELIMITER.  $sql_insert_variable ;
}


/*
 * Installer work starts here
 *
 ***********************************************************************/

// SET STEP
if ( isset($_REQUEST['step']) ) {
	$step = (int) $_REQUEST['step'];
	if ( $step<0 || $step> 4 ) {
		$step=1;
	}
} elseif ( isset($_POST["try_step_2"]) ) {
	$step=2;
} elseif ( isset($_POST["try_step_3"]) ) {
	$step=3;
} elseif ( isset($_POST["try_step_4"]) ) {	
	$step=4;
} else {
	$step=1;
}

$message  = "";
$aditional= ""; // for aditional buttons
$help     = ""; // help info.

$message = check_step($step);
if ($message ) {
	$step=0;
}

switch ( $step){
	case 0:
	case 1:
		break;  // only must echo template;

	case 2:
		$step=1;
		$test = isset($_POST["testDB"]);

		if ( !$config['db_host'] || !$config['db_user'] ) {
			$message = "<div class='error'>Please, fill all required fields</div>";
		} elseif ( !@mysql_connect ($config['db_host'],  $config['db_user'], $config['db_pass']) ) {
			$message = "<div class='error'>Error: no connection</div>";
		} elseif ( !@mysql_selectdb ($config['db_name']) ){
			$message = ( $test ?
							"<div class='ok'>Connection OK</div><div class='error'>no database name</div>":
							"<div class='error'>Error: can't select database</div>" );
		} else {
			if ( $test ) {
				$message = "<div class='ok'> Connection and database OK</div>";
			} else {
				$step=2;
				$message = "<div class='message'><p><strong>created tables</strong><br>";

				$errors=""	;
				foreach ( explode ( SQLS_DELIMITER, sqls() ) as $sql ){
					$table = "";
					if ( preg_match ( "/CREATE TABLE (IF NOT EXISTS )?`?([^\(`]*)/i", $sql, $table) ){
						$message .= $table[2] ;
						if  ( mysql_query($sql)  ) {
							$message .=  " Ok<br>";
						} else {
							$message .="<span class='error'>error</span><br>";
							$errors .= "<br>". mysql_error() ;
						}
					} else  {
						mysql_query($sql);
					}
				}

				$message .= "</p></div>";
				$userData = "Admin login: {$config['ADMIN_USER']}<br>Password: {$config['ADMIN_PASSWORD']}";
				$help    = "<div class='help'><strong>Please, annotate</strong> login and password. You will need them</div>";
				
				if ( send_data_by_email() ){
					$help .= "<div class='help'>Data had send to {$config['ADMIN_EMAIL']}</div>";
				}
				
				if ( $errors  ) {
					$message   = "<div class='ok'>$userData</div>".
								 "<div class='error'>Some errors during creating tables <em>(perhaps tables already exists)</em></div>"
								 . $message
								 . "<textarea class='file_dump'>$errors</textarea>";

					$aditional = "<input type='submit' name='try_step_2' value='Try again' class='button' >";
					$help  .= "<div class='help'>Delete all tables for new installtion, or push next for upgrading</div>";
				} else {
					$message = 	"<div class='ok'>All tables was created correctly<em>$userData</em></div>" . $message;
				}
				

			}
		}
		break;

	case 3:
		$config_file = file_get_contents("$AIKI_ROOT_DIR/configs/config.php");
		if ( false == $config_file ) {
			// file exists had been checked ..but can fails
			$message = "<div class='error'>FATAL ERROR: failed to read config template file".
			           "<em>Path $AIKI_ROOT_DIR/configs/config.php</em></div>";
			break;
		}

		$replace= array (
			"DB_TYPE"   => "\"{$config['db_type']}\"",
			"DB_NAME"   => "\"{$config['db_name']}\"",
			"DB_USER"   => "\"{$config['db_user']}\"",
			"DB_PASS"   => "\"{$config['db_pass']}\"",
			"DB_HOST"   => "\"{$config['db_host']}\"",
			"DB_ENCODE" => "\"{$config['db_encoding']}\"",
			"@AIKI_SITE_URL@"     => $AIKI_SITE_URL,
			"@AIKI_REWRITE_BASE@" => clean_url($_SERVER["REQUEST_URI"]) );
		$config_file = strtr($config_file, $replace);

		if ( @file_put_contents ( "$AIKI_ROOT_DIR/config.php", $config_file) ){
			$message="<div class='ok'>Config file created.</div>";
		} else {
			$aditional = "<input type='submit' name='try_step_3' value='Try again' class='button' >";
			$message=
				"<div class='error'>Aiki can't write config file: <em>Path: $AIKI_ROOT_DIR/config.php </em></div>".
				"<div class='message'><p>Please, copy this code, create file and paste.</p><textarea class='filedump'>". $config_file . "</textarea></div>";
			$help  = "<div class='help'>Check permission in directory</div>";
		}
		break;

	case 4:
		$htaccess_file = file_get_contents("$AIKI_ROOT_DIR/configs/htaccess.inc");
		if ( false == $htaccess_file ) {
			$message = "<div class='error'>FATAL ERROR: failed to read htaccess.inc file"-
			           "<em>Path $AIKI_ROOT_DIR/configs/config.php</em></div>";
			break;
		}

		$replace= array (	"@AIKI_REWRITE_BASE@" => clean_url($_SERVER["REQUEST_URI"]) );
		$htaccess_file = strtr( $htaccess_file, $replace);

		if ( @file_put_contents ( "$AIKI_ROOT_DIR/.htaccess", $htaccess_file) ){
			$message= "<div class='ok'>Installation finished <em>pretty urls are enabled</em></div>";
		} else {
			$aditional= "<input type='submit' name='try_step_4' value='Try again' class='button' >";
			$message=
				"<div class='error'>Aiki can't write .htaccess file: <em>Path: $AIKI_ROOT_DIR/.htaccess</em></div>".
				"<div class='message'><p>Please, copy this code, create file and paste.</p><textarea class='filedump'>". $htaccess_file . "</textarea></div>";
			$help  = "<div class='help'>Check permission in directory</div>";
		}
		break;

	default:
		$step=1;
}

// echo results.
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Aiki Framework Installer</title>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />

<link rel='stylesheet' href='./installer.css' type="text/css" media="all">

</style>

</head>


<body>
    <div id="page">
<?php


echo "<h1>Aiki installer<em><strong>" .
	( $step ? "Step $step of " . ( count($steps)-1)  : "Pre-installation check" ).
	"</strong> | " .
	$steps[$step]  ."</em></h1>";
echo sprintf($template[$step], $message, $aditional) . $help ;

?>
	</div>
</body>
</html>
