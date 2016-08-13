<?php

/** Aiki Framework (PHP)
 *
 * BackupInterface is the interface all Backups must implement.
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

interface BackupInterface {

    /** Save a Backup
     * @param string $destination Directory to save the backup in
     * @param array $exclude Target files or directories to exclude
     * @throws AikiException */
    public function save($destination, Array $exclude = Array());

    /** Restore a Backup
     * @param string $source Directory where the backup is
     * @param string $destination Directory to restore the backup in
     * @param array $exclude Target files or directories to exclude
     * @throws AikiException */
    public function restore($source, $destination, Array $exclude = Array());
}