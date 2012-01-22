<?php
/*
 * default for AIKI INSTALLER
 *
 * You can change this values. 
 *
 * Update po files, if you edit this values.
 * 
 * In $INSTALLER_WELCOME_TEXT and $INSTALLER_REQUIREMENTS you can use
 * html tag, and all literal must be surround by $t->t("LITERAL"). Please, split literals by line.
 *
 * Remember: this file is php.
 */
 
// title tag for web page
$INSTALLER_TITLE_TAG = $t->t("Aiki Framework Installer");
 
// title (h1) for web page
$INSTALLER_TITLE    = $t->t("Aiki Installer");
 
// Welcome text
$INSTALLER_WELCOME_TEXT =
	"<p>" .
	$t->t("<strong>Aiki Framework</strong> is an open source high-performance Web Application Framework for rapid web application development using Open Standards.").
	"</p>";
 
// Requirements text
$INSTALLER_REQUIREMENTS_TEXT = "
	<h2>" . $t->t("Requirements") . "</h2>
	<p>"  . $t->t("Before we start you need the following:"). "</p>
	<ol>
		<li>" . $t->t("An empty database, with collation set to") . "<em>utf8_general_ci.</em></li>
		<li>" . $t->t("PHP 5.2 or above and Apache2"). "</li>
		<li>" . $t->t("mod_rewrite must be enabled inside apache2 httpd.conf"). "</li>
	</ol>";
