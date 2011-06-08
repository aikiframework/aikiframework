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
 * @link        http://aikiframework.org
 * @category    Aiki
 * @package     Aiki
 * @filesource
 */

/**
 * @see aiki-defs.php
 */
if (file_exists("$system_folder/configs/aiki-defs.php")){
	require_once("$system_folder/configs/aiki-defs.php");
}

/**
 * Used to store global configuration options
 * @global array $config
 */
$config = array();

// set the database type such as mysql
$config["db_type"] = DB_TYPE;
// set the database name
$config["db_name"] = DB_NAME;
// set the database user name
$config["db_user"] = DB_USER;
// set the database user password
$config["db_pass"] = DB_PASS;
// set the database host server
$config["db_host"] = DB_HOST;
// set the database encoding such as utf8
$config["db_encoding"] = DB_ENCODE;
// enable use of mysql set charset
$config["db_use_mysql_set_charset"] = false;

// set the full path for SQLite and sqlite PDO
$config["db_path"] = "";

// sqlite PDO only
$config["db_dsn"] = "";

// set time out for deleting db cached queries - in hours
$config["db_cache_timeout"] = 24;

// db caching directory
$config["cache_dir"] = "cache";

// if set to true will cache the results of sql queries to files
$config["enable_query_cache"] = false;

// enable multi databases connections
$config["allow_multiple_databases"] = false;

// use html tidy php extension to format the html output
$config["html_tidy"] = false;
// enable html tidy compression
$config["tidy_compress"] = false;
// html tidy default configuration
$config["html_tidy_config"] = array(
 'indent'       => true,
 'output-xhtml' => true,
 'wrap'         => '0',
);

// remove empty spaces and lines and have the whole html on one line
$config["compress_output"] = false;

// cache each widget individually in its own file
$config["widget_cache"] = false;

// full path to widgets cache directory in case $config["widget_cache"] was true 
$config["widget_cache_dir"] = "";

// enable caching of css
$config["css_cache"] = true;
// css cache timeout
$config["css_cache_timeout"] = 24;
// css cache file name
$config["css_cache_file"] = "";

// full html cache
// full path to html cache directory
// this will store each page in its own file
$config["html_cache"] = false; 

// time out for pages cache - in milliseconds
$config["cache_timeout"] = "86400";

// session life time before auto log out 
// if no activities from the logged in user - in milliseconds
$config["session_timeout"] = 7200;

// if true will allow same username and password to login from two different IPs
$config["allow_multiple_sessions"] = false;

// register guests sessions
// for tracking how many users are currently visiting the site
$config["allow_guest_sessions"] = false;

// enable version control system
// this will store each change for any sql UPDATE command in aiki_revisions
$config["save_revision_history"] = false;

// store information about not found pages in aiki_redirects
// so admin can later add redirects
$config["register_errors"] = false;

// custom 404 error page
$config["error_404"] = "<h1>404 Page Not Found</h1>
<p>This page is not found</p>
<p>Please visit <a href=\"".AIKI_SITE_URL."\">Home page</a> so you may find what you are looking for.</p>"; 

// the default site URL
$config["url"] = AIKI_SITE_URL;

// show admin widgets or not?
$config['admin_widgets_display'] = false;
 
// enable debugging
$config["debug"] = false;

?>
