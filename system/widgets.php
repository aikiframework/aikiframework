<?php
if(!defined('IN_AIKI')){die('No direct script access allowed');}


class CreateLayout
{
	var $html_output;
	var $CallJavaScript;
	var $kill_widget;
	var $widget_html;
	var $forms;
	var $inherent_operators;
	var $inherent_id;
	var $create_widget_cache;
	var $output_modifiers;
	var $widgets_css;
	var $widget_custome_output;


	function CreateLayout(){
		global $db, $site, $aiki, $url, $errors, $layout;


		$this->output_modifiers = $db->get_results("SELECT modifiers_name from aiki_plugins where modifiers_type = 'output_modifier'");

		$output_modifiers = $db->get_results("SELECT modifiers_name from aiki_plugins where modifiers_type = 'output_modifier'");
		foreach ($output_modifiers as $output_modifier){

			$output_modifier = $output_modifier->modifiers_name;

			if (file_exists("assets/plugins/$output_modifier.php")){
				require_once ("assets/plugins/$output_modifier.php");
			}
		}

		if (isset($_REQUEST["widget"])){

			$get_widget_id = $db->get_var("SELECT id from aiki_widgets where widget_name ='".$_REQUEST['widget']."' and is_active='1' and widget_group='$site'");
			if ($get_widget_id){

				$this->createWidget($get_widget_id);

			}

		}else{

			$module_widgets = $db->get_results("SELECT id, display_urls, kill_urls FROM aiki_widgets where (display_urls = '".$url->url['0']."' or display_urls LIKE '%|".$url->url['0']."%' or display_urls LIKE '%".$url->url['0']."|%' or display_urls LIKE '%".$url->url['0']."/%') and is_active=1 and father_widget=0 and widget_site='$site' order by display_order, id");

			if ($module_widgets){


				foreach ( $module_widgets as $widget )
				{

					$url->widget_if_match_url($widget);

					if ($url->create_widget){

						$this->createWidget($widget->id);

					}
				}

			}else{

				$this->html_output .= $errors->page_not_found();
			}
		}


	}




	function get_global_vars_in_text($text){
		global $aiki;
		$get_glob = $aiki->get_string_between($text, "({", "})");
		if (isset($get_glob)){
			if (isset(${$get_glob})){
				$text = str_replace("({".$get_glob."})", ${$get_glob}, $text);
			}else{
				$text = str_replace("({".$get_glob."})", '', $text);
			}
		}
		return $text;
	}



	function createWidget($widget_id){
		global $db, $aiki,$url, $operators, $language, $operators_key, $dir, $page, $site, $module, $custome_output;

		$this->widgets_css .= $widget_id.'_';

		//TODO: catch all widgets then select them with one query, then loop over them
		//TODO: Make the catch all thing optional for special cases of memory usage

		$widget = $db->get_row("SELECT * FROM aiki_widgets where id='$widget_id' limit 1");
		if ($widget){

			if ($widget->custome_output){
				$custome_output = true;
				$this->widget_custome_output = true;

				if ($widget->custome_header){
					header("$widget->custome_header");
				}
			}

			if ($widget->javascript){
				$this->CallJavaScript[$widget_id] = $widget->javascript;
			}

			if (!$custome_output){
				$this->widget_html .= "\n <!--start $widget->id--> \n";
				//$this->widget_html .= "<div id=\"$widget->style_id\">\n";
				$this->widget_html .= "<$widget->widget_type id=\"$widget->widget_name\" class=\"$widget->style_id\">\n";
			}

			$this->createWidgetContent($widget);

			if ($widget->is_father){

				$son_widgets = $db->get_results("SELECT id, display_urls,kill_urls FROM aiki_widgets where father_widget='$widget->id' and is_active=1 and (widget_site='$site' or widget_site ='aiki_shared') and (display_urls = '".$url->url['0']."' or display_urls LIKE '%|".$url->url[0]."%' or display_urls LIKE '%".$url->url[0]."|%' or display_urls = '*' or display_urls LIKE '%".$url->url[0]."/%') order by display_order, id");

				if ($son_widgets){
					foreach ( $son_widgets as $son_widget )
					{

						$url->widget_if_match_url($son_widget);
						if ($url->create_widget){
							$this->createWidget($son_widget->id);

						}
					}

				}
			}

			if (!$custome_output){
				$this->widget_html .= "\n</$widget->widget_type>\n\r";
				$this->widget_html .= "\n <!--$widget->id end--> \n";
			}

		}

		if ($this->kill_widget){

			if ($widget->if_no_results){
				$widget->if_no_results =  $aiki->processVars ($aiki->L10n ("$widget->if_no_results"));
				$widget->if_no_results = $this->doAikiDefines($widget->if_no_results);
				$widget->if_no_results = $this->get_global_vars_in_text($widget->if_no_results);

				$dead_widget = '<'.$widget->widget_type.' id="'.$widget->style_id.'">'.$widget->if_no_results.'</'.$widget->widget_type.'>';

			}else{
				$dead_widget = "";
			}
			$this->widget_html = preg_replace("/<!--start $this->kill_widget-->(.*)<!--$this->kill_widget end-->/s", $dead_widget, $this->widget_html, 1, $count);
			$this->kill_widget = '';
		}

		//Fix & problem in links for the w3  Markup Validation Service
		//$this->widget_html = str_replace("&", "&amp;", $this->widget_html);

		$this->html_output .= $this->widget_html;
		$this->widget_html = "";

	}


	function createWidgetContent($widget){
		global $aiki, $db, $widget_cache, $widget_cache_dir, $url, $language, $dir, $align, $page, $membership, $nogui, $highlight, $records_libs, $image_processing, $custome_output, $config;

		//Set page title
		if ($widget->pagetitle){

			$widget->pagetitle = $aiki->processVars($widget->pagetitle);

			$widget->pagetitle = $this->get_global_vars_in_text($widget->pagetitle);

			$widget->pagetitle = $aiki->url->apply_url_on_query($widget->pagetitle);

			if ($widget->dynamic_pagetitle){
				$title = $db->get_var("$widget->pagetitle");
			}else{
				$title = $widget->pagetitle;
			}


			$aiki->html->set_title($title);
		}


		//Get ready for cache
		if ($widget->normal_select){
			$widget_cache_id = $widget->id."_".$_SERVER['QUERY_STRING'];
		}else{
			$widget_cache_id = $widget->id;
		}

		$widget_file = 'var/'.$widget_cache_dir.'/'.md5($widget_cache_id);

		if ($widget->widget_cache_timeout){
			$widget_cache_timeout = $widget->widget_cache_timeout;
		}

		if ($widget_cache and $widget_cache_timeout > 0 and file_exists($widget_file) and ((time() - filemtime($widget_file)) < ($widget_cache_timeout*3600) ) and $membership->permissions != "SystemGOD" and $membership->permissions != "ModulesGOD"){

			//Display widget from cache
			$widget_html_output = file_get_contents($widget_file);
			$this->widget_html .= $widget_html_output;
			$this->create_widget_cache = false;

		}else{
			//widget can't be displayed from cache, so go create it

			//Flag the widget as cachable, and try to delete the old cache file
			$this->create_widget_cache = true;
			if (file_exists($widget_file) and $membership->permissions != "SystemGOD" and $membership->permissions != "ModulesGOD"){
				unlink($widget_file);
			}


			////
			if ($this->inherent_id == $widget->id){
				if ($this->inherent_operators){
					$operators = $this->inherent_operators;
				}
				$widget->pagetitle = '';
			}



			if ($widget->nogui_widget and $nogui){
				$widget->widget = $widget->nogui_widget;
			}

			//security check to view which widget content to display
			if ($widget->is_admin){

				if ($membership->permissions and $widget->if_authorized){

					$get_group_level = $db->get_var ("SELECT group_level from aiki_users_groups where group_permissions='$widget->permissions'");
					if ($widget->permissions == $membership->permissions or $membership->group_level < $get_group_level){
						$widget->widget = $widget->if_authorized;
						$widget->normal_select = $widget->authorized_select;
					}
				}
			}


			$widget->widget = htmlspecialchars_decode($widget->widget);

			$widget->widget = $aiki->processVars($widget->widget);

			$widget->widget = $this->get_global_vars_in_text($widget->widget);

			$no_loop_part = $aiki->get_string_between ($widget->widget, '(noloop(', ')noloop)');

			$widget->widget = str_replace('(noloop('.$no_loop_part.')noloop)', '', $widget->widget);

			$no_loop_bottom_part = $aiki->get_string_between ($widget->widget, '(noloop_bottom(', ')noloop_bottom)');

			$widget->widget = str_replace('(noloop_bottom('.$no_loop_bottom_part.')noloop_bottom)', '', $widget->widget);



			//TODO: Finish the last page first thing
			//TODO: add this to settings editor
			$last_page_first = false;

			if ($widget->normal_select){

				$widget->normal_select = $aiki->url->apply_url_on_query($widget->normal_select);

				$widget->normal_select = $aiki->processVars ($aiki->L10n ("$widget->normal_select"));
				$widget->normal_select = $this->get_global_vars_in_text($widget->normal_select);

				$widget->normal_select = preg_replace('/and(.*)RLIKE \'\'/U', '', $widget->normal_select, 999, $num_no_res);
				$widget->normal_select = preg_replace('/RLIKE \'\'/U', '', $widget->normal_select, 999, $num_no_res_first);

				if ($num_no_res > 0 or $num_no_res_first > 0){
					$widget->normal_select = '';
					$this->kill_widget = $widget->id;

				}else{

					//Support DISTINCT selection
					preg_match('/select DISTINCT(.*)from/i', $widget->normal_select, $get_DISTINCT);

					preg_match('/select(.*)from/i', $widget->normal_select, $selectionmatch);
					if ($selectionmatch[1]){
						if ($get_DISTINCT[1]){
							$mysql_count = ' count(DISTINCT('.$get_DISTINCT[1].')) ';
						}else{
							$mysql_count = ' count(*) ';
						}
						$records_num_query = str_replace($selectionmatch[1], $mysql_count, $widget->normal_select);
					}
					$records_num_query = preg_replace('/ORDER BY(.*)DESC/i', '', $records_num_query);
					$records_num = $db->get_var($records_num_query);

				}


				$widget->widget = str_replace("[records_num]", $records_num, $widget->widget);



				if ($widget->records_in_page and $widget->normal_select){

					if ($records_num != $widget->records_in_page){
						$numpages = $records_num / $widget->records_in_page;
						$numpages = (int)($numpages+1);
					}else{
						$numpages = 1;
					}
					$fnumre = $page * $widget->records_in_page;

					if($last_page_first){

						$widget->normal_select = $widget->normal_select." limit $fnumre,".$widget->records_in_page;
						$widget->normal_select = str_replace("DESC", "ASC", $widget->normal_select);

					}else{

						$widget->normal_select = $widget->normal_select." limit $fnumre,".$widget->records_in_page;
						$widget->normal_select = str_replace("ASC", "DESC", $widget->normal_select);
					}


				}


				if ($num_no_res == 0){
					$widget_select = $db->get_results("$widget->normal_select");
					//echo count($widget_select);
				}


				$num_results = $db->num_rows;


				if ($widget->link_example){

					$widget->link_example = str_replace("[operators]", $operators, $widget->link_example);
					$widget->link_example = str_replace("[global_index]", $global_index, $widget->link_example);
					$widget->link_example = str_replace("[operators_key]", $operators_key, $widget->link_example);
					$widget->link_example = $this->get_global_vars_in_text($widget->link_example);
					if ($numpages > 1){

						//TODO: add this to settings editor
						$pagesgroup = 10;
						$group_pages = true;
						$full_numb_of_pages = $numpages;
						$pagination = '';
						$page2 = $page + 1;
						$pagination .= "<br />
			 <p class='pagination'>Move to page:<br />";

						if( $page ) {
							$first_page = str_replace("[page]", '0', $widget->link_example);
							$pagination .= "<a href=\"$first_page\"><-الأولى</a>";
						}
						if ($group_pages){

							$numpages = $pagesgroup;
							$numpages = $numpages + $page;

							if ($page > ($pagesgroup / 2)){
								$pages_to_display = $page - (int)($pagesgroup / 2);
								$numpages =  $numpages - (int)($pagesgroup / 2);
							}else{
								$pages_to_display = 0;
							}

							if ($numpages > $full_numb_of_pages){
								$numpages = $full_numb_of_pages;
							}

							for ($i=$pages_to_display; $i <$numpages; $i++)
							{

								$y = $i+1;
								if ($i == $page){
									$pagination .= "<span class='pagination_notactive'> $y </span>";
								}else{
									$next_link = str_replace("[page]", $i, $widget->link_example);
									$pagination .= "<b> <a href=\"$next_link\">$y</a> </b>";
								}
							}



						}else{
							if($last_page_first){
								for ($i=$numpages-1; $i>=0; $i--)
								{
									$y = $i + 1;
									if ($i == $page){
										$pagination .= "<b> $y </b>";
									}else{
										$next_link = str_replace("[page]", $i, $widget->link_example);
										$pagination .= "<b> <a href=\"$next_link\">$y</a> </b>";
									}
								}
							}else{
								for ($i=0; $i <$numpages; $i++)
								{
									$y = $i + 1;
									if ($i == $page){
										$pagination .= "<b> $y </b>";
									}else{
										$next_link = str_replace("[page]", $i, $widget->link_example);
										$pagination .= "<b> <a href=\"$next_link\">$y</a> </b>";
									}
								}
							}


						}

						if( $page != ($numpages-1) ) {
							$last_page = str_replace("[page]", $full_numb_of_pages -1, $widget->link_example);
							$pagination .= "<a href=\"$last_page\">الأخيرة-></a>";
						}
						$pagination .= "</p>";
					}
				}




				$widget->widget = str_replace("[#[language]#]", $aiki->setting['default_language'], $widget->widget);
				$widget->widget = str_replace("[#[dir]#]", $dir, $widget->widget);
				$widget->widget = str_replace("[#[align]#]", $align, $widget->widget);

				//$widget->widget = str_replace("((","((", $widget->widget, $count);
				//$count = preg_match_all( '/\(\(/', $widget->widget, $matches );

				$newwidget = $widget->widget;

				if ($widget_select and $num_results and $num_results > 0){

					$widgetContents = '';
					foreach ( $widget_select as $widget_value )
					{

						if (!$custome_output){
							$widgetContents .= "\n<!-- The Beginning of a Record -->\n";
						}
						$widget->widget = $newwidget;

						$widget->widget = $this->datetime($widget->widget, $widget_value);

						//TODO: add output modifiers like this in mysql
						$normaldatetime = $aiki->get_string_between($widget->widget, "(#(normaldatetime:", ")#)");
						if ($normaldatetime){
							$widget_value->$normaldatetime = $aiki->createnormaldate($widget_value->$normaldatetime);
							$widget->widget = str_replace("(#(normaldatetime:$normaldatetime)#)", $widget_value->$normaldatetime , $widget->widget);
						}

						$related = $aiki->get_string_between($widget->widget, "(#(related:", ")#)");
						if ($related){
							$relatedsides = explode("||", $related);

							$related_cloud = "
						<ul class='relatedKeywords'>";

							$related_links = explode("|", $widget_value->$relatedsides[0]);
							$related_array = array();
							foreach ($related_links as $related_link){

								$get_sim_topics = $db->get_results("SELECT $relatedsides[2], $relatedsides[7] FROM $relatedsides[1] where ($relatedsides[3] LIKE '%|".$related_link."|%' or $relatedsides[3] LIKE '".$related_link."|%' or $relatedsides[3] LIKE '%|".$related_link."' or $relatedsides[3]='$related_link') and $relatedsides[7] != '$operators' and publish_cond=2 order by $relatedsides[5] DESC limit $relatedsides[4]");
								//$get_sim_topics = $db->get_results("SELECT $relatedsides[2], $relatedsides[7] FROM $relatedsides[1] where ($relatedsides[3] LIKE '%".$related_link."|%' or $relatedsides[3] LIKE '%|".$related_link."%' or $relatedsides[3]='$related_link') and $relatedsides[7] != '$operators' and publish_cond=2 order by $relatedsides[5] DESC limit $relatedsides[4]");
								//$get_sim_topics = $db->get_results("SELECT $relatedsides[2], $relatedsides[7] FROM $relatedsides[1] where $relatedsides[3]='$related_link' and $relatedsides[7] != '$operators' and publish_cond=2 order by $relatedsides[5] DESC limit $relatedsides[4]");
								if ($get_sim_topics){

									foreach($get_sim_topics as $related_topic){
										$related_cloud_input = '<li><a href="aikicore->setting[url]/'.$relatedsides[6].'">'.$related_topic->$relatedsides[2].'</a></li>';
										$related_cloud_input = str_replace("_self", $related_topic->$relatedsides[7], $related_cloud_input);
										$related_array[$related_topic->$relatedsides[7]] = $related_cloud_input;
										$related_cloud_input = '';
									}

								}

							}
							foreach ($related_array as $related_cloud_output){
								$related_cloud .= $related_cloud_output;
							}

							$related_cloud .= "</ul>";
							$widget->widget = str_replace("(#(related:$related)#)", $related_cloud , $widget->widget);
						}




						$tags = $aiki->get_string_between($widget->widget, "(#(tags:", ")#)");
						if ($tags){
							$tagsides = explode("||", $tags);

							/*$tag_cloud = "[[relatedKeywords]]:
							 <br />
							 <ul>";*/
							$tags_links = explode("|", $widget_value->$tagsides[0]);
							foreach ($tags_links as $tag_link){
								if ($tag_link){
									$tag_cloud .= ' | <a href="aikicore->setting[url]/'.$tagsides[1].'" rel="tag">'.$tag_link.'</a>';
									$tag_cloud = str_replace("_self", $tag_link, $tag_cloud);
								}
							}
							//$tag_cloud .= "</ul>";
							$widget->widget = str_replace("(#(tags:$tags)#)", $tag_cloud , $widget->widget);
						}


						$widget->widget = $this->parsDBpars($widget->widget, $widget_value);


						$nl2br = $aiki->get_string_between($widget->widget, "[*[", "]*]");
						if ($nl2br){
							$nl2br_processed = nl2br($nl2br);
							$widget->widget = str_replace("[*[".$nl2br."]*]", $nl2br_processed, $widget->widget);
						}

						$dobluebr = $aiki->get_string_between($widget->widget, "[{[", "]}]");
						if ($dobluebr){
							$dobluebr_processed = str_replace("<br>", "<br><br>", $dobluebr);
							$dobluebr_processed = str_replace("<br />", "<br /><br />", $dobluebr_processed);
							$widget->widget = str_replace("[{[".$dobluebr."]}]", $dobluebr_processed, $widget->widget);
						}


						$widgetContents .= $widget->widget;
						if (!$custome_output){
							$widgetContents .= "\n<!-- The End of a Record -->\n";
						}
					}
					if ($widget->display_in_row_of > 0){
						$widgetContents = $this->displayInTable($widgetContents, $widget->display_in_row_of);
					}

					$widgetContents = $this->noaiki($widgetContents);


					$widgetContents = $this->inlinePermissions($widgetContents);


					$widgetContents= $this->doAikiDefines($widgetContents);

					$no_loop_part = $this->parsDBpars($no_loop_part, $widget_value);
					$no_loop_bottom_part = $this->parsDBpars($no_loop_bottom_part, $widget_value);
					$widgetContents = $no_loop_part.$widgetContents;
					$widgetContents = $widgetContents.$no_loop_bottom_part;

					$widgetContents = $this->inline_widgets($widgetContents);
					$widgetContents = $this->inherent_widgets($widgetContents);

					$widgetContents = $this->aikiTemplates($widgetContents);

					$widgetContents = $this->call_javascripts($widgetContents, $widget_id);

					$widgetContents = $this->sql($widgetContents);
					//TODO: Make hits counter a function and let it work when widget is cached man !!!
					$hits_counter = preg_match("/\(\#\(hits\:(.*)\)\#\)/U",$widgetContents, $hits_counter_match);
					if ($hits_counter > 0){

						$aiki_hits_counter = explode("|", $hits_counter_match[1]);
						$update_hits_counter = $db->query("UPDATE $aiki_hits_counter[0] set $aiki_hits_counter[2]=$aiki_hits_counter[2]+1 where $aiki_hits_counter[1]");
					}
					$widgetContents = preg_replace("/\(\#\(hits\:(.*)\)\#\)/U", '', $widgetContents);


					if (isset($pagination)){

						$widgetContents = str_replace ("[#[pagination]#]", $pagination, $widgetContents);

						$widgetContents .= $pagination;
					}

					if (isset($highlight)){
						$widgetContents = $aiki->highlight_this($widgetContents, $highlight);
					}

					//Delete empty widgets
					if ($widgetContents == "\n<!-- The Beginning of a Record -->\n\n<!-- The End of a Record -->\n"){
						$this->kill_widget = $widget->id;
					}else{

						$processed_widget =  $widgetContents;


					}


				}else{
					$this->kill_widget = $widget->id;
				}



			}else{

				$widget->widget = $this->noaiki($widget->widget);
				$widget->widget = $aiki->security->inlinePermissions($widget->widget);
				$widget->widget = $this->inline_widgets($widget->widget);
				$widget->widget = $this->inherent_widgets($widget->widget);
				$widget->widget = $aiki->aiki_markup->aikiTemplates($widget->widget);

				//TODO: $widget->widget = $this->call_javascripts($widget->widget, $widget_id);

				//TODO: $widget->widget = $this->sql($widget->widget);


				$processed_widget =  $widget->widget;

			}

			/////////End OF select and not select

			$processed_widget =  $aiki->processVars ($aiki->languages->L10n ("$processed_widget"));
			$processed_widget = $aiki->url->apply_url_on_query($processed_widget);
				

			if($widget->output_modifiers){
				if ($widget->output_modifiers != '[all]'){
					$output_modifiers = explode("\n", $widget->output_modifiers);
				}else{
					$output_modifiers = $this->output_modifiers;
				}
				foreach ($output_modifiers as $output_modifier){

					if ($widget->output_modifiers != '[all]'){
						$output_modifier = trim($output_modifier);
					}else{
						$output_modifier = $output_modifier->modifiers_name;
					}

					//TODO: STOP creating the same class over and over
					$modifier = new $output_modifier();

					$output_modifier_function = "do_$output_modifier";

					$processed_widget = $modifier->$output_modifier_function($processed_widget);


				}
			}


			//apply new location for the whole page
			$new_header = preg_match("/\(\#\(header\:(.*)\)\#\)/U",$processed_widget, $new_header_match);
			if ($new_header > 0 and $new_header_match[1]){
				Header("Location: $new_header_match[1]", false, 301);
			}


			$processed_widget =  $aiki->processVars ($aiki->languages->L10n ("$processed_widget"));


			if (isset($widgetContents) and $widgetContents == "\n<!-- The Beginning of a Record -->\n\n<!-- The End of a Record -->\n"){
				$this->kill_widget = $widget->id;
			}else{
				if ($widget_cache and $this->create_widget_cache and $widget_cache_dir and $widget_cache_timeout>0 and is_dir('var/'.$widget_cache_dir) and !$membership->permissions and !$_GET['dc']){
					$processed_widget_cach = $processed_widget."\n\n<!-- Served From Cache -->\n\n";
					error_log ( $processed_widget_cach, 3, $widget_file);
				}

				if ($widget_cache and ($membership->permissions == "SystemGOD" or $membership->permissions == "ModulesGOD") and $widget_cache_dir and $widget_cache_timeout>0){
					$processed_widget = $processed_widget."<a href='&dc=".md5($widget_cache_id)."'><small>Empty Cache</small></a><br />";
				}
			}
			if ($membership->permissions == "SystemGOD" and $widget->widget and $config['show_edit_widgets'] == 1){
				$processed_widget = $processed_widget."<a href='".$aiki->setting[url]."index.php?language=arabic&module=admin&operators=module|aiki_widgets|edit&op=edit&do=editaiki_widgets&pkey=".$widget->id."'><small>Edit Widget</small></a>";
			}



			$replace_string = $aiki->get_string_between($processed_widget, "{#{", "}#}");
			if ($replace_string){
				$replace_string = explode(":", $replace_string);
				switch ($replace_string[0]){

					case "edit_css":
						require_once ("system/libs/admin_tools/modules_tools.php");
						$Modules_Tools = new Modules_Tools();
						$Modules_Tools->EditMultiRecordsInPlace();
						break;


					case "edit_widget":
						require_once ("system/libs/admin_tools/modules_tools.php");
						$Modules_Tools = new Modules_Tools();
						$Modules_Tools->EditMultiWidgetsInPlace();
						break;

					case "config_editor":

						require_once ("system/libs/array_tools.php");
						$array_tools = new array_tools();
						$processed_widget .= $array_tools->array_editor('config_id', 'config_type', 'config_data', 'aiki_config', '');

						break;


					case "delete_record":
						$processed_widget .= $records_libs->dbUpdateDelete($url->url[2], 'del', $url->url[3], '', '', "", "");
						break;

					case "LogOut":
						$membership->LogOut();

						break;

					default:
						break;
				}

				$processed_widget = preg_replace("/\{\#\{(.*)\}\#\}/U", '', $processed_widget);

			}


			$processed_widget = $aiki->forms->displayForms($processed_widget);

			$this->widget_html .=  $processed_widget;




		}



	}


	function noaiki($text){
		global $aiki;

		$widget_no_aiki = $aiki->get_string_between($text, "<no_aiki>", "</no_aiki>");

		if ($widget_no_aiki){

			$html_widget = htmlspecialchars($widget_no_aiki);

			//For some reason htmlspecialchars() don't work all the time
			//so we have to do this

			$html_chars = array(")", "(", "[", "]", "{", "|", "}");
			$html_entities = array("&#41;", "&#40;", "&#91;", "&#93;", "&#123;", "&#124;", "&#125;");

			$html_widget = str_replace($html_chars, $html_entities, $html_widget);

			$text = str_replace("<no_aiki>$widget_no_aiki</no_aiki>", $html_widget, $text);

		}
		return $text;
	}



	function parsDBpars($text, $widget_value){
		global $aiki;

		$count = preg_match_all( '/\(\((.*)\)\)/U', $text, $matches );

		foreach ($matches[1] as $parsed){

			//$parsed = "";
			//$parsed = $aiki->get_string_between($text, "((", "))");


			if ($parsed){

				//((if||writers||الكاتب: _self))

				$parsedExplode = explode("||", $parsed);
				if (isset($parsedExplode[1]) and $parsedExplode[0] == "if"){
					$parsedValue = $widget_value->$parsedExplode[1];

					if ($parsedValue){
						$parsedExplode[2] = str_replace("_self", $parsedValue, $parsedExplode[2]);
						$widget_value->$parsed = $parsedExplode[2];
					}
					elseif ($parsedExplode[4] and $parsedExplode[3] == "else"){
						$else_stetment = explode(":", $parsedExplode[4]);

						if ($else_stetment[0] == "redirect" and $else_stetment[1]){
							$text_values .="<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=$else_stetment[1]\">";
							if (!$widget_value->$parsed){
								$widget_value->$parsed = $text_values;
							}
						}
					}

				}

				$text = str_replace("(($parsed))", $widget_value->$parsed, $text);

			}
		}
		return $text;
	}

	function inline_widgets($widget){
		//TODO: put the widget in it's place not in top .i.e replace (#(widget:123)#) with contents
		$numMatches = preg_match_all( '/\(\#\(widget\:(.*)\)\#\)/', $widget, $matches);
		if ($numMatches > 0){
			foreach ($matches[1] as $widget_id){
				$this->createWidget($widget_id);
			}

			$widget = preg_replace('/\(\#\(widget\:(.*)\)\#\)/', '', $widget);
		}
		return $widget;
	}


	function inherent_widgets($widget){
		global $db;

		//TODO: put the widget in it's place not in top .i.e replace (#(inherent:123)#) with contents
		$numMatches = preg_match_all( '/\(\#\(inherent\:(.*)\)\#\)/', $widget, $matches);
		if ($numMatches > 0){
			foreach ($matches[1] as $widget_id){

				$widget_id = explode("|", $widget_id);

				$this->inherent_operators = $widget_id[1];
				$this->inherent_id = $widget_id[0];
				$widget_id = $widget_id[0];

				$widget_data = $db->get_row("SELECT * FROM aiki_widgets where id='$widget_id' limit 1");

				$this->createWidgetContent($widget_data);

			}

			$widget = preg_replace('/\(\#\(inherent\:(.*)\)\#\)/', '', $widget);


		}
		return $widget;
	}













}
?>