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
 * @package     installer
 * @filesource
 *
 *
 * @TODO use ezSQL,
 * @TODO VERSION, REVISION,
 * @TODO AIKI_LOG_DIR, AIKI_LOG_FILE,AIKI_LOG_PROFILE, AIKI_LOG_LEVEL;
 *
 */


 /* 
  * How works
  * =========
  * installer defines the steps necesary during install process.
  * 
  * Steps:
  * 0 Checks if neccesary file exists, and there is not a previous config.php file,
  * 1 select language for installation
  * 2 Ask for setting
  * 3 Create database. If can't do, go  step 1.
  * 4 Create config. If can't do, show config file.
  * 5 Create htaccess. If can't do, show htaccess and continue.
  * 
  * Each steps has associate a template (see $template array), a check 
  * (see check_step function ) and action
  * 
  * how to translate
  * =================
  * Copy language/en.pot file to your_language_iso.po. 
  * For example de.po, es.po, fr.po
  * Translate this file with a po editor (poedit for example).
  * 
  * If you language need diferents style o text direction
  * translate terms "dir='ltr'" and "installer.css"
  * 
  * how customize
  * ================
  * Edit default.php
  * 
  */


include_once ("library.php");

// initiate translation system
include_once("TranslateUsingPo.php");
$t = new TranslateUsingPo("en", "language",false);
$t->addDomain("installer","languages");

// construct the select control to choose language
$selectLanguage = "";
$translations = $t->translations();
if ( is_array($translations) && count($translations)>0 ){
	$options = "<option value='en'>English</option>";
	
	foreach ( $translations as $isoCode ){
		$options .= "<option value='$isoCode'>" . iso639($isoCode) ."</option>\n";
	}	
	$selectLanguage= 
		"<form id='f_language'><label for='language'>_t(Select language for installation)</label>\n" .
	    "<select name='language' id='language' class='user-input'>".	                
	    $options.
	    "</select><input type='submit' value='_t(Change)'></form>";
} 


/* 
 * @TODO this must manually udpated..
 */

if ( !defined("AIKI_VERSION") ) {
	define(AIKI_VERSION,"0.8.24");
}

if ( !defined("AIKI_REVISION") ) {
	define(AIKI_REVISION,"+1019");
}

$AIKI_ROOT_DIR = realpath( dirname(__FILE__ ). "/../../..");
$AIKI_AUTHORS  = @file_get_contents("../../../AUTHORS") ;
$AIKI_SITE_URL = clean_url("http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]) ;

// Variables necesary for installation
$config = array(
    "db_type"        => "mysql",
	"db_host"        => "localhost",
	"db_name"        => "aiki",
	"db_user"        => "",
	"db_pass"        => "",
	"db_encoding"    => "UTF-8",
	"language"       => "en",
	"ADMIN_EMAIL"    => "",
	"ADMIN_USER"     => "admin",
	"ADMIN_FULLNAME" => "adminstrator");

// request via $POST all config var
foreach ( $config as $key => $value) {
    if ( isset( $_REQUEST[$key] ) &&  $_REQUEST[$key] ) {
		$config[$key] = addslashes($_REQUEST[$key]);
	}
}



/*
 * 
 * templates for each step
 * 
 ***************************************************************/

// read titles and welcome text, creating global var $INSTALLER_TITLE_TAG,
// $INSTALLER_TITLE, $INSTALLER_WELCOME_TEXT, $INSTALLER_REQUIREMENTS_TEXT,
include_once("defaults.php"); 


$template[0]="<div class='error'>_t(Installation aborted)</div><div class='error'>%s</div>";

$template[1]=
	"<div id='welcome'>$INSTALLER_WELCOME_TEXT</div>
    <div id='requirements'>$INSTALLER_REQUIREMENTS_TEXT</div>".
	$selectLanguage .
	form_hidden(2, "<input type='submit' class='button next' value='_t(Next): _t(Settings)'>");

$template[2]= "%s<form method='post'>
	<fieldset class='db'><legend>_t(Database)</legend>
	<p><label for='db_type'>SQL</label>" . select_db_type($config['db_type']) . "<span class='required'>*</span></p>
	<p><label for='db_host'>_t(Host)</label><input type='text' name='db_host' id='db_host' class='user-input' value='{$config['db_host']}'><span class='required'>*</span></p>
	<p><label for='db_name'>_t(Database name)</label><input type='text' name='db_name' id='db_name' class='user-input' value='{$config['db_name']}'><span class='required'>*</span></p>
	<p><label for='db_user'>_t(User)</label><input type='text' name='db_user' id='db_user' class='user-input' value='{$config['db_user']}'><span class='required'>*</span></p>
	<p><label for='db_pass'>_t(Password)</label><input type='password' name='db_pass' id='db_pass' class='user-input' value='{$config['db_pass']}'><span class='required'>*</span></p>
	<p><label for='db_encoding'>_t(Encoding)</label><input type='text' name='db_encoding' id='db_encoding' class='user-input' value='{$config['db_encoding']}'><span class='required'>*</span></p>
	</fieldset>

    <fieldset class='other'><legend>_t(Admin user / Site)</legend>
    <p><label for='ADMIN_USER'>_t(Login)</label><input type='text' name='ADMIN_USER'  id='ADMIN_USER'  class='user-input' value='{$config['ADMIN_USER']}'></p>
	<p><label for='ADMIN_FULLNAME'>_t(Full name)</label> <input type='text' name='SITE_TITLE'  id='SITE_TITLE'  class='user-input' value='{$config['ADMIN_FULLNAME']}'></p>
	<p><label for='ADMIN_EMAIL'>_t(Email)</label> <input type='text' name='ADMIN_EMAIL' id='ADMIN_EMAIL' class='user-input' value='{$config['ADMIN_EMAIL']}'></p>
	<p class='note'>_t(Aiki will send login and password using this email).</p>    
    </fieldset>

    <input type='hidden' name='step' value='3'>
	<p class='required'><span class='required'>*</span> _t(Required Fields)</p>

    <div class='actions'>
    <input type='submit' value='_t(Test connection)' class='button' name='testDB'>
	<input type='submit' value='_t(Next): _t(Create database)' class='button next'>
	</div>
	</form>";
// removed: <p><label for='SITE_URL'>Site url</label> <input type='text' name='SITE_URL' id='SITE_URL' class='user-input' value='{$config['SITE_URL']}'></p>

$template[3]= "%s " . form_hidden(4, "<input type='submit' class='button next' value='_t(Next): _t(Write configuration)'>");
$template[4]= "%s " . form_hidden(5, "<input type='submit' class='button next' value='_t(Next): _t(Pretty urls)'>");
$template[5]= "%s <div class='actions'>%s</div>";


// Description of steps
$steps = array (
    0=>"Pre-installation check",
    1=>"Requirements & language",
	2=>"Settings",
    3=>"Create database",
	4=>"Config file",
    5=>"Pretty urls");



/*
 * Installer work starts here
 *
 ***********************************************************************/

// SET STEP
if ( isset($_REQUEST['step']) ) {
	$step = (int) $_REQUEST['step'];
	if ( $step<0 || $step> 5 ) {
		$step=2;
	}
} elseif ( isset($_POST["try_step_2"]) ) {
	$step=2;
} elseif ( isset($_POST["try_step_3"]) ) {
	$step=3;
} elseif ( isset($_POST["try_step_4"]) ) {	
	$step=4;
} elseif ( isset($_POST["try_step_5"]) ) {		
	$step=5;
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

/*
 * ACTION FOR EACH STEP
 *
 **************************************************************/

switch ( $step){
	case 0: // pre-installation check
	case 1: // welcome, language
	case 2: // settings
		break;  // only must echo template;

	case 3: // create database
		$step=2;
		$test = isset($_POST["testDB"]);

		if ( !$config['db_host'] || !$config['db_user'] ) {
			$message = "<div class='error'>_t(Please, fill all required fields)</div>";
		} elseif ( !@mysql_connect ($config['db_host'],  $config['db_user'], $config['db_pass']) ) {
			$message = "<div class='error'>_t(Error: no connection)</div>";
		} elseif ( !@mysql_selectdb ($config['db_name']) ){
			$message = ( $test ?
							"<div class='ok'>_t(Connection OK)</div><div class='error'>_t(no database name)</div>":
							"<div class='error'>_t(Error: can't select database)</div>" );
		} else {
			if ( $test ) {
				$message = "<div class='ok'> _t(Connection and database OK)</div>";
			} else {
				$step=3;
				$message = "<div class='message'><p><strong>_t(created tables)</strong><br><div id='file-list'>";

				$errors="";
				$cont  =0;
				foreach ( sqls() as $sql ){
					if ( trim($sql) =="" ){
						continue;
					}
					$table = "";
					if ( preg_match ( "/CREATE TABLE (IF NOT EXISTS )?`?([^\(`]*)/i", $sql, $table) ){
						if ( $cont % 10 == 0 ){
							$message .= ( $cont ? "</div>":"") ."<div class='col'>";
						}
						$message .= $table[2] ;
						if  ( mysql_query($sql)  ) {
							$message .=  " <strong>Ok</strong><br>";
						} else {
							$message .="<span class='error'>_t(error)</span><br>";
							$errors .= "<br>". mysql_error() ;
						}
						$cont++;
					} else {
						mysql_query($sql);
					}
				}

				$message .= ($cont ? "</div>" : "") . "</div></div>";
				$userData = "_t(Admin login): {$config['ADMIN_USER']}<br>_t(Password): {$config['ADMIN_PASSWORD']}";
				$help    = "<div class='help'>_t(<strong>Please, annotate</strong> login and password. You will need them)</div>";
				
				if ( send_data_by_email() ){
					$help .= "<div class='help'>_t(Data had send to ){$config['ADMIN_EMAIL']}</div>";
				}
				
				if ( $errors  ) {
					$message   = "<div class='ok'>$userData</div>".
								 "<div class='error'>_t(Some errors during creating tables <em>,perhaps tables already exists</em>)</div>"
								 . $message
								 . "<textarea class='file_dump'>$errors</textarea>";

					$aditional = "<input type='submit' name='try_step_2' value='_t(Try again)' class='button' >";
					$help  .= "<div class='help'>_t(Delete all tables for new installtion, or push next for upgrading)</div>";
				} else {
					$message = 	"<div class='ok'>_t(All tables was created correctly)<em>$userData</em></div>" . $message;
				}
				

			}
		}
		break;

	case 4:
	    // STEP 4 Write config.php -------------------------------------------------
		$config_file = file_get_contents("$AIKI_ROOT_DIR/configs/config.php");
		if ( false == $config_file ) {
			// file exists had been checked ..but can fails
			$message = "<div class='error'>t(FATAL ERROR: failed to read config template file)".
			           "<em>_t(Path) $AIKI_ROOT_DIR/configs/config.php</em></div>";
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
			$message="<div class='ok'>_t(Config file created).</div>";
		} else {
			$aditional = "<input type='submit' name='try_step_3' value='_t(Try again)' class='button' >";
			$message=
				"<div class='error'>_t(can't write config file:) <em>_t(Path): $AIKI_ROOT_DIR/config.php </em></div>".
				"<div class='message'><p>_t(Please, copy this code, create file and paste).</p><textarea class='filedump'>". $config_file . "</textarea></div>";
			$help  = "<div class='help'>_t(Check permission in directory)</div>";
		}
		break;

	case 5:
		// STEP 5 Write htaccess -------------------------------------------------
		$htaccess_file = get_new_htaccess($AIKI_ROOT_DIR);
		
		if ( file_exists($AIKI_ROOT_DIR ."/.htaccess" ) ) {			
			if ( file_get_contents($AIKI_ROOT_DIR ."/.htaccess") == $htaccess_file ){
				$message= "<div class='ok'>_t(Installation finished <em>pretty urls are enabled with previous .htaccess</em>)</div>";
				$aditional="<a href='{$AIKI_SITE_URL}' class='button'>_t(Test my site!!)</a>";
			} else {			
				$aditional= "<input type='submit' name='try_step_4' value='_t(Try/Check again)' class='button' >";
				$message = "<div class='error'>_t(There is a existing .htaccess file)</div>".
				  		   "<div class='message'><p>_t(Please, remove file or rewrite file with this code):</p><textarea class='filedump'>". $htaccess_file . "</textarea></div>";
			}		
		
			break;	
			           			           
		} else {								
			if ( !$htaccess_file ) {
				$message = "<div class='error'>_t(FATAL ERROR: failed to read htaccess.inc file)"-
						   "<em>_t(Path) $AIKI_ROOT_DIR/configs/config.php</em></div>";
				break;
			}
			
			if ( @file_put_contents ( "$AIKI_ROOT_DIR/.htaccess", $htaccess_file) ){
				$message= "<div class='ok'>_t(Installation finished <em>pretty urls are enabled</em>)</div>";
				$aditional="<a href='{$AIKI_SITE_URL}' class='button'>_t(Test my site!!)</a>";
			} else {	
				$aditional= "<input type='submit' name='try_step_4' value='_t(Try again)' class='button' >";
				$message=
					"<div class='error'>_t(Aiki can't write .htaccess file): <em>_t(Path): $AIKI_ROOT_DIR/.htaccess</em></div>".
					"<div class='message'><p>_t(Please, copy this code, create file and paste).</p><textarea class='filedump'>". $htaccess_file . "</textarea></div>";
				$help  = "<div class='help'>_t(Check permission in directory)</div>";
			}
		}	
		break;

	default:
		$step=1;
}


// internationalization
$language= $t->translateTo();
$css           = $t->t("installer.css");
$text_direction= $t->t("dir='ltr'");
// note: which css to use, and text direction can be set in .po file

// insert values and result in html template
$stepOf = sprintf( $t->t("Step %d of %d"), $step, count($steps)-1) ;
$result = sprintf($template[$step], $message, $aditional.$help) ;

$html = <<< HTML
<!DOCTYPE HTML>
<html lang="{$language}" {$text_direction}>
<head>
	<title>_t({$INSTALLER_TITLE_TAG})</title>
	<meta charset='utf-8' >
	<link rel='stylesheet' href='./{$css}' type="text/css" media="all">
</head>

<body>
    <div id="page">
		<h1>_t({$INSTALLER_TITLE})<em><strong>{$stepOf}</strong> | _t({$steps[$step]})</em></strong></h1>
		$result		
	</div>
</body>
</html>    
HTML;

// make translation  and echo
echo preg_replace_callback (
	"/_t\(([^\)]*)\)/",  // all _t(literals)
	array($t,"t"),       // will be translated by $t->t()
	$html );
?>
