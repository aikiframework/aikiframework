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

/** @see Updater.php */
require_once("$AIKI_ROOT_DIR/libs/Updater.php");

// membership and log should be instantiated from bootstrap
if ($membership->permissions != "SystemGOD") {
    $log->message("Invalid permission " . $membership->permissions, Log::WARN);
    die();
}

$available = 0;
$contents = '<pre style="line-height:normal;" id="update-check">';
$callScript = "";

if (AIKI_INSTALLED_BY_MAKE) {
    $contents .= "The update feature is not\navailable for this package.";
}
else {
    $downloadButton = '<button id="download-update">Download</button>';
    $updateData = Array();
    $installedData = Array();
    $version = AIKI_VERSION . "." . AIKI_REVISION;

    // Uncomment the following line to test the update feature
    //$version = "0.4.22.854";

    try {
        $updater = new Updater(Array("File" => new File($AIKI_ROOT_DIR)));
        $available = $updater->checkForUpdate($version, AIKI_UPDATE_URL);
        $updateData = $updater->getUpdateData();
        $installedData = $updater->getInstalledData();
    }
    catch (AikiException $e) {
        $log->exception($e);
    }

    if (array_key_exists("aiki", $updateData) and isset($updateData["aiki"])) {

        /* add a javascript handler for the download button click
         * event which passes the aiki update version (see update.js) */
        $callScript = '<script type="text/javascript" id="update-script">' .
            '$("#download-update").click(function(event) {' .
            'updateDownload("' . $updateData["aiki"] . '");});</script>';

        switch ($available) {
            case Updater::UNAVAILABLE:
                $contents .= "Update Unavailable\n\nLatest Stable Version\naiki " .
                    $updateData["aiki"] . "\n";
                break;
            case Updater::AVAILABLE:
                $contents .= "Update Available\naiki " .
                    $updateData["aiki"] . "\t\t$downloadButton";
                break;
            case Updater::REQUIREMENT_NOT_MET:
                $contents .= "Requirements Not Met\n\nMinimum Required\n";
                foreach ($updateData as $key => $val) {
                    if (! ($key === "aiki"))
                       $contents .= "$key $val\n";
                }
                $contents .= "\nCurrently Installed\n";
                foreach ($installedData as $key => $val) {
                    if (! ($key === "aiki"))
                        $contents .= "$key $val\n";
                }
                break;
            default:
                $contents .= "Update Unavailable\n";
                break;
        }
    }
    else {
        $log->message("Failed to get aiki update version", Log::ERROR);
        $contents .= "Failed To Get Update Data\n";
    }
}
$contents .= "</pre>";
echo $callScript . $contents;
