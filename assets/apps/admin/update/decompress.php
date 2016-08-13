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
require_once("$AIKI_ROOT_DIR/libs/Archive.php");

// membership and log should be instantiated from bootstrap
if ($membership->permissions != "SystemGOD") {
    $log->message("Invalid permission " . $membership->permissions, Log::WARN);
    die();
}

$destination = (AIKI_INSTALLED_BY_MAKE ?
    AIKI_SAVE_DIR . "/" . AIKI_DOWNLOAD_DIR :
    "$AIKI_ROOT_DIR/" . AIKI_SAVE_DIR . "/" . AIKI_DOWNLOAD_DIR);
$success = "false";
$arc = "";

if (array_key_exists("archive", $_GET)
    and isset($_GET["archive"])) {

    $arc = $_GET["archive"];
    try {
        $archive = new Archive();
        if (extension_loaded("zip")) {
            $archive->setExtenderArguments(
                Array("ZipArchive" => new ZipArchive()));
            $success = ($archive->decompress($arc, $destination) ? "true" : "false");
        }
        else {
            throw new AikiException("Extension zip is not loaded.");
        }
    }
    catch (AikiException $e) {
        $log->exception($e);
    }
}
else {
    $log->message("Failed to get aiki archive data", Log::ERROR);
}
$callScript = '<script type="text/javascript" id="update-script">' .
    'updateBackup("' . $arc . '", ' . $success . ');</script>';

echo $callScript;
