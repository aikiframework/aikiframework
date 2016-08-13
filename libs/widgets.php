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
 * Widget library
 *
 * @category    Aiki
 * @package     System
 *
 * @todo refactor this all, or else!
 */

class widgets {

    /**
     * return sql filter to select only widget of current apps & engine
     *
     * @return string part of a SQL WHERE clausule
     */

    private function widget_scope ( $checkSite= true  ){
        global $aiki;
        return "is_active=1" . ( $checkSite ? " AND ( widget_site='{$aiki->site}' OR widget_site ='aiki_shared')": "");
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
            "SELECT id, display_urls, kill_urls, widget_name, widget_target, css<>'' as have_css" .
            " FROM aiki_widgets " .
            " WHERE father_widget=$father AND " .
            $this->widget_scope() ." AND ".
            " (display_urls LIKE '%$search%' OR display_urls = '*' OR ".
            " display_urls LIKE '%#%#%') AND " .
            " (kill_urls='' OR kill_urls not rlike '^$search\$|^$search\\\\||\\\\|$search\$|\\\\|$search\\\\|') " .
            " ORDER BY  display_order, id";
         return $db->get_results($SQL);
    }


    /**
     * return widget that responds a error_404 page.
     *
     * @return array of id
     */

    function get_page_not_found_widgets() {
        global $db, $aiki;

        $SQL =
            "SELECT id, display_urls, kill_urls, widget_name, widget_target, css<>'' as have_css" .
            " FROM aiki_widgets WHERE " .
            $this->widget_scope() . " AND ".
            " (display_urls LIKE '%error_404%' OR display_urls = '*' OR " .
            " display_urls LIKE '%#%#%') AND " .
            " (kill_urls='' OR kill_urls not rlike '^error_404\$|\\\\|error_404\$|^error_404\\\\|\\\\|error_404\\\\|') " .
            " ORDER BY display_order, id";
        return $db->get_results($SQL);
    }

    /**
     * lookup a widget by id or name
     *
     * @param  mixed  v$widgetNameOrId Widget name or id.
     * @return integer widget_ir
     */

    function get_widget_id($widgetNameOrId) {
        return $this->get_widget_helper($widgetNameOrId, false);
    }

    /**
     * get all data from a widget given by id or name
     *
     * @param  mixed  v$widgetNameOrId Widget name or id.
     * @return object_Ezsql_row
     */

    function get_widget($widgetNameOrId, $checkSite = true) {
        return $this->get_widget_helper($widgetNameOrId, true, $checkSite);
    }


    private function get_widget_helper($widgetNameOrId, $record=false, $checkSite=true) {
        global $db;
        if ( (int)$widgetNameOrId > 0 ) {
            $where= "id='$widgetNameOrId' AND is_active='1'";
        } else {
            $where =
                $this->widget_scope($checkSite) ." AND " .
                "widget_name='" . addslashes($widgetNameOrId) ."'"; // paranoic test.
        }

        $searchSQL =
            "SELECT " . ($record? "*" :"id" ).  " FROM aiki_widgets ".
            "WHERE {$where} LIMIT 1" ;
        return ( $record ? $db->get_row($searchSQL) : $db->get_var($searchSQL));
    }

}
