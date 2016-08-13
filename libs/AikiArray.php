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
 * A class for manipulating arrays and objects and creating the aiki forms
 * editor.
 *
 * @category    Aiki
 * @package    Library
 *
 * @todo    This is the aiki form editor, but the class is named AikiArray?
 */
class AikiArray {
    /**
     * @var array
     */
    public $createdArray;
    /**
     * @var string
     */
    public $insertQuery;
    /**
     * @var string
     */
    public $new_array_field;

    /**
     * Returns content for an array editor
     *
     * @param   string  $text   text for processing
     * @global  array   $db     global db instance
     * @global  aiki    $aiki   global aiki instance
     */
    public function displayArrayEditor($text) {
        global $db, $aiki;

        $arrays_count =
            preg_match_all("/\(\#\(array\:(.*)\)\#\)/U", $text, $arrays);
        /**
         * @todo what does this do? if nothing, remove it
         */
        //id:form_name:form_array:aiki_forms:3)#)
        if ( $arrays_count > 0 ) {
            foreach ( $arrays[1] as $array_data ) {
                if ($array_data) {
                    $arrayEditor = explode(":", $array_data);

                    $output =
                        $aiki->AikiArray->editor($arrayEditor['1'],
                            $arrayEditor['2'],
                            $arrayEditor['3'],
                            $arrayEditor['4'],
                            "where ".$arrayEditor['1']."=".$arrayEditor['5']."");

                    $text = preg_replace("/\(\#\(array\:$array_data\)\#\)/U",
                                         $output, $text);
                }
            }
        }
        return $text;
    } // end of displayArrayEditor function


    /**
     * Creates an array from exploding a string at a delimeter
     *
     * @param   string  $data           data for exploding
     * @param   string  $explodeFactor  delimeter for exploding at
     * @return  array
     *
     * @todo    rename function to createArrayByExploding
     */
    public function CreateArrayByExploding($data, $explodefactor) {
        if (isset($data)) {
            $this->createdArray = explode($explodefactor, $data);
            return $this->createdArray;
        }
    }

    /**
     * Converts an array to an object. The code is GPL.
     *
     * @Source http://www.jonasjohn.de/snippets/php/array2object.htm
     *
     * @param   array   $arrGiven   array to conver to object
     * @return  object
     */
    public function array2object($arrGiven) {
        //create empty class
        $objResult = new stdClass();

        foreach ( $arrLinklist as $key => $value ) {
            //recursive call for multidimensional arrays
            if (is_array($value)) {
                $value = $this->array2object($value);
            }
            $objResult->{$key} = $value;
        }
        return $objResult;
    }

    /**
     * Converts an object to array
     *
     * @param   object  $object object to convert to array
     * @return  array
     */
    public function object2array($object) {
        if ( is_object($object) || is_array($object) ) {
            foreach ( $object as $key => $value ) {
                $array[$key] = $this->object2array($value);
            }
        } else {
            $array = $object;
        }
        return $array;
    }

    /**
     * This appears to the aiki form editor.
     *
     * @param   string      $id     id field name
     * @param   string      $name   name field name
     * @param   string      $data   data field name
     * @param   string      $table  table name
     * @param   string      $where  full where statement
     * @global  array       global db instance
     * @return  string
     *
     * @todo    this code really should be in a view class for aiki forms.
     */
    public function editor($id, $name, $data, $table, $where) {
        global $db;

        $pageURL = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

        $setting = array();
        $settings =
            $db->get_results("SELECT $id, $data, $name FROM $table $where");
        $i=0;
        $y=0;

        /**
         * @todo get rid of hard coded html!
         */
        $html_form = "\n<form method='post' id='edit_form' name='edit_form' ".
                     "action='$pageURL' class='edit_form' >";
        foreach ( $settings as $setting_group ) {
            /**
             * @todo appears this data type is set to two different data
             * types which needs to be changed to only one data type.
             */
            $setting_group->$data = unserialize($setting_group->$data);
            $arrykeys = array_keys($setting_group->$data);
            $output_array = array();

            /**
             * @todo remove hardcoded html where possible
             */
            $html_form .= "<label>".$setting_group->$name."</label>";

            foreach( $setting_group->$data as $field ) {
                if (isset($_POST['edit_array'])) {
                    $outp_key = $_POST[$y.$arrykeys[$i]."_type"];

                    if ( $outp_key != 'tablename' and
                        $outp_key != 'pkey' and
                        $outp_key != 'send_email' and
                        $outp_key != 'permission' and
                        $outp_key != 'events' and
                        $table == "aiki_forms" ) {
                        $outp_key = $outp_key.$i;
                    }

                    $output_array[$outp_key] = $_POST[$y.$arrykeys[$i]];

                    $field = $_POST[$y.$arrykeys[$i]];
                }
                $toreplace =
                    array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
                $display = str_replace($toreplace, "", $arrykeys[$i]);

                /**
                 * @todo remove hardcoded strings where possible
                 */
                $html_form .= '<div id="'.$y.$arrykeys[$i].'">';

                if ( $table == "aiki_forms" ) {
                    $html_form .= '<select name="'.$y.$arrykeys[$i]."_type".'" >
                <option value="'.$display.'" selected="selected">'.$display.'</option>
                <option value="textinput" >Textinput</option>
                <option value="selection" >Dynamic Select Menu</option>
                <option value="staticselect" >Static Select Menu</option>
                <option value="unique_textinput" >Unique Textinput</option>
                <option value="password" >Password</option>
                <option value="textblock" >Text Block</option>
                <option value="bigtextblock" >Big Text Block</option>
                <option value="hidden" >Hidden Field</option>
                <option value="static_input" >Static Textinput</option>
                <option value="autofield" >Auto Filled Field</option>
                <option value="submit" >Submit</option>
                <option value="pkey" >Primary Key</option>
                <option value="send_email" >Send Email</option>
                <option value="tablename" >Database Table Name</option>
                <option value="filemanager" >File Manager</option>
                <option value="permission" >Permissions</option>
                <option value="events" >Events</option>
                <option value="captcha" >Captcha</option>
                <option value="edit_type" >Editing Type</option>
                </select>';

                } else {
                    $html_form .= "<input type='text' name=\"".
                        $y.$arrykeys[$i]."_type\" value=\"".
                        $display."\" size='25'>";
                }

                $html_form .= "<input type='text' name=\"".
                    $y.$arrykeys[$i]."\" value=\"".
                    $field."\" size='35'><a id=\"remove\" ".
                    "href=\"#\">Delete</a><br /><br /></div>";
                $i++;
            } // end of foreach over all settings
            $newfield = $setting_group->$name;

            if ( $table == "aiki_forms" ) {
                $html_form .= '<select name="left_'.$newfield.'" >
                <option value="textinput" >Textinput</option>
                <option value="selection" >Dynamic Select Menu</option>
                <option value="staticselect" >Static Select Menu</option>
                <option value="unique_textinput" >Unique Textinput</option>
                <option value="password" >Password</option>
                <option value="textblock" >Text Block</option>
                <option value="bigtextblock" >Big Text Block</option>
                <option value="hidden" >Hidden Field</option>
                <option value="static_input" >Static Textinput</option>
                <option value="autofield" >Auto Filled Field</option>
                <option value="submit" >Submit</option>
                <option value="pkey" >Primary Key</option>
                <option value="send_email" >Send Email</option>
                <option value="tablename" >Database Table Name</option>
                <option value="filemanager" >File Manager</option>
                <option value="permission" >Permissions</option>
                <option value="events" >Events</option>
                <option value="captcha" >Captcha</option>
                <option value="edit_type" >Editing Type</option>
                </select>';
            } else {
                $html_form .= "<input type='text' name=\"left_".$newfield.
                              "\" value=\"\" size='25'>";
            }
            $html_form .= "<input type='text' name=\"right_".
                          "$newfield"."\" value=\"\" size='35'>";
            $y++;
            $i=0;
            if (isset($_POST['edit_array'])) {
                if ( $_POST['left_'.$newfield] and
                    $_POST['right_'.$newfield] ) {
                    $output_array[$_POST['left_'.$newfield]] =
                        $_POST['right_'.$newfield];
                    $this->new_array_field = true;
                }

                foreach ( $output_array as $key => $value ) {
                    if($value == "")
                        unset($output_array[$key]);
                }

                $output_array = serialize($output_array);

                $output_id = $setting_group->$id;

                $update =
                    $db->query("UPDATE $table set ".
                    "$data = '$output_array' where $id='$output_id'");

                $output_array = '';
            }
        }
        /**
         * @todo replace hardcoded html
         */
        $html_form .= "
        <p class=\"form-buttons\">
        <input class=\"button\" type=\"submit\" value=\"Save\" name=\"edit_array\">
        </p>
        </form>";

        return $html_form;

    } // end of editor function

    /**
     * Creates query inserts from an array.
     *
     * @param    array    $array                    array exploding into
     * @param    string    $inArrayExplodeFactor    separator for explosion
     * @param    string    $tablename                table name of inserts
     * @param    string    $queryExample            template of a query
     * @global    array    $db                        global db instance
     *
     * @todo    rename function to createInsertsFromArray
     */
    public function CreateInsertsFromArray($array, $inArrayExplodeFactor, $tablename, $queryExample) {
        global $db;
        if (isset($array)) {
            $this->insertQuery = "insert into $tablename VALUES";
            foreach ( $array as $value ) {
                $value = trim($value);
                if (isset($inArrayExplodeFactor)) {
                    $value = explode($inArrayExplodeFactor, $value);
                    $value[0] = trim($value[0]);
                    $value[1] = trim($value[1]);
                    $innerQuery =
                        str_replace('$value[0]', $value[0], $queryExample);
                    $innerQuery =
                        str_replace('$value[1]', $value[1], $innerQuery);
                }
                $this->insertQuery .= "(".$innerQuery ."), ";
            }
            $this->insertQuery =
                substr($this->insertQuery,0,(strLen($this->insertQuery)-2));
            $db->query("$this->insertQuery");
        }

    } // end of CreateInsertsFromArray function

} // end of AikiArray class
