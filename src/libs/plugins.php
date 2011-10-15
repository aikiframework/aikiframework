<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Roger Martin  - Aikilab http://www.aikilab.com
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @categor     Aiki
 * @package     Library
 * @filesource
 *
 * A class plugin class for aiki. Based in wordpress plugin systems.
 *
 */

if (!defined('IN_AIKI')) {
	die('No direct script access allowed');
}


/**
 * Plugins class
 * A store of plugins. Plugins loads all active plugins, loads actions
 *
 * Actions is stored in 'three-dimensional' array:
 *  - $actions[action] is array of priorities for a given action.
 *  Before calling plugins, the class checks if priorities are sorted.
 *  - $action[action][priority] is a array containing references to plugin
 *	that must be called.
 *
 */

define ("AIKI_PLUGIN_DIR", "plugins");

class plugins {

	private $plugins;
	private $actions, $must_sorted;

	function __construct(){
		global $aiki, $db;
		global $AIKI_ROOT_DIR;

		$this->plugins = array();
		$this->actions = array();
		$this->must_sorted = array();

		// read active plugins configuration
		$site = $aiki->site->get_site();
		$view = $aiki->site->view();
		$language = $aiki->site->language();
		$sql =
			"SELECT plconf_routes, plconf_values, plconf_plugin_id" .
			" FROM aiki_plugin_configurations" .
			" WHERE plconf_active='active'" .
			" ORDER BY plconf_priority";
		$configurations = $db->get_results($sql);
		if (is_null($configurations)) {
			return;
		}

		// select plugins that match site/view/language
		$pluginsActivated= array();
		foreach ($configurations as $configuration) {
			if (!isset($pluginsActivated[$configuration->plconf_plugin_id]) &&
				 $aiki->match_pair($configuration->plconf_routes,
									$site, $view, $language)){
				// is active plugins
				$pluginsActivated[$configuration->plconf_plugin_id] = $configuration->plconf_values;
			}
		}

		// init plugins: set action and load configuration		
		if (count($pluginsActivated) > 0) {
			$sql =
				"SELECT plugin_id, plugin_file,plugin_class_name FROM aiki_plugins " .
				" WHERE plugin_state='available' AND (plugin_id='" .
				implode("OR plugin_id='", array_keys($pluginsActivated)) . "')";
			$toload= $db->get_results($sql);
			if (!is_null($toload)) {
				foreach ($toload as $load) {
					$file = $AIKI_ROOT_DIR . "/" . AIKI_PLUGIN_DIR . "/" . $load->plugin_file;
					if (file_exists ($file)) {
						include_once($file);
						$this->plugins[] = new $load->plugin_class_name($this, $pluginsActivated[$load->plugin_id]);
					} else {
						// @todo conditional warning when file not exist.
						$aiki->message->error("<br>Can't load plugin:" . $file, NULL, false);
						// deactive plugins.
						// @todo warning to system adminstrator?
						$db->query("UPDATE plugin_state='missing' WHERE plugin_id='" . $load->plugin_id . "'");
					}
				}
			}
		   $pluginsActivated = "";
		}

	} // end of constructor

	/**
	 * Add a action to aiki
	 *
	 * @param string $action
	 * @param object $callback. A reference to plugin objetc
	 * @param optional integer $priority
	 */

	function add_action($action, $callback, $priority=999) {
		$this->actions[$action][$priority][] = $callback;
		$this->must_sorted[$action] = true;
	}

	/**
	 * call all plugin that are attached to action
	 *
	 * @param string $action
	 * @param byref $string
	 */

	function do_action($action, &$text) {

		if (!isset($this->actions[$action])) {
			return 0;
		}

		// if needed sorts callback by prioty
		if ($this->must_sorted[$action]) {
			asort($this->actions[$action]);
			$this->must_sorted[$action]= false;
		}

		$i=0;
		// call plugin
		foreach ($this->actions[$action] as $priority => $callbacks) {
			foreach ($callbacks as $callback) {
				$callback->action($action,$text);
				$i++;
			}
		}
		return $i;
	}


	/**
	 * Extract a field (text like FIELD: ... from a given text)
	 *
	 * @param string $field Field to extract, without ":".
	 * @param string $text Text to parse.
	 */

	static private function extract_field($field, $text) {
		$matches="";
		$pattern= "#^[ ]*\*[ ]+" . preg_quote($field) . "\:(.*)#im";
		if (preg_match($pattern, $text, $matches)){
			return $matches[1];
		}
		return false;
	 }


	static private function data(&$data, $field) {
		if (isset($data[$field])) {
			return "'" . addslashes($data[$field]) . "'";
		}
		return "''";
	}


	/**
	 * Save and activate a plugin for given route.
	 *
	 * @param array   $founded Founded plugins
	 * @retun integer $number of inserted plugins
	 */

	 static function insert_plugin_configuration($plugin_id, $route, $priority=999, $vars=array()) {
		 global $db;
		 $sql =
			"INSERT INTO aiki_plugin_configurations" .
			" (plconf_plugin_id, plconf_routes, plconf_priority, plconf_values, plconf_active)" .
			" VALUES ({$plugin_id},'{$route}', '{$priority}','" .
			addslashes(serialize($vars))."','active')";				   
		 echo "insertando:", $sql, "<br>";
		 
		 $db->query($sql);

	}


	/**
	 * Update aiki_plug with new plugins
	 *
	 * @param array   $founded Founded plugins
	 * @retun integer $number of inserted plugins
	 */

	static function available_plugins($founded) {
		global $db;
		if ( !is_array($founded) || count($founded) == 0 ) {
			return 0;
		}
		$inserted = 0;
		// note than in case that plugin is in $founded, it will be re-available
		$db->query("UPDATE aiki_plug SET plugin_state='missing' WHERE plugin_state='available'");
		foreach ($founded as $file=>$data) {
			$id= $db->get_row(
					"SELECT plugin_id, plugin_state FROM aiki_plugins".
					" WHERE plugin_file='" .addslashes($file). "'");
			if (!is_null($id)) {
				// update plugin if found
				$set =
					($id->plugin_state == "missing" ? "plugin_state='available'," : "") .
					"plugin_class_name='{$data['class_name']}'," .
					"plugin_name=" . plugins::data( $data,"name") ."," .
					"plugin_version=" . plugins::data( $data,"version") ."," .
					"plugin_author=" . plugins::data( $data,"author") ."," .
					"plugin_short_description=". plugins::data( $data,"description");
				$db->query("UPDATE aiki_plugins SET $set WHERE plugin_id='" . $id->plugin_id . "'");

			} else {
				// insert a new plugin
				$values =
					"'$file'," .
					plugins::data($data, "name") . ",".
					plugins::data($data, "class_name") . ",".
					plugins::data($data, "version") . ",".
					plugins::data($data, "author") . ",".
					plugins::data($data, "description") . ",".
					"'available'";
				 $db->query("INSERT INTO aiki_plugins " .
					"(plugin_file,plugin_name,plugin_class_name,plugin_version, plugin_author," .
					"plugin_short_description, plugin_state) VALUES ( $values )");
				 $inserted++;
			}
		}
		return $inserted;
	}



	/**
	 * Search a directory (and subdirectories) for plugin
	 *
	 * @param $dir  directory to search.
	 * @retun $array of all plugins
	 */

	 static function search_plugins($dir=".") {
		 $founded = false;

		 // search php files that contains /* This is a aiki plugin
		 // and extract information
		 foreach (glob("$dir/*.php") as $file) {
			 $file_content = file_get_contents($file);
			 $start = stripos($file_content,"* This is a aiki plugin:");
			 if ($start && preg_match("/class ([a-z_0-9]+) extends plugin \{/i", $file_content, $temp_match) ) {
				 $file = preg_replace( "~^" . preg_quote(AIKI_PLUGIN_DIR . "/" ) . "~", "", $file);
				 $founded[$file]['class_name'] = $temp_match[1];
				 $end = stripos($file_content, "*/", $start);
				 $information = substr($file_content, $start, $end - $start);
				 $file_content = ""; // clear buffer
				 $search_for = array("author", "version", "description", "name");
				 foreach ($search_for as $search) {
					$field = plugins::extract_field($search, $information);
					if ($field) {
						$founded[$file][$search] = $field;
					}
				 }
			 } 
		 }
		 // now search subdirectories
		 foreach (glob("$dir/*", GLOB_ONLYDIR) as $directory) {
			 $new = $this->search_plugins($directory);
			 if ($new) {
				 if ($founded) {
					 $founded = $founded + $new;
				 } else {
					 $founded = $new;
				 }
			 }
		 }
		 return $founded;
	 }


}


/**
 * Abstract class to implements plugins
 *
 * You need declare a las class :
 * class MyPluginName extends plugin { ..here code }
 *
 * The code must contain the two functions:
 * - set_actions that must return a array of actions=>priorities
 * - do_action that receive two parameters action and &text ..
 *
 * You must not:
 * - create a plugin object.
 * - declare a constructor in class.
 *
 */


abstract class plugin {
	protected $plugins;
	protected $parameters;

	function __construct($pluginStore, $serializedParameters="") {
		global $aiki, $db;
		$this->plugins= $pluginStore;
		$this->parameters = array();

		// set actions
		foreach ($this->set_actions() as $key=>$value ){
			if (is_numeric($key)) {
				$this->plugins->add_action($value, &$this);
			} else {
				$this->plugins->add_action($key, &$this ,$value);
			}
		}
		// read configuration
		if ( $serializedParameters!="" &&
			 preg_match('~^a:[1-9]+[0-0]*:\{~',$serializedParameters) ){ //is array ? a:99:{...
			$this->parameters = unserialize($serializedParameters);
		}
		
		if (method_exists($this,"onload")) {
			$this->onload();
		}
		
	}

	function get($parameter) {
		if (isset($this->parameters[$parameter])) {
			return $this->parameters[$parameter];
		}
		return NULL;
	}

	abstract function set_actions();
	abstract function action($action, &$text);

}

?>