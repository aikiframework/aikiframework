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
 * @package     System
 * @filesource
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


/**
 * Creates the Aiki page layout.
 *
 * @category    Aiki
 * @package     System
 *
 * @todo refactor this all, or else!
 */
class CreateLayout
{
	/**
     * The full HTML output of all the widgets.
     */
	public $html_output;

	/**
     * Widgets that need to be killed.
     */
	public $kill_widget;

	/**
     * Stores the output of one widget at a time.
     */
	public $widget_html;

    /**
     * An empty variable for storing forms?
     * @todo trace this and see if it used. If not, delete it.
     */ 
	public $forms;

	/**
     * Stores the ID of inhereted widget.
     */
	public $inherent_id;

	/**
     * Boolean: to create a cache for this widget.
     */
	public $create_widget_cache;

	/**
     * Stores the css output from the selected widgets.
     */
	public $widgets_css;

	/**
     * Boolean: is the widget require custom output.
     */
	public $widget_custom_output;

	/**
     * Stores the head output of a widget.
     */
	public $head_output;

	/**
     * Stores the values of SQL results of the current selected widgets.
     */
	private $global_values = null;

    /**
     * This is the basic Layout Creation method.
     * @global array  $db
     * @global string $site
     * @global aiki   $aiki
     * @global string $url
     * @global errors $errors
     */
	public function CreateLayout(){
		global $db, $site, $aiki, $url, $errors;

		// Convert global_values to an object to cache the SOL results in parsDBpars function.
		$this->global_values=new stdClass();

		if (isset($_GET["widget"])){

			$get_widget_id = $db->get_var("SELECT id from aiki_widgets where widget_name ='".$_GET['widget']."' or id = '".$_GET['widget']."' and is_active='1'");
			if ($get_widget_id){
				$this->createWidget($get_widget_id);
			}

		}else{

			$module_widgets = $db->get_results("SELECT id, display_urls, kill_urls FROM aiki_widgets where (display_urls = '".$url->url['0']."' or display_urls LIKE '%|".$url->url['0']."|%' or display_urls LIKE '%|".$url->url['0']."' or display_urls LIKE '".$url->url['0']."|%' or display_urls LIKE '%".$url->url['0']."/%' or display_urls = '*') and is_active=1 and father_widget=0 and widget_site='$site' order by display_order, id");

			if ($module_widgets){
				$test_if_no_unique_widgets = $module_widgets;
				foreach($test_if_no_unique_widgets as $tested_widget){
					if ($tested_widget->display_urls != "*"){
						$unique_widget_exists = true;
					}
				}
			}

			if ($module_widgets and isset($unique_widget_exists)){

				$widget_group = array();

				foreach ( $module_widgets as $widget )
				{

					$url->widget_if_match_url($widget);

					/**
                     * @TODO move this to widget_if_match_url
                     */
					if (isset($_GET["pretty"]) and $widget->kill_urls != "" and ($widget->kill_urls == $_GET["pretty"] or preg_match('/'.preg_quote($widget->kill_urls, '/').'/', $_GET["pretty"]))){

						$kill_this_widget = true;

					}else{

						$kill_this_widget = false;

						if ($url->create_widget or $widget->display_urls == "*"){
							$widget_group[] = $widget->id;
							//$this->createWidget($widget->id);
						}

					}

				}

				$this->createWidget('', $widget_group);

			}else{
				$this->html_output .= $errors->page_not_found();
			}

		}
	}


    /**
     * Creates a widget internal to this class.
     * 
     * @param integer $widget_id id of a widget
     * @param string  $widget_group the group of the widget
     * @global array  $db global database object
     * @global aiki   $aiki glboal aiki object
     * @global string $url 
     * @global string $page 
     * @global string $site
     * @global string $custom_output
     *
     * @todo this all need to be broken down into helper functions, too big!
     */
	private function createWidget($widget_id, $widget_group='')
    {
		global $db, $aiki,$url, $page, $site, $custom_output;

		if ($widget_group) 
		{
			$widgets_query = '';
	
			foreach ($widget_group as $widget_id)
			{
				if (!$widgets_query)
					$widgets_query .= " id = '$widget_id'";
				else
					$widgets_query .= " or id = '$widget_id'";
			}

			/**
			 * Daddy this is where widgets come from...
			 */
			$widget_result = $db->get_results("SELECT * FROM aiki_widgets where $widgets_query order by display_order, id");

		} elseif ($widget_id) {
			$widget_result = $db->get_results("SELECT * FROM aiki_widgets where id='$widget_id' limit 1");
		}

		if (isset($widget_result)) 
		{
			/**
			 * Where the search for widgets, gets put into a widget
			 * @TODO so the widget itself is scoped wrong! should be
			 *		 outside this block to be proper!
			 * @TODO This is totally too long iteration to create $widget
			 */
			foreach ($widget_result as $widget) 
			{

				if ($widget->css)
					$this->widgets_css .= $widget->id.'_';

				if ($widget->custom_output) {
					$custom_output = true;
					$this->widget_custom_output = true;
				}

				if ($widget->custom_header and $widget->custom_header != '')
				{
					$custom_headers = explode("\n", $widget->custom_header);
					foreach ($custom_headers as $custom_header){
						if ($custom_header != ""){
							header("$custom_header");
						}
					}
				}

				if (!$custom_output and 
					$widget->widget_type and 
					$widget->remove_container != 1)
				{
					/**
					 * @todo all output of comments needs to be an option, since
					 *		 it makes output pages bigger by default.
					 *		 Should only turn on for debug mode IMO.
					 */
					$this->widget_html .= 
					"\n <!--start {$widget->widget_name}({$widget->id})--> \n";

					$this->widget_html .= 
					"<$widget->widget_type id=\"$widget->widget_name\"";

					if ($widget->style_id){
						$this->widget_html .= " class=\"$widget->style_id\"";
					}
					$this->widget_html .= ">\n";
				}

				$this->createWidgetContent($widget);

				if ($widget->is_father)
				{
					/**
					 * @todo run for the woods son! this is an insane query
					 *		 need to test this query for performance and see
					 *		 if way to optimize or break down.
					 */
					$son_widgets = $db->get_results("SELECT id, display_urls,kill_urls FROM aiki_widgets where father_widget='$widget->id' and is_active=1 and (widget_site='$site' or widget_site ='aiki_shared') and (display_urls = '".$url->url['0']."' or display_urls LIKE '%|".$url->url['0']."|%' or display_urls LIKE '%|".$url->url['0']."' or display_urls LIKE '".$url->url['0']."|%' or display_urls = '*' or display_urls LIKE '%".$url->url['0']."/%') order by display_order, id");

					if ($son_widgets)
					{
						$son_widget_group = array();

						foreach ( $son_widgets as $son_widget )
						{
							$url->widget_if_match_url($son_widget);

							/**
							 * @TODO move this to widget_if_match_url
							 */
							if (isset($_GET["pretty"]) and 
								$son_widget->kill_urls != "" and 
								($son_widget->kill_urls == $_GET["pretty"] or 
								preg_match('/'.preg_quote(
									$son_widget->kill_urls, '/').'/U', 
									$_GET["pretty"])))
							{
								$kill_this_widget = true;
							} else {
								$kill_this_widget = false;
							}

							if ($url->create_widget and !$kill_this_widget)
							{
								$son_widget_group[] = $son_widget->id;
								/**
								 * @todo why is this commented out?
								 */
								//$this->createWidget($son_widget->id);
							}
						}
						$this->createWidget('', $son_widget_group);
						$son_widget_group = '';
					}
				} // end of handling father

				if (!$custom_output and 
					$widget->widget_type and 
					$widget->remove_container != 1)
				{
					$this->widget_html .= "\n</$widget->widget_type>\n";
					/**
					 * @todo all output of comments needs to be an option, since
					 *		 it makes output pages bigger by default.
					 *		 Should only turn on for debug mode IMO.
					 */
					$this->widget_html .= 
					"\n <!--{$widget->widget_name}({$widget->id}) end--> \n";
				}

				if ($this->kill_widget)
				{
					if ($widget->if_no_results)
					{
						$widget->if_no_results = 
							stripcslashes($widget->if_no_results);
						$widget->if_no_results =  
							$aiki->processVars ($widget->if_no_results);
						$widget->if_no_results = 
							$aiki->url->apply_url_on_query(
								$widget->if_no_results);
						$widget->if_no_results = 
							$aiki->input->requests($widget->if_no_results);

						$dead_widget = 
							'<'.$widget->widget_type.' id="'.
							$widget->widget_name.'">'.$widget->if_no_results.
							'</'.$widget->widget_type.'>';
					} else {
						$dead_widget = "";
					}
					/**
					 * @todo looks like some text is placed into the output
					 *		 stream and then replaced here!!! Nooo!
					 */
					$this->widget_html = 
						preg_replace("/<!--start $this->kill_widget-->(.*)<!--$this->kill_widget end-->/s", $dead_widget, $this->widget_html, 1, $count);
					$this->kill_widget = '';
				}

				if ($widget->widget_target == 'body') 
					$this->html_output .= $this->widget_html;
				else if($widget->widget_target == 'header')
					$this->head_output .= $this->widget_html;

				$this->widget_html = "";
			} // end of foreach iterating over results to get/set $widget
		} // end of checking for widgets
	} // end of createWidget method


    /** 
     * Creates Widget Content
     *
     * @todo keep going
	 * @param	array		$widget				the widget to create
	 * @param	bool		$output_to_string
	 * @param	string		$normal_select		???
	 * @global	aiki		$aiki				global object
	 * @global	array		$db					global db object
	 * @global	string		$url				the url
	 * @global	membership	$membership			global membership object
	 * @global	bool		$nogui				
	 * @global	string		$custom_output
	 * @global	array		$config				global array of config options
	 * @return	mixed
	 *
	 * @todo needless to say, this has to be refactored or redone. 
     */
	private function createWidgetContent($widget, 
										 $output_to_string='', 
										 $normal_select='')
	{
		global $aiki, $db, $url, $membership, $nogui, $custom_output, $config;

		/**
		 * @todo scoped wrong
		 */
		if (isset($_GET['page']))
			$page = addslashes($_GET['page']);
		else
			$page = "";

		if ($page > 0)
			$page = $page - 1;

		if (isset($config["widget_cache"]) and 
			$config["widget_cache"] and 
			isset($config["widget_cache_dir"]) and 
			$widget->widget_cache_timeout)
		{
			// Get widget ready for cache.
			if ($widget->normal_select)
				$widget_cache_id = $widget->id."_".$_SERVER['QUERY_STRING'];
			else
				$widget_cache_id = $widget->id;

			$widget_file = 
				$config["widget_cache_dir"].'/'.md5($widget_cache_id);

			$widget_cache_timeout = $widget->widget_cache_timeout;
		}

		// Security check to determine which widget content to display.
		if ($widget->is_admin)
		{
			if ($membership->permissions and $widget->if_authorized)
			{
				$get_group_level = 
					$db->get_var("SELECT group_level from aiki_users_groups where group_permissions='$widget->permissions'");
				if ($widget->permissions == $membership->permissions or 
					$membership->group_level < $get_group_level)
				{
					$widget->widget = $widget->if_authorized;
					$widget->normal_select = $widget->authorized_select;
					$stopcaching = true;
				}
			}
		}

		// Kill the query if it is not select.
		if (preg_match("/TRUNCATE|UPDATE|DELETE(.*)from/i", 
					   $widget->normal_select))
		{
			$widget->normal_select = "";
		}

		if (isset($widget->normal_select))
		{
			$widget->normal_select = 
				str_replace("\n", " ", $widget->normal_select);
			$widget->normal_select = 
				str_replace("\r", " ", $widget->normal_select);
		}

		if (!isset($stopcaching))
			$stopcaching = false;

		if ( isset($config["widget_cache"]) and 
			 $config["widget_cache"] and 
			 isset($widget_cache_timeout) and 
			 $widget_cache_timeout > 0 and 
			 file_exists($widget_file) and 
			 ((time() - filemtime($widget_file)) < ($widget_cache_timeout) ) and
			 $membership->permissions != "SystemGOD" and 
			 $membership->permissions != "ModulesGOD" and !$stopcaching )
		{
			// Display widget from cache.
			$widget_html_output = file_get_contents($widget_file);
			$this->widget_html .= $widget_html_output;
			$this->create_widget_cache = false;
		} else {
			// Widget can't be rendered from cache.
			// Flag the widget as cachable, and try to delete the old cache file
			$this->create_widget_cache = true;
			if ( isset ($widget_file) and 
				 $membership->permissions != "SystemGOD" and 
				 $membership->permissions != "ModulesGOD" and 
				 !$stopcaching )
			{
				if (file_exists($widget_file))
					unlink($widget_file);
			}

			if ($this->inherent_id == $widget->id)
				$widget->pagetitle = '';

			if ($widget->nogui_widget and isset($nogui))
				$widget->widget = $widget->nogui_widget;

			$widget->widget = $aiki->input->requests($widget->widget);

			$widget->normal_select = $aiki->input->requests($widget->normal_select);
			$widget->normal_select =  $this->parsDBpars($widget->normal_select);
			$widget->normal_select = 
				str_replace("[guest_session]", $membership->guest_session, 
							$widget->normal_select);
			$widget->normal_select = 
				str_replace("[user_session]", $membership->user_session, 
							$widget->normal_select);

			/**
			 * @TODO why is this commented out? if no takers, delete!
			 */
			//$widget->widget = htmlspecialchars_decode($widget->widget);

			$widget->widget = $aiki->processVars($widget->widget);

			$no_loop_part = 
				$aiki->get_string_between ($widget->widget, 
										   '(noloop(', ')noloop)');

			$widget->widget = 
				str_replace('(noloop('.$no_loop_part.')noloop)', '', 
							$widget->widget);

			$no_loop_bottom_part = 
				$aiki->get_string_between ($widget->widget, 
										  '(noloop_bottom(', ')noloop_bottom)');

			$widget->widget = str_replace(
				'(noloop_bottom('.$no_loop_bottom_part.')noloop_bottom)', '', 
				$widget->widget);

			if (isset($normal_select) and $normal_select)
				$widget->normal_select = trim($normal_select);
			else
				$widget->normal_select = trim($widget->normal_select);

			$widget->normal_select = 
				str_replace("\'", "'", $widget->normal_select);
			$widget->normal_select = 
				str_replace('\"', '"', $widget->normal_select);

			if ($widget->normal_select)
			{
				$normal_selects = explode("|OR|", $widget->normal_select);
				$widget->normal_select = $normal_selects[0];

				$widget->normal_select = 
					$aiki->url->apply_url_on_query($widget->normal_select);

				$widget->normal_select = 
					$aiki->processVars ($aiki->languages->L10n(
											"$widget->normal_select"));

				// Support DISTINCT selection
				preg_match('/select DISTINCT(.*)from/si', 
						   $widget->normal_select, $get_DISTINCT);

                preg_match('/select(.*) from /si', 
						   $widget->normal_select, $selectionmatch);

                if ( isset($selectionmatch['1']))
				{
                    if ( stripos($widget->normal_select," GROUP BY ") || 
                         stripos($widget->normal_select," LIMIT" )) {
                        // with GROUP or LIMIT clausule must do a query
                        $db->get_results($widget->normal_select);
                        $records_num= $db->num_rows;                
                    } else {
                        if (isset ($get_DISTINCT['1'])){
                            $mysql_count = 
								' count(DISTINCT('.$get_DISTINCT['1'].')) ';
                        }else{  
                            $mysql_count = ' count(*) ';
                        }
                        $records_num_query = 
							str_replace($selectionmatch['1'], $mysql_count, 
										$widget->normal_select);
                        $records_num_query = 
							preg_replace('/ORDER BY(.*)(DESC|ASC)?/i', '', 
										 $records_num_query);
                        $records_num = $db->get_var($records_num_query);
                    }
                }    

				if (isset($records_num)) {
					$widget->widget = str_replace("[records_num]", 
					$records_num, $widget->widget);
				} else {
					$records_num = '';
				}

				// Default pages links settings.
				$pagesgroup = 10;
				$group_pages = false;

				if ($widget->records_in_page and !$widget->link_example)
					$widget->link_example = "?page=[page]";

				// Custom pages links setting from link_example.
				if (isset($widget->link_example))
				{
					$link_config = 
						preg_match('/config\[(.*)\]/U', 
								   $widget->link_example, $link_config_data);
					if ($link_config and isset($link_config_data['1']))
					{
						if (preg_match('/group\_by\:/',$link_config_data['1']))
						{
							$group_pages = true;
							$pagesgroup = 
								str_replace('group_by:', '', 
											$link_config_data['1']);
						}

						$widget->link_example = 
							preg_replace('/config\[(.*)\]/U', '', 
										 $widget->link_example);
						$widget->link_example = trim($widget->link_example);
					}
				}

				if ($widget->records_in_page and $widget->normal_select)
				{
					if ($records_num != $widget->records_in_page)
					{
						$numpages = $records_num / $widget->records_in_page;

						if (is_float($numpages))
						{
							$numpages = floor($numpages);
							$numpages = $numpages+1;
						}
					} else {
						$numpages = 1;
					}

					$fnumre = $page * $widget->records_in_page;

					$widget->normal_select = 
						$widget->normal_select." limit $fnumre,".
						$widget->records_in_page;
				}

				$widget_select = $db->get_results("$widget->normal_select");
					
				if (!$widget_select and isset($normal_selects['1']))
					$widget_select = $db->get_results($normal_selects['1']);

				$num_results = $db->num_rows;

				if ($widget->link_example)
				{
					$widget->link_example = 
						$aiki->input->requests($widget->link_example);

					/**
					 * @todo abstract this all into a pagination class
					 */
					if (isset($numpages) and $numpages > 1)
					{
						$numpages = $numpages + 1;

						$full_numb_of_pages = $numpages;
						$pagination = '';
						$page2 = $page;
						/**
						 * @todo the harcoded pagination must die
						 */
						$pagination .= 
							"<div class='pagination'>" . 
							"<span class='pagination_move_to_page'>" . 
							"__move_to_page__</span><br />";

						if ($page)
						{
							$previous = 
								str_replace("[page]", $page, 
											$widget->link_example);
							/**
							 * @todo more hardcoded pagination, must change!
							 */
							$pagination .= 
								"<span class='pagination_previous'>" . 
								"<a href=\"$previous\">__previous__</a></span>";
						}

						if ($group_pages)
						{
							$numpages = $pagesgroup;
							$numpages = $numpages + $page + 1;

							if ($page > ($pagesgroup / 2))
							{
								$pages_to_display = $page - (int)($pagesgroup / 2);
								$numpages =  $numpages - (int)($pagesgroup / 2);
							} else {
								$pages_to_display = 0;
							}

							if ($numpages > $full_numb_of_pages)
								$numpages = $full_numb_of_pages;

							for ($i=$pages_to_display +1 ; $i <$numpages; $i++)
							{
								/**
								 * @todo more hardcoded html
								 */
								if ($i == $page + 1 )
								{
									$pagination .= 
									"<span class='pagination_notactive'>" . 
									" $i </span>";
								} else {
									$next_link = 
										str_replace("[page]", $i, 
										$widget->link_example);
									$pagination .= 
										"<span class='pagination_active'>" . 
										"<a href=\"$next_link\">$i</a></span>";
								}
							} // end of for loop

						} else {

							for ($i=1; $i <$numpages; $i++)
							{
								if ($i == $page + 1)
								{
									$pagination .= 
										"<span class='pagination_notactive'>".
										" $i </span>";
								} else {
									$next_link = str_replace("[page]", $i, 
												 $widget->link_example);
									$pagination .= 
										"<span class='pagination_active'>" . 
										"<a href=\"$next_link\">$i</a></span>";
								}
							}
						}

						if( $page+2 != ($numpages)) 
						{
							$next = str_replace("[page]", $page + 2, 
												$widget->link_example);
							$pagination .= 
								"<span class='pagination_next'>". 
								"<a href=\"$next\">__next__</a></span>";
						}

						if( $page ) 
						{
							$first_page = str_replace("[page]", '1', 
													  $widget->link_example);
							$pagination .= 
								"<span class='pagination_first'>" . 
								" <a href=\"$first_page\">__first_page__</a>" .
								"</span>";
						}

						if( $page != ($numpages-2) ) 
						{
							$last_page = str_replace("[page]", 
								$full_numb_of_pages -1, $widget->link_example);
							$pagination .= 
								"<span class='pagination_last'>" . 
								"<a href=\"$last_page\">__last_page__</a>" . 
								"</span>";
						}
						$pagination .= "</div>";
					} // basically end of pagination code
				} // end of if ($widget->link_example)

				$newwidget = $widget->widget;

				if ($widget_select and $num_results and $num_results > 0)
				{
					$widgetContents = '';
					foreach ( $widget_select as $widget_value )
					{
						/**
						 * @todo put this behind debug time option
						 */
						if (!$custom_output)
							$widgetContents .= 
								"\n<!-- The Beginning of a Record -->\n";
						$widget->widget = $newwidget;
						$widget->widget = 
							$aiki->parser->datetime(
								$widget->widget, $widget_value);
						$widget->widget = 
							$aiki->parser->tags($widget->widget, $widget_value);
						$widget->widget = $this->noaiki($widget->widget);
						$widget->widget = 
							$this->parsDBpars($widget->widget, $widget_value);
						$widget->widget = 
							$aiki->records->edit_in_place($widget->widget, 
														  $widget_value);
						$widget->widget = 
							$aiki->text->aiki_nl2br($widget->widget);
						$widget->widget = 
							$aiki->text->aiki_nl2p($widget->widget);

						$widgetContents .= $widget->widget;
						if (!$custom_output){
							$widgetContents .= 
								"\n<!-- The End of a Record -->\n";
						}
					} // end of foreach

					if ($widget->display_in_row_of > 0)
						$widgetContents = 
							$aiki->output->displayInTable($widgetContents, 
								$widget->display_in_row_of);

					$widgetContents = $this->noaiki($widgetContents);
					$widgetContents = 
						$aiki->url->apply_url_on_query($widgetContents);

					$widgetContents = 
						$aiki->security->inlinePermissions($widgetContents);

					$no_loop_part = 
						$this->parsDBpars($no_loop_part, $widget_value);
					$no_loop_bottom_part = 
						$this->parsDBpars($no_loop_bottom_part, $widget_value);
					$widgetContents = $no_loop_part.$widgetContents;
					$widgetContents = $widgetContents.$no_loop_bottom_part;
					$widgetContents = $aiki->php->parser($widgetContents);
					$widgetContents = $this->inline_widgets($widgetContents);
					$widgetContents = $this->inherent_widgets($widgetContents);
					$widgetContents = $aiki->sql_markup->sql($widgetContents);

					$hits_counter = 
						preg_match("/\(\#\(hits\:(.*)\)\#\)/U",$widgetContents,
								   $hits_counter_match);
					if ($hits_counter > 0)
					{
						$aiki_hits_counter = 
							explode("|", $hits_counter_match['1']);
						$update_hits_counter = 
							$db->query("UPDATE ".$aiki_hits_counter['0'].
									   " set ".$aiki_hits_counter['2']."=".
									   $aiki_hits_counter['2']."+1 where ".
									   $aiki_hits_counter['1']."");
					}
					$widgetContents = 
						preg_replace("/\(\#\(hits\:(.*)\)\#\)/U", '', 
									 $widgetContents);

					if (isset($pagination) and 
						!preg_match('/\[no\_pagination\]/', $widgetContents))
					{
						$widgetContents = 
							str_replace ("[pagination]", $pagination, 
										 $widgetContents);
						$widgetContents .= $pagination;
					}
					$widgetContents = 
						str_replace("[no_pagination]", "", $widgetContents);

					if (!isset($next))
						$next = '';

					$widgetContents = 
						str_replace("[next]", $next, $widgetContents);

					if (!isset($previous))
						$previous = '';

					$widgetContents = 
						str_replace("[previous]", $previous, $widgetContents);

					/**
					 * Delete empty widgets
					 * @todo hide this behind debug time option
					 */
					if ($widgetContents == 
						"\n<!-- The Beginning of a Record -->\n\n<!-- The End of a Record -->\n"){
						$this->kill_widget = $widget->id;
					} else {
						$processed_widget =  $widgetContents;
					}
				} else {
					$this->kill_widget = $widget->id;
				}
			} else {
				$widget->widget = $this->parsDBpars($widget->widget, '');
				$widget->widget = $this->noaiki($widget->widget);
				$widget->widget = $aiki->url->apply_url_on_query(
								      $widget->widget);
				$widget->widget = $aiki->security->inlinePermissions(
									  $widget->widget);
				$widget->widget = $this->inline_widgets($widget->widget);
				$widget->widget = $this->inherent_widgets($widget->widget);
				$widget->widget = $aiki->sql_markup->sql($widget->widget);

				$processed_widget =  $widget->widget;
			}

			if (!isset($processed_widget))
				$processed_widget = '';

			$processed_widget = $this->parsDBpars($processed_widget, '');
			$processed_widget = $aiki->processVars ($processed_widget);
			$processed_widget = 
				$aiki->url->apply_url_on_query($processed_widget);
			$processed_widget = $aiki->text->aiki_nl2br($processed_widget);
			$processed_widget = $aiki->text->aiki_nl2p($processed_widget);

			// Apply new headers.
			$new_header = 
				preg_match_all("/\(\#\(header\:(.*)\)\#\)/U",$processed_widget,
							   $new_header_match);

			if ($new_header > 0 and $new_header_match['1'])
			{
				foreach ($new_header_match['1'] as $header_match)
				{
					$header_parts = explode("|", $header_match);

					if (isset($header_parts['0']) and 
						isset($header_parts['1']) and 
						isset($header_parts[2]))
					{
						header("$header_parts[0]", $header_parts['1'], 
						$header_parts['2']);
					} elseif (isset($header_parts['0']) and 
							  isset($header_parts['1']))
					{
						header("$header_parts[0]", $header_parts['1']);
					} elseif (isset($header_parts['0']))
					{
						header("$header_parts[0]");
					}
				}
			}

			$processed_widget = 
				preg_replace("/\(\#\(header\:(.*)\)\#\)/U",'', 
							 $processed_widget);

			if (isset($widget_cache_id))
			{
				$widget_cache_id_hash = md5($widget_cache_id);
				$processed_widget = 
					str_replace("(#(cache_file_name)#)",$widget_cache_id_hash, 
								$processed_widget);
			}

			// Set page title.
			if ($widget->pagetitle)
			{
				$widget->pagetitle = $aiki->processVars($widget->pagetitle);
				$widget->pagetitle = 
					$aiki->url->apply_url_on_query($widget->pagetitle);

				if (!isset($widget_value))
					$widget_value = '';

				$title = $this->parsDBpars($widget->pagetitle, $widget_value);
				$title = $aiki->input->requests($title);
				$aiki->output->set_title($title);
			}

			$processed_widget = $aiki->processVars ($processed_widget);
			$processed_widget = $aiki->parser->process($processed_widget);
			$processed_widget = 
				$aiki->aiki_array->displayArrayEditor($processed_widget);
			$processed_widget = $aiki->forms->displayForms($processed_widget);
			$processed_widget = $aiki->input->requests($processed_widget);
			$processed_widget = $aiki->php->parser($processed_widget);
			$processed_widget = stripslashes($processed_widget);

			if (isset($widgetContents) and 
				$widgetContents == "\n<!-- The Beginning of a Record -->\n\n<!-- The End of a Record -->\n")
			{
				$this->kill_widget = $widget->id;
			} else {
				if (isset($config["widget_cache"]) and 
					$config["widget_cache"] and 
					$this->create_widget_cache and 
					$config["widget_cache_dir"] and 
					is_dir($config["widget_cache_dir"]) and 
					!$membership->permissions and 
					$widget->widget_cache_timeout > 0)
				{
					$processed_widget_cach = 
						$processed_widget."\n\n<!-- Served From Cache -->\n\n";
					$processed_widget_cach = 
						$aiki->languages->L10n($processed_widget_cach);

					error_log ( $processed_widget_cach, 3, $widget_file);
				}
			}

			if ($membership->permissions == "SystemGOD" and 
				$widget->widget and 
				$config['show_edit_widgets'] == 1 and 
				$widget->widget_target == 'body' and 
				!preg_match("/admin/", $widget->display_urls) and 
				$widget->custom_output == 0)
			{
				$processed_widget = 
					$processed_widget."<a href='".$config['url'].
					"admin_tools/edit/20/".$widget->id.
					"' style='position: absolute; z-index: 100000; " . 
					"background: none repeat scroll 0% 0% rgb(204, 204, 204); ".
					"padding: 3px; -moz-border-radius: 3px 3px 3px 3px; " . 
					"color: rgb(0, 0, 0);'>Edit Widget: ".
					$widget->widget_name."</a>";
			}
			
			// yes or no to output, and howto return
			if ($output_to_string)
			{
				if (!$processed_widget and $widget->if_no_results)
				{
					$widget->if_no_results = 
						$aiki->processVars ($widget->if_no_results);
					return stripslashes($widget->if_no_results);
				} else {
					return $processed_widget;
				}
			} else {
				$this->widget_html .=  $processed_widget;
			}
		} // end of handling cached widget
	} // end of createWidgetContent() 


	/**
	 * Handles content not to be processed by aiki markup
	 *
	 * @param	string	$text	for processing
	 * @global	aiki	$aiki	global aiki object
	 * @return	string
	 */
	public function noaiki($text)
	{
		global $aiki;

		$widget_no_aiki = 
			$aiki->get_string_between($text, "<noaiki>", "</noaiki>");

		if ($widget_no_aiki)
		{
			$html_widget = 
				$aiki->convert_to_specialchars($widget_no_aiki);

			$text = 
				str_replace("<noaiki>$widget_no_aiki</noaiki>", 
							$html_widget, $text);
		}
		return $text;
	}


	/**
	 * @param	string	$text			text for processing
	 * @param	array	$widget_value
	 * @global	aiki	$aiki			global aiki object
	 * @return	string
	 */
	private function parsDBpars($text, $widget_value = '')
	{
		global $aiki;

		$count = preg_match_all( '/\({2,}(.*?)\){2,}/', $text, $matches );

		if (!$widget_value)
		{
			$widget_value = $this->global_values;
			$cached_values = true; //so it don't cache them again
		} else {
			$cached_values = false;
		}

		foreach ($matches['1'] as $parsed)
		{
			if ($parsed)
			{
				$is_array = $aiki->get_string_between($parsed, "[", "]");
				if ($is_array) 
				{
					$parsed_array = str_replace("[$is_array]", "", $parsed);
					$array = @unserialize($widget_value->$parsed_array);
					if (isset($array["$is_array"]))
						$widget_value->$parsed = $array["$is_array"];
					else
						$widget_value->$parsed = '';
				}

				/**
				 * @todo what is this? if no claimants, lets nuke
				 */
				// ((if||writers||writer: _self))

				$parsedExplode = explode("||", $parsed);

				if (isset($parsedExplode['1']) and $parsedExplode[0] == "if")
				{
					$cached_values = true;

					if (isset($widget_value->$parsedExplode['1']))
						$parsedValue = $widget_value->$parsedExplode['1'];

					if (isset($parsedValue) and $parsedValue)
					{
						$parsedExplode[2] = 
							str_replace("_self", $parsedValue, 
										$parsedExplode[2]);
						$widget_value->$parsed = $parsedExplode[2];
					}elseif (isset($parsedExplode[4]) and 
							 $parsedExplode[4] and 
							 $parsedExplode[3] == "else")
					{
						$else_stetment = explode(":", $parsedExplode[4]);

						if ($else_stetment[0] == "redirect" and 
							$else_stetment['1'])
						{
							/**
							 * @todo don't we have a tag builder?
							 */
							$text_values .= 
								"<meta HTTP-EQUIV=\"REFRESH\" " . 
								"content=\"0; url=".$else_stetment['1']."\">";
							if (!$widget_value->$parsed)
								$widget_value->$parsed = $text_values;
						}
					}
				}

				if (!isset($widget_value->$parsed) and 
					isset($this->global_values->$parsed))
				{
					$widget_value->$parsed = $this->global_values->$parsed;
				}

				if (!isset($widget_value->$parsed))
					$widget_value->$parsed = '';

				$widget_value->$parsed = 
					$aiki->security->remove_markup($widget_value->$parsed);

				// If there are results and the results are not from cache 
				// then cache them.
				if ($widget_value->$parsed and !$cached_values)
					$this->global_values->$parsed = $widget_value->$parsed;

				$text = str_replace("(($parsed))", $widget_value->$parsed, 
									$text);
			} // end of if ($parsed)
		} // end of foreach
		return $text;
	}


	/**
	 * Handle inline widgets.
	 *
	 * @param	array	$widget	itza widget!
	 * @return	array	$widget
	 *
     * @TODO: replace widget with the markup
     */
	private function inline_widgets($widget)
	{
		$numMatches = preg_match_all('/\(\#\(widget\:(.*)\)\#\)/', 
									 $widget, $matches);
		if ($numMatches > 0)
		{
			foreach ($matches['1'] as $widget_id)
			{
				$this->createWidget($widget_id);
			}
			$widget = preg_replace('/\(\#\(widget\:(.*)\)\#\)/', '', $widget);
		}
		return $widget;
	}

	/**
	 * Handle inherent widgets
	 * 
	 * @param	array	$widget
	 * @global	array	$db		global db object
	 *
	 * @todo fix the spelling of inherent to inherit and keep backwards compat
	 */
	private function inherent_widgets($widget)
	{
		global $db;

		// Fix a typo that was in the first version inherit was called inherent.
		$widget = str_replace("(#(inherit", "(#(inherent", $widget);

		$numMatches = 
			preg_match_all( '/\(\#\(inherent\:(.*)\)\#\)/Us', $widget,$matches);

		if ($numMatches > 0)
		{
			foreach ($matches['1'] as $widget_info)
			{
				$widget_id = explode("|", $widget_info);

				if (isset($widget_id['1'])){
					$normal_select = $widget_id['1'];
				} else {
					$normal_select = '';
				}

				$this->inherent_id = $widget_id[0];
				$widget_id = $widget_id[0];

				$widget_data = 
					$db->get_row("SELECT * FROM aiki_widgets " . 
								 "where id='$widget_id' limit 1");

				$widget_data = 
					$this->createWidgetContent($widget_data, true, 
											   $normal_select);

				$widget = 
					str_replace('(#(inherent:'.$widget_info.')#)', 
								$widget_data, $widget);
			}
		}
		return $widget;
	} // end of inherent_widgets function

} // end of CreateLayout Class
