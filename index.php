<?php

/*
 * Aikiframework
 *
 * @author		Bassel Khartabil
 * @copyright	Copyright (C) 2008-2009 Bassel Khartabil.
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link		http://www.aikiframework.org
 */

error_reporting(E_ALL);

$html_output = '';

require_once("aiki.php");


require_once ("system/widgets.php");
$layout = new CreateLayout();

if (isset($_GET['dc']) and $membership->permissions == "SystemGOD" and $widget_cache_dir){
	$widget_file = 'var/'.$widget_cache_dir.'/'.$_GET['dc'];
	if (file_exists($widget_file)){
		unlink($widget_file);
	}
}

if (isset($global_widget)){
	$noheaders = true;
	$nogui = true;
}

$user_ip = $membership->get_ip();

if (isset($_SESSION['guest']))
$current_session = $_SESSION['guest'];


if (isset($_SESSION['aiki']))
$current_session = $_SESSION['aiki'];

if (!isset($_SESSION['aiki']) and !isset($_SESSION['guest'])){

	//session_start();
	$_SESSION['guest'] = $membership->generate_session(100);
	$current_session = $_SESSION['guest'];

	$insert_session = $db->query("INSERT INTO aiki_guests VALUES ('', NOW(), NOW(),".time().",'$user_ip', '', 'guest' ,'$_SESSION[guest]','0', '1')");


}elseif (isset($_SESSION['aiki']) and isset($_SESSION['guest'])){


	$make_offline = $db->query("UPDATE `aiki_guests` SET `is_online`='0' WHERE `guest_session`='$_SESSION[guest]' LIMIT 1");

	unset($_SESSION['guest']);
	$current_session = $_SESSION['aiki'];

	$insert_session = $db->query("INSERT INTO aiki_guests VALUES ('', NOW(), NOW(),".time().",'$user_ip', '', '$membership->username' ,'$_SESSION[aiki]','0', '1')");

}else{
	$update_guest = $db->query("UPDATE `aiki_guests` SET `last_hit`=NOW(), last_hit_unix = ".time()." ,`last_ip`='$user_ip', `hits`=`hits`+1 WHERE `guest_session`='$current_session' LIMIT 1");
}

//Delete inactive online users
$last_hour = time()."-3600";
$make_offline = $db->query("DELETE FROM `aiki_guests` WHERE last_hit_unix < $last_hour");


if ($layout->widget_custome_output){
	$noheaders = true;
}

if (!isset($noheaders)){
	$html_output = $aiki->html->write_headers();
	//$html_output .= "<div id=\"container\">\n\r";
}


if ($config['html_cache'] and $html_cache_file and !$noheaders){
	$full_html_input = $aiki->write_headers();
	//$full_html_input .= "<div id=\"container\">\n\r";
}


if ($config['html_cache'] and $html_cache_file){

	$full_html_input .= $layout->html_output;
}


$html_output .= $layout->html_output;





if (!isset($noheaders)){
	//$html_output .= "</div>";
}

if ($config['html_cache'] and $html_cache_file and !$noheaders){
	//$full_html_input .= "</div>";
}


if (!isset($noheaders)){
	$html_output .= $aiki->html->write_footer();
}


// Tidy from php5-tidy using libtidy
if ( extension_loaded('tidy' ) and function_exists('tidy_parse_string') and isset($html_tidy) and isset($tidy_config)) {

	$tidy = new tidy();
	$tidy->parseString($html_output, $tidy_config, 'utf8');
	$tidy->cleanRepair();

	if ($tidy_compress){
		echo preg_replace("/\r?\n/m", "",tidy_get_output($tidy));
	}else{
		echo tidy_get_output($tidy);
	}

}else{


	print $html_output;


}


if ($config['html_cache'] and $html_cache_file and !$noheaders){

	$full_html_input .= $aiki->write_footer();
}


if ($config['html_cache'] and $html_cache_file){

	if ( ! is_dir('var/'.$html_cache) )
	{
		echo("Could not open cache dir: var/$html_cache");
	}
	else
	{
		error_log ( $full_html_input, 3, $html_cache_file);

	}
}

?>