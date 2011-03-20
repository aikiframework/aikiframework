<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Aikilab http://www.aikilab.com
 * @copyright   (c) 2008-2010 Aikilab
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Aiki
 * @filesource
 */

error_reporting(E_STRICT | E_ALL);

$start_time = (float) array_sum(explode(' ',microtime()));

$html_output = '';

/**
 * @see aiki.php
 */
require_once("aiki.php");

$html_cache_file = $aiki->output->from_cache();

/**
 * @see widgets.php
 */
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
	$html_output = $aiki->output->write_headers();
}



if ($config['html_cache'] and isset($html_cache_file) and !isset($noheaders)){
	$full_html_input = $aiki->output->write_headers();
}


if ($config['html_cache'] and isset($html_cache_file)){

	$full_html_input .= $layout->html_output;
}

if (isset($aiki->output->title)){
	$layout->html_output = str_replace('[page_title]', $aiki->output->title, $layout->html_output);
}
$html_output .= $layout->html_output;

if (!isset($noheaders)){
	$html_output .= $aiki->output->write_footer();
}

$html_output = $aiki->languages->L10n($html_output);

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


	if (isset($_REQUEST['compress_output']) or (isset($config["compress_output"]) and $config["compress_output"])){
		$html_output = preg_replace("/\<\!\-\-(.*)\-\-\>/U", "", $html_output);

		$search = array(
		'/\n/',			// replace end of line by a space
		'/\>[^\S ]+/s',		// strip whitespaces after tags, except space
		'/[^\S ]+\</s',		// strip whitespaces before tags, except space
	 	'/(\s)+/s'		// shorten multiple whitespace sequences
	 );

	 $replace = array(
		' ',
		'>',
	 	'<',
	 	'\\1'
	  );

	  $html_output  = preg_replace($search, $replace, $html_output );
	}

	if (!isset($_GET['no_output'])){
		print htmlspecialchars_decode($html_output);
	}
}


if ($config['html_cache'] and isset($html_cache_file) and !isset($noheaders)){
	$full_html_input .= $aiki->output->write_footer();
}


if ($config['html_cache'] and isset($html_cache_file)){

	if ( ! is_dir($config['html_cache']) )
	{
		echo($config['html_cache']);
	}
	else
	{

		$full_html_input = $aiki->languages->L10n($full_html_input);

		//write the cache file
		error_log ( $full_html_input, 3, $html_cache_file);

	}
}

if (isset($config["debug"]) and $config["debug"]){
	$end = (float) array_sum(explode(' ',microtime()));
	$end_time = sprintf("%.4f", ($end-$start_time));
	echo "\n <!-- queries: ".$db->num_queries." -->\n";
	echo "\n <!-- Time: ".$end_time." seconds -->";
}