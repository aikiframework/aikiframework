<?php

/** Aiki Framework (PHP)
 *
 * DatabaseBackup utility library.
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
 * @filesource */

// disable php script access
if(!defined("IN_AIKI")) { die("No direct script access allowed"); }

/** @see BackupInterface.php */
require_once("libs/backup/BackupInterface.php");

class DatabaseBackup implements BackupInterface {
    
    /** Constructs a new DatabaseBackup
     * @param object $database Database object
     * @return void */
    public function __construct($database) {
        $this->_database = $database;
    }
    
    /** Restore a Backup 
     * @param string $source Directory where the backup is 
     * @param string $destination Directory to restore the backup in 
     * @param array $exclude Target files or directories to exclude
     * @throws AikiException */
    public function restore($source, $destination, Array $exclude = Array() {
    	
    }
    
    /** Save a Backup 
     * @param string $destination Directory to save the backup in 
     * @param array $exclude Target files or directories to exclude
     * @throws AikiException */
    public function save($destination, Array $exclude = Array()) {
    	$destination .= "/sql";
        $this->_save_tables($destination);
    }

    /** @TODO Implement Aiki database class instead of relying on
     * third-party database code which lacks many needed features. */
    
	/** Save all database tables to a SQL file format
     * @param string $destination Directory to save the backup in
     * @param string $tables Tables to save
     * @return void 
     * @throws AikiException */
	protected function _save_tables($destination, $tables = '*') {
        $contents = "";
        
        // adjust some settings
        $sql = "SET SESSION SQL_QUOTE_SHOW_CREATE = 1;";
        $this->_database->query($sql);
        
        // get all of the tables
        if($tables == '*') {
            $tables = Array();
            $result = mysql_query('SHOW TABLES');
            if ($result) {
	            while($row = mysql_fetch_row($result)) {
	                $tables[] = $row[0];
	            }
            }
            else {
            	throw new AikiException("Failed to get tables.");
            }
        }
        else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }
	  
        // cycle through
        foreach($tables as $table) {
            $result = mysql_query('SELECT * FROM ' . $table);
            if ($result) {
	            $num_fields = mysql_num_fields($result);
		    
	            $contents .= 'DROP TABLE ' . $table . ';';
	            $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' .
                                                    $table));
	            $contents .= "\n\n" . $row2[1] . ";\n\n";
		    
	            for ($i = 0; $i < $num_fields; $i++) {
	                while($row = mysql_fetch_row($result)) {
	                    $contents .= 'INSERT INTO ' . $table . ' VALUES(';
	                    for($j = 0; $j < $num_fields; $j++) {
	                        $row[$j] = addslashes($row[$j]);
	                        $row[$j] = preg_replace("/\n/", "\\n", $row[$j]);
	                        if (isset($row[$j])) {
	                            $contents .= '"' . $row[$j] . '"';
	                        }
	                        else {
	                        	$contents .= '""';
	                        }
	                        if ($j < ($num_fields - 1)) {
	                        	$contents .= ',';
	                        }
	                    }
	                    $contents .= ");\n";
	                }
	            }
	            $contents .="\n\n\n";
            }
            else {
            	throw new AikiException("Failed to get fields from " . $table);
            }
        }
	  
        // save file
        $stream = fopen($destination . '/_DatabaseBackup-' . time() . '-' .
                        (md5(implode(',', $tables))) . '.sql', 'wb+');
        if ($stream) {
	        fwrite($stream, $contents);
	        fclose($stream);
        }
        else {
        	throw new AikiException("Failed to open file in " . $destination);
        }
    }
}
