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
	

if (!defined('IN_AIKI')){
	die('No direct script access allowed');
}


/**
 * Creates the Aiki page layout.
 *
 * @category	Aiki
 * @package	 System
 *
 * @todo refactor this all, or else!
 */
class CreateLayout {
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
	 * Boolean: to create a cache for this widget.
	 */
	public $create_widget_cache;

	/**
	 * Stores a string with a the "list" of widget included in widget. 
	 * 
	 * it will be used for loading style as  as 
	 * <link rel='stylesheet' .. href="style.php?site=default&amp;widgets=14_21_16_595_17....
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
	 * @global aiki   $aiki
	 */
	public function CreateLayout() {
		global $db, $aiki;

		// Initialize
		$this->widgets_css = array();

		// Convert global_values to an object to cache the SOL results in parsDBpars function.
		$this->global_values = new stdClass();

		// the widget is given directly or
		if (isset($_GET["widget"])) {
			if ($getWidgetId = $this->get_widget_id($_GET['widget'])) {
				$this->createWidget($getWidgetId);				
			}
			return;//all work is done
		}		
		
		// or in url, search widget and test there is a unique response
		$module_widgets = $this->get_candidate_widgets();
		$unique_widget_exists = false;		
		if ($module_widgets) {				
			foreach($module_widgets as $tested_widget){
				if ($tested_widget->display_urls != "*"){
					$unique_widget_exists = true;
					break;
				}
			}		
								
		}
		
		$widget_group = array();
		// ..page not found..
		if (!$unique_widget_exists) {
			$aiki->Errors->pageNotFound();
			$widget_group = $this->getPageNotFoundWidgets();
		} else {
			// now filter canditate widgets, before create content
			foreach ($module_widgets as $widget) {
				if ($aiki->url->match($widget->display_urls) && 
					!$aiki->url->match($widget->kill_urls) ) {
					$widget_group[] = $widget->id;
				}
			}	
		}
		$this->createWidget($widget_group);			
	}


	/**
	 * Creates a widget internal to this class.
	 * 
	 * @param integer $widget_id id of a widget
	 * @param string  $widget_group the group of the widget
	 * @global array  $db global database object
	 * @global aiki   $aiki glboal aiki object
	 * @global string $url 
	 * @global string $custom_output
	 *
	 * @todo this all need to be broken down into helper functions, too big!
	 */
	private function createWidget($widget_group) {
		global $db, $aiki, $custom_output;

		/**
		 * Daddy this is where widgets come from...
		 *
		 */
		if ( is_array($widget_group) && count($widget_group) > 0 ) {
			$widgets_list  = "id='" . implode("' or id='", $widget_group) . "'";
			$widgets_query = "SELECT * FROM aiki_widgets WHERE $widgets_list ORDER BY display_order, id";
		} elseif ($widget_group) {
			$widgets_query = "SELECT * FROM aiki_widgets WHERE id='{$widget_group}' LIMIT 1";
		} 

		if (isset($widgets_query)) {
			$widget_result = $db->get_results($widgets_query);
		}
		if ( !isset($widgets_query) || is_null($widget_result) ) {
			return ; // @TODO debug error
		}
		   
		/**
		 * Where the search for widgets, gets put into a widget
		 * @TODO so the widget itself is scoped wrong! should be
		 *		 outside this block to be proper!
		 * @TODO This is totally too long iteration to create $widget
		 */
	
		foreach ($widget_result as $widget) {

			if ($widget->css) {
				$this->widgets_css[]= $widget->id ;
			}	

			if ($widget->custom_output) {
				$custom_output = true;
				$this->widget_custom_output = true;
			}

			if ( $widget->custom_header && $widget->custom_header != '' ) {
				$custom_headers = explode("\n", $widget->custom_header);
				foreach ($custom_headers as $custom_header) {
					if ( $custom_header != "" ) {
						header($custom_header);
					}
				}
			}
			/**
			 * @todo all output of comments needs to be an option, since
			 *		 it makes output pages bigger by default.
			 *		 Should only turn on for debug mode IMO.
			 */
			
			
			
			if (!$custom_output) {
				$this->widget_html .=
					"\n <!--start {$widget->widget_name}({$widget->id})--> \n";
				
				if ( $widget->widget_type &&
					$widget->remove_container != 1 ) {

					$this->widget_html .= 
						"<$widget->widget_type id=\"$widget->widget_name\"";

					if ($widget->style_id){
						$this->widget_html .= " class=\"$widget->style_id\"";
					}
					$this->widget_html .= ">\n";
				}
			}

			$this->createWidgetContent($widget);

			if ($widget->is_father)	{
								
				$son_widgets = $this->get_candidate_widgets($widget->id);					
						
				if ($son_widgets) {
			
					$son_widget_group = array();

					foreach ($son_widgets as $son_widget) {
						if ( $aiki->url->match($son_widget->display_urls) &&
							!$aiki->url->match($son_widget->kill_urls) ) {
							$son_widget_group[] = $son_widget->id;
						}

					}
					$this->createWidget($son_widget_group);
					$son_widget_group = '';
				}
			} // end of handling father

			if (!$custom_output) {
				if ($widget->widget_type and 
					$widget->remove_container != 1) {
					$this->widget_html .= "\n</$widget->widget_type>\n";
				}
				
				/**
				 * @todo all output of comments needs to be an option, since
				 *		 it makes output pages bigger by default.
				 *		 Should only turn on for debug mode IMO.
				 */
				$this->widget_html .=
					"\n <!--{$widget->widget_name}({$widget->id}) end--> \n";
			}
			

			if ($this->kill_widget) {
				if ($widget->if_no_results) { 
					
					$dead_widget = 
						'<'.$widget->widget_type.' id="'.
						$widget->widget_name.'">' . $this->parse_no_results($widget->if_no_results) .
						'</'.$widget->widget_type.'>';
				} else {
					$dead_widget = "";
				}
				/**
				 * @todo looks like some text is placed into the output
				 *		 stream and then replaced here!!! Nooo!
				 */
				$subpattern="[A-z0-9\-_]*\({$this->kill_widget}\)";	
				$this->widget_html = 
						preg_replace("/<!--start {$subpattern}-->(.*)<!--{$subpattern} end-->/s", 
									   $dead_widget, $this->widget_html, 1, $count);
				$this->kill_widget = '';
				
			}

			switch ($widget->widget_target){
				case "body": 
					$this->html_output .= $this->widget_html;
					break;
				case "header": 
					$this->head_output .= $this->widget_html; 
					break;
			}

			$this->widget_html = "";
		} // end of foreach iterating over results to get/set $widget
	
	} // end of createWidget method


	/** 
	 * Creates Widget Content
	 *
	 * @todo keep going
	 * @param	array		$widget				the widget to create
	 * @param	string		$normal_select		inline widget can define the 
	 *										  normal_select.
	 * @global	aiki		$aiki				global object
	 * @global	array		$db					global db object
	 * @global	membership	$membership			global membership object
	 * @global	bool		$nogui				
	 * @global	string		$custom_output
	 * @global	array		$config				global array of config options
	 * @return	mixed
	 *
	 * @todo needless to say, this has to be refactored or redone. 
	 */
	
	
	
	private function createWidgetContent($widget, $normal_select=false) {
		global $aiki, $db, $membership, $nogui, $custom_output, $config;

		$is_inline = ( $normal_select !== false ? true : false );
		$stopcaching = false;
		if ( isset($config["widget_cache"]) && 
			$config["widget_cache"] &&
			isset($config["widget_cache_dir"]) &&
			$widget->widget_cache_timeout ) {
			
			// Get widget ready for cache.
			if ($widget->normal_select) {
				$widget_cache_id = $widget->id . "_" . $_SERVER['QUERY_STRING'];
			} else {
				$widget_cache_id = $widget->id;
			}

			$widget_file = 
				$config["widget_cache_dir"] . '/' . md5($widget_cache_id);

			$widget_cache_timeout = $widget->widget_cache_timeout;
		} else {
			$stopcaching = true;
		}

		// Security check to determine which widget content to display.	
		if ( $widget->is_admin 
			&& $membership->permissions 
			&& $widget->if_authorized 
			&& $membership->have_permission($widget->permissions) ) {
				$widget->widget = $widget->if_authorized;
				$widget->normal_select = $widget->authorized_select;
				$stopcaching = true;	
		}

		if ( !$stopcaching and 
			 $widget_cache_timeout > 0 and 
			 file_exists($widget_file) and 
			 ( (time() - filemtime($widget_file)) < ($widget_cache_timeout) ) and
			 $membership->permissions != "SystemGOD" and 
			 $membership->permissions != "ModulesGOD" ) {
			
			// Display widget from cache.
			$widget_html_output = file_get_contents($widget_file);
			$this->widget_html .= $widget_html_output;
			$this->create_widget_cache = false;
			return;
		} 
		
		
		// Widget can't be rendered from cache.
		// Flag the widget as cachable, and try to delete the old cache file
		$this->create_widget_cache = true;
		if ( isset($widget_file) and 
			 $membership->permissions != "SystemGOD" and 
			 $membership->permissions != "ModulesGOD" and 
			 !$stopcaching ) {
			if (file_exists($widget_file)) {
				unlink($widget_file);
			}
		}

	
		if ($is_inline) {
			$widget->pagetitle = '';
		}

		if ( $widget->nogui_widget && isset($nogui) ) {
			$widget->widget = $widget->nogui_widget;
		}
		
		/**
		 * @TODO why is this commented out? if no takers, delete!
		 */
		//$widget->widget = htmlspecialchars_decode($widget->widget);

		$widget->widget = $aiki->view_parser->parse(
			$widget->widget, 
			$aiki->site->view(),
			$aiki->site->language());

		$widget->widget = $aiki->input->requests($widget->widget);
		$widget->widget = $aiki->processVars($widget->widget);


		// noloop part are extracted and deleted.
		$no_loop_part = $aiki->get_string_between(
			$widget->widget, 
			'(noloop(', ')noloop)');

		$widget->widget = str_replace(
			'(noloop(' . $no_loop_part . ')noloop)', '', 
			$widget->widget);

		$no_loop_bottom_part = $aiki->get_string_between(
			$widget->widget, 
			'(noloop_bottom(', ')noloop_bottom)');

		$widget->widget = str_replace(
			'(noloop_bottom(' . $no_loop_bottom_part . ')noloop_bottom)', '', 
			$widget->widget);

		$widget->normal_select = $this->parse_select(
			$widget->normal_select,
			$normal_select);
		
		
		if (!$widget->normal_select) {
			$processed_widget = $this->parse_widget_without_data($widget->widget);
		} else {				
			$selects = explode("|OR|", $widget->normal_select);
			foreach ($selects as $select) {								
				$widget->normal_select = $select;
				$records_num = $this->records_num($widget->normal_select);
				
				// pagination change normal_select adding limit.
				$pagination = $this->pagination($widget, $records_num);
				
				$widget_select = $db->get_results($widget->normal_select);
				if ($widget_select) {
					break;
				}	
			}		
				
			if ( $records_num !== false ) {
				$widget->widget = str_replace("[records_num]", $records_num, $widget->widget);
				$template = $widget->widget;
				$num_results = $db->num_rows;
			} 						
			
			if ( $widget_select && isset($num_results) && $num_results > 0 ) {
				$widgetContents = '';
				foreach ($widget_select as $widget_value) {
					/**
					 * @todo put this behind debug time option
					 */
					if (!$custom_output) {
						$widgetContents .= 
							"\n<!-- The Beginning of a Record -->\n";
					}
					$widgetContents .=  $this->parse_widget_with_data(
						$template,
						$widget_value);
					if (!$custom_output) {
						$widgetContents .= 
							"\n<!-- The End of a Record -->\n";
					}
				} // end of foreach

				// puts records in row
				if ( $widget->display_in_row_of > 0 ) {
					$widgetContents = $aiki->Output->displayInTable(
						$widgetContents, 
						$widget->display_in_row_of);
				}
				
				// more parser..
				$widgetContents = $this->noaiki($widgetContents);
				$widgetContents = $aiki->url->apply_url_on_query($widgetContents);
				$widgetContents = $aiki->security->inlinePermissions($widgetContents);
			
				$widgetContents =
					$this->parsDBpars($no_loop_part, $widget_value).
					$widgetContents.
					$this->parsDBpars($no_loop_bottom_part, $widget_value);
				
				// now widget is complete other parser can aplied.
				$widgetContents = $aiki->AikiScript->parser($widgetContents);
				$widgetContents = $this->inline_widgets($widgetContents);
				$widgetContents = $this->inherent_widgets($widgetContents);
				$widgetContents = $aiki->SqlMarkup->sql($widgetContents);


				// Hits sustitution 
				$widgetContents = $this->parse_hits($widgetContents);			
				
				// insert pagination.
				if ( $pagination != false && strpos($widgetContents, "[no_pagination]") === false ) {
					if (strpos($widgetContents, "[pagination]")) {
					$widgetContents = str_replace(
						"[pagination]",
						$pagination,
						$widgetContents);
					} else {	
						$widgetContents .= $pagination;
					}
				}
				$widgetContents = str_replace("[no_pagination]", "", $widgetContents);

				
				/**
				 * Delete empty widgets
				 * @todo hide this behind debug time option
				 */
				if ($widgetContents == 
					"\n<!-- The Beginning of a Record -->\n".
					"\n<!-- The End of a Record -->\n"){
					$this->kill_widget = $widget->id;
				} else {
					$processed_widget = $widgetContents;
				}
			} else {
				$this->kill_widget = $widget->id;
			}
		} 


		if (!isset($processed_widget)) {
			$processed_widget = '';
		} else {	
			
			$processed_widget = $this->parsDBpars($processed_widget, '');
			$processed_widget = $aiki->processVars($processed_widget);
			$processed_widget = $aiki->url->apply_url_on_query($processed_widget);
			$processed_widget = $aiki->text->aiki_nl2br($processed_widget);
			$processed_widget = $aiki->text->aiki_nl2p($processed_widget);
			
			$processed_widget = $aiki->processVars($processed_widget);
			$processed_widget = $aiki->parser->process($processed_widget);
			$processed_widget = $aiki->AikiArray->displayArrayEditor($processed_widget);
			$processed_widget = $this->parse_translate_aiki_core($processed_widget);
			$processed_widget = $this->parse_translate_widget($processed_widget);
			// Apply (#(header:...
					
			$processed_widget = $this->parse_header($processed_widget);
			$processed_widget = $aiki->Forms->displayForms($processed_widget);
			$processed_widget = $aiki->input->requests($processed_widget);
			$processed_widget = $aiki->AikiScript->parser($processed_widget);
			
			//$processed_widget = stripslashes($processed_widget);
		}
		
		

		
			
		if (isset($widget_cache_id)) {
			$widget_cache_id_hash = md5($widget_cache_id);
			$processed_widget = 
				str_replace("(#(cache_file_name)#)", $widget_cache_id_hash, 
							$processed_widget);
		}

		// Set page title.
		if ($widget->pagetitle) {
			$widget->pagetitle = $aiki->processVars($widget->pagetitle);
			$widget->pagetitle = $aiki->url->apply_url_on_query($widget->pagetitle);

			if (!isset($widget_value)) {
				$widget_value = '';
			}

			$title = $this->parsDBpars($widget->pagetitle, $widget_value);
			$title = $aiki->input->requests($title);
			$aiki->Output->set_title($title);
		}

		

		if (isset($widgetContents) and 
			$widgetContents == "\n<!-- The Beginning of a Record -->\n\n<!-- The End of a Record -->\n") {
			$this->kill_widget = $widget->id;
		} else {
			if (isset($config["widget_cache"]) and 
				$config["widget_cache"] and 
				$this->create_widget_cache and 
				$config["widget_cache_dir"] and 
				is_dir($config["widget_cache_dir"]) and 
				!$membership->permissions and 
				$widget->widget_cache_timeout > 0) {
				$processed_widget_cach = 
					$processed_widget."\n\n<!-- Served From Cache -->\n\n";
				$processed_widget_cach = 
					$aiki->languages->L10n($processed_widget_cach);

				error_log ( $processed_widget_cach, 3, $widget_file);
			}
		}

		if ( $membership->permissions == "SystemGOD" and 
			$widget->widget and 
			$config['show_edit_widgets'] == 1 and 
			$widget->widget_target == 'body' and 
			!preg_match("/admin/", $widget->display_urls) and 
			$widget->custom_output == 0 ) {
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
		if ($is_inline) {
			if ( !$processed_widget and $widget->if_no_results ) {
				$widget->if_no_results = 
					$aiki->processVars($widget->if_no_results);
				return $widget->if_no_results;
				//return stripslashes($widget->if_no_results);
			} else {
				return $processed_widget;
			}
		} else {

			$this->widget_html .=  $processed_widget;
		}
	} // end of createWidgetContent() 


	private function pagination($widget, $records_num) {
		global $db, $aiki;
		
		if (!$widget->records_in_page) {
			return false;
		}
		
		$pagesgroup = 10;
		$group_pages = false;

		$pagination = "";

		$page = isset($_GET['page']) ? addslashes($_GET['page']) : 0;
		if ( $page > 0 ) {
			$page = $page - 1;
		}	

		if ( $widget->records_in_page and !$widget->link_example ) {
			$widget->link_example = "?page=[page]";
		}
		
		// Custom pages links setting from link_example.
		if (isset($widget->link_example)){
			$link_config = preg_match(
				'/config\[(.+)\]/U', 
				$widget->link_example, 
				$link_config_data);
			if ($link_config) {
				if (preg_match('/group\_by\:/',$link_config_data[1])) {
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
		
		// calculate the number of pages and prepare the sql 
		// to extract this rows
		$numpages=0;
		if ( $widget->link_example and
			$widget->records_in_page and
			$widget->normal_select ) {
			if ( $records_num >= $widget->records_in_page ) {
				$numpages = ceil($records_num / $widget->records_in_page);
			} else {
				$numpages = 1;
			}
			$fnumre = $page * $widget->records_in_page;
			
			// Remove LIMIT clausule 
			$widget->normal_select = preg_replace(
				'/ LIMIT[0-9, ]+$/i',"",$widget->normal_select);

			$widget->normal_select = 
				$widget->normal_select." LIMIT $fnumre,".
				$widget->records_in_page;
		}

		if ( $numpages <= 1 ) {
			return "<div class='pagination'></div>";
		}
			
		$widget->link_example = $aiki->input->requests($widget->link_example);

		/**
		 * @todo abstract this all into a pagination class
		 */

		$numpages = $numpages + 1; // WHAT?????

		$full_numb_of_pages = $numpages;
		$pagination = '';
		$page2 = $page;
		
		/**
		 * @todo the harcoded pagination must die
		 */
		$pagination .= 
		  "<div class='pagination'>" .
		  "<span class='pagination_move_to_page'>" . 
		  __("move to page") . "</span><br />";

		if ($page) {
			$previous = 
				str_replace("[page]", $page, 
							$widget->link_example);
			/**
			 * @todo more hardcoded pagination, must change!
			 */
			$pagination .= 
			  "<span class='pagination_previous'>" . 
			  "<a href=\"$previous\">" . __("previous") . "</a></span>";
		}

		if ($group_pages)
		{
			$numpages = $pagesgroup;
			$numpages = $numpages + $page + 1;

			if ( $page > ($pagesgroup / 2) )
			{
				$pages_to_display = $page - (int)($pagesgroup / 2);
				$numpages =  $numpages - (int)($pagesgroup / 2);
			} else {
				$pages_to_display = 0;
			}

			if ( $numpages > $full_numb_of_pages )
				$numpages = $full_numb_of_pages;

			for ($i = $pages_to_display +1; $i < $numpages; $i++) {
				/**
				 * @todo more hardcoded html
				 */
				if ( $i == $page + 1 ) {
					$pagination .= 
						"<span class='pagination_notactive'>" . 
						" $i </span>";
				} else {
					$next_link = 
						str_replace("[page]", $i, 
						$widget->link_example);
					$pagination .= 
						"<span class='pagination_active'>" . 
						" <a href=\"$next_link\">$i</a> </span>";
				}
			} // end of for loop

		} else {

			for ($i = 1; $i < $numpages; $i++) {
				if ( $i == $page + 1 ) {
					$pagination .= 
						"<span class='pagination_notactive'>".
						" $i </span>";
				} else {
					$next_link = str_replace("[page]", $i, 
								 $widget->link_example);
					$pagination .= 
					  "<span class='pagination_active'>" . 
					  " <a href=\"$next_link\">$i</a> </span>";
				}
			}
		}

		if ( $page+2 != ($numpages) ) {
			$next = str_replace("[page]", $page + 2, 
								$widget->link_example);
			$pagination .= 
			  "<span class='pagination_next'>". 
			  "<a href=\"$next\">" . __("next") . "</a></span>";
		}

		if($page) {
			$first_page = str_replace("[page]", '1', 
				$widget->link_example);
			$pagination .= 
			  "<span class='pagination_first'>" . 
			  " <a href=\"$first_page\">" . __("first page") . "</a> " .
			  "</span>";
		}

		if( $page != ($numpages-2) ) 
		{
			$last_page = str_replace("[page]", 
									 $full_numb_of_pages -1, 
									 $widget->link_example);
			$pagination .= 
			  "<span class='pagination_last'>" . 
			  "<a href=\"$last_page\">" . __("last page") . "</a>" . 
			  "</span>";
		}
		
		if (isset($next)) {					
			$pagination = str_replace("[next]", $next, $pagination);
		}	

		if (isset($previous)) {
		  $pagination = str_replace("[previous]", $previous, $pagination);
		}
		
		$pagination .= "</div>";
		return $pagination;	   	
	} // basically end of pagination code




	/**
	 * Handles content not to be processed by aiki markup
	 *
	 * @param	string	$text	for processing
	 * @global	aiki	$aiki	global aiki object
	 * @return	string
	 */
	public function noaiki($text) {
		global $aiki;

		$widget_no_aiki = 
			$aiki->get_string_between($text, "<noaiki>", "</noaiki>");

		if ($widget_no_aiki) {
			$html_widget = 
				$aiki->convert_to_specialchars($widget_no_aiki);

			$text = 
				str_replace("<noaiki>$widget_no_aiki</noaiki>", 
							$html_widget, $text);
		}
		return $text;
	}

	private function parse_widget_with_data($template, $values) {
		global $aiki;
		$template = $aiki->parser->datetime($template, $values);			
		$template = $aiki->parser->tags($template, $values);
		$template = $this->noaiki($template);
		$template = $this->parsDBpars($template, $values);
		$template = $aiki->Records->edit_in_place($template, $values);
		$template = $aiki->text->aiki_nl2br($template);
		$template = $aiki->text->aiki_nl2p($template);
		return $template;
	}


	private function parse_widget_without_data($template) {
		global $aiki;
		$template = $this->parsDBpars($template, '');
		$template = $this->noaiki($template);
		$template = $aiki->url->apply_url_on_query($template);
		$template = $aiki->security->inlinePermissions($template);
		$template = $this->inline_widgets($template);
		$template = $this->inherent_widgets($template);
		$template = $aiki->SqlMarkup->sql($template);
		return $template;
	}




	/**
	 * @param	string	$text			text for processing
	 * @param	array	$widget_value
	 * @global	aiki	$aiki			global aiki object
	 * @return	string
	 */
	private function parsDBpars($text, $widget_value = '') {
		global $aiki;

		$count = preg_match_all('/\({2,}(.*?)\){2,}/', $text, $matches);

		if (!$widget_value) {
			$widget_value = $this->global_values;
			$cached_values = true; //so it don't cache them again
		} else {
			$cached_values = false;
		}

		foreach ($matches['1'] as $parsed) {
			if ($parsed) {
				$is_array = $aiki->get_string_between($parsed, "[", "]");
				if ($is_array) {
					$parsed_array = str_replace("[$is_array]", "", $parsed);
					$array = @unserialize($widget_value->$parsed_array);
					if (isset($array["$is_array"])) {
						$widget_value->$parsed = $array["$is_array"];
					} else {
						$widget_value->$parsed = '';
					}
				}

				/**
				 * @todo what is this? if no claimants, lets nuke
				 */
				// ((if||writers||writer: _self))

				$parsedExplode = explode("||", $parsed);

				if ( isset($parsedExplode['1']) and $parsedExplode[0] == "if" ) {
					$cached_values = true;

					if (isset($widget_value->$parsedExplode['1'])) {
						$parsedValue = $widget_value->$parsedExplode['1'];
					}
					if ( isset($parsedValue) and $parsedValue ) {
						$parsedExplode[2] =
							str_replace("_self", $parsedValue, 
										$parsedExplode[2]);
						$widget_value->$parsed = $parsedExplode[2];
					} elseif (isset($parsedExplode[4]) and 
							 $parsedExplode[4] and 
							 $parsedExplode[3] == "else") {
						$else_stetment = explode(":", $parsedExplode[4]);

						if ( $else_stetment[0] == "redirect" and 
							$else_stetment['1'] ) {
							/**
							 * @todo don't we have a tag builder?
							 */
							$text_values .= 
								"<meta HTTP-EQUIV=\"REFRESH\" " . 
								"content=\"0; url=".$else_stetment['1']."\">";
							if (!$widget_value->$parsed) {
								$widget_value->$parsed = $text_values;
							}
						}
					}
				}

				if (!isset($widget_value->$parsed) and 
					isset($this->global_values->$parsed)) {
					$widget_value->$parsed = $this->global_values->$parsed;
				}

				if (!isset($widget_value->$parsed)) {
					$widget_value->$parsed = '';
				}
				$widget_value->$parsed = 
					$aiki->security->remove_markup($widget_value->$parsed);

				// If there are results and the results are not from cache 
				// then cache them.
				if ( $widget_value->$parsed and !$cached_values ) {
					$this->global_values->$parsed = $widget_value->$parsed;
				}
				$text = str_replace("(($parsed))", $widget_value->$parsed, 
									$text);
			} // end of if ($parsed)
		} // end of foreach
		return $text;
	}

	/**
	 * Proccesed all (t(text_to_translate)t) .
	 *
	 * @PARA  string $widget_content to be translated
	 * @RETUN string widget translated
	 */


	function parse_translate_widget(&$widgetContents) {				
	global $aiki;		
		if (!$aiki->site->need_translation()) {	
			return preg_replace('/\(t\((.*)\)t\)/Us','$1', $widgetContents);
		} else {				
			return preg_replace_callback(
				'/\(t\((.*)\)t\)/Us',
				create_function(
					'$matches',
					'return t($matches[1]);')
				,$widgetContents );
		}		
	}
	
	/**
	 * Proccesed all (__(text_to_translate)__) .
	 *
	 * @PARA  string $widget_content to be translated
	 * @RETUN string widget translated
	 */


	function parse_translate_aiki_core(&$widgetContents) {				
	global $aiki;		
		if ($aiki->site->language()=="en") {	
			return preg_replace('/\(__\((.*)\)__\)/Us','$1', $widgetContents);
		} else {					
			return preg_replace_callback(
				'/\(__\((.*)\)__\)/Us',
				create_function(
					'$matches',
					'return __($matches[1]);')
				,$widgetContents );
		}		
	}
	
	
	/**
	 * Proccesed all (#(hits:..)#) in widget content.
	 *
	 * @PARAM  string $widget_content widget with (#(hit..
	 * @RETUN string widget parsed. (his have been count)
	 */

	private function parse_hits(&$widgetContents) {		
		global $db;
		$hits_counter = preg_match_all(
			"/\(\#\(hits\:(.*)\)\#\)/U",
			$widgetContents,
			$matchs);
		
		if ( $hits_counter > 0 ) {
			foreach ($matchs[1] as $hitData) {
				$hit = explode("|", $hitData);
				$db->query(
					"UPDATE {$hit[0]}".
					" SET {$hit[2]}={$hit[2]}+1".
					" WHERE {$hit[1]}");
			}					   
			return  preg_replace(
				"/\(\#\(hits\:(.*)\)\#\)/U", 
				'', 
				$widgetContents);
		} 
		return $widgetContents;
	}	
		
	
	/**
	 * Parsed a if_no_result.
	 *
	 * @PARAM  string $widget_content widget with (#(header..
	 * @RETUN string widget parsed. (header is sent)
	 */	
		
		
	private function parse_no_results($text) {
		global $aiki;
		//$text = stripcslashes($text);
		$text = $aiki->processVars($text);
		$text = $aiki->url->apply_url_on_query($text);
		$text = $aiki->input->requests($text);
		$text = $aiki->AikiScript->parser($text);
		$text = $this->parse_translate_aiki_core($text);
		$text = $this->parse_translate_widget($text);
		$text = $this->parse_header($text);
		return $text;
	}
		
		
	private function parse_select($select, $inline_select) {
		global $aiki, $membership;
		
		if ($inline_select) {
			$select = trim($inline_select);
		} else {
			// Kill the query if it is not select.
			// roger: this filter is not aplied over $inline_select
			if (preg_match("/TRUNCATE|UPDATE|DELETE(.*)from/i", $select)) {
				return "";
			} else { 
				// roger: i don't know why this parse is applie only on normal_select 
				// and no over inline..Perhaps must remove it.
				$select = strtr($select, array("\n"=> " ", "\r"=>"")); // delete line-feed
				$select = $aiki->input->requests($select); // replace GET[] and POST[]
				$select = $this->parsDBpars($select);
				$select = strtr( 
					$select,
					array("[guest_session]" => $membership->guest_session, 
						"[user_session]" => $membership->user_session));
			}
		}
		
		// more parse
		$select= strtr(trim($select), array ("\'" => "'", '\"' => '"'));
		$select = $aiki->url->apply_url_on_query($select);
		$select = $aiki->languages->L10n($select);
		$select = $aiki->processVars($select);
			
		return $select;	
	}	
		
		
	/**
	 * Proccesed all (#(header:..)#) in widget content.
	 *
	 * @PARAM  string $widget_content widget with (#(header..
	 * @RETUN string widget parsed. (header is sent)
	 */

	private function parse_header(&$widgetContents) {		
		$is_header = preg_match_all(
			"/\(\#\(header\:(.+)\)\#\)/U",
			$widgetContents,
			$match);

		if ($is_header) {
			foreach ($match[1] as $header) {
				$para = explode("|", $header);
				switch ( substr_count($header, "|") ) {
					case 0 : header($para[0]); break;					
					case 1 : header($para[0], $para[1]); break;
					default: header($para[0], $para[1], $para[2]); break;					
				}
			}
			return preg_replace(
				"/\(\#\(header\:(.+)\)\#\)/U",
				'', 
				$widgetContents);
		}
		return $widgetContents;
	}

	
	/**
	 * get a group of widget
	 *	
	 * If no parameters is given search for layout widgets.
	 * 
	 * @param  integer 
	 * @return array  widgets with id,display_urls,kill_urls,
	 */
	
	function get_candidate_widgets($father=0) {
		global $db, $aiki;
		
		$search = $aiki->url->url[0];
		$SQL =
			"SELECT id, display_urls, kill_urls, widget_name FROM aiki_widgets " .
			" WHERE father_widget=$father AND is_active=1 AND " .
			" (widget_site='{$aiki->site}' OR widget_site ='aiki_shared') AND " . // default.
			" (display_urls LIKE '%$search%' OR display_urls = '*' OR ".
			" display_urls LIKE '%#%#%') AND " .
			" (kill_urls='' OR kill_urls not like '%$search%') " .	
			" ORDER BY  display_order, id";
		 return $db->get_results($SQL);
	}	
	
	function getPageNotFoundWidgets() {
		global $db, $aiki;
		
		$SQL =
			"SELECT id FROM aiki_widgets WHERE is_active=1 AND " .
			" (widget_site='{$aiki->site}' OR widget_site ='aiki_shared') AND " .
			" (display_urls LIKE '%error_404%' OR display_urls = '*' OR " .
			" display_urls LIKE '%#%#%') AND " .
			" (kill_urls='' OR kill_urls not like '%error_404%') " .
			" ORDER BY display_order, id";
		return $db->get_col($SQL);
	}
	/**
	 * lookup a widget_id.
	 *	
	 * @param  mixed  v$widgetNameOrId Widget name or id.
	 * @return integer widget_ir
	 */
	  
	private function get_widget_id($widgetNameOrId) {
		global $db;
		if ( (int)$widgetNameOrId > 0 ) {
			$fieldTest= "id='$widgetNameOrId'";
		} else {
			//sql injection test or '		
			$fieldTest = "widget_name='" . str_replace("'", "", $widgetNameOrId) . "'"; 
		}	
	
		$searchSQL =
			"SELECT id FROM aiki_widgets ".
			"WHERE {$fieldTest} AND is_active='1' LIMIT 1" ;	
		return $db->get_var($searchSQL);			
	}


	/**
	 * Handle inline widgets.
	 *
	 * @param	string	$widget	.Input widget
	 * @return	string Output widget.
	 *
	 * @TODO: replace widget with the markup
	 */
	  
	private function inline_widgets($widget){ 
		$matches = "";
		if (preg_match_all('/\(\#\(widget\:(.*)\)\#\)/Us', $widget, $matches)){
			foreach ($matches[1] as $widget_id) {
				$widget_id= $this->get_widget_id($widget_id);	
				$this->createWidget(array($widget_id) );
			}
			$widget = preg_replace('/\(\#\(widget\:(.*)\)\#\)/Us', '', $widget);
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
	 
	private function inherent_widgets($widget) {
		global $db;

		// Fix a typo that was in the first version inherit was called inherent.
		$widget = str_replace("(#(inherit", "(#(inherent", $widget);

		if (preg_match_all('/\(\#\(inherent\:(.*)\)\#\)/Us', $widget, $matches)) {
		
			foreach ($matches['1'] as $i=>$widget_info) {
				$widget_para = explode("|", $widget_info);
				$widget_id  = $this->get_widget_id($widget_para[0]);
				$normal_select = ( isset($widget_para[1]) ? $widget_para[1] : "" );
														
				$widget_data = $db->get_row(
					"SELECT * FROM aiki_widgets WHERE id='{$widget_id}' LIMIT 1");
					
				// widget css is added	
				if ( trim($widget_data->css) <> "" &&
				     !in_array( $widget_data->id, $this->widgets_css)) {					
					$this->widgets_css[]= $widget_data->id ;
				}				
				$widget_html = $this->createWidgetContent($widget_data, $normal_select);
				
				
				

				// if the same widget appears two times..it will be replaced.
				$widget = str_replace($matches[0][$i], $widget_html, $widget);
			}
		}
		return $widget;
	} // end of inherent_widgets function


	/*
	 * Counts number of records of a query.
	 * 
	 * Is used in pagination. Try to make a 'rapid' converting
	 * a SELECT .... FROM in a SELECT count(*) FROM..
	 * 
	 * @PARAM  string $sql Query.
	 * @RETURN mixed  number of records, or false if is not select.
	 */

	private function records_num($sql) {
		global $db;
		
		if (!preg_match('/^select(.*) from /Usi', $sql, $select)){
			return false;
		}
		
		if (stripos($sql, " GROUP BY ") || stripos($sql, " LIMIT")) {
			// with GROUP or LIMIT clausule must do a query 
			$db->get_results($sql);
			$records_num = $db->num_rows;			
		} else {
			// try made a substituion of all select field with count(*)
			
			// Support DISTINCT selection
			if (preg_match('/^select DISTINCT(.*) from/Usi', $sql, $distinct)){
				$mysql_count = ' count(DISTINCT({$distinc[1]})) ';
			} else {  
				$mysql_count = ' count(*) ';
			}
			$new_sql = preg_replace(
				"/^select.*from /Usi",
				"SELECT $mysql_count FROM ",
				$sql);
			// if there is a unique 'ORDER BY', try remove it.	
			if ( substr_count($new_sql, "ORDER BY") == 1 ){	
				$new_sql = preg_replace(
					'/ORDER BY(.*) (DESC|ASC)$/Usi', '', 
					$new_sql);
			}	 
			$records_num = $db->get_var($new_sql);
		}
		return $records_num;		
	}	

}
