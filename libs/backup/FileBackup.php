<?php

/** Aiki Framework (PHP)
 *
 * FileBackup utility library.
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
if(!defined("IN_AIKI")) {
    die("No direct script access allowed");
}

/** @see BackupInterface.php */
require_once("libs/backup/BackupInterface.php");

/** @see File.php */
require_once("libs/File.php");

class FileBackup implements BackupInterface {

    /** File object utility library */
    protected $_File = "";

    /** Constructs a new FileBackup. This should
     * look like: new FileBackup(Array("File" => new File()));
     * @param array $deps The dependencies of the FileBackup.
     * @return void
     * @throws AikiException */
    public function __construct(Array $deps = NULL) {
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

    /** Restore a Backup
     * @param string $source Directory where the backup is
     * @param string $destination Directory to restore the backup in
     * @param array $exclude Target files or directories to exclude
     * @throws AikiException */
    public function restore($source, $destination, Array $exclude = Array() ) {

    }

    /** Save a Backup
     * @param string $destination Directory to save the backup in
     * @param array $exclude Target files or directories to exclude
     * @throws AikiException */
    public function save($destination, Array $exclude = Array()) {
        if ($this->_File instanceof File) {
            $this->_File->makeDir($destination);
            $target = $this->_File->getRootDir();
            $this->_File->recurseCopy($target, "$destination/", $exclude);
        }
        else {
            throw new AikiException("Invalid File instance.");
        }
    }
}