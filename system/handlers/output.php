<?php

class aiki_output
{

	var $html;
	
	function output_from_cache(){
		global $config;

		if ($config['html_cache'] and !preg_match('/admin/i', $_SERVER['QUERY_STRING'])){ //TODO: add here urls not to cache

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