<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
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


if ($layout->widget_custome_output){
	$noheaders = true;
}

if (!isset($noheaders)){
	$html_output = $aiki->html->write_headers();
}



if ($config['html_cache'] and $html_cache_file and !$noheaders){
	$full_html_input = $aiki->html->write_headers();
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
if ( extension_loaded('tidy' ) and function_exists('tidy_parse_string') and $config["html_tidy"]) {

	$tidy = new tidy();
	$tidy->parseString($html_output, $config["html_tidy_config"], 'utf8');
	$tidy->cleanRepair();

	if ($config["tidy_compress"]){
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

if (isset($config["debug"]) and $config["debug"]){
	echo "\n <!-- queries: ".$db->num_queries." -->";
}

?>