<?php

/** Aiki Framework (PHP)
 * 
 * Some of the values for the define statements in this file are generated
 * automatically when the autoconf configure script is executed.
 * These are meant to be used as the default configuration. Please,
 * be aware that the configuration is expected change during run-time.
 * This resulting file name will be defs.php and is meant for ALL
 * types of the distribution packages. This file should only contain
 * constants that can be reasonably shared between all distributions.
 * This means that during configuration of a distribution, the values
 * can be set to generally support all the targeted platforms
 * as well as a specific platform.
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Aikilab http://www.aikilab.com
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        @PACKAGE_URL@
 * @category    Aiki
 * @package     Configs
 * @version     @VERSION@.@REVISION@
 * @filesource */

/* the following file should NOT exist
 * in a run-time installation distribution package */
if (file_exists("$AIKI_ROOT_DIR/configs/aiki-defs.php")) {
    /** @see aiki-defs.php */
    require_once("$AIKI_ROOT_DIR/configs/aiki-defs.php");
    define("AIKI_INSTALLED_BY_MAKE", TRUE);
}
else {
    define("AIKI_INSTALLED_BY_MAKE", FALSE);
}

// **** Begin Automake generated constants **** //

/** Aiki Framework Revision */
define("AIKI_REVISION", "@REVISION@");

/** Aiki Authors */
define("AIKI_AUTHORS", '@AUTHORS@');

/** Aiki Log Directory */
define("AIKI_LOG_DIR", "@AIKI_LOG_DIR@");

/** Aiki Log File Name */
define("AIKI_LOG_FILE", "@AIKI_LOG_FILE@");

/** Aiki Host Profile File Name */
define("AIKI_LOG_PROFILE", "@AIKI_LOG_PROFILE@");

/** Aiki Log Level */
define("AIKI_LOG_LEVEL", "@AIKI_LOG_LEVEL@");

/** Aiki directory that stores downloads and
 *  backup files used to revert from update */
define("AIKI_SAVE_DIR", "@AIKI_SAVE_DIR@");

/** Aiki directory that stores downloads.
 * This must be relative to the aiki save directory */
define("AIKI_DOWNLOAD_DIR", "@AIKI_DOWNLOAD_DIR@");

/** Aiki directory that stores backups.
 * This must be relative to the aiki save directory */
define("AIKI_BACKUP_DIR", "@AIKI_BACKUP_DIR@");

/** Aiki directory that stores updates.
 * This must be relative to the aiki save directory */
define("AIKI_UPDATE_DIR", "@AIKI_UPDATE_DIR@");

/** Whether suPHP is turned on or off. Default is off.
 * When --enable-update is given, then it is on. */
define("SUPHP_ENGINE", "@SUPHP_ENGINE@");

/** Aiki Random string should be different everytime
 * a distribution package is configured and built */
define("AIKI_RANDOM", "@AIKI_RANDOM@");

// **** Begin non-generated constants **** //

/** Aiki Update URL with update information and requirements */
define("AIKI_UPDATE_URL", "http://aikiframework.org/files/update");

/** Whether or not to check for update after admin login */
define("AIKI_UPDATE_CHECK", true);

/** Aiki Update URL to the update package excluding filename */
define("AIKI_UPDATE_PATH", "http://aikiframework.org/files/");

/** First part of the update package filename excluding version and extension */
define("AIKI_UPDATE_PREFIX", "aiki-src-");

/** Last part of the update package filename indicating file format */
define("AIKI_UPDATE_EXT", ".zip");

/** Last part of the sum filename indicating file format */
define("AIKI_SUM_EXT", ".shasum-256.txt");

/** Retry to download or validate the downloaded update package
 * this number of times if failed. Do NOT put the following value in quotes. */
define("AIKI_UPDATE_RETRY", 3);

/** Whether or not this update involves changes to the Aiki
 *  configuration (aiki_config table, config.php or .htaccess) 
 *  Do NOT put the following value (TRUE or FALSE) in quotes. */
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
