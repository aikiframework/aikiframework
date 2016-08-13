<?php

/** Prepend to the include path for all unit tests.
 * Tests should always prefer the configured and built
 * src before the distributed source.
 *
 * Add like the following if need in other folders:
 *
 * __DIR__ . "/../build/src" . PATH_SEPARATOR .
 */
set_include_path(
    __DIR__ . "/.." . PATH_SEPARATOR .
    get_include_path()
);

/** define this for script access */
define("IN_AIKI", TRUE);


$AIKI_ROOT_DIR = realpath(dirname(__FILE__)) . '/..';
// echo $AIKI_ROOT_DIR;
