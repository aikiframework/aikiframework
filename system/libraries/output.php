<?php

/**
 * Aiki framework (PHP)
 *
 * @author		Aikilab http://www.aikilab.com
 * @copyright  (c) 2008-2010 Aikilab
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class output
{

	public $html;
	public $title;



	public function set_title($title){
		global $aiki;
		$this->title = $title;
	}

	public function write_title_and_metas($title){
		global $site_info, $db, $config, $aiki;
		$header = '
		<meta http-equiv="Content-Type" content="text/html; charset=__encoding__" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<title>'; if (!$title){$header .= $site_info->site_name;}else{$header .= $title." - ".$site_info->site_name;} $header .='</title>
		<meta name="generator" content="Aikiframework '.AIKI_VERSION.'" />
		';

		//a fix for the w3  Markup Validation Service
		$header = str_replace("&", "&amp;", $header);

		return $header;

	}

	public function write_doctype(){
		global $header, $dir, $language_short_name;
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="'.$language_short_name.'" xml:lang="'.$language_short_name.'"
	dir="'.$dir.'">
';

	}


	public function write_headers(){
		global $aiki, $db, $layout, $nogui, $site, $config;

		$header = "";
		$header .= $this->write_doctype();
		$header .= '<head>';
		$header .= $this->write_title_and_metas("$this->title");

		if (!$nogui){

			if (isset($layout->widgets_css) and $layout->widgets_css != ''){

				$layout->widgets_css = preg_replace('/_$/i', '', $layout->widgets_css);

				$header .= '<link rel="stylesheet" type="text/css" href="'.$config['url'].'style.php?site='.$site."&widgets=$layout->widgets_css\" />\n";

			}

			$header .= '<link rel="icon" href="'.$config['url'].'assets/images/favicon.ico" type="image/x-icon" />';

		}

		if (isset ($layout->head_output)){
			$header .= $layout->head_output;
		}

		$header .= "</head>";

		$header .= "\n<body>\n";

		return $header;

	}

	public function write_footer(){
		return "\n</body>\n</html>";
	}

	public function displayInTable($widget, $columns){
		$widgetTabled = "<table width='100%'>";
		$widgetExploded = explode("<!-- The End of a Record -->", $widget);
		$i = 0;
		foreach ($widgetExploded as $cell){


			if ($i == $columns or $i == 0){
				$widgetTabled .= "<tr>";
			}

			$widgetTabled .= "<td>";
			$widgetTabled .= $cell;
			$widgetTabled .= "</td>";
			$i++;
			if ($i == $columns){
				$widgetTabled .= "</tr>";
			}

			if ($i == $columns){
				//$widgetTabled .= "<tr><td colspan='".$columns."'></td></tr>";
				$i = 0;
			}

		}

		$widgetTabled .= "</table>";

		return $widgetTabled;
	}

	public function from_cache(){
		global $config, $membership;

		if ($config['html_cache'] and !$membership->permissions){

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


?>