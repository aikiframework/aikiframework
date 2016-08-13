<?php

/** Aiki Framework (PHP)
 *
 * ArchiveInterface is the interface that all Archives must implement.
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Aikilab http://www.aikilab.com
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Library
 * @filesource */

// disable php script access
if(!defined("IN_AIKI")) { die("No direct script access allowed"); }

interface ArchiveInterface {
    /** Compress something */
    public function compress();

    /** Decompress an archive to a destination directory.
     * @param string $archive Archive file to decompress
     * @param string $destination Directory for decompressed contents
     * @return boolean $success Whether or not decompress succeeds */
    public function decompress($archive, $destination);
}