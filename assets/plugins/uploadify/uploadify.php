<?php

require_once("../../../aiki.php");

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];

	// $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
	// $fileTypes  = str_replace(';','|',$fileTypes);
	// $typesArray = split('\|',$fileTypes);
	// $fileParts  = pathinfo($_FILES['Filedata']['name']);

	// if (in_array($fileParts['extension'],$typesArray)) {
	// Uncomment the following line if you want to make the directory if it doesn't exist
	// mkdir(str_replace('//','/',$targetPath), 0755, true);


	$original_filename = $_FILES['Filedata']['name'];

	$type = substr($_FILES['Filedata']['name'], strrpos($_FILES['Filedata']['name'], '.') + 1);
	
	$title = str_replace('.'.$type, '', $_FILES['Filedata']['name']);

	$name = time().'_'.rand(1, time()).".".$type;
	$targetFile =  str_replace('//','/',$targetPath) . $name;

	move_uploaded_file($tempFile,$targetFile);
	echo "1";
	
	$keywords = mysql_real_escape_string($_POST['keywords']);
	$source_url = mysql_real_escape_string($_POST['source_url']);
	$article_id = mysql_real_escape_string($_POST['article_id']);
	$categorie = mysql_real_escape_string($_POST['categorie']);
	
	
	$db->query("insert into apps_photo_archive (id, title, upload_file_name, filename, keywords, source_url, article_id, categorie) values ('', '$title', '$original_filename', '$name', '$keywords', '$source_url', '$article_id', '$categorie')");

	// } else {
	// 	echo 'Invalid file type.';
	// }
}
?>