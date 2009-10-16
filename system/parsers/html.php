<?php
if(!defined('IN_AIKI')){die('No direct script access allowed');}


class aiki_html
{
	
	var $title;
	
	function set_title($title){
		global $aiki;
		$this->title = $aiki->languages->L10n($title);
	}

	public function write_title_and_metas($title){
		global $site_info, $db, $config, $aiki;
		$header = '
		<meta http-equiv="Content-Type" content="text/html; charset='.$config['db_encoding'].'" />
		<meta http-equiv="Content-Style-Type" content="text/css" />';

		$site = $config['site'];

		$get_rss = $db->get_results("SELECT display_name, module_link from aiki_modules where rss != '0' and module_group='$site'");
		if ($get_rss){

			foreach ($get_rss as $rss){
				$header .= '<link rel="alternate" type="application/rss+xml" title="'.$rss->display_name.'" href="'.$this->setting['url'].'feed.php?module='.$rss->module_link.'&type=rss" />';
			}
		}

		$header .= '
		<title>'; if (!$title){$header .= $aiki->languages->L10n($site_info->site_name);}else{$header .= $title." - ".$aiki->languages->L10n($site_info->site_name);} $header .='</title>
		<meta name="keywords" content="" />
		<meta name="description" content="" />
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


	public function write_javascript(){
		global $aiki, $db, $layout, $nogui, $site;

		$header = '';

		$JavaScriptsToUse = array();

		//global use javascripts
		$javascripts = $db->get_results("SELECT id, script FROM aiki_javascript where global_use=1 and is_active=1 order by script_group,id");

		if ($javascripts){
			foreach ( $javascripts as $javascript )
			{
				$JavaScriptsToUse[$javascript->id] = "\n\r".$javascript->script."\n\r";

			}
		}

		//private javascripts called by widgets
		if ($layout->CallJavaScript){
			foreach ($layout->CallJavaScript as $PrivateScript){
				$PrivateScripts = $db->get_row("SELECT id, father, script FROM aiki_javascript where id='$PrivateScript' and global_use=0 and is_active=1 order by id");


				if ($PrivateScripts){


					if ($PrivateScripts->father == 0){
						$JavaScriptsToUse[$PrivateScripts->id] = "\n\r".$PrivateScripts->script."\n\r";
					}else{
						$father = $db->get_row("SELECT id, father, script FROM aiki_javascript where id='$PrivateScripts->father' and global_use=0 and is_active=1 order by id");
						if ($father){

							$JavaScriptsToUse[$father->id] = "\n\r".$father->script."\n\r";
							$JavaScriptsToUse[$PrivateScripts->id] = "\n\r".$PrivateScripts->script."\n\r";

						}
					}

				}

			}
		}


		//print results
		foreach ($JavaScriptsToUse as $script){

			$header .= $aiki->processVars($script);
		}
		
		
		return $header;

	}


	public function write_headers(){
		global $aiki, $db, $layout, $nogui, $site, $config;

		$header = "";
		$header .= $this->write_doctype();
		$header .= '<head>';
		$header .= $this->write_title_and_metas("$this->title");
		if (!$nogui){
			$header .= '<link rel="stylesheet" type="text/css" href="'.$config['url'].'style.php?site='.$site.'';
			if (isset($layout->widgets_css) and $layout->widgets_css != ''){
				$header .= "&widgets=$layout->widgets_css";
			}
			$header.= '" />
<link rel="icon" href="'.$config['url'].'/assets/images/favicon.ico" type="image/x-icon" />';

			$header .= $this->write_javascript();


		}
		$header .= "</head>";

		//TODO: custome onload and onuload
		$header .= "\n<body>\n";

		//TODO: $header = $layout->doInnerSql($header);
		return $header;

	}

	public function write_footer(){
		return "\n</body>\n</html>";
	}

	function displayInTable($widget, $columns){
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

}

?>