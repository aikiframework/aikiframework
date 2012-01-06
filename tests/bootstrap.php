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
    get_include_path()
);
