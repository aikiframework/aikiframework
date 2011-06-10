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

/**
 * Set the database type such as mysql
 * @global string $config["db_type"] 
 */
$config["db_type"] = DB_TYPE;
/**
 * Set the database name
 * @global string $config["db_name"] 
 */
$config["db_name"] = DB_NAME;
/** 
 * Set the database user name
 * @global string $config["db_user"] 
 */
$config["db_user"] = DB_USER;
/** 
 * Set the database user password
 * @global string $config["db_pass"] 
 */
$config["db_pass"] = DB_PASS;
/**
 * Set the database host server
 * @global string $config["db_host"] 
 */
$config["db_host"] = DB_HOST;
/**
 * Set the database encoding such as utf8
 * @global string $config["db_encoding"] 
 */
$config["db_encoding"] = DB_ENCODE;
/**
 * Enable use of mysql set charset
 * @global bool $config["db_use_mysql_set_charset"] 
 */
$config["db_use_mysql_set_charset"] = false;

/**
 * Set the full path for SQLite and sqlite PDO
 * @global string $config["db_path"] 
 */
$config["db_path"] = '';

/**
 * sqlite PDO only
 * @global string $config["db_dsn"] 
 */
$config["db_dsn"] = '';

/**
 * Set time out for deleting db cached queries - in hours
 * @global integer $config["db_cache_timeout"] 
 */
$config["db_cache_timeout"] = 24;

/** 
 * db caching directory
 * @global string $config["cache_dir"] 
 */
$config["cache_dir"] = "cache";

/** 
 * if set to true will cache the results of sql queries to files
 * @global bool $config["enable_query_cache"] 
 */
$config["enable_query_cache"] = false;

/**
 * enable multi databases connections
 * @global bool $config["allow_multiple_databases"] 
 */
$config["allow_multiple_databases"] = false;

/** 
 * use html tidy php extension to format the html output
 * @global bool $config["html_tidy"] 
 */ 
$config["html_tidy"] = false;
/**
 * enable html tidy compression
 * @global bool $config["tidy_compress"] 
 */
$config["tidy_compress"] = false;
/**
 * html tidy default configuration
 * @global array $config["html_tidy_config"] 
 */
$config["html_tidy_config"] = array(
 'indent'       => true,
 'output-xhtml' => true,
 'wrap'         => '0',
);

/**
 * Remove empty spaces and lines and have the whole html on one line
 * @global bool $config["compress_output"] 
 */
$config["compress_output"] = false;

/**
 * Cache each widget individually in its own file
 * @global bool $config["widget_cache"] 
 */
$config["widget_cache"] = false;

/** 
 * Full path to widgets cache directory in case $config["widget_cache"] 
 * was true 
 * @global string $config["widget_cache_dir"] 
 */
$config["widget_cache_dir"] = '';

/** 
 * Enable caching of css
 * @global bool $config["css_cache"] 
 */
$config["css_cache"] = true;
/**
 * CSS cache timeout
 * @global integer $config["css_cache_timeout"] 
 */
$config["css_cache_timeout"] = 24;
/**
 * CSS cache file name
 * @global string $config["css_cache_file"] 
 */
$config["css_cache_file"] = "";

/** 
 * This will store each page in its own file.
 * full html cache with full path to cache
 * @global bool $config["html_cache"] 
 */
$config["html_cache"] = false; 

/** 
 * Time out for pages cache, in milliseconds
 * @global integer $config["cache_timeout"] 
 */
$config["cache_timeout"] = "86400";

/**
 * Session lifetime before auto log out if no activities from the logged 
 * in user - in milliseconds
 * @global integer $config["session_timeout"] 
 */
$config["session_timeout"] = 7200;

/** 
 * If true will allow same username and password to login from two different IPs
 * @global bool $config["allow_multiple_sessions"] 
 */
$config["allow_multiple_sessions"] = false;

/**
 * Register guests sessions for tracking how many users are currently 
 * visiting the site
 * @global bool $config["allow_guest_sessions"] 
 */
$config["allow_guest_sessions"] = false;

/**
 * Enable version control system this will store each change for any sql 
 * UPDATE command in aiki_revisions
 * @global bool $config["save_revision_history"] 
 */
$config["save_revision_history"] = false;

/** 
 * Store information about not found pages in aiki_redirects so admin can 
 * later add redirects
 * @global bool $config["register_errors"]
 */
$config["register_errors"] = false;

/**
 * Custom 404 error page
 * @global string $config["error_404"] 
 */
$config["error_404"] = "<h1>404 Page Not Found</h1>
<p>This page is not found</p>
<p>Please visit <a href=\"".AIKI_SITE_URL."\">Home page</a> so you may find what you are looking for.</p>"; 

/**
 * Your default timezone. Before code had "America/Los_Angeles", should try
 * defaults first. If one has set this, then it overrides the system.
 * @global string $config["timezone"]
 */
$config["timezone"] = '';

/**
 * The default site URL
 * @global string $config["url"] 
 */
$config["url"] = AIKI_SITE_URL;

/**
 * Show admin widgets or not?
 * @global bool $config["admin_widgets_display"] 
 */
$config["admin_widgets_display"] = false;
 
/**
 * Enable debug mode
 * @global bool $config["debug"] 
 */
$config["debug"] = false;

/**
 * Set the aiki log directory
 * @global string $config["log_dir"]
 */
$config["log_dir"] = AIKI_LOG_DIR;

/**
 * Set the aiki log file name
 * @global string $config["log_file"]
 */
$config["log_file"] = AIKI_LOG_FILE;

/**
 * Set the aiki host profile name
 * @global string $config["log_profile"]
 */
$config["log_profile"] = AIKI_LOG_PROFILE;

/**
 * Set the aiki log level
 * @global string $config["log_level"]
 */
$config["log_level"] = AIKI_LOG_LEVEL;

?>
