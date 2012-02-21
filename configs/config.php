<?php

/** Aiki Framework (PHP)
 * 
 * Global configuration options
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
 * @package     Configs
 * @filesource */


/**
 * These are needed in the web installer
 */
if (!defined("AIKI_REWRITE_BASE")) {
	/** Aiki Rewrite Base (for htaccess) if NOT defined */
    define("AIKI_REWRITE_BASE", "@AIKI_REWRITE_BASE@");
}
if (!defined("AIKI_SITE_URL")) {
    /** Aiki Site URL if NOT defined */
    define("AIKI_SITE_URL", "@AIKI_SITE_URL@");
}

/** @todo review how to replace the rest of these constants, but they 
          aren't breaking anything right now and can be overridden from
          config.php file. */

/** Aiki Log Directory */
if (!defined("AIKI_LOG_DIR"))
    define("AIKI_LOG_DIR", "@AIKI_LOG_DIR@");

/** Aiki Log File Name */
if (!defined("AIKI_LOG_FILE"))
    define("AIKI_LOG_FILE", "@AIKI_LOG_FILE@");

/** Aiki Host Profile File Name */
if (!defined("AIKI_LOG_PROFILE"))
    define("AIKI_LOG_PROFILE", "@AIKI_LOG_PROFILE@");

/** Aiki Log Level */
if (!defined("AIKI_LOG_LEVEL"))
    define("AIKI_LOG_LEVEL", "@AIKI_LOG_LEVEL@");

/** Aiki directory that stores downloads and
 *  backup files used to revert from update */
if (!defined("AIKI_SAVE_DIR"))
    define("AIKI_SAVE_DIR", "@AIKI_SAVE_DIR@");

/** Aiki directory that stores downloads.
 * This must be relative to the aiki save directory */
if (!defined("AIKI_DOWNLOAD_DIR"))
    define("AIKI_DOWNLOAD_DIR", "@AIKI_DOWNLOAD_DIR@");

/** Aiki directory that stores backups.
 * This must be relative to the aiki save directory */
if (!defined("AIKI_BACKUP_DIR"))
    define("AIKI_BACKUP_DIR", "@AIKI_BACKUP_DIR@");

/** Aiki directory that stores updates.
 * This must be relative to the aiki save directory */
if (!defined("AIKI_UPDATE_DIR"))
    define("AIKI_UPDATE_DIR", "@AIKI_UPDATE_DIR@");

/** Aiki Random string should be different everytime
 * a distribution package is configured and built */
if (!defined("AIKI_RANDOM"))
    define("AIKI_RANDOM", "@AIKI_RANDOM@");

// **** Begin non-generated constants **** //

/** Aiki Update URL with update information and requirements */
if (!defined("AIKI_UPDATE_URL"))
    define("AIKI_UPDATE_URL", "http://aikiframework.org/files/update");

/** Whether or not to check for update after admin login */
if (!defined("AIKI_UPDATE_CHECK"))
    define("AIKI_UPDATE_CHECK", true);

/** Aiki Update URL to the update package excluding filename */
if (!defined("AIKI_UPDATE_PATH"))
    define("AIKI_UPDATE_PATH", "http://aikiframework.org/files/");

/** First part of the update package filename excluding version and extension */
if (!defined("AIKI_UPDATE_PREFIX"))
    define("AIKI_UPDATE_PREFIX", "aiki-src-");

/** Last part of the update package filename indicating file format */
if (!defined("AIKI_UPDATE_EXT"))
    define("AIKI_UPDATE_EXT", ".zip");

/** Last part of the sum filename indicating file format */
if (!defined("AIKI_SUM_EXT"))
    define("AIKI_SUM_EXT", ".shasum-256.txt");

/** Retry to download or validate the downloaded update package
 * this number of times if failed. Do NOT put the following value in quotes. */
if (!defined("AIKI_UPDATE_RETRY"))
    define("AIKI_UPDATE_RETRY", 3);

/** Whether or not this update involves changes to the Aiki
 *  configuration (aiki_config table, config.php or .htaccess) 
 *  Do NOT put the following value (TRUE or FALSE) in quotes. */
if (!defined("AIKI_UPDATE_CONFIG"))
    define("AIKI_UPDATE_CONFIG", false);

/* the following error related constants
 * are built-in on newer versions of PHP */
if (!defined("E_RECOVERABLE_ERROR")) {
	/** Define E_RECOVERABLE_ERROR if not defined */
    define("E_RECOVERABLE_ERROR", 4096);
}
if (!defined("E_DEPRECATED")) {
    /** Define E_DEPRECATED if not defined */
    define("E_DEPRECATED", 8192);
}
if (!defined("E_USER_DEPRECATED")) {
    /** Define E_USER_DEPRECATED if not defined */
    define("E_USER_DEPRECATED", 16384);
}



/** Used to store global configuration options
 * @global array $config */
$config = array();

/** Set the database type such as mysql
 * @global string $config["db_type"] */
$config["db_type"] = DB_TYPE;

/** Set the database name
 * @global string $config["db_name"] */
$config["db_name"] = DB_NAME;

/** Set the database user name
 * @global string $config["db_user"] */
$config["db_user"] = DB_USER;

/** Set the database user password
 * @global string $config["db_pass"] */
$config["db_pass"] = DB_PASS;

/** Set the database host server
 * @global string $config["db_host"] */
$config["db_host"] = DB_HOST;

/** Set the database encoding such as utf8
 * @global string $config["db_encoding"] */
$config["db_encoding"] = DB_ENCODE;

/** Enable use of mysql set charset
 * @global bool $config["db_use_mysql_set_charset"] */
$config["db_use_mysql_set_charset"] = false;

/** Set the full path for SQLite and sqlite PDO
 * @global string $config["db_path"] */
$config["db_path"] = '';

/** sqlite PDO only
 * @global string $config["db_dsn"] */
$config["db_dsn"] = '';

/** Set time out for deleting db cached queries - in hours
 * @global integer $config["db_cache_timeout"] */
$config["db_cache_timeout"] = 24;

/** db caching directory
 * @global string $config["cache_dir"] */
$config["cache_dir"] = "cache";

/** if set to true will cache the results of sql queries to files
 * @global bool $config["enable_query_cache"] */
$config["enable_query_cache"] = false;

/** enable multi databases connections
 * @global bool $config["allow_multiple_databases"] */
$config["allow_multiple_databases"] = false;

/** use html tidy php extension to format the html output
 * @global bool $config["html_tidy"] */ 
$config["html_tidy"] = false;

/** enable html tidy compression
 * @global bool $config["tidy_compress"] */
$config["tidy_compress"] = false;

/** html tidy default configuration
 * @global array $config["html_tidy_config"] */
$config["html_tidy_config"] = array(
 'indent'       => true,
 'output-xhtml' => true,
 'wrap'         => '0',
);

/** Remove empty spaces and lines and have the whole html on one line
 * @global bool $config["compress_output"] */
$config["compress_output"] = false;

/** Cache each widget individually in its own file
 * @global bool $config["widget_cache"] */
$config["widget_cache"] = false;

/** Full path to widgets cache directory in case $config["widget_cache"] 
 * was true 
 * @global string $config["widget_cache_dir"] */
$config["widget_cache_dir"] = '';

/** Enable caching of css
 * @global bool $config["css_cache"] */
$config["css_cache"] = true;

/** CSS cache timeout
 * @global integer $config["css_cache_timeout"] */
$config["css_cache_timeout"] = 24;

/** CSS cache file name
 * @global string $config["css_cache_file"] */
$config["css_cache_file"] = "";

/** This will store each page in its own file.
 * full html cache with full path to cache
 * @global bool $config["html_cache"] */
$config["html_cache"] = false; 

/** Time out for pages cache, in milliseconds
 * @global integer $config["cache_timeout"] */
$config["cache_timeout"] = "86400";

/** Session lifetime before auto log out if no activities from the logged 
 * in user - in milliseconds
 * @global integer $config["session_timeout"] */
$config["session_timeout"] = 7200;

/** If true will allow same username and password to login from two different IPs
 * @global bool $config["allow_multiple_sessions"] */
$config["allow_multiple_sessions"] = false;

/** Register guests sessions for tracking how many users are currently 
 * visiting the site
 * @global bool $config["allow_guest_sessions"] */
$config["allow_guest_sessions"] = false;

/** Enable version control system this will store each change for any sql 
 * UPDATE command in aiki_revisions
 * @global bool $config["save_revision_history"] */
$config["save_revision_history"] = false;

/** Store information about not found pages in aiki_redirects so admin can 
 * later add redirects
 * @global bool $config["register_errors"] */
$config["register_errors"] = false;

/** Your default timezone. Before code had "America/Los_Angeles", should try
 * defaults first. If one has set this, then it overrides the system.
 * @global string $config["timezone"] */
$config["timezone"] = '';

/** Show admin widgets or not?
 * @global bool $config["admin_widgets_display"] */
$config["admin_widgets_display"] = false;
 
/** Enable debug mode
 * @global bool $config["debug"] */
$config["debug"] = false;

/** Set the aiki log directory
 * @global string $config["log_dir"] */
$config["log_dir"] = "";

/** Set the aiki log file name
 * @global string $config["log_file"] */
$config["log_file"] = "";

/** Set the aiki host profile name
 * @global string $config["log_profile"] */
$config["log_profile"] = "";

/** Set the aiki log level
 * @global string $config["log_level"] */
$config["log_level"] = "NONE";

?>
