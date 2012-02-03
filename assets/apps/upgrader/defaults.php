<?php
/*
 * default text for AIKI UPGRADER
 *
 * Don't change this values. This file will be overwritten in a aiki upgrade.
 * If you need change this values, copy this file to siteDefaults.php. 
 *
 * Update po files, if you edit this values.
 * 
 * All literal must be surround by $t->t("LITERAL"). Please, split literals by line.
 *
 * Remember: this file is php.
 */
 
// title tag for web page
$UPGRADER_TITLE_TAG = $t->t("Aiki Framework Upgrader");
 
// title (h1) for web page
$UPGRADER_TITLE    = $t->t("Aiki Upgrader");
 
// Welcome text
$UPGRADER_WELCOME_TEXT =
	"<p>" .
	$t->t("This is aiki UPGRADER.")."<br>".
	$t->t("Please <strong>make a backup</strong> for all files and don't forget copy database.").
	"</p>";

// don't define UPGRADER_CSS if you want use aiki admin theme
// Don't add extension: .css or rtl.css will be added. 
// Include path.
// separate css files by ;
// $UPGRADER_CSS= "./../../theme/default/installer_upgrader;./mysite";
