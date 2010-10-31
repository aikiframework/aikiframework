<?php
/**
 * @see aiki-defs.php
 */
require_once("$system_folder/configs/aiki-defs.php");

$config = array();

$config["db_type"] = DB_TYPE;
$config["db_name"] = DB_NAME;
$config["db_user"] = DB_USER;
$config["db_pass"] = DB_PASS;
$config["db_host"] = DB_HOST;
$config["db_encoding"] = DB_ENCODE;
$config["db_use_mysql_set_charset"] = false;

$config["db_path"] = ""; //SQLite and sqlite PDO
$config["db_dsn"] = ""; //sqlite PDO

$config["db_cache_timeout"] = 24;
$config["cache_dir"] = "";//db cacheing
$config["enable_query_cache"] = false;

$config["html_tidy"] = false;
$config["tidy_compress"] = false;
$config["html_tidy_config"] = array(
 'indent'         => true,
 'output-xhtml' =>    true,
 'wrap' =>    '0',
);

$config["compress_output"] = false;

//widget cache
$config["widget_cache"] = false;
$config["widget_cache_dir"] = ""; //full path to widgets cache directory

$config["css_cache"] = true;
$config["css_cache_timeout"] = 24;
$config["css_cache_file"] = "";

//full html cache
$config["html_cache"] = false; //full path to full html cache directory
$config["cache_timeout"] = "86400"; //ms

$config["session_timeout"] = 7200; //ms
$config["allow_multiple_sessions"] = false;
$config["allow_guest_sessions"] = false;

$config["save_revision_history"] = false;

$config["register_errors"] = false;

$config["error_404"] = "<h1>404 Page Not Found</h1>

<p>This page is not found</p>
<p>Please visit <a href=\"".AIKI_SITE_URL."\">Home page</a> so you may find what you are looking for.</p>"; 

$config["debug"] = false;

?>