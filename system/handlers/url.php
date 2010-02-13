<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class aiki_url
{

	var $url;
	var $create_widget;
	var $url_count;


	function aiki_url(){


		if (isset($_GET["pretty"])){
			$pretty = $_GET["pretty"];
			$this->url = $pretty;
		}

		$this->url = str_replace("|", "/", $this->url);

		$this->url = explode("/", $this->url);

		if (!$this->url[0]){
			$this->url[0] = 'homepage';
		}

		$this->url_count = count($this->url);


	}


	function apply_url_on_query($query){

		$count = preg_match_all( '/\(\!\((.*)\)\!\)/U', $query, $matches );

		if ($count > 0){
			foreach ($matches[1] as $parsed){
				if (isset($parsed)){
					$query = @str_replace("(!($parsed)!)", $this->url[$parsed], $query);
				}
			}
		}

		return $query;
	}



	function widget_if_match_url($widget){
			
		$do_not_display_widget = '';
		$display_widget = '';

		$display_urls_array = explode("|", $widget->display_urls);

		foreach ($display_urls_array as $display_url){

			if (preg_match('/^'.$this->url['0'].'$/D', $display_url)){

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
?>