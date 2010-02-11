<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class aiki_output
{

	var $html;
	
	function output_from_cache(){
		global $config;

		if ($config['html_cache']){ 

			$start = (float) array_sum(explode(' ',microtime()));

			$html_cache_file = $config['html_cache'].'/'.md5($_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']);


			if (file_exists($html_cache_file) )
			{
				// Only use this cache file if less than 'cache_timeout' (hours)
				if ( (time() - filemtime($html_cache_file)) > ($cache_timeout*3600) )
				{
					unlink($html_cache_file);
				}
				else
				{
					$full_html_output = file_get_contents($html_cache_file);
					echo $full_html_output;



					$end = (float) array_sum(explode(' ',microtime()));
					$end_time = sprintf("%.4f", ($end-$start));;
					die("\n<!--Served From Cache in $end_time seconds-->");
				}
			}

		}

	}



}


?>