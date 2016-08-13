<?php

/**
 * Aiki Framework (PHP)
 *
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Roger Martin
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Aiki
 * @filesource
 */

class UpgradeDB {

    private $upgrading_errors;

    /**
     * check if a table exist in database
     * @global $db
     * @return boolean true if exists, else false
     */

    function exists_table( $name ){
        global $db;
        return $db->query("SHOW TABLES LIKE '$name'") ;
    }

    /**
     * create a table
     * @global $db, $config
     * @return boolean true if have created a new table, else false
     */

    function create_table ($name, $fields, $saveMode= true  ) {
        global $db, $config;

        if ( !$this->exists_table($name) ) {
            $charset=   isset($config["encoding"]) ? $config["encoding"] :  "UTF8";
            $sql = "CREATE TABLE $name ( $fields ) CHARSET=$charset";
            $db->query($sql);
            return $db->result;
        }
        return false;
    }

    /**
     * upgrade a table: if table doesn't exist, creates it, else upgrades.
     *
     * @return boolean true if have created new table, else false
     */

    function upgrade_table ($name, $fields) {

        if ( !$this->exists_table($name) ) {
            return $this->create_table ($name, $fields);
        }

        $ok = true;
        $this->upgrade_errors ="";
        $structure = $this->parse_definition($fields);


        if ( ! $this->_upgrade_fields ( $name, $structure["fields"])){
            $ok= false;
        }

        if ( !$this->_upgrade_index( $name, $structure["keys"])){
            $ok= false;
        }

        return ($ok);
    }


    function _upgrade_fields ( $table, $expected){
        //source for SQL: http://troels.arvin.dk/db/rdbms/
        global $db;

        $table = stripcslashes( $table);

        $changes = array();

        foreach ($db->get_results("DESCRIBE `$table`", ARRAY_N ) as $field ){
            $founded =  sprintf("`{$field[0]}` {$field[1]}%s%s%s",
                ( $field[2]=="NO" ? " NOT NULL" : " NULL"),
                ( $field[4]==""   ? "" : " ". strtoupper($field[4]) ),
                ( $field[5]==""   ? "" : " ". strtoupper($field[5]) ) );
            // if field existe, add in changes or delete it.
            if ( isset($expected[$field[0]] ) ) {
                if ( $expected[$field[0]] != $founded ){
                    $changes[$field[0]] = "CHANGE `{$field[0]}` ". $expected[$field[0]];
                }
                unset($expected[$field[0]]);
            }     // tip else :field[0] can be deleted
        }

        $nExpected = count($expected);
        $nChanges  = count($changes);
        if ( $nExpected || $nChanges  ) {
            $SQL= "ALTER TABLE `$table`".
                     ( $nExpected ?  " ADD " . implode(", ADD " , $expected) : ""  ) .
                     ( $nChanges  ? ($nExpected ? ",":"") . implode("," , $changes) :"" );
            $db->query($SQL);
        }

        return true;


    }


    /**
     * parse table string definition.
     *
     * @return array with two key [keys] a list of index, [fields] a list of fields
     */

    function parse_definition( $tableSQL, $return="both"){
        $ret = array ("fields"=>"", "keys"=>"");
        $lines = explode("\n", $tableSQL);

        foreach ( $lines as $line ){
            $fieldname= "";
            $line= preg_replace( '/,$/', "", trim($line) ); // remove spaces and trailing ,
            if ( !$line ) {
                continue;
            } elseif( preg_match("/(PRIMARY KEY|KEY|UNIQUE KEY|FULLTEXT KEY)(.*)$/ui",$line) ) {
                $ret["keys"][]= $line;
            } elseif ( preg_match ('/^[\s]*`?([^\s`]*)/m',$line,$fieldname) ) {
                $ret["fields"][$fieldname[1]] = $line;
            }
        }
        switch ( $return){
            case "keys": return $ret["keys"];
            case "fields":return $ret["fields"];
            default: return $ret;
        }
    }



    function get_fields($ExistingTtable ) {
        return _get_structure($table, true);
    }

    function get_index ($table ) {
        global $db;
        $ret= array();
        // @TESTED ONLY IN MYSQL
        foreach ($db->get_results("SHOW INDEX FROM `$table`", ARRAY_N ) as $key ){

            if ( $key[3]!=1 ){
                $ret[]=  str_replace(")",",{$key[4]})", array_pop($ret));
            } elseif ( $key[2]=="PRIMARY" ){
                $ret[]= "PRIMARY KEY( {$key[4]})";
            } elseif ($key[1]==0 ){
                $ret[]= "UNIQUE KEY {$key[2]} ({$key[4]})";
            } elseif ($key[10]=="FULLTEXT"){
                $ret[]= "FULLTEXT {$key[2]} ({$key[4]})";
            } else {
                $ret[]= "KEY {$key[2]} ({$key[4]})";
            }

        }
        return $ret;
    }


    /**
     * upgrade index of a table
     *
     * @return boolean f
     */

    function upgrade_index($table, $tableSQL){
        return $this->_upgrade_index($table, $this->parse_definition($tableSQL,"keys") );
    }

    private function _upgrade_index($table, $keys) {
        global $db;

        if ( !$this->exists_table($table) ){
            return false;
        }

        /* @TODO TESTED ONLY IN MYSQL*/

        // delete all index, except primary
        foreach ($db->get_results("SHOW INDEX FROM `$table`", ARRAY_N ) as $key ){
            //key is sequence,
            if ( $key[3]!=1 && $key[2]!="PRIMARY" ){
                $db->query("ALTER TABLE `$table` DROP INDEX '{$key[2]}';");
            }
        }

        // add new index.
        foreach ( $keys as $key ){
            if ( strpos($key, "PRIMARY KEY")===false ){
                $db->query("ALTER TABLE `$table` ADD $key ;");
            }
        }

    }

    function show_table ($table, $breakLine="<br>"){
        global $db;
        $rs = $db->get_results("DESCRIBE `$table`",ARRAY_N);
        if (is_null($rs) ){
            return "no Table";
        }
        foreach ( $rs as $field){
            $fields[] = implode(" ",$field);
        }
        $ret = implode($breakLine, $fields);

        $rs= $db->get_results("SHOW INDEX FROM `$table`", ARRAY_N );
        if ( is_null($rs) ) {
            return $ret .$breakLine ."no indexes found";
        }
        foreach ($rs as $key ){
            $ret .= $breakLine . implode(" ", array_slice($key,2));
        }
        return $ret;
    }

}
