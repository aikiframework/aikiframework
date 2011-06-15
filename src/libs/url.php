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
 * Handles urls in the widget system, fixes, them, and grants creation
 * of widgets depending upon the url path set for a widget.
 *
 * @category    Aiki
 * @package     Library
 * @todo fix the class name to be Url
 */
class url
{
    /**
     * @var string 
     * @access public
     */
	public $url;
    /**
     * @var bool
     * @access public
     */
	public $create_widget;
    /**
     * @var integer
     * @access public
     */
	public $url_count;

	/**
	 * Sets up the url for further processing.
	 */
	public function url()
	{
		/**
		 * @todo trace to find our what happens if not pretty url
		 */
		if (isset($_GET["pretty"]))
			$this->url = $_GET["pretty"];

		$this->url = str_replace("|", "/", $this->url);

		$this->url = explode("/", $this->url);

		/**
		 * @todo find and remove this homepage option. if anything should
		 * be an option acros the codebase, but consolidated into configs.
		 */
		if (!$this->url[0])
			$this->url[0] = 'homepage';

		$this->url_count = count($this->url);
	}


	/**
	 * Apply a url on a query.
	 *
	 * @param array $query a constructed query
	 * @return arry
	 */
	public function apply_url_on_query($query)
	{
		$count = preg_match_all( '/\(\!\((.*)\)\!\)/U', $query, $matches );

		if ($count > 0){
			foreach ($matches[1] as $parsed){
				$query = @str_replace("(!($parsed)!)", $this->url[$parsed], 
									  $query);
			}
		}
		return $query;
	}


	/**
	 * Clean up a url.
	 * @param string $text
	 * @return string
	 */
	public function fix_url($text)
	{
		$text = trim($text);
		$text = strtr ( $text, array (
                      " " =>"_",
                      "'" =>"" ,
                      '"' =>""));
		$text = strtolower($text);

		return $text;
	}

	/**
	 * Decide if widget should be displayed if set to match a url.
	 *
	 * @param array $widget
	 */
	public function widget_if_match_url($widget)
	{
			
		$do_not_display_widget = '';
		$display_widget = '';

		$display_urls_array = explode("|", $widget->display_urls);

		foreach ($display_urls_array as $display_url)
		{
			if ( strpos( $display_url, '/' ) !== false )
				$match_pattern = '/'.$this->url['0'].'/D';
			else
				$match_pattern = '/^'.$this->url['0'].'$/D';

			if (preg_match($match_pattern, $display_url))
			{
				$display_url = explode("/", $display_url);

				$display_count = count($display_url);

				if ($display_count < $this->url_count)
				{
					$comapre = $this->url;
					$second_side = $display_url;
				}else{
					$comapre = $display_url;
					$second_side = $this->url;
				}

				$i = 0;

				foreach ($comapre as $operator)
				{
					if (isset ($second_side[$i]))
					{
						if (!preg_match("/^$operator$/D", "$second_side[$i]"))
							$do_not_display_widget = 1;
						else
							$display_widget = 1;
					}

					$i++;
				}

				if ($display_widget and !$do_not_display_widget)
					$this->create_widget = true;
				else
					$this->create_widget = false;

				$display_widget = '';
				$do_not_display_widget = '';
			}
		} // end of foreach loop
	} // end of widget_if_match_url method
} // end of url class
