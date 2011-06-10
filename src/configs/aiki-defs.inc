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
 * @link        @PACKAGE_URL@
 * @category    Aiki
 * @package     Aiki
 * @version     @VERSION@.@REVISION@
 * @filesource
 * 
 * IMPORTANT
 * 
 * Some of the values for the define statements in this file are generated
 * automatically when the autoconf configure script is executed.
 * These are ment to be used as the default configuration. Please,
 * be aware that the configuration is expected change during run-time.
 */

/**
 * Aiki Framework Version
 */
define("AIKI_VERSION", "@VERSION@");
/**
 * Aiki Framework Revision
 */
define("AIKI_REVISION", "@REVISION@");
/**
 * At run-time, write config and database or use an install-time config and database
 * Do NOT put the following value (TRUE or FALSE) in quotes.
 */
define("ENABLE_RUNTIME_INSTALLER", @ENABLE_RUNTIME_INSTALLER@);
/**
 * Aiki Database Type
 */
define("DB_TYPE", "@DB_TYPE@");
/**
 * Aiki Database Name
 */
define("DB_NAME", "@DB_NAME@");
/**
 * Aiki Database User Name
 */
define("DB_USER", "@DB_USER@");
/**
 * Aiki Database Password
 */
define("DB_PASS", "@DB_PASS@");
/**
 * Aiki Database Host
 */
define("DB_HOST", "@DB_HOST@");
/**
 * Aiki Database Encoding
 */
define("DB_ENCODE", "@DB_ENCODE@");
/**
 * Aiki Site URL
 */
define("AIKI_SITE_URL", "@AIKI_SITE_URL@");
/**
 * Aiki Rewrite Base (for htaccess)
 */
define("AIKI_REWRITE_BASE", "@AIKI_REWRITE_BASE@");
/**
 * Aiki Log Directory
 */
define("AIKI_LOG_DIR", "@AIKI_LOG_DIR@");
/**
 * Aiki Log File Name
 */
define("AIKI_LOG_FILE", "@AIKI_LOG_FILE@");
/**
 * Aiki Host Profile File Name
 */
define("AIKI_LOG_PROFILE", "@AIKI_LOG_PROFILE@");
/**
 * Aiki Log Level
 */
define("AIKI_LOG_LEVEL", "@AIKI_LOG_LEVEL@");