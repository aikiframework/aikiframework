<?php
/**
 * Modifed version of upload.php
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 *
 */

if (!isset($_GET['key'])){
	die("No key");
}

define('IN_AIKI', true);

require_once("../../../aiki.php");

//TODO store authorization key in db and check it
if (!isset($_SESSION['aikiuser'])){
	die("Not authorized");
}

if ($_GET['key'] != $_SESSION['aikiuser']){
	die("Not authorized");
}


if (isset($config["plupload_path"])){
	$targetDir = $system_folder."/";
	$targetDir .= $aiki->processVars($config["plupload_path"]);
}else{
	die("please configure upload_path in aiki settings");
}

// HTTP headers for no cache etc
header('Content-type: text/plain; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Settings
$cleanupTargetDir = false; // Remove old files
$maxFileAge = 60 * 60; // Temp file age in seconds

// 5 minutes execution time
@set_time_limit(5 * 60);


// Get parameters
$chunk = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
$chunks = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

// Clean the fileName for security reasons
$fileName = preg_replace('/[^\w\._]+/', '', $fileName);

if (!preg_match("/^[a-zA-Z0-9\-\_\.]+\.(".$config['allowed_extensions'].")$/i",$fileName)){

	die ("Not valid filename");
}

if (file_exists($targetDir.$fileName)){
	die ("Sorry the same file exists");
}

// Create target dir
if (!file_exists($targetDir))
@mkdir($targetDir);

// Remove old temp files
if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
	while (($file = readdir($dir)) !== false) {
		$filePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// Remove temp files if they are older than the max age
		if (preg_match('/\\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
		@unlink($filePath);
	}

	closedir($dir);
} else
die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');

// Look for the content type header
if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

if (isset($_SERVER["CONTENT_TYPE"]))
$contentType = $_SERVER["CONTENT_TYPE"];

if (strpos($contentType, "multipart") !== false) {
	if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
		// Open temp file
		$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
		if ($out) {
			// Read binary input stream and append it to temp file
			$in = fopen($_FILES['file']['tmp_name'], "rb");

			if ($in) {
				while ($buff = fread($in, 4096))
				fwrite($out, $buff);
			} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

			fclose($out);
			unlink($_FILES['file']['tmp_name']);
		} else
		die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	} else
	die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
} else {
	// Open temp file
	$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
	if ($out) {
		// Read binary input stream and append it to temp file
		$in = fopen("php://input", "rb");

		if ($in) {
			while ($buff = fread($in, 4096))
			fwrite($out, $buff);
		} else
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

		fclose($out);
	} else
	die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

// Return JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
?>