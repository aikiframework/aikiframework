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
 * @package     Library
 * @filesource
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


/**
 * BriefDescription
 *
 * @category    Aiki
 * @package     Library
 */
class output
{

	public $html;
	private $title;



	public function set_title($title){
		$this->title = $title;
	}

	public function write_title_and_metas(){
		global $site_info;
		$header = '
		<meta http-equiv="Content-Type" content="text/html; charset=__encoding__" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<title>' . ( $this->title ? "$this->title - " : "" ) .  $site_info->site_name . '</title>
        <meta name="generator" content="Aikiframework '.AIKI_VERSION.'" />
		';

		return $header;

	}

	public function write_doctype(){
		global $dir, $language_short_name;

		//don't change the direction if the page is the admin panel on /admin
		if (isset($_GET["pretty"]) and $_GET["pretty"] == 'admin'){
			$language_short_name = "en";
			$dir = "ltr";
		}

		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="'.$language_short_name.'" xml:lang="'.$language_short_name.'"
	dir="'.$dir.'">
';

	}


	public function write_headers(){
		global $aiki, $db, $layout, $nogui, $site, $config, $language;

		$header = $this->write_doctype();
		$header .= '<head>';
		$header .= $this->write_title_and_metas();

		if (!$nogui){

			if (isset($layout->widgets_css) and $layout->widgets_css != ''){

				$layout->widgets_css = preg_replace('/_$/i', '', $layout->widgets_css);

				if(isset($_GET['language'])){
					$language=$_GET['language'];
				}else{
					$language = $config['default_language'];
				}

				$header .= '<link rel="stylesheet" type="text/css" href="'.$config['url'].'style.php?site='.$site."&widgets=$layout->widgets_css&language=$language\" />\n";

			}

			$header .= '<link rel="icon" href="'.$config['url'].'assets/images/favicon.ico" type="image/x-icon" />';

		}

		if (isset ($layout->head_output)){
			$header .= $layout->head_output;
		}

		$header .= "</head>";

		$header .= "\n<body>\n";

		//a fix for the w3  Markup Validation Service
		$header = str_replace("&", "&amp;", $header);

		return $header;

	}

	public function write_footer(){
		return "\n</body>\n</html>";
	}

	public function displayInTable($widget, $columns){
		$widgetTabled = "<table width='100%'>";
		$widgetExploded = explode("<!-- The End of a Record -->", $widget);
		if ( !$columns ) {
			$columns = 1; // to avaid %i % 0 error.
		}

		$i = 0;
		foreach ($widgetExploded as $cell){

			if ($i % $columns == 0){
				$widgetTabled .= "<tr>";
			}
			$widgetTabled .= "<td>$cell</td>";
			$i++;
			if ($i % $columns == 0){
				$widgetTabled .= "</tr>\n";	//add a \n line.
			}
		}

		// add remained columns and close tr
		if ( $i % $columns ) {
			for ( ;$i % $columns; $i++){
				$widgetTabled.= "<td></td>";
			}
			$widgetTabled.= "</tr>\n";
		}
		$widgetTabled .= "</table>";

		return $widgetTabled;
	}


	/**
	 * return false if no cache configuration or nor had permissiones.
	 * if exist fresh file ( no time-out) it is served and application dies.
	 * in other case, return the name (including path) of cache file that must be created
	 * and if exist, delete the obsolete cache file.
	 */
	public function from_cache(){
		global $config, $membership;

		if ($config['html_cache'] != false and !$membership->permissions){

			$start = (float) array_sum(explode(' ',microtime()));

			$html_cache_file = $config['html_cache'].'/'.md5($_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']);

			$html_cache_file = str_replace("//", "/", $html_cache_file);

			if (file_exists($html_cache_file) )
			{
				// Only use this cache file if less than 'cache_timeout' (MS)
				if ( (time() - filemtime($html_cache_file)) > ($config["cache_timeout"]) )
				{
					unlink($html_cache_file);
				}
				else
				{
					$full_html_output = file_get_contents($html_cache_file);

					echo $full_html_output;

					$end = (float) array_sum(explode(' ',microtime()));
					$end_time = sprintf("%.4f", ($end-$start));
					die("\n<!--Served From Cache in $end_time seconds-->");
				}
			}

		}else{
			$html_cache_file = false;
		}

		return $html_cache_file;

	}



}