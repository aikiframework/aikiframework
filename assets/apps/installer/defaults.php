<?php
/*
 * default for AIKI INSTALLER
 *
 * You can change this values. 
 *
 * Update po files, if you edit this values.
 * 
 * In $INSTALLER_WELCOME_TEXT and $INSTALLER_REQUIREMENTS you can use
 * html tag, and all literal must be surround by _t(LITERTAL). Please, split literals by line.
 *
 * Remember: this file is php.
 */
 
// title tag for web page
$INSTALLER_TITLE_TAG="Aiki Framework Installer";
 
// title (h1) for web page
$INSTALLER_TITLE    ="Aiki Installer";
 
// Welcome text
$INSTALLER_WELCOME_TEXT="<p>
_t(<strong>Aiki Framework</strong> is an open source high-performance Web Application Framework for rapid web application development using Open Standards.)
</p>";
 
// Requirments text
$INSTALLER_REQUIREMENTS_TEXT="
<h2>_t(Requirements)</h2>
<p>_t(Before we start you need the following):</p>
<ol>
<li>_t(An empty database, with collation set to) <em>utf8_general_ci.</em></li>
<li>_t(PHP 5.2 or above and Apache2).</li>
<li>_t(mod_rewrite must be enabled inside apache2 httpd.conf)</li>
</ol>";
