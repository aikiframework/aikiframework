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
 * @package     Aiki
 * @version     @VERSION@.@REVISION@
 * @filesource */

/* the following file should NOT exist
 * in a run-time installation distribution package */
if (file_exists("$AIKI_ROOT_DIR/configs/aiki-defs.php")) {
    /** @see aiki-defs.php */
    require_once("$AIKI_ROOT_DIR/configs/aiki-defs.php");
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

// **** Begin non-generated constants **** //

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