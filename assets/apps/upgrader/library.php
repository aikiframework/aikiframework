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
 * Upgrader function library
 *
 */


/** 
 *
 * construct html form inserting buttons, and next step.
 * 
 * @param $step next step
 * @param $buttons string with additional html controls.
 *
 * @return string containg complete form
 */



function form_hidden ( $step , $withForm = true) {
	global $t;
	$next  = $t->t("Next:");
	$cStep = $t->t( steps($step));
	return "\n<div class='actions'>".
		   ($withForm ? "\n<form method='post'>%s":"" ). 
	       "\n<input type='hidden' name='step' value='$step'>".
	       "\n<input type='submit' class='button next' value='$next $cStep'>".
	       ($withForm ? "\n</form>": "") .
	       "\n</div>\n";	
}


/** 
 *
 * construct select control for language select
 * 
 * @param array translations available
 * 
 * @return string containg comple html select or "" if not necesary
 */

function select_language (){
	global $t;

	$translations = $t->translations();
	if ( !is_array($translations) || count($translations)==0 ){
		return ""; 
	}
	
	$options = "<option value='en'>English</option>";	
	foreach ( $translations as $isoCode ){
		$options .= "<option value='$isoCode'>" . Util::iso639($isoCode) ."</option>\n";
	}	
	return 
		"<form id='f_language'><label for='language'>" . $t->t("Select language for installation") ."</label>\n" .
	    "<select name='language' id='language' class='user-input'>".	                
	    $options.
	    "</select><input type='submit' value='" . $t->t("Change") ."'></form>";	
}


function steps($step ){
	global $t;
	$steps = array(
		0=>"checking",
		"Welcome",
		"Autentification",
		"Upgrade database",
		"Upgrade Aiki Data" );
	
	if ( $step == -1 ){
		return (count($steps)-1);
	}
	return ( isset($steps[$step]) ?	$t->t($steps[$step]) : "");
}


function template($step) {
	global $t, $UPGRADER_WELCOME_TEXT;

	switch ( $step ) {
		case 0:
			return "<div class='error'>" . $t->t("Update canceled")."</div><div class='error'>%s</div>";
		
		case 1:
			return 
				"<div id='welcome'>$UPGRADER_WELCOME_TEXT</div>
				<div class='links'>
					<a href='#changelog'    class='toggle'>". $t->t("Changelog") ."</a>
					<a href='#license'      class='toggle'>". $t->t("License")   ."</a>
					<a href='#authors'      class='toggle'>". $t->t("Authors")   ."</a>
				</div>
				<div id='changelog'    class='toggle'>" . Util::get_changelog($revision) ."</div>
				<div id='license'      class='toggle'><pre>" . Util::get_license() ."</pre></div>
				<div id='authors'      class='toggle'><h3>". $t->t("Authors")."</h3>". Util::get_authors("list")."</div>".
				select_language() .
				form_hidden(2 );

		case 2:
			return "<span class='wrong'>%s</span><form method='post'>
		<p><label for='login'>". $t->t("Superuser login") ."</label><input type='text' name='login' id='login' class='user-input'></p>
		<p><label for='pass'>" . $t->t("Password")        ."</label><input type='password' name='password' id='password' class='user-input'></p>
		<p><label for='captcha'>" . $t->t("Security Captcha check")        ."</label>
		<img src='../captcha/captcha.php' class='captcha' alt='Captcha'>
		<input type='text' name='captcha' id='captcha' class='user-input'>
		</p>".
		form_hidden(3,false)."
		</form>";
		
		case 3:
			return "%s " . form_hidden(4);
			
		case 4:
			return "%s " . 
				sprintf("<div class='ok finished'>%s <em>%s</em>",
						$t->t("Upgrade has finished") ,
						$t->t("Thanks for using Aiki") );
	}			
}


/** 
 *
 * check step
 * 
 * @param by value step.
 * 
 * @return message or "". Correct step.
 */

function check_step(&$step) {
	global $t, $AIKI_ROOT_DIR, $db;
	switch ( $step) {		
		case 0:
		case 1:
		case 2:
			$lastRevision = util::get_last_revision();			
			if ( $lastRevision == 0){
				$step=0;
				return $t->t("Can't get last revision"). 
					"<br><em>". $t->t("Copy .bzr/branch/last-version to config dir") ."</em>";
			}	
			
			$revision = config("AIKI-REVISION",0);
			if ( $revision >= $lastRevision){
				$step=0;
				return $t->t("No upgrade is necesary.").
						"<br><em>". t("Installed revision:") . " $revision </em>";
			}			
			if ( $step==0){
				$step=1;
			}
			break;
			
		case 3:		  
		   		    
			$username = stripslashes($_REQUEST["login"] );
			$password = md5(md5(stripslashes($_REQUEST["password"] )));
			
			if ( md5($_REQUEST["captcha"]) != $_SESSION['captcha_key'] ){
				$step=2;
				return $t->t("Wrong captcha");
			}	
			
			$get_user = $db->get_row(
				"SELECT * FROM aiki_users".
				" WHERE username='$username' ".
				"  AND password='$password' ".
				"  AND usergroup=1 ".
				"  AND is_active=1" .		  
				" LIMIT 1");
								
			if (!$get_user) {
				$step=2;
				return $t->t("Wrong user name" );
			} 
			
			session_start(); // don't remove this line. IT'S NECESSARY			
			$_SESSION["updater_is_root"]=1;			
			return "";
		
		case 4:
		case 5:
			if ( !isset($_SESSION["updater_is_root"])){
				$step=2;	
			}							
    }
    			
}


function clean_url($url){
	$top= strpos( $url, "/assets/apps/upgrader");
	return ( $top ? substr($url,0,$top) ."/" : $url . "/");
}




function upgradeDB (){
	global $t, $AIKI_ROOT_DIR;

	require_once ("$AIKI_ROOT_DIR/libs/UpgradeDB.php");
	$upgradeDB = new UpgradeDB();

	$files = array (
		"$AIKI_ROOT_DIR/sql/CreateTables.sql",
		"$AIKI_ROOT_DIR/sql/Site.sql");

	$ret = "<strong>".$t->t("Database upgrading")."</strong>";
	
	// extract table(2), fields definition(3) and Engine(4) from a 
	// CREATE TABLE IF NOT EXISTS `table' (DEFINTIONs) ENGINE.charset;
	//  IF NOT EXISTS optional,   `are optional
	
	$pattern = '#CREATE\s+TABLE(\s+IF NOT EXISTS)?\s+`?([^ ]+)`?\s+\((.*)\)([^\);]*);#Uis';
						
	foreach ($files as $file ){
		if ( file_exists($file) && preg_match_all ( $pattern, @file_get_contents($file), $sqls ) ) {			
			foreach ( $sqls[2] as $i=>$table ){					
				$upgradeDB->upgrade_table($table, trim($sqls[3][$i])) ;				
				$ret  .= "<br>". sprintf( $t->t( "%s upgraded"),$table);
			}							
		} 
	}
	
	return $ret;
}


function upgradeAikiData (){	
	global $t, $db, $AIKI_ROOT_DIR;

	$replaces = array (
		"@VERSION@"  =>AIKI_VERSION,
		"@REVISION@" =>util::get_last_revision() );
	
	// Re-install data: remove & insert -------------------------------
	$files = array (
		"$AIKI_ROOT_DIR/sql/UpdateDefaults.sql"    => $t->t("Removing aiki core data"),
		"$AIKI_ROOT_DIR/sql/InsertDefaults.sql"    => $t->t("Inserting new aiki core data"),
		"$AIKI_ROOT_DIR/sql/UpdateDefaultsSite.sql"=> $t->t("Preparing upgrading site"),
		"$AIKI_ROOT_DIR/sql/InsertDefaultsSite.sql"=> $t->t("Upgrading site"));
			
	$ret = "<strong>". $t->t("Update Aiki & Site Data") . "</strong>";
	foreach ($files as $file => $message ){
		if ( file_exists($file) ) {
			$db->query ( strtr( @file_get_contents($file), $replaces));
			$ret .= "<br>$message";
		}		
	}
	
	// Manual upgrades by SQL statments -------------------------------
	$sqls = array();
	include_once "upgrades.php";
	// filter revision
	foreach ( $upgrades as $revision=>$sql ){
		if ( $revision > $REVISION ) {
			$sqls[$revision]= $sql;
		}
	}	
	if ( count($sqls)>0 ){
		ksort($sqls);
		foreach ($sqls as $revision=>$sql){
			$db->query($sql);
			$ret .= "<br>" . sprintf("%s updated", $revision );
		}
	}
	
	return $ret;
}	
