<?php

/** Aiki Framework (PHP)
 * 
 * All of the values for the define statements in this file are generated
 * automatically when the autoconf configure script is executed.
 * These are meant to be used as a default configuration. Please,
 * be aware that the configuration is expected change during run-time.
 * This resulting file name will be aiki-defs.php and is NOT meant for
 * the run-time installer distribution package. This file is included by
 * defs.php and should NOT contain constants that are defined therein.
 * This file is only intended to support the Automake installation distribution.
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

/** If this is NOT defined or set to TRUE, the run-time installer will run.
 * Do NOT put the following value (TRUE or FALSE) in quotes. */
define("ENABLE_RUNTIME_INSTALLER", @ENABLE_RUNTIME_INSTALLER@);

/** Aiki Database Type */
define("DB_TYPE", "@DB_TYPE@");

/** Aiki Database Name */
define("DB_NAME", "@DB_NAME@");

/** Aiki Database User Name */
define("DB_USER", "@DB_USER@");

/** Aiki Database Password */
define("DB_PASS", "@DB_PASS@");

/** Aiki Database Host */
define("DB_HOST", "@DB_HOST@");

/** Aiki Database Encoding */
define("DB_ENCODE", "@DB_ENCODE@");

/** Aiki Site URL */
define("AIKI_SITE_URL", "@AIKI_SITE_URL@");

/** Aiki Rewrite Base (for htaccess) */
define("AIKI_REWRITE_BASE", "@AIKI_REWRITE_BASE@");
