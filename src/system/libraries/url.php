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
 * BriefDescription
 *
 * @category    Aiki
 * @package     Library
 */
class url
{

	public $url;
	public $create_widget;
	public $url_count;


	public function url(){

		if (isset($_GET["pretty"])){
			$this->url = $_GET["pretty"];
		}

		$this->url = str_replace("|", "/", $this->url);

		$this->url = explode("/", $this->url);

		if (!$this->url[0]){
			$this->url[0] = 'homepage';
		}

		$this->url_count = count($this->url);
	}


	public function apply_url_on_query($query){

		$count = preg_match_all( '/\(\!\((.*)\)\!\)/U', $query, $matches );

		if ($count > 0){
			foreach ($matches[1] as $parsed){
				$query = @str_replace("(!($parsed)!)", $this->url[$parsed], $query);
			}
		}

		return $query;
	}


	public function fix_url($text){

		$text = trim($text);
		$text = strtr ( $text, array (
                      " " =>"_",
                      "'" =>"" ,
                      '"' =>""));
		$text = strtolower($text);

		return $text;
	}

	public function widget_if_match_url($widget){
			
		$do_not_display_widget = '';
		$display_widget = '';

		$display_urls_array = explode("|", $widget->display_urls);

		foreach ($display_urls_array as $display_url){

			if ( strpos( $display_url, '/' ) !== false ){
				$match_pattern = '/'.$this->url['0'].'/D';
			}else{
				$match_pattern = '/^'.$this->url['0'].'$/D';
			}

			if (preg_match($match_pattern, $display_url)){

				$display_url = explode("/", $display_url);

				$display_count = count($display_url);

				if ($display_count < $this->url_count){
					$comapre = $this->url;
					$second_side = $display_url;
				}else{
					$comapre = $display_url;
					$second_side = $this->url;
				}

				$i=0;

				foreach ($comapre as $operator){

					if (isset ($second_side[$i])){
						if (!preg_match("/^$operator$/D", "$second_side[$i]")){
							$do_not_display_widget = 1;
						}else{
							$display_widget = 1;
						}
					}

					$i++;
				}

				if ($display_widget and !$do_not_display_widget){
					$this->create_widget = true;
				}else{
					$this->create_widget = false;
				}

				$display_widget = '';
				$do_not_display_widget = '';

			}

		}

	}


}
