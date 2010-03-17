<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

error_reporting(E_ALL);

$start_time = (float) array_sum(explode(' ',microtime()));

$html_output = '';

require_once("aiki.php");

$html_cache_file = $aiki->output->output_from_cache();

require_once ("system/widgets.php");
$layout = new CreateLayout();


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



if ($config['html_cache'] and isset($html_cache_file) and !isset($noheaders)){
	$full_html_input = $aiki->html->write_headers();
	//$full_html_input .= "<div id=\"container\">\n\r";
}


if ($config['html_cache'] and isset($html_cache_file)){

	$full_html_input .= $layout->html_output;
}


$html_output .= $layout->html_output;

if (!isset($noheaders)){
	//$html_output .= "</div>";
}

if ($config['html_cache'] and isset($html_cache_file) and !isset($noheaders)){
	//$full_html_input .= "</div>";
}


if (!isset($noheaders)){
	$html_output .= $aiki->html->write_footer();
}


//Tidy html using libtidy
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


	if (isset($config["compress_output"]) and $config["compress_output"]){
		$html_output = preg_replace("/\<\!\-\-(.*)\-\-\>/U", "", $html_output);
		$html_output = str_replace("\n", "", $html_output);
		$html_output = str_replace("\r", "", $html_output);
	}

	print $html_output;


}


if ($config['html_cache'] and isset($html_cache_file) and !isset($noheaders)){

	$full_html_input .= $aiki->html->write_footer();
}


if ($config['html_cache'] and isset($html_cache_file)){

	if ( ! is_dir($config['html_cache']) )
	{
		echo($config['html_cache']);
	}
	else
	{
		error_log ( $full_html_input, 3, $html_cache_file);

	}
}

if (isset($config["debug"]) and $config["debug"]){
	$end = (float) array_sum(explode(' ',microtime()));
	$end_time = sprintf("%.4f", ($end-$start_time));
	echo "\n <!-- queries: ".$db->num_queries." -->\n";
	echo "\n <!-- Time: ".$end_time." seconds -->";
}

?>