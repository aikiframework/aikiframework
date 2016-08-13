<?php

/** Aiki Framework (PHP)
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
 * @package     Admin
 * @filesource */

/** @see bootstrap.php */
require_once("../../../../bootstrap.php");

/** @see Archive.php */
require_once("$AIKI_ROOT_DIR/libs/Backup.php");

/** @see File.php */
require_once("$AIKI_ROOT_DIR/libs/File.php");

// membership and log should be instantiated from bootstrap
if ($membership->permissions != "SystemGOD") {
    $log->message("Invalid permission " . $membership->permissions, Log::WARN);
    die();
}

$destination = (AIKI_INSTALLED_BY_MAKE ?
    AIKI_SAVE_DIR . "/" . AIKI_BACKUP_DIR . "/aiki-" . AIKI_VERSION . "." . AIKI_REVISION:
    "$AIKI_ROOT_DIR/" . AIKI_SAVE_DIR . "/" . AIKI_BACKUP_DIR . "/aiki-" . AIKI_VERSION . "." . AIKI_REVISION);
$success = "false";
$arc = "";

if (array_key_exists("archive", $_GET)
    and isset($_GET["archive"])) {

    $arc = $_GET["archive"];
    try {
        $backup = new Backup(Array(
                    "FileBackup" => new FileBackup(
                        Array("File" => new File($AIKI_ROOT_DIR))),
                    "DatabaseBackup" => new DatabaseBackup($db)));
        $backup->save($destination, Array(AIKI_SAVE_DIR));
        $success = true;
    }
    catch (AikiException $e) {
        $log->exception($e);
    }
}
else {
    $log->message("Failed to get data", Log::ERROR);
}
$callScript = '<script type="text/javascript" id="update-script">' .
    'updateOverwrite(' . $success . ');</script>';

echo $callScript;
