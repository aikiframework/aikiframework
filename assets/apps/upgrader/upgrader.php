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
 * @category    Aiki apps
 * @package     installer
 * @filesource
 *
 * 
 */


 /* 
  * How works
  * =========
  * Upgrader dfines these steps necesary during upgradeprocess.
  * 
  * Steps:
  * 0 Checks if neccesary a upgrade is necesary
  * 1 Welcome
  * 2 request admin password.
  * 3 Upgrade DB
  * 4 Upgrade AIKI data
  *
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

session_start(); 
if ( !defined('IN_AIKI') ) {
	// upgrader can be called directly.
	include ("../../../bootstrap.php");
}

require_once ( "$AIKI_ROOT_DIR/config.php");
require_once ( "$AIKI_ROOT_DIR/libs/Util.php");
require_once ( "library.php");

// initiate translation system
include_once("TranslateUsingPo.php");
$t = new TranslateUsingPo("en", "language",false);
$t->addDomain("upgrader","languages");

// read titles and welcome text, creating global vars $UPGRADER_TITLE_TAG,
// $UPGRADER_TITLE, $UPGRADER_WELCOME_TEXT
include_once("defaults.php"); 
if ( file_exists("siteDefaults.php") ){
	include_once("siteDefaults.php"); 
}


/*
 * Installer work starts here
 *
 ***********************************************************************/

// SET STEP
$step = (int) $_REQUEST['step'];
if ( $step<0 || $step > steps(-1) ) {
	$step=0;
}

$aditional= ""; // for aditional buttons
$help     = ""; // help info.
$message  = check_step($step);

/*
 * ACTION FOR EACH STEP
 *
 **************************************************************/
$javascripts="";
switch ( $step){
	
	case 1: // welcome, language
		$javascripts = <<<JAVASCRIPT
		<script src="../../javascript/jquery/jquery-1.4.2.min.js"></script>
		<script>
jQuery(document).ready ( function() {
	  jQuery('#license,#authors').hide();
      jQuery('[href=#changelog]').addClass("active");
	  		
	  jQuery('a.toggle').click ( function() { 
			jQuery('div.toggle').hide(); 
			div = jQuery(this).attr('href');	
			jQuery(div).show(0);
			jQuery('a.toggle').removeClass("active");
			jQuery(this).addClass("active");	
			 } );	  
	});	
		</script>
		
JAVASCRIPT;
		
	case 0: // pre-installation check
	case 2: // ask login & password
		break;  // only must echo template;

	case 3: $message= upgradeDB()      ; break;
	case 4: $message= upgradeAikiData(); break;

	default:
		$step=1;
}


// internationalization
$language      = $t->translateTo();
$css           = $t->t("upgrader.css");
$text_direction= $t->t("dir='ltr'");
// note: which css to use, and text direction can be set in .po file

// insert values and results in html template
$stepOf = sprintf( $t->t("Step %d of %d"), $step, steps(-1) ) ;
$cSteps = steps($step);
$result = sprintf(template($step), $message, $aditional.$help) ;

echo <<< HTML
<!DOCTYPE HTML>
<html lang="{$language}" {$text_direction}>
<head>
	<title>{$UPGRADER_TITLE_TAG}</title>
	<meta charset='utf-8' >
	<link rel='stylesheet' href='./{$css}' type="text/css" media="all">
	{$javascripts}
</head>

<body>
    <div id="page">
		<h1>{$UPGRADER_TITLE}<em><strong>{$stepOf}</strong> | {$cSteps} </em></strong></h1>
		$result		
	</div>
</body>
</html>    
HTML;
?>
