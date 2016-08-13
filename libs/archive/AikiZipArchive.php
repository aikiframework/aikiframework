<?php

/** Aiki Framework (PHP)
 *
 * AikiZipArchive utility library.
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

/** @see ArchiveInterface.php */
require_once("libs/archive/ArchiveInterface.php");

class AikiZipArchive implements ArchiveInterface {

    /** ZipArchive object utility library */
    protected $_ZipArchive = "";

    /** Constructs a new AikiZipArchive. This should
     * look like:
     * $zip = new AikiZipArchive(Array("ZipArchive" => new ZipArchive()));
     * @param array $deps The dependencies of the Updater.
     * @return void
     * @throws AikiException */
    public function __construct(Array $deps = NULL) {
        if (extension_loaded("zip")) {
            if (isset($deps)) {
                foreach ($deps as $key => $val) {
                    $dep = "_" . $key;
                    if (isset($this->$dep) and $val instanceof $key) {
                       $this->$dep = $val;
                    }
                    else {
                        throw new AikiException("Invalid dependency " . $key);
                    }
                }
            }
        }
        else {
            throw new AikiException("Extension zip is not loaded.");
        }
    }

    /** Compress something */
    public function compress() {

    }

    /** Decompress an archive to a destination directory.
     * @param string $archive Archive file to decompress
     * @param string $destination Directory for decompressed contents
     * @return boolean $success Whether or not decompress succeeds */
    public function decompress($archive, $destination) {
        $success = false;
        if ($this->_ZipArchive instanceof ZipArchive) {
            $success = $this->_ZipArchive->open($archive);
            if ($success) {
                $success = $this->_ZipArchive->extractTo($destination);
                if (false === $success) {
                    throw new AikiException(
                        "Failed to extract to " . $destination);
                }
                $this->_ZipArchive->close();
            }
            else {
                throw new AikiException("Failed to open archive " . $archive);
            }
        }
        else {
            throw new AikiException("Invalid ZipArchive instance.");
        }
        return $success;
    }
}