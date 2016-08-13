<?php

/** Aiki Framework (PHP)
 *
 * Archive utility library.
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
if (!defined("IN_AIKI")) {
    die("No direct script access allowed");
}

/** @see ArchiveInterface.php */
require_once("libs/archive/ArchiveInterface.php");

class Archive implements ArchiveInterface {

    /** Archive extender */
    protected $_extender = "AikiZipArchive";

    /** Extender constructor arguments */
    protected $_extenderArguments = Array();

    /** Constructs a new Archive */
    public function __construct() {

    }

    /** Compress something */
    public function compress() {
        $result = $this->getExtender()->compress();
        return $result;
    }

    /** Decompress an archive to a destination directory.
     * @param string $archive Archive file to decompress
     * @param string $destination Directory for decompressed contents
     * @return boolean $success Whether or not decompress succeeds */
    public function decompress($archive, $destination) {
        $result = $this->getExtender()->decompress($archive, $destination);
        return $result;
    }

    /** Validate an archive using a downloaded sum file. Expects the
     * accompanying sha256 sum file from http://aikiframework.org/files/
     * @param string $archive Archive file to validate
     * @param string $sum Sum file to validate the archive with
     * @return boolean $result Whether or not the archive is valid
     * @throws AikiException */
    public function validate($archive, $sum) {
        $result = false;
        if (extension_loaded("hash")) {
            /** @todo make the algorithm configurable */
            $newSum = hash_file("sha256", $archive);
            $contents = file_get_contents($sum);
            if ($contents) {
                // remove file name and new line from the contents
                $oldSum = preg_replace('/[ \t].+[\r\n]*/', '', $contents);
                $result = ( "$newSum" == "$oldSum" ? true : false );
            }
            else {
                throw new AikiException("Failed to get contents of " . $sum);
            }
        }
        else {
            throw new AikiException("Extension hash is not loaded.");
        }
        return $result;
    }

    /** Call extender methods
     * @param string $method  the method to call
     * @param array $arguments the arguments for this method
     * @return mixed Returns the function result, or FALSE on error */
    public function __call($method, $arguments) {
        $extender = $this->getExtender();
        if ( false === method_exists($extender, $method) ) {
            throw new AikiException("Failed to find method: $method");
        }
        $result = call_user_func_array(array($extender, $method), $arguments);
        return $result;
    }

    /** Returns the current extender, instantiating it if necessary
     * @return string */
    public function getExtender() {
        if ( $this->_extender instanceof ArchiveInterface ) {
            return $this->_extender;
        }
        $extender = $this->_extender;
        $arguments = $this->getExtenderArguments();
        if ( false === class_exists($extender) ) {
            require_once("libs/archive/" . $extender . ".php");
        }
        $this->_extender = new $extender($arguments);
        if ( false === $this->_extender instanceof ArchiveInterface ) {
            throw new AikiException("This $extender must implement ArchiveInterface");
        }
        return $this->_extender;
    }

    /** Sets Archive extender
     * @param  ArchiveInterface $extender Extender to use
     * @return Archive */
    public function setExtender($extender) {
        if ( $extender instanceof ArchiveInterface ) {
            $this->_extender = $extender;
            return $this;
        }
        if ( false === is_string($extender) ) {
            throw new AikiException("Invalid type: $extender");
        }
        $this->_extender = $extender;
        return $this;
    }

    /** Get extender arguments
     * @return array */
    public function getExtenderArguments() {
        return $this->_extenderArguments;
    }

    /** Set extender arguments
     * @param  array $arguments
     * @return void */
    public function setExtenderArguments(Array $arguments) {
        $this->_extenderArguments = $arguments;
    }
}