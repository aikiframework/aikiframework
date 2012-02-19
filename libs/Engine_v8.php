<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author	  Roger Martin (rg1024) - Aikilab http://www.aikilab.com
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license	 http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 * @category	Aiki
 * @package	 Library
 * @version 0.01 ALPHA!!
 * @filesource
 * PARENT
 * update aiki_widgets inner join aiki_widget_porsi on aiki_widgets.father_widget=aiki_widget_porsi.id set parent_widget= aiki_widget_porsi.widget_name
 */

if (!defined('IN_AIKI')) {
	die('No direct script access allowed');
}

class engine_v8 {

	private $widget_css;
	private $widget_html;

	/*
	 * Create layout
	 */

	function layout( $parameters ){
		global $db, $aiki;

		// Initialize
		
		// @TODO javascript? id for some elements?
		$this->target = array(
			"body"=>"" ,
			"header"=>"",
			"css"=>array() );

		// the widget is given directly or
		if (isset($_GET["widget"])) {
			if ($getWidgetId = $aiki->widgets->get_widget_id($_GET['widget'])) {
				return $this->parse($getWidgetId);
			}
			return;//all work is done
		}

		// or in url,
		// search widget and test there is a unique response
		$module_widgets = $aiki->widgets->get_candidate_widgets();
		$unique_widget_exists = false;
		if ($module_widgets) {
			foreach($module_widgets as $tested_widget){
				if ($tested_widget->display_urls != "*"){
					$unique_widget_exists = true;
					break;
				}
			}
		}

		// Error 404 page not found
	    $allMatch=false;
		if (!$unique_widget_exists) {
			// first look for widget that responds error_404,
			// else use config error_404.
			$module_widgets= $aiki->widgets->get_Page_Not_Found_Widgets();
			if ( $module_widgets ) {
				$aiki->Errors->pageNotFound(false);
				$allMatch = true;
			} else {
				return $aiki->Errors->pageNotFound(true);
			}
		}

		// now filter canditate widgets, before create content
		foreach ( $module_widgets as $parent ) {

			// first parent
			if ( $allMatch or
			    ($aiki->url->match($parent->display_urls) && !$aiki->url->match($parent->kill_urls)) ) {
				if ( $parent->have_css == 1) {
					$this->target["css"][] = $parent->id;
				}
				$this->target[$parent->widget_target] .= $this->parse($parent->id);

				// children..
				/* @TODO..a function */
				if ( is_array($descendants = $aiki->widgets->get_candidate_widgets($parent->id)) ){
					foreach ($descendants as $descendant){
						if ( $aiki->url->match($descendant->display_urls) && !$aiki->url->match($descendant->kill_urls) ) {
							$this->target["css"][] = $descendant->id;
							$this->target[$descendant->widget_target] .= $this->parse($descendant->id);
						}
					}
				}
			}
		}

		return $this->render_html();
	}


	function render_html(){	
		global $aiki;
		$html  = $aiki->Output->header($this->target['css'],  $this->target['header']);
		$html .= $aiki->Output->body($this->target['body']);
		$html .= $aiki->Output->end();

		return $html;
	}


	/**
	 *  parse a given widget
	 */

	function parse($widgetID){
		global $aiki, $db;

		// preParsers and postParser can be:
		// regex (string)  => function | array(object,method).
		// a numeric index => function | array(object,method).

		$preParsers = array (
		    '/\[\$([a-z0-9_]+)\]/i'                    => array( $this, "parse_vars"),
		    '/\[GET\[([a-z0-9]+)\]\]/i'				   => array( $this, "parse_get"),
		    '/\[POST\[([a-z0-9]+)\]\]/i'			   => array( $this, "parse_post"),
		    "/\(template\(([a-z0-9_]*)\)template\)/Ui" => array( $this, "parse_template")) ;
                
		$postParsers =array (
			 array ($aiki->languages,"L10n") ) ;  // added for test purpose only

		// parsers are defined here
		$parsers = array (
		    "widget"      => "parse_widget",
			"permissions" => "parse_permissions",
			"view"        => "parse_view",
			"noaiki"      => "parse_noaiki",
			"sql"         => "parse_sql",
			"script"      => "parse_script",
			"t"			  => "parse_t",
			"__"		  => "parse_translate");

		$widgetData = $db->get_row ("SELECT widget,widget_name FROM aiki_widgets WHERE id=" . (int) $widgetID );
		$widget    = $widgetData->widget;
		$widgetName= $widgetData->widget_name;

		// process pre-parsers.
		foreach ( $preParsers as $pattern => $callback ){
			if ( is_string($pattern) ){
				$widget = preg_replace_callback ( $pattern, $callback, $widget);
			} else {
				$widget = call_user_func   ( $callback, $widget);
			}
		}

		// now the normal parser.
		$match = false;
		$offset=0;
		while ( $match = $aiki->outer_markup ( $widget,$offset ) ){
			$parserToCall= $match[2];
			$len        = strlen($parserToCall)+2;

			// call parser
			if ( isset( $parsers[ $parserToCall]) ){
				$text    = substr($widget, $match[0]+ $len,$match[1]-$len );
				$replace= call_user_func( array($this, $parsers[ $parserToCall] ), &$text);
				if (is_int($replace) ){ // necesary for noaki
					$offset += $replace;
					continue;
				}
			} else {
				// @TODO Error
				$replace= t("Parser $parserToCall not found");
			}

			//Replacement.
			$widget= substr($widget,0,$match[0]) . $replace . substr($widget,$match[0]+$match[1]+$len) ;
		}
		
		
		// process post-parsers.		
		foreach ( $postParsers as $pattern => $callback ){		
			if ( is_string($pattern) ){
				$widget = preg_replace_callback ( $pattern, $callback, $widget);
			} else {
				$widget = call_user_func   ( $callback, $widget);
			}
		}
		
		if ( is_debug_on() ){
			return "\n<!-- start {$widgetName} ($widgetID) -->" . $widget . "\n<!-- end {$widgetName} ($widgetID) -->";
		}		
	    return $widget;
	}


    /**
     *  parse vars
     */

	function parse_vars($match){
		static $bufferReplace;
		global $aiki, $page;

		if ( $bufferReplace == NULL ) {
			/* @TODO unified with aiki processVars*/

			$pretty = $aiki->config->get('pretty_urls', 1);
			$url = $aiki->config->get('url');

			$current_month = date("n");
			$current_year = date("Y");
			$current_day = date("j");

			// calculate view, prefix, route
			$view = $aiki->site->view();
			$language = $aiki->site->language();
			$prefix = $aiki->site->prefix();
			$view_prefix= $aiki->site->view_prefix();
				
			$paths= array();
			if ($prefix) {
				$paths[] = $prefix;
			}

			if ($view_prefix) {
				$paths[] = $view_prefix;
			}
			if ( count($aiki->site->languages()) > 1 ) {
				$paths[] = $language;
			}
			$paths = implode("/",$paths);
			
			if ( isset($_SERVER["HTTPS"])) {
				$url = str_replace("http://", "https://", $url);
			}	

			$trimedUrl = preg_replace('#/$#',"",$url); // reg: remove ending /
							
			$bufferReplace = array(
				'[$userid]'	=> $aiki->membership->userid,
				'[$full_name]' => $aiki->membership->full_name,
				'[$username]'  => $aiki->membership->username,
				'[$user_group_level]' => $aiki->membership->group_level,
				'[$user_permissions]' => $aiki->membership->permissions,			
				'[$language]'  => $aiki->site->language(),		   
				'[$page]'	  => $page,
				'[$site_name]' => $aiki->site->site_name(),
				'[$site]'	  => $aiki->site->get_site(),
				'[$view]'	  => $aiki->site->view(),
				'[$direction]' => $aiki->languages->dir,
				'[$insertedby_username]' => $aiki->membership->username,
				'[$insertedby_userid]' => $aiki->membership->userid,
				'[$current_month]' => $current_month,
				'[$current_year]' => $current_year,
				'[$current_day]' => $current_day,
				'[$root]'		  => $url,
				'[$root-language]' => $trimedUrl .  "/" . $aiki->site->language(),
				'[$site_prefix]'   => $prefix ,
				'[$view_prefix]'   => $view_prefix ,
				'[$route]'		 => $trimedUrl.  "/". $paths,
				'[$route-local]'	 => $paths );
			}

		$token = $match[1];
		if ( isset($bufferReplace[$token]) ){
			return $bufferReplace[$token];
		} else {
			return $match[0];
		}
	}

	function parse_get( $matchs){
		$token= $matchs[0];
		return $token && isset($_GET[ $token]) ? $_GET[$token] : "";
	}

	function parse_post( $token){
		$token= $matchs[0];
		return $token && isset($_POST[$token]) ? $_POST[$token] : "";
	}


	/*
	 * Parse template
	 */
	function parse_template($matches){
		global $db;
		$id= $this->get_widget_id($matches[1]);
		return  is_null($id) ? "": $db->get_var ("SELECT widget FROM aiki_widgets WHERE id='$id'" );
	}

	function parse_script($code){
		global $aiki;
		return $aiki->AikiScript->parser($code,false);
	}


	/**
	 * translation
	 */

	function parse_t($term) {
		static $translate;
		global $aiki;
		if ( is_null($translate) ){
			$translate= $aiki->site->language()!="en";
		}
		return $translate ? t($term): $term ;
	}


	function parse_translate($term) {
		return __($term);		
	}



	/*
	 * Parse sql markup
	 */

	function parse_sql( &$text){
		global $db;				
		if ( strpos($text,"||")===false){
			return $text;
		}
		list($select,$content)= explode("||", $text,2);
		
		$results = $db->get_results($select);

		$html="";
		if ($results) {
			foreach ( $results as $row ) {
				$fields= array();
				foreach ( $row as $field=>$value ){
					$fields[ "[$field]" ]= $value;
				}
				$html .= strtr( $content, $fields);
			}
		}
		return $html;

	}

	/*
	 * Parse hits
	 * @TODO implements trigger.
	 */

	private function parse_hits(&$hidData) {
		global $db;

		$hit = explode("|", $hitData);
		if ( len($hit) == 3 ){
			$db->query(
					"UPDATE {$hit[0]}".
					" SET {$hit[2]}={$hit[2]}+1".
					" WHERE {$hit[1]}");
		} elseif (is_debug_on() ) {
				return sprintf( __("BAD HITS PARAMETERS: 3 expected, %d  given"), len($hit) );
		}
		return "";
	}


	function parse_widget( &$text ){

		if ( strpos( $widget,"||")!== false ) {
			list($wigetId, $select) = explode("||",$widget,2);
		} else {
			$widgetId= $widget ;
		}

		return  $this->parse($widgetId, $select);
	}


	function parse_view( &$text){
		global $aiki;
		if ( strpos($text,"||") !== false ){
			list($filter,$content) = explode("||", $text, 2);
			if ($trim($filter)=="") {
				return $text;
			}
		} else {
			return $text;
		}
		
		list($view,$language)= exlode("/",$filter."/*",2);
	
		if  ( match_pair_one( $view, $aiki->site->view()) &&
		      match_pair_one( $language, $aiki->site->language() )){
			return $content;
		}
		return "";

	}


	function parse_permission($widget){
		global $aiki, $db;
		if ( strpos($widget,"||") !== false ){
			list($filter,$content) = explode("||", $widget, 2);
		} else {
			return $widget;
		}

		/* fake permission */
		if ( trim($filter) == "user" ){
			return $content;
		}
		return "";

		$sql = "SELECT group_level" .
			   " FROM  aiki_users_groups".
			   " WHERE group_permissions='". addslashes($filter) ."'";

		$get_group_level = $db->get_var($sql);

		if ( trim($filter) == $aiki->membership->permissions ||
			$aiki->membership->group_level < $get_group_level ) {
			return $content;
		}

		return "";
	}

	function parse_noaiki(&$text){
		return strlen($text) ;
	}


}
