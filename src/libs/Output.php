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

if (!defined('IN_AIKI')) {
	die('No direct script access allowed');
}


/**
 * This is used for generating and providing functions for outputting content.
 *
 * @category    Aiki
 * @package     Library
 *
 */
class Output {

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
	 * @param   string  $headers	header content
	 */
	public function set_headers($headers)
	{
		$this->headers = $headers;
	}

	/**
	 * Returns the title and default meta tags as html
	 *
	 * @return  string
	 *
	 * @todo title has a hardcoded default here, need to remove
	 */
	public function title_and_metas() {
		global $aiki, $config;
		$title = '<title>' . ( $this->title ? "$this->title - " : "" ) .
			$aiki->site->site_name() . '</title>';
		$aiki->Plugins->doAction("output_title", $title);						
		
		$encoding = isset($config["db_encoding"]) ? $config["db_encoding"] : "utf-8";
		
		$header = sprintf("\n".
			"<meta charset='$encoding' >\n" .
			"<meta name='generator' content='Aikiframework %s.%s' >\n",
			AIKI_VERSION , AIKI_REVISION );			
		
		$aiki->Plugins->doAction("output_meta", $header);

		return $header.$title;
	}

	/**
	 * Returns the doctype
	 *
	 * @global  $aiki
	 * @return  string
	 *
	 * @todo we really should have a default template for pages somewhere and
	 * NOT have default doctype written directly inside of aiki!
	 */
	public function doctype() {
		global $aiki;

		/**
		 * don't change the direction if the page is the admin panel on /admin
		 * @todo instance of where admin and rest of code needs separation
		 */
		if ( isset($_GET["pretty"]) && $_GET["pretty"] == 'admin' ) {
			$aiki->languages->language = "en";
			$aiki->languages->dir = "ltr";
		}
		/**
		 * @todo this really needs to be abstracted? why just output xthml???
		 */
		$lang=  $aiki->site->language();
		$dir =  $aiki->languages->dir;
		 
		return 
			"<!doctype html>\n".
			"<html lang='$lang' dir='$dir'>\n";
	} // end of doctype function


	/**
	 * This returns header content for output.
	 *
	 * @global      aiki            $aiki   main obj manipulating configs + urls
	 * @global      array           $db     global db instance
	 * @global      CreateLayout	$layout global layout object
	 * @global      bool            $nogui  global yes or no about gui
	 * @global      array           $config global config options instance
	 * @return string
	 *
	 * @see bootstrap.php
	 * @todo this is super nasty function that pulls in globals
	 * @todo the html hardcoded in here needs abstraction and shouldn't make
	 * assumptions about setup
	 */
	public function headers() {
		global $aiki, $db, $layout, $nogui, $config;

		$header = $this->doctype();
		$header .= '<head>';
		$header .= $this->title_and_metas();

		if (!$nogui) {
			if (count($layout->widgets_css)) {

				// handle language settings
				if(isset($_GET['language'])) {
					$language=$_GET['language'];
				} else {
					$language = $config['default_language'];
				}
				$view = $aiki->site->view();// comodity
				$header .= sprintf(
					'<link rel="stylesheet" type="text/css" ' .
					' href="%sstyle.php?site=%s&amp;%swidgets=%s&amp;language=%s" />',
					$config['url'],
					$aiki->site->get_site(),
					( $view ? "view={$view}&amp;" : ""),
					implode("_", $layout->widgets_css),
					$language);
			}
			// set favicon, but doesn't really check to see if it exists
			$header .=
				'<link rel="icon" href="' . $config['url'] .
				'assets/images/favicon.ico" type="image/x-icon" />';
		}

		if (isset($layout->head_output)){
			$header .= $layout->head_output;
		}

		$header .= $this->headers;
		$header .= "</head>";
		$aiki->Plugins->doAction("output_head", $header);
				
		$bodybegin = "\n<body>\n";
		$aiki->Plugins->doAction("output_body_begin", $bodybegin);
		$header .= $bodybegin;
		return $header;
	} // end of headers function


	/**
	 * Returns a footer for output.
	 *
	 * @return	string
	 *
	 */
	public function footer() {
		global $aiki;
		$footer = "\n</body>";
		$aiki->Plugins->doAction("output_body_end", $footer);
		$html= "</html>";
		$aiki->Plugins->doAction("output_html_end", $html);
		return $footer.$html;
	}


	/**
	 * Returns an formatted table from a widget. An autoformatted widget.
	 *
	 * This is useful for debugging a widget.
	 *
	 * @param     array    $widget      a widget with its data
	 * @param     integer  $columns     number of columns
	 * @return    string
	 *
	 * @todo remove hardcoded html
	 */
	public function displayInTable($widget, $columns) {
		$widgetTabled = "<table width='100%'>";
		/**
		 * @todo this looks a bit buggy
		 */
		$widgetExploded = explode("<!-- The End of a Record -->", $widget);
		if (!$columns) {
			$columns = 1; // to avoid $i % 0 error.
		}
		$i = 0;
		foreach ($widgetExploded as $cell) {
			if ( $i % $columns == 0 ) {
				$widgetTabled .= "<tr>";
			}
			$widgetTabled .= "<td>$cell</td>";
			$i++;
			if ($i % $columns == 0) {
				$widgetTabled .= "</tr>\n";	//add a \n line.
			}
		}

		// add remaining columns and close table
		if ( $i % $columns ) {
			for ( ;$i % $columns; $i++) {
				$widgetTabled.= "<td></td>";
			}
			$widgetTabled.= "</tr>\n";
		}
		$widgetTabled .= "</table>";

		return $widgetTabled;
	} // end of displayInTable


	/**
	 * Checks if cache setup and user don't have special permissions (for security
	 * reason this pages will not be cached),if so, then returns path
	 * to cache file.
	 *
	 * Returns false if no cache configuration or had permissions.
	 * If exist fresh file ( no time-out) it is served and application dies.
	 * In other case, return the name (including path) of cache file that must
	 * be created and if exist, delete the obsolete cache file.
	 *
	 * @global    array         $config       global configuration options
	 * @global    membership    $membership   global membership instance
	 * @return    mixed
	 *
	 * @todo this function abuses php and has too many functions, simplify
	 * and break this function apart
	 * @todo this function has side effect to output HTML
	 * @todo think good to rename this function too
	 */
	public function cache_file($type="html") {
		global $config, $membership;

		if (isset($config[$type . '_cache'])
			  && $config[$type . '_cache']
			  && is_dir($config[$type . '_cache'])
			  && !$membership->permissions) {

			$start = microtime(true); // require PHP5.

			$cached_file =
				$config[$type . '_cache']. '/' . md5($_SERVER['PHP_SELF'] . "?".
				$_SERVER['QUERY_STRING']);

			$cached_file = str_replace("//", "/", $cached_file);

			$timeout = (isset($config[$type . '_cache_timeout'])?
					$config[$type . '_cache_timeout']:
					5000); // in MS.

			if (file_exists($cached_file)) {
				// Only use this cache file if less than 'cache_timeout' (MS)
				if ((time() - filemtime($cached_file)) > $timeout) {
					// remove cache file, because aiki means on next attempt, creates
					// new cache
					unlink($cached_file);
				} else {

					readfile($cached_file);

					/**
					 * @todo this server speed test should ONLY be shown
					 * if in debug mode
					 */
					$time= sprintf("%4.f seconds", microtime(true)-$start);
					switch ($type) {
						case "html":
							$message= "\n<!--Served From HTML Cache in $time";
							break;
						case "css" :
							$message= "\n/* Served From CSS Cache in $time";
							break;
						default:
							$message="";
					}
					die($message);
				}
			}
			return $cached_file;
		}
		return false;
	} // end of from_cache function


	/**
	 * Compress HTML, deleting line space, doble spaces, space in tags..
	 * and HTML coments
	 *
	 * @param  $string $input String to be cleaned
	 * @return $string Cleaned input
	 */

	function compress(&$input) {
		$output = preg_replace("/\<\!\-\-(.*)\-\-\>/U", "", $input);

		$search = array(
			'/\n/',			// replace end of line by a space
			'/\>[^\S ]+/s',	// strip whitespaces after tags, except space
			'/[^\S ]+\</s',	// strip whitespaces before tags, except space
			'/(\s)+/s'		// shorten multiple whitespace sequences
			);

		$replace = array(
			' ',
			'>',
			'<',
			'\\1'
			);

		$output  = preg_replace($search, $replace, $output );
		return $output;
	}


} // end of Output class

?>
