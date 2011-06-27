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

/** @see File.php */
require_once("$AIKI_ROOT_DIR/libs/File.php");

// membership and log should be instantiated from bootstrap
if ($membership->permissions != "SystemGOD") {
    $log->message("Invalid permission " . $membership->permissions, Log::WARN);
    die();
}

$url = "";
$destination = (AIKI_INSTALLED_BY_MAKE ? "" : "$AIKI_ROOT_DIR/");
$callScript = "";
$version = "";
$success = "false";

if (array_key_exists("aiki", $_GET) and isset($_GET["aiki"])) {
	$version = $_GET["aiki"];

	try {
	    $file = new File($AIKI_ROOT_DIR);
	    $url = AIKI_UPDATE_PATH .
	            AIKI_UPDATE_PREFIX .
	            $version .
	            AIKI_UPDATE_EXT;
        $dir = $destination . AIKI_SAVE_DIR . "/" . AIKI_DOWNLOAD_DIR;
        $file->makeDir($dir);
        $destination = "$dir/" .
	                AIKI_UPDATE_PREFIX .
	                $version .
	                AIKI_UPDATE_EXT;
        if ($file->download($url . AIKI_SUM_EXT, $destination . AIKI_SUM_EXT)
            and $file->download($url, $destination)) {
	    	$success = "true";
	    }
	}
	catch (AikiException $e) {
	    $log->exception($e);
	}
}
else {
	$log->message("Failed to get aiki update version", Log::ERROR);
}
$callScript = '<script type="text/javascript" id="update-script">' .
    'updateValidate("' . 
    $destination . AIKI_SUM_EXT . '", "' . 
    $destination . '", ' . $success . ');</script>';

echo $callScript;
