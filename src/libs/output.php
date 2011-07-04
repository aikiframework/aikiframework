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
 * @package     Library
 * @filesource
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


/**
 * This is used for generating and providing functions for outputting content.
 *
 * @category    Aiki
 * @package     Library
 *
 * @todo rename class to Output
 */
class output
{

    /**
     * storage tank for html output
     * @var string  
     */
	public $html;
    /**
     * storage tank for output
     * @var string  
     */
	public $title;
    /**
     * buffer for header output
     * @var string
     */
	private $headers = '';

    /**
     * Mutator for setting title
     *
     * @param   strign  $title  page title
     */
	public function set_title($title)
    {
		$this->title = $title;
	}

    /**
     * Mutator for setting header
     *
     * @param   string  $headers    header content
     */
	public function set_headers($headers)
    {
		$this->headers = $headers;
	}

    /**
     * Returns the title and default meta tags as html
     * 
     * @global  array   $site_info
     * @return  string
     * 
     * @todo title has a hardcoded default here, need to remove
	 * @todo rename this because its not writing, its returning output
     */
	public function write_title_and_metas()
    {
		global $site_info;
		$header = '
		<meta http-equiv="Content-Type" content="text/html; charset=__encoding__" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<title>' . ( $this->title ? "$this->title - " : "" ) . 
        $site_info->site_name . '</title>
        <meta name="generator" content="Aikiframework '.
        AIKI_VERSION.'.'.AIKI_REVISION.'" />
		';
		return $header;
	}

    /**
     * Returns the doctype
     * 
     * @global  languages   $languages  global languages instance
     * @return  string
     * 
     * @todo we really should have a default template for pages somewhere and
     * NOT have default doctype written directly inside of aiki! 
	 * @todo rename this because its not writing, its returning output
     */
	public function write_doctype()
    {
		global $languages;

		/**
         * don't change the direction if the page is the admin panel on /admin
         * @todo instance of where admin and rest of code needs separation
         */
		if (isset($_GET["pretty"]) and $_GET["pretty"] == 'admin')
        {
			$languages->language_short_name = "en";
			$languages->dir = "ltr";
		}
        /**
         * @todo this really needs to be abstracted? why just output xthml???
         */
		return 
'<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="'.$languages->language_short_name.'" xml:lang="'.$languages->language_short_name.'"
	dir="'.$languages->dir.'">
';
	} // end of write_doctype function


    /**
     * This returns header content for output.
     *
     * @global      aiki            $aiki   main obj manipulating configs + urls
     * @global      array           $db     global db instance 
     * @global      CreateLayout    $layout global layout object
     * @global      bool            $nogui  global yes or no about gui
     * @global      string          $site   site name 
     * @global      array           $config global config options instance
     * @return string
     * 
     * @see bootstrap.php
     * @todo this is super nasty function that pulls in globals
     * @todo the html hardcoded in here needs abstraction and shouldn't make
     * assumptions about setup
	 * @todo rename this because its not writing, its returning output
     */
	public function write_headers()
    {
		global $aiki, $db, $layout, $nogui, $site, $config;

		$header = $this->write_doctype();
		$header .= '<head>';
		$header .= $this->write_title_and_metas();

		if (!$nogui)
        {
			if ( count($layout->widgets_css) )  {
				
                // handle language settings
				if(isset($_GET['language']))
					$language=$_GET['language'];
				else
					$language = $config['default_language'];

				$header .= sprintf(
                    '<link rel="stylesheet" type="text/css" '.
                    ' href="%sstyle.php?site=%s&widgets=%s&language=%s" />',
                    $config['url'],
                    $site,
                    implode("_", $layout->widgets_css),
                    $language);
			}
            // set favicon, but doesn't really check to see if it exists
			$header .= '<link rel="icon" href="'.$config['url'].
                       'assets/images/favicon.ico" type="image/x-icon" />';
		}

		if (isset ($layout->head_output)){
			$header .= $layout->head_output;
		}	
		
		$header .= $this->headers;
		$header .= "</head>";
		$header .= "\n<body>\n";

		return $header;
	} // end of write_headers function


	/**
	 * Returns a footer for output.
	 * 
	 * @return	string
	 * 
	 * @todo rename this because its not writing, its returning output
	 */
	public function write_footer()
	{
		return "\n</body>\n</html>";
	}


	/**
	 * Returns an formatted table from a widget. An autoformatted widget.
	 * 
	 * This is useful for debugging a widget.
	 *
	 * @param	array	$widget		a widget with its data
	 * @param	integer	$columns	number of columns	
	 * @return	string
	 *
	 * @todo remove hardcoded html
	 */
	public function displayInTable($widget, $columns)
	{
		$widgetTabled = "<table width='100%'>";
		/**
		 * @todo this looks a bit buggy
		 */
		$widgetExploded = explode("<!-- The End of a Record -->", $widget);
		if ( !$columns )
			$columns = 1; // to avoid $i % 0 error.

		$i = 0;
		foreach ($widgetExploded as $cell)
		{
			if ($i % $columns == 0)
				$widgetTabled .= "<tr>";
			$widgetTabled .= "<td>$cell</td>";
			$i++;
			if ($i % $columns == 0)
				$widgetTabled .= "</tr>\n";	//add a \n line.
		}

		// add remaining columns and close table
		if ( $i % $columns ) 
		{
			for ( ;$i % $columns; $i++)
			{
				$widgetTabled.= "<td></td>";
			}
			$widgetTabled.= "</tr>\n";
		}
		$widgetTabled .= "</table>";

		return $widgetTabled;
	} // end of displayInTable


	/**
	 * Checks if cache setup and if have permissions, if so, then returns path
	 * to cache file.
	 *
	 * Returns false if no cache configuration or nor had permissions.
	 * If exist fresh file ( no time-out) it is served and application dies.
	 * In other case, return the name (including path) of cache file that must 
	 * be created and if exist, delete the obsolete cache file.
	 *
	 * @global	array	$config			global configuration options
	 * @global	membership	$membership	global membership instance
	 * @return	mixed
	 *
	 * @todo this function abuses php and has too many functions, simplify 
	 * and break this function apart
	 * @todo this function has side effect to output HTML
	 * @todo think good to rename this function too
	 */
	public function from_cache()
	{
		global $config, $membership;

		if ($config['html_cache'] != false and !$membership->permissions)
		{
			$start = (float) array_sum(explode(' ',microtime()));

			$html_cache_file = 
				$config['html_cache'].'/'.md5($_SERVER['PHP_SELF']."?".
				$_SERVER['QUERY_STRING']);

			$html_cache_file = str_replace("//", "/", $html_cache_file);

			if (file_exists($html_cache_file) )
			{
				// Only use this cache file if less than 'cache_timeout' (MS)
				if ( (time() - filemtime($html_cache_file)) > 
					($config["cache_timeout"]) )
				{
					// remove cache file, means on next attempt, creates
					// new cache
					unlink($html_cache_file);
				}
				else
				{

					$full_html_output = file_get_contents($html_cache_file);
					echo $full_html_output;

					/**
					 * @todo this server speed test should ONLY be shown
					 * if in debug mode
					 * @todo this server speed test should be factored out
					 * to a separate function to for use even inside aiki
					 */
					$end = (float) array_sum(explode(' ',microtime()));
					$end_time = sprintf("%.4f", ($end-$start));
					die("\n<!--Served From Cache in $end_time seconds-->");
				}
			}

		} else {
			$html_cache_file = false;
		}

		return $html_cache_file;
	} // end of from_cache function

} // end of Output class
