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
 * @package     Library
 * @filesource
 */

if (!defined('IN_AIKI')) {
    die('No direct script access allowed');
}


/**
 * BriefDescription
 *
 * @category        Aiki
 * @package        Library
 *
 *
 * @todo        implement these removed methods:
 *                * public function import_javascript;
 *                * public function import_image;
 *                * public function create_mockup_from_psd;
 *                * public function create_mockup_from_svg;
 *
 * @todo        this code needs review, this is a misc. class, should consider
 *            changing to import.php and split out the debug code into debug.php
 * @todo        another consideration is that this is an extension and not in the
 *            aiki core
 */
class Bot {
    /**
     * @var integer timeout for using curl
     */
    public  $timeout = 20;
    /**
     * @var string  url to access
     */
    public  $url;

    /**
     * Attempts to import an entire html mockup into aiki framework's widgets
     *
     * @param   string    $url            url of the html mockup
     * @param    string    $theme            currently unused
     * @param    string    $display_url
     * @global    aiki    $aiki            global aiki instance
     * @global    array    $db                global db instance
     * @global    array    $config            global config options instance
     *
     * @todo    this should really return true if successful or false if not
     * @todo    $theme doesn't do anything? use it or loose it.
     */
    public function import_mockup($url, $theme, $display_url) {
        global $aiki, $db, $config;

        $this->url = $url;

        /**
         * @todo why doesn't this use the AikiCurl class?
         * @see AikiCurl.php
         */
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

        ob_start();
        curl_exec($ch);
        curl_close($ch);
        $content = ob_get_contents();
        ob_end_clean();

        if ( $content !== false ) {
            if ( extension_loaded('tidy') and
                function_exists('tidy_parse_string') ) {
                $tidy = new tidy();
                $tidy->parseString($content,
                    $config["html_tidy_config"], 'utf8');
                $tidy->cleanRepair();
                $content = tidy_get_output($tidy);
            }

            $head = $aiki->get_string_between($content , "<head>", "</head>");

            if (isset($head)) {
                $head = trim($head);
                $css = $this->import_css($head);
                $css = addslashes($css);
            } else {
                $css = '';
            }

            $body = $aiki->get_string_between($content, "<body>", "</body>");

            if (isset($body)) {
                $body = trim($body);
                $body = str_replace('"', "'", $body);

                $doc = new DOMDocument();
                @$doc->loadHTML($body);
                $divs = $doc->getElementsByTagName('div');

                $i = 0;
                foreach ( $divs as $div ) {
                    $i++;
                    $match = "/\<$div->nodeName";
                    if ($div->getAttribute('id')) {
                        $widgetname = $div->getAttribute('id');
                        $match .= " id\='".$div->getAttribute('id')."'";
                    } else {
                        $widgetname = 'nonamne';
                    }

                    if ($div->getAttribute('class')) {
                        $styleid = $div->getAttribute('class');
                        $match .= " class\='".$div->getAttribute('class')."'";
                    } else {
                        $styleid = 'nostyle';
                    }

                    $match .= "\>(.*)\<\/$div->nodeName\>/Us";

                    $item = preg_match($match, $body, $match);

                    if (!preg_match('/\<div/', $match[1])) {
                        $match[1] = addslashes($match[1]);

                        $do = $db->query("INSERT INTO aiki_widgets (`id` ,`widget_name` ,`widget_site` " .
                            ",`widget_target` ,`widget_type` ,`display_order` ,`style_id` ,`is_father` " .
                            ",`father_widget` ,`display_urls` ,`widget` ,`is_active`) VALUES (NULL, " .
                            "'$widgetname', 'default', 'body', 'div', '$i', '$styleid', '0', '', " .
                            "'$display_url', '$match[1]', '1')");

                    } else {
                        $father_dev =
                            $aiki->get_string_between($match[0], "<div", ">");
                        $father_name =
                            $aiki->get_string_between($father_dev, "id='", "'");
                        $father_class =
                            $aiki->get_string_between($father_dev,
                            "class='", "'");

                        $do = $db->query("INSERT INTO aiki_widgets(`id` ,`widget_name` ,`widget_site` " .
                            ",`widget_target` ,`widget_type` ,`display_order` ,`style_id` ,`is_father` ".
                            ",`father_widget` ,`display_urls` ,`widget` ,`is_active`, `css`) VALUES " .
                            "(NULL, '$father_name', 'default', 'body', 'div', '$i', '$father_class', " .
                            "'1', '', '$display_url', '', '1', '$css')");
                        $css = '';
                    }
                } // handle each div

                //set fathers
                $widgets = $db->get_results("SELECT id, is_father FROM aiki_widgets WHERE display_urls='$display_url'" .
                    " ORDER BY display_order");
                if ($widgets) {
                    foreach ( $widgets as $widget ) {
                        if ( isset($next_is_son) and $next_is_son != 0 ) {
                            $update = $db->query("update aiki_widgets set father_widget='$next_is_son' where id = '$widget->id'");
                        }

                        if ( $widget->is_father == '1' ) {
                            $next_is_son = $widget->id;
                        } else {
                            $next_is_son = 0;
                        }
                    }

                    /**
                     * @todo remove the output of this message or use message
                     * class to do properly.
                     */
                    echo "Imported the mockup successfully.";
                } else {
                    echo "Failed to import the mockup.";
                }

            } else {
                echo "Failed to locate the body of the document.";
            }

        } else {
            echo "Failed to load contents form file.";
        }
    } // end of import_mockup function

    /**
     * Attempts to import css from a file.
     *
     * @param    string    $head    text for extracting css
     * @return    string
     */
    public function import_css($head) {
        $css_matchs = preg_match_all('/\<link href\=\"(.*)\" type\=\"text\/css\" rel=\"stylesheet\" \/\>/Us', $head, $matchs);

        if ( $css_matchs > 0 ) {
            $css = '';
            foreach ( $matchs[1] as $css_link ) {
                if (preg_match('/http/', $css_link)) {
                    $link = $css_link;
                } else {
                    $link = preg_replace('/(.*)\/(.*)/',
                            '\\1' . '/' . $css_link, $this->url);
                }

                /**
                 * @todo replace this with the AikiCurl class
                 */
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $link);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

                ob_start();
                curl_exec($ch);
                curl_close($ch);
                $content = ob_get_contents();
                ob_end_clean();

                if ( $content !== false ) {
                    $css .= $content;
                }
            }
        }
        return $css;
    } // end of import_css


    /**
     * Debug function to rneame a file and print out its path.
     *
     * @param    string    $path    path to the file
     */
    public function rename_files_give_timestamp($path) {
        if (!isset($path)) {
            return;
        }

        $handle = opendir($path);
        $path = str_replace(" ", "\ ", $path);
        while ( ($file = readdir($handle)) !== false ) {
            if ( $file != "." and $file != ".." ) {

                $file = str_replace(" ", "\ ", $file);
                $file = str_replace("(", "\(", $file);
                $file = str_replace(")", "\)", $file);

                $or_file = $file;

                $file = time().".jpg";

                /**
                 * @todo replace with message class.
                 */
                echo $or_file."<br>";
                sleep(1);
                exec("mv -v $path/$or_file $path/$file", $output);
                print_r($output);
                sleep(1);
            }
        }
        closedir($handle);
    }


    /**
     * Create photos archive metadata in a table.
     *
     * @param    string        $tablename    name of table for meta
     * @global    array        $config        global config options
     */
    public function create_photos_archive_meta($tablename) {
        global $config;

        $photos = $db->get_results(
            "SELECT * FROM $tablename WHERE checksum_sha1 =''");
        foreach ( $photos as $photo ) {
            $path = $photo->full_path;

            if (file_exists($config['top_folder'] . '/' . $path.$photo->filename)) {
                $sha1 =
                    sha1_file($config['top_folder'] . '/' . $path.$photo->filename);
                $md5 =
                    md5_file($config['top_folder'] . '/' . $path.$photo->filename);
                $filesize =
                    filesize($config['top_folder'] . '/' . $path.$photo->filename);
                $size =
                getimagesize($config['top_folder'] . '/' . $path.$photo->filename);

                $width = $size["0"];
                $hight = $size["1"];

                $db->query("UPDATE $tablename SET checksum_sha1='$sha1', checksum_md5='$md5', " .
                    "upload_file_size='$filesize', width='$width', height='$hight', is_missing='0'" .
                    " WHERE id='$photo->id'");

            } else {
                $db->query("UPDATE $tablename SET is_missing='1' WHERE id='$photo->id'");
            }
            /**
             * @todo why is this echoing content? Remove this and separate
             * this function from its view. And, use message class.
             */
            echo $photo->id . "<br>";
        }
    } // end of create_photos_archive_meta



    /**
     * An aiki version of mysql dump.
     *
     * The flow is
     * * dump all
     * * dump app
     * * dump aiki installtion with all apps and no data
     * * dump user data or not
     *
     * @param    string    $database    the database name
     */
    function mysql_dump($database) {
        $query = '';

        $tables = @mysql_list_tables($database);
        while ($row = @mysql_fetch_row($tables)) {
            $table_list[] = $row[0];
        }

        for ( $i = 0; $i < @count($table_list); $i++ ) {
            $results =
                mysql_query('DESCRIBE ' . $database . '.' . $table_list[$i]);
            $query .='CREATE TABLE `' . $table_list[$i] . '` (' . "\r";

            $tmp = '';

            while ($row = @mysql_fetch_assoc($results)) {
                $query .= '`' . $row['Field'] . '` ' . $row['Type'];

                if ($row['Null'] != 'YES') {
                    $query .= ' NOT NULL';
                }
                if ($row['Default'] != '') {
                    $query .= ' DEFAULT \'' . $row['Default'] . '\'';
                }
                if ($row['Extra']) {
                    $query .= ' '.strtoupper($row['Extra']);
                }
                if ( $row['Key'] == 'PRI' ) {
                    $tmp = 'primary key(' . $row['Field'] . ')';
                }
                $query .= ','. "\r";
            }
            $query .= $tmp . "\r" . ');' . str_repeat("\r", 1);

            $query .=
                '--------------------------------------------------------'."\r";

            $results = mysql_query(
                'SELECT * FROM ' . $database . '.' . $table_list[$i]);

            while ($row = @mysql_fetch_assoc($results)) {
                $query .= 'INSERT INTO `' . $table_list[$i] .'` (';
                $data = Array();

                while (list($key, $value) = @each($row)) {
                    $data['keys'][] =
                    $key; $data['values'][] =
                    addslashes($value);
                }

                $query .= join($data['keys'], ', ') . ')' . "\r" .
                'VALUES (\'' . join($data['values'], '\', \'') . '\');' . "\r";

                $query .=
                '--------------------------------------------------------'."\r";
            }
        } // end of for loop

        $query = str_replace(
            "'CURRENT_TIMESTAMP'", 'CURRENT_TIMESTAMP', $query );
        return $query;

    } // end of mysql_dump function

    /**
     * Show a named table's structure debug function.
     *
     * @param    string    $table    table name to show structure of
     * @global    aiki    $aiki    global aiki instance
     * @return    string
     *
     * @todo    rename function to showTableStructure
     */
    function ShowTableStructure($table) {
        global $aiki, $db;

        $results = $db->get_results('SHOW COLUMNS FROM ' . $table) or die('cannot show columns from ' . $table);

        if ( !is_null($results)) {
            $output = '<div id="table_information_container">
            <table cellpadding="0" cellspacing="0" class="db-table" '.
                'style="width: 100%">';
            $output .=
            '<tr><td><b>Field</b></td><td><b>Type</b></td><td><b>Null</b></td>
            <td><b>Key</b></td><td><b>Default</b><td><b>Extra</b></td></tr>';
            foreach ( $results as $row)
            {
                $output .= '<tr>';
                foreach($row as $key=>$value)
                {
                    $output .= '<td>'.$value.'</td>';
                }
                $output .= '</tr>';
            }
            $output .= '</table>
            </div>';
            return $output;
        }
    }


    /**
     * Show data from a table.
     *
     * @param    string    $table_name    name of table
     * @global    array    $db            global db instance
     * @global    array    $config        global config instance
     * @global    aiki    $aiki        global aiki instance
     *
     * @todo    rename class to dataGrid
     */
    function DataGrid($table_name) {
        global $db, $config, $aiki;

        $table_name = trim($table_name);

        $table_info = $db->get_row(
            "SELECT * FROM aiki_forms WHERE form_table LIKE '$table_name'");
        if (!$table_info) {
            /**
             * @todo replace with message class
             */
            return "no form was found for the table <b>$table_name</b> " .
                   "please generate the form first";
        }
        $tablename = $table_info->form_table;
        $form_array = $table_info->form_array;
        $form_array = unserialize($form_array);
        $pkey = $form_array['pkey'];

        /**
         * @todo replace with message class!
         */
        $output = ("
        <style type=\"text/css\">
        .dashboard_grid_container ul li{
            float: left;
            padding-right: 10px;
        }
        .dashboard_grid_container ul li ul li{
         float: none;
        }
        </style>
        <form method=\"POST\">
            Search:
            <input type=\"text\" name=\"keyword\" size=\"30\">
            <select name=\"wheresearch\">");
        foreach ( $form_array as $field ) {
            if ( $field != $tablename ) {
                $intwalker = explode(":", $field);

                if (!isset($intwalker[1])) {
                    $intwalker[1] = $intwalker[0];
                }
                $output .=
                "<option value=\"{$intwalker[0]}\">{$intwalker[1]}</option>";
            }
        }
        $output .=
        "</select><input type=\"submit\" value=\"Go\" name=\"search\"></form>";

        if (isset($orderby)) {
            $orderby = "order by ".$orderby;
        } elseif ($pkey) {
            $orderby = "order by ".$pkey;
        } else {
            $orderby = '';
        }

        $data = $db->get_results("select * from $tablename $orderby LIMIT 250", ARRAY_A); //* TODO PAGINATION.

        if ( is_null($data) ){
            return t("No records found");
        }

        $output .=
            "<table class='db-table'>\n".
            "<thead>\n".
            "<tr>\n".
            ($pkey ? "<th>Tools</th>":"" ) . "<th>". implode("</th><th>", array_keys (reset($data))). "</th>"    .
            "</tr></thead>\n".
            "<tbody>";

        reset($data);
        $odd=false;
        foreach ( $data as $row ) {
            if ($pkey ){
                $id= $row[$pkey];
                $edit =
                    "<td class='tools'>".
                    "<a href='{$config['url']}admin_tools/edit/$tablename/$id'  rel=\"edit_record\" rev=\"#table_information_container\">edit</a> -".
                    "<a href='{$config['url']}admin_tools/delete/$tablename/$id' rel=\"delete_record\" rev=\"#table_information_container\">delete</a>";
                    "</td>\n";
            } else {
                $edit ="";
            }
            $odd= !$odd;

            $output .= "\n<tr class='". ($odd? "odd": "even") ."'>$edit";
            foreach ( $row as $field ){
                $output .= "<td>".htmlspecialchars($field)."</td>";
            }
            $output .= "</tr>";
        }
        $output .= "\n</tbody>\n</table>\n";
        return $output;
    } // end of dataGrid function


    /**
     * This is supposed to update aiki's sql to the latest.
     *
     * Upon inspection, this looks at the sql in a current site and compares
     * it to the current sql. If problem it skips the creation of new sql.
     *
     * @global  string      $AIKI_ROOT_DIR  path to system folder
     * @globa   array       $db             global db instance
     *
     * @todo @link http://aikiframework.org/wiki/Aiki_Robust_Update_System
     * @todo this is what bassel called the update system, need to review it
     * @todo if there is any problem, really shouldn't run this.
     * @todo this should be saving the current admin widgets somewhere and
     * installing new widgets in place IMO, but need to look into more.
     * @todo admin widgets definitely should be replaced upon each new version
     * @todo this function is too big, and needs to take into account changes
     * from version to version
     * @todo hardcoded strings need to be removed
     */
    function auto_update_to_latest_aiki() {
        global $AIKI_ROOT_DIR, $db;

        $output = '';
        //create tables if one doesn't exists and check structure
        $sql_create_tables =
            file_get_contents("$AIKI_ROOT_DIR/sql/CreateTables.sql");
        if ( false == $sql_create_tables ) {
            die("<br />FATAL: failed to read file -> ".
                "$AIKI_ROOT_DIR/sql/CreateTables.sql<br />");
        }

        define("SQL_DELIMIT",
            '-- ------------------------------------------------------');
        $sql = $sql_create_tables;

        $sql = explode(SQL_DELIMIT, $sql);

        foreach ( $sql as $sql_statment ) {
            mysql_query($sql_statment);

            $table_name = preg_match(
                '/CREATE TABLE IF NOT EXISTS \`(.*)\` \(/Usi',
                $sql_statment, $matches);
            $table_name = trim($matches['1']);

            //get the current table structure
            $descripe_table = mysql_query('describe '.$table_name);
            while($field_name = mysql_fetch_row($descripe_table)) {
                $current_table_structure[] = $field_name[0];
            }

            //get the table structure from stored database query
            $table_fields = preg_match_all(
                '/\`(.*)\`/Usi', $sql_statment, $table_fields_matches);
            foreach ( $table_fields_matches['1'] as $field ) {
                //find if column exists
                if ( $field != $table_name and
                    !in_array($field, $fields_array) and
                    !in_array($field, $current_table_structure) ) {
                    //get column description
                    $field_description = preg_match(
                    '/\`'.$field.'\`(.*)\,/Usi', $sql_statment, $desc_matches);

                    $sql = "ALTER TABLE `$table_name` ADD `$field` ".
                           $desc_matches[1];
                    $update_table = mysql_query($sql);

                    if ($update_table) {
                        $output .= "Added the field $field to ".
                                   "table <b>$table_name</b><br>";
                    }
                }
                $fields_array[] = $field;
            }

            $output .= "the table <b>$table_name</b> ".
                       "exists and have the latest structure.<br>";
        }

        //check for default values
        $sql_insert_defaults =
            file_get_contents("$AIKI_ROOT_DIR/sql/InsertDefaults.sql");
        if ( false == $sql_insert_defaults ) {
            die("<br />FATAL: failed to read file -> ".
                "$AIKI_ROOT_DIR/sql/InsertDefaults.sql<br />");
        }
        $output .=
            "Your aiki installation is now up to date using version ".
            AIKI_VERSION . "." . AIKI_REVISION;
        return $output;

    } //end of auto_update_to_latest_aiki function

} // end of Bot class
