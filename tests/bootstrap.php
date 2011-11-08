<?php

/** Prepend to the include path for all unit tests.
 * Tests should always prefer the configured and built
 * src before the distributed source. */
set_include_path(
    __DIR__ . "/../build/src" . PATH_SEPARATOR .
    __DIR__ . "/../src" . PATH_SEPARATOR .
    get_include_path()
);