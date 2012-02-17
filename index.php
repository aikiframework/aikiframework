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
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Aiki
 * @filesource
 */


/**
 * Starts the clock so we can measure loading time of the site
 * @global float $start_time the start time
 */
$start_time = (float)array_sum(explode(' ',microtime()));

/**
 * Creates a container for the total generated public HTML to display in browser
 * @global string $html_output the HTML
 */
$html_output = '';

/**
 * @see bootstrap.php
 */
require_once("bootstrap.php");
config_error_reporting();

/**
 * cache_file have tree result:
 *  false: if no cache file must be created
 *  string: cache file that must be created
 *  die, after echo a previous fresh cache file.
 * @global string $html_cache_file
 */
$html_cache_file = $aiki->Output->cache_file();


$engineType = config("engine","aiki");

if ( $engineType =="aiki" ){

	/**
	 * @see widgets.php
	 */
	require_once("libs/Engine_aiki.php");
	$layout = new Engine_Aiki();
	$layout->layout();	
	
	if (isset($global_widget) or isset($_REQUEST['noheaders'])) {
		$noheaders  = true;
	} else {
		$noheaders  = false;	
	}

	if ( $noheaders || $layout->widget_custom_output) {
		$html_output = $aiki->Output->custom_output($layout->html_output);
	} else {   
		$html_output = $aiki->Output->header($layout->widgets_css, $layout->head_output);		
		$layout->html_output = str_replace(
				'[page_title]', 
				$aiki->Output->get_title(),
				$layout->html_output);		
		$html_output .= $aiki->Output->body($layout->html_output);
		$html_output .= $aiki->Output->end();
	} 

	$html_output = $aiki->languages->L10n($html_output);

} elseif  ( file_exists( "$AIKI_ROOT_DIR/libs/Engine_$engineType.php") ) {	
    include "$AIKI_ROOT_DIR/libs/Engine_$engineType.php";
    $engineClass= "Engine_" . $engineType;    
    $engine = new $engineClass();
    $html_output = $engine->layout();
	
} else {
	$html_output = 
		"<p>".
		t( sprintf('Unkown engine %1$s. File /libs/Engine_%1$s.php doesn\'t exists.',$engineType)).
		"</p>";
}


/**
 * Tidy html using libtidy, or output compressed output, or just output.
 */
if ( extension_loaded('tidy') &&
	 function_exists('tidy_parse_string') &&
	 $config["html_tidy"] ) {
	$tidy = new tidy();
	$tidy->parseString($html_output, $config["html_tidy_config"], 'utf8');
	$tidy->cleanRepair();

	if ($config["tidy_compress"]) {
		echo preg_replace("/\r?\n/m", "",tidy_get_output($tidy));
	} else {
		echo tidy_get_output($tidy);
	}
} else {

	if ( isset($_REQUEST['compress_output']) || 
		( isset($config["compress_output"]) && $config["compress_output"] ) ) {
		$html_output = $aiki->Output->compress($html_output);
	}

	if (!isset($_GET['no_output']))  {
		$aiki->Plugins->doAction("output_html", $html_output);
		$html_output = stripslashes($html_output);   
		print $html_output;
	}
} // end of using tidy


if ($html_cache_file) {
	//write the cache file
	error_log($html_output, 3, $html_cache_file);
}

/**
 * For ending the counter to see the page load time.
 */
if ( is_debug_on() ){
	$end = (float)array_sum(explode(' ',microtime()));
	$end_time = sprintf("%.4f", ($end-$start_time));
	echo "\n <!-- queries: ".$db->num_queries." -->\n";
	echo "\n <!-- Time: ".$end_time." seconds -->";
}
