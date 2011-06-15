<?php

if(!defined('IN_AIKI')){die('No direct script access allowed');}

if (function_exists('mysql_set_charset') === false) {
	/**
	 * Sets the client character set.
	 *
	 * Credits: http://www.php.net/manual/en/function.mysql-set-charset.php#81560
	 * Note: This function requires MySQL 5.0.7 or later.
	 *
	 * @see http://www.php.net/mysql-set-charset
	 * @param string $charset A valid character set name
	 * @param resource $link_identifier The MySQL connection
	 * @return TRUE on success or FALSE on failure
	 */
	function mysql_set_charset($charset, $link_identifier = null)
	{
		if ($link_identifier == null) {
			return mysql_query('SET NAMES "'.$charset.'"');
		} else {
			return mysql_query('SET NAMES "'.$charset.'"', $link_identifier);
		}
	}
}

/**
 * ezSQL
 *
 * @author		Justin Vincent (justin@visunet.ie)
 * @copyright   Copyright (C) 2010 Justin Vincent
 * @license		LGPL http://www.gnu.org/licenses/lgpl.html
 * @link		http://justinvincent.com/ezsql
 * @category    Aiki
 * @package     Database
 * @filesource
 */


/**********************************************************************
 *  ezSQL error strings - mySQL
 */

$ezsql_mysql_str = array
(
1 => 'Require $dbuser and $dbpassword to connect to a database server',
2 => 'Error establishing mySQL database connection. Correct user/password? Correct hostname? Database server running?',
3 => 'Require $dbname to select a database',
4 => 'mySQL database connection is not active',
5 => 'Unexpected error while trying to select database'
);

/**********************************************************************
 *  ezSQL Database specific class - mySQL
 */

if ( ! function_exists ('mysql_connect') ) die('<b>Fatal Error:</b> ezSQL_mysql requires mySQL Lib to be compiled and or linked in to the PHP engine');
if ( ! class_exists ('ezSQLcore') ) die('<b>Fatal Error:</b> ezSQL_mysql requires ezSQLcore (ez_sql_core.php) to be included/loaded before it can be used');

/**
 * BriefDescription
 *
 * @category    Aiki
 * @package     Database
 */
class ezSQL_mysql extends ezSQLcore
{

	public  $dbuser = false;
	public  $dbpassword = false;
	public  $dbname = false;
	public  $dbhost = false;

	/**********************************************************************
		*  Constructor - allow the user to perform a qucik connect at the
		*  same time as initialising the ezSQL_mysql class
		*/

	public function ezSQL_mysql($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost')
	{
		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;
	}

	/**********************************************************************
		*  Short hand way to connect to mySQL database server
		*  and select a mySQL database at the same time
		*/

	public function quick_connect($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost')
	{
		$return_val = false;
		if ( ! $this->connect($dbuser, $dbpassword, $dbhost,true) ) ;
		else if ( ! $this->select($dbname) ) ;
		else $return_val = true;
		return $return_val;
	}

	/**********************************************************************
		*  Try to connect to mySQL database server
		*/

	public function connect($dbuser='', $dbpassword='', $dbhost='localhost')
	{
		global $ezsql_mysql_str, $config; $return_val = false;
		// Must have a user and a password
		if ( ! $dbuser )
		{
			$this->register_error($ezsql_mysql_str[1].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[1],E_USER_WARNING) : null;
		}
		// Try to establish the server database handle
		else if ( ! $this->dbh = @mysql_connect($dbhost,$dbuser,$dbpassword,true) )
		{
			$this->register_error($ezsql_mysql_str[2].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[2],E_USER_WARNING) : null;
		}
		else
		{

			if (isset($config['db_encoding']) and isset($config['db_use_mysql_set_charset']) and $config['db_use_mysql_set_charset']){

				$db_encoding = $config['db_encoding'];

				mysql_set_charset("$db_encoding", $this->dbh);
			}

			$this->dbuser = $dbuser;
			$this->dbpassword = $dbpassword;
			$this->dbhost = $dbhost;
			$return_val = true;
		}

		return $return_val;
	}

	/**********************************************************************
		*  Try to select a mySQL database
		*/

	public function select($dbname='')
	{
		global $ezsql_mysql_str; $return_val = false;

		// Must have a database name
		if ( ! $dbname )
		{
			$this->register_error($ezsql_mysql_str[3].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[3],E_USER_WARNING) : null;
		}

		// Must have an active database connection
		else if ( ! $this->dbh )
		{
			$this->register_error($ezsql_mysql_str[4].' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($ezsql_mysql_str[4],E_USER_WARNING) : null;

		}

		// Try to connect to the database
		else if ( !@mysql_select_db($dbname,$this->dbh) )
		{
			// Try to get error supplied by mysql if not use our own
			if ( !$str = @mysql_error($this->dbh))
			$str = $ezsql_mysql_str[5];

			$this->register_error($str.' in '.__FILE__.' on line '.__LINE__);
			$this->show_errors ? trigger_error($str,E_USER_WARNING) : null;
			die("Fatal error: Lost Connection to database. please make sure the information in your config.php are correct");
		}
		else
		{
			$this->dbname = $dbname;
			$return_val = true;
		}

		return $return_val;
	}

	/**********************************************************************
		*  Format a mySQL string correctly for safe mySQL insert
		*  (no mater if magic quotes are on or not)
		*/

	public function escape($str)
	{
		return mysql_real_escape_string(stripslashes($str));
	}

	/**********************************************************************
		*  Return mySQL specific system date syntax
		*  i.e. Oracle: SYSDATE Mysql: NOW()
		*/

	public function sysdate()
	{
		return 'NOW()';
	}

	/**********************************************************************
		*  Perform mySQL query and try to detirmin result value
		*/

	public function query($query)
	{
		global $config;

		//echo "<p align='left'>".$query."</p><hr>";
		// Initialise return
		$return_val = 0;

		// Flush cached values..
		$this->flush();

		// For reg expressions
		$query = trim($query);

		// Log how the function was called
		$this->func_call = "\$db->query(\"$query\")";

		// Keep track of the last query for debug..
		$this->last_query = $query;



		// Use core file cache function
		$cache = $this->get_cache($query);
		if ($cache)
		{
			return $cache;

		}else{

			//Count how many (not cached) queries there have been
			$this->num_queries++;

			// If there is no existing database connection then try to connect
			if ( ! isset($this->dbh) || ! $this->dbh )
			{
				$this->connect($this->dbuser, $this->dbpassword, $this->dbhost);
				$this->select($this->dbname);
			}

			// Perform the query via std mysql_query function..
			$this->result = @mysql_query($query,$this->dbh);

			// If there is an error then take note of it..
			if ( $str = @mysql_error($this->dbh) )
			{
				//check if multi database is enabled
				if (isset ($config["allow_multiple_databases"]) and $config["allow_multiple_databases"]){

					$find_request_to_external_db = preg_match('/\bfrom\b\s*(\w+)\.(\w+)/i',$query,$matches);
					if ($find_request_to_external_db){

						$database_and_table = str_replace("from ", "", $matches['0']);
						$database_and_table = str_replace("FROM ", "", $matches['0']);

						//is from external db then look like: database.tablename
						$database_and_table = explode(".", $database_and_table);
						if ($database_and_table['0'] and $database_and_table['1']){
							$external_db_tablename = trim($database_and_table['0']);

							//check if connection information exists in aiki_databases
							$external_db = $this->get_row("select * from aiki_databases where db_name = '$external_db_tablename'");
							if ($external_db){

								//found database now connect
								$this->connect($external_db->db_user, $external_db->db_pass,  $external_db->db_host);
								$this->select($external_db->db_name);

								$this->result = @mysql_query($query,$this->dbh);
					
								//connect back to original host
								$this->connect($config['db_user'], $config['db_pass'], $config['db_host']);
								$this->select($config['db_name']);
							}
						}

					}
				}else{

					$is_insert = true;
					$this->register_error($str);

					// -
					//$this->show_errors ? trigger_error($str,E_USER_WARNING) : null;
					return false;
				}
			}
			// Query was an insert, delete, update, replace
			$is_insert = false;
			if ( preg_match("/^(insert|delete|update|replace)\s+/i",$query) )
			{
				$this->rows_affected = @mysql_affected_rows();

				// Take note of the insert_id
				if ( preg_match("/^(insert|replace)\s+/i",$query) )
				{
					$this->insert_id = @mysql_insert_id($this->dbh);
				}

				// Return number fo rows affected
				$return_val = $this->rows_affected;
			}
			// Query was a select
			else
			{

				// Take note of column info
				$i=0;
				while ($i < @mysql_num_fields($this->result))
				{
					$this->col_info[$i] = @mysql_fetch_field($this->result);
					$i++;
				}


				// Store Query Results
				$num_rows=0;
				while ( $row = @mysql_fetch_object($this->result) )
				{
					// Store relults as an objects within main array
					$this->last_result[$num_rows] = $row;
					$num_rows++;
				}

				@mysql_free_result($this->result);

				// Log number of rows the query returned
				$this->num_rows = $num_rows;

				// Return number of rows selected
				$return_val = $this->num_rows;
			}

			// disk caching of queries
			$this->store_cache($query,$is_insert);

			// If debug ALL queries
			$this->trace || $this->debug_all ? $this->debug() : null ;


			return $return_val;
		}

	}

}