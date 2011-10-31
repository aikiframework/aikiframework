<?php

/**
 * Aiki Framework (PHP)
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
 * @package     utility apps
 * @filesource
 */

define ("VERSION", "0.01 alpha");

 /*
  * PURPOSE 
  * A tools for recovery, dignostic or mantain aiki site, that works
  * even if aiki site is crashed. 
  * 
  * It's not other admin interface.
  * 
  *  
  */


$start= microtime(true);
session_start();

// try connection 
$aikiDB = false;
if ( isset($_SESSION["connection"]) ){
	if ( !@mysql_connect( $_SESSION["db_host"], $_SESSION["db_user"], $_SESSION["db_pass"]) ){
		unset($_SESSION["connection"]);
	} elseif ( @mysql_select_db( $_SESSION["db_name"]) ){
		$aikiDB = true;
	} 
} 

// set action to do
$action = @$_POST["_action"] or $action=@$_GET["_action"] or $action="";
$action = strtolower($action);
if ( !$aikiDB && $action!="connect" ) {
	$action="no-action";	
}
		
// take decision using $action
switch ( $action ) {
	case "no-action":
		$content= ask_connection_data();
		break;
		
	case "connect":
	    if ( !isset($_SESSION["time"]) || time()-$_SESSION["time"] < 10 ) {
			message("Please, wait 10 sec. before click on connect button","warning");
			$content= ask_connection_data();	    
	    } elseif ( @mysql_connect( $_POST["db_host"], $_POST["db_user"], $_POST["db_pass"]) ){
			$_SESSION['db_host']= $_POST["db_host"];
			$_SESSION['db_user']= $_POST["db_user"];
			$_SESSION['db_pass']= $_POST["db_pass"];		  
			$_SESSION["connection"]=true;			
			$message ="Conected {$_POST['db_user']}@{$_POST['db_host']}";
			
			if ( @mysql_select_db( $_POST["db_name"])){
				$_SESSION["db_name"]= $_POST["db_name"];
				$aikiDB= true;
				message ( $message . "<br>Open {$_POST['db_name']}" );
			} else {				
				$content  = ask_connection_data() ;
				message ( $message . "<br><strong>No database selected</strong>", "warning");
			}			
			
		} else {			
			$content = ask_connection_data();
			message ("Error: can't connect","error");
		}  
		break;
	
	case "search widget":			
		$widget= str_replace("'","",$_GET["search"]);
		$SQL= "SELECT * FROM aiki_widgets WHERE " . ( (integer) $widget>0 ? "id='$widget'" :  "widget_name='$widget'" );		
		$content= show_mysql($SQL);
		break;
	
	case "search content":
		$widget_content= str_replace("'","",$_GET["search"]);
		$link="'NO_FILTER_HTML:','<a href=\"?_action=query&SQL=SELECT * FROM aiki_widgets WHERE id=',id,'\">',id,'</a>'";
		$SQL= "SELECT concat($link,'\n',widget_name, '\n', widget_site) as id_data,".
			   "widget,display_urls FROM aiki_widgets WHERE widget like '%$widget_content%' ";
		$content= show_mysql($SQL);
		break;
	
	case "search url":			
		$widget= str_replace("'","",$_GET["search"]);
		$SQL= "SELECT * FROM aiki_widgets WHERE display_urls like '%|$widget|%' OR display_urls='$widget' or display_urls like '%|$widget' or display_urls like '$widget|%'";
		$content= show_mysql($SQL);
		break;
					
	case "edit":
		if ( isset($_GET["table"]) && isset($_GET["id"])) {
			$table = clean($_GET["table"]) ;
			$id	= clean($_GET["id"]);
			$content = form_record ( $table, $id );
		} else {
			message( "Table name or id value not found","error");
		}
		break;
	
	case "frame-phpinfo":
		phpinfo();
		die();
	
	case "phpinfo":		
		$content = ""; // phpinfo is executed in html layout
		break;
	
	case "save record":
		if ( !isset($_POST["_table"]) || !isset($_POST["_table_id"])) {
			message("Error saving: missing table of id field value","error");
		} elseif ( sql_save_record( $_POST["_table"], $_POST["_table_id"]) ){
			message("Record saved");
		} else {
			message("Error saving:<br><em>". mysql_error()."</em>","error") ;	
		}
		break;
	
	case "clone record":
		if ( !isset($_POST["_table"]) || !isset($_POST["_table_id"])) {
			message("Error cloning: missing table of id field value","error");
		} elseif ( sql_clone_record( $_POST["_table"], $_POST["_table_id"]) ){
			message("Record cloned");
		} else {
			message( "Error cloning:" . mysql_error(), "error");
		}
		break;
	
	case "save as new":
	case "add record":
		if ( !isset($_POST["_table"]) ) {
			message("Error adding: missing table","error");
		} elseif ( sql_add_record( $_POST["_table"])){
			message("Record added");
		} else { 
			message("Error adding:" . mysql_error());	
		}		
		break;
	
	case "logout":			
		unset($_SESSION['db_host']);
		unset($_SESSION['db_user']);
		unset($_SESSION['db_pass']);
		unset($_SESSION['db_name']);
		unset($_SESSION['connection']);
		header("location:aiki_tools.php");
		break;
	
	case "show tables":
		$content = show_tables();
		break;
	
	case "show widgets":
		$content = show_widgets();
		break;
	
	case "query":
		$content= ask_for_query();
		break;
	
	case "send query":			
		$SQL = $_GET["SQL"];
		$content= ask_for_query() .
				  show_mysql($SQL);
		break;
		
	case "":
		$content="(no action)";
		break;

	default:
		$content="invalid action .$action";
	} 



/*
 * EMBED LIBRARY
 *
 **********************************************************/


/**
 *  show a form with host,user,password, and database
 *  @return string html form.
 */

function ask_connection_data(){
	$file    = basename(__FILE__);
	
	$_SESSION["time"] = time();
	
	$db_host = isset($_POST["db_host"]) ? $_POST['db_host']: "localhost";
	$db_user = isset($_POST["db_user"]) ? $_POST['db_user']: "";
	$db_pass = isset($_POST["db_pass"]) ? $_POST['db_pass']: "";
	$db_name = isset($_POST["db_name"]) ? $_POST['db_name']: "";
	
	if ( isset($_SESSION["connection"]) ) {
		
		$rs= mysql_query("SHOW DATABASES");
		$select_db="";
		while ($row= mysql_fetch_row($rs)) {
			if ( $row[0] != "information_schema" ){
				$select_db .= "<option value='{$row[0]}'>{$row[0]}</option>\n";		
			}
		}
		
		if ( $select_db=="") {
			$select_db = "No database avalaible";
		} else {
			$select_db =
				"<select name='db_name'>\n".
			    $select_db.
			    "</select>\n";			
		}
	} else {
	  $select_db= "<input type='text' name='db_name' size='30' value='$db_name'>" ;
	} 
	  	
	$form = <<<EOF
<form id="f_conection" class="pretty" method="POST" action="$file" >
	<label>Host:	  </label><input type="text" name="db_host" size="30" value="$db_host" >
	<label>User:	  </label><input type="text" name="db_user" size="30" value="$db_user" >
	<label>Password:</label><input type="password" name="db_pass" size="30" value="$db_pass" >
	<label>Database:</label>$select_db	
	<input type="submit" value="Connect" name="_action" class='button unique-button'>
</form> 
EOF;
	return $form;
}

/**
 * ask for a query (using a textarea)
 *
 * @return string HTML 
 */

function ask_for_query(){

	$file = basename(__FILE__);
	$SQL  = isset($_GET["SQL"]) ? $_GET["SQL"] : "" ;
	
	$form= "
<div id='database'>	
	<h3>Write a SQL query</h3>
	<form method='get' action='$file'>		
	<textarea name='SQL' rows='6' cols='60'>$SQL</textarea>
	<input type='submit' value='Send Query' name='_action' class='button unique-button'>			
	</form>
</div>";

	return $form;
}


/**
 *  set/get a little message.
 *  With no parameters, function work like get, returning last stored
 *  message. With a paramertes, set (store) the message.
 *  Message will be sorrounded by a div class=message messaget-type.
 
 *  @param string [optional] message
 *  @param string $type  literal warning, ok (default), error. 
 *  @return string actual message
 */

function message( $text=NULL, $type="ok"){
	static $message="";
	if ( !is_null($text) ) {
		$message = "<div class='message message-$type'>$text</div>\n";
	}
	return $message;	
}


/**
 *  list all tables, and details of a given table by $_GET[table]
 *
 *  @return string HTML of list, actual table
 */

function show_tables(){
	
	$rs= mysql_query("SHOW TABLES");
	$list = "<ul class='tables'>\n";
	
	$table = isset($_GET['table'] ) ?  clean($_GET['table']) : "" ;
	
	while ($row= mysql_fetch_row($rs)) {
		$active = ( $row[0]==$table ? " class='active'": "" );
		$list .= "<li$active>".
					"<span class='table-name'>{$row[0]}</span>". 
					"<a href='?_action=send+query&SQL=SELECT * FROM $row[0]'>data</a> ".
					"<a href='?_action=show+tables&table=$row[0]'>structure</a>".
				  "</li>\n";
	}
	$list .= "</ul>";
	
	$structure = ( $table ? "<h3>$table</h3>\n". show_mysql ("SHOW COLUMNS FROM $table", false) : "");

	$content = "<div class='col-container two-cols col-tables'>\n<div class='col-list'>$list</div>\n<div class='structure col-data'>$structure</div>";
	
	return $content;
}	


/**
 *  list all widet in list, and details of a given widget by $_GET[widget]
 *
 *  @return string HTML 
 */

function show_widgets(){
	
	$rs= mysql_query("SELECT * FROM aiki_widgets ORDER BY id");
	$widget = isset($_GET['widget'] ) ?  clean($_GET['widget']) : "" ;
	$list = "<ul class='tables'>\n";
	
	while ($row= mysql_fetch_assoc($rs)) {
		$active = ( $row['id']==$widget ? " class='active'": "" );
		$list .= "<li$active>".
					"<span class='table-name'>{$row['id']}: {$row['widget_name']}</span>". 
					"<a href='?_action=show+widgets&amp;widget={$row['id']}'>brief</a> ".
				  "</li>\n";
	}
	$list .= "</ul>";
		
	$brief = ( $widget ? show_widget($widget) : "");

	$content = "<div class='col-container two-cols col-tables'>\n<div class='col-list'>$list</div>\n<div class='widget-data col-data'>$brief</div>";
	
	return $content;
}	


/**
 * show a unique widget in details
 * show_widgets (note s) use this function.
 * @param $id integer . Id of widget to show
 * @return string HTML 
 */

function show_widget( $id){
	$id= (int) $id;
	
	$content = 
	    "<div id='widget_link'>
	    <a href=?_action=edit&table=aiki_widgets&id=$id class='button'>EDIT</a> <a href='#page_data'>Id</a><a href='#page_display'>Display</a><a href='#page_permissions'>Permission</a></div>".	
		"<form method='post'>\n".
		"<input type='hidden' name='table'    value='widget'>\n".
		"<input type='hidden' name='table_id' value='$id'>\n".
		"</form>".
		"<h3>Widget Data</h3>\n".
		"<div id='widget_wrapper'>".
		"<div id='widget_pages'>";
	
	$content .=
		"<div id='page_data' class='widget_page'>\n".
		"<h4>Identification data</h4>\n".
		sql_show_record(
			"SELECT id,widget_name, if(is_father,'yes','no') as father, father_widget,widget_site,".
			" if(is_active,'Yes','No') as active".
			" FROM aiki_widgets".
			" WHERE id=$id LIMIT 1", "widget") .
		"</div>";
		
	$content .=
		"<div id='page_display' class='widget_page'>\n".
		"<h4>Display</h4>".
		sql_show_record(
			"SELECT display_urls, kill_urls, widget, css, if_no_results".
			" FROM aiki_widgets".
			" WHERE id=$id LIMIT 1","widget").
		"</div>";	
		
	$content .= 
		"<div id='page_permissions' class='widget_page'>\n".
		"<h4>Permissions</h4>".
		sql_show_record(
			"SELECT authorized_select, if_authorized, if( is_admin,'yes', 'no') as admin, permissions".
			" FROM aiki_widgets".
			" WHERE id=$id LIMIT 1","widget").
		"</div>" ;	
	
	$content .=  "</div>" . // close wrappers.
	             "</div>";
		
	return $content;
}

/**
 * show search bar that containts shortcut, and search utilities.
 *
 * @return string HTML 
 */

function show_search_bar(){
	
$file  = basename(__FILE__);
$widget = isset($_GET["search"] ) ? $_GET["search"] : "" ;
$search = "
<div id='search-area'>			
	<a href='?_action=show+tables'>Show tables</a> <a href='?_action=show+widgets'>Show widgets</a>
	<a href='?_action=phpinfo'>phpinfo</a>
	<form method='get' action='$file' >
		<input name='search' value='$widget'  />	
		<input type='submit' value='Search widget'  name='_action' class='shortcut'/>
		<input type='submit' value='Search content' name='_action' class='shortcut'/>
		<input type='submit' value='Search url'     name='_action' class='shortcut'/>
	</form>
	
</div>";
return $search;
}


/**
 * execute a SQL query and show the result
 *
 * @param string $SQL the query
 * @param boolean $showRecordsFound when true (Default) show nº of records found.
 * @return string HTML 
 */

function show_mysql($SQL, $showRecordsFound=true){
	$file = basename(__FILE__);
	
	// try to insert primary key in result.
	$primaryKey= false;
	if ( ( $table = sql_extract_table($SQL)) && ( $primaryKey= sql_get_primary_key($table) ) ){
		if ( !preg_match ("/^SELECT\s+\*\s+FROM/i", $SQL) ) {
			$SQL = "SELECT $primaryKey as PRYMARI_KEI6," . substr($SQL,6); // PRYMARI is intencioned
			$primaryKey = "PRYMARI_KEI6";
		}	
	}	

	// execute query
	$result = mysql_query($SQL);
	if ( mysql_errno() ) {
	  message("BAD QUERY. MYSQL ERROR ". mysql_errno() ."<br><em>" . mysql_error()."</em>","error")	;
	  return " :-(";
	} 
	
	$rows = mysql_num_rows($result);
	if ( $showRecordsFound ) {
		if ( $rows==0) {
			message ( "No records found", "warning");
		} else {
			message ( "Found: $rows record" . ( $rows>1?"s" : "")  );
		}
	}	
	
	
	if ( $rows < 3 ) {		
		//show records one field per row.
		$contenido .="<table class='record'>";
		while ( $row= mysql_fetch_assoc($result) ){	 
			$contenido .="<table class='record'>";
			if ( $primaryKey ) {
				$contenido.= "<tr ><th class='action'><a href='$file?_action=edit&table=$table&id={$row[$primaryKey]}'>Edit</a></th></tr>";
			}
			foreach ($row as $field=> $value ){
				if ( $field<>"PRYMARI_KEI6") {
					$contenido.= "<tr><th>$field</th><td>" . filter($value) ."</td></tr>\n";
				}
			}			
			$contenido .="</table>\n";
		}
	} else {
		// show records as table (one record per row)		
		$count=0;
		$contenido .= "<table><tr>";
		while ( $row= mysql_fetch_assoc($result) ){
			if ( !$count ){			
				foreach ($row as $key=>$value ){
					if ( $key<>"PRYMARI_KEI6") {
						$contenido.= "<th>$key</th>";
					}
				}
				$contenido .="</tr>\n";
			}
			$contenido .="<tr class='". ( $count%2 ? "even": "odd") . "'>\n";
			foreach ($row as $key=>$value ){
				if ( $key<>"PRYMARI_KEI6") {
					$contenido.= "<td>" . filter($value) ."</td>";
				}
			}
			$contenido .="</tr>\n";
			$count++;
			if ( $count > 100 ){
				break;
			}
				
		}
		$contenido .= "</table>";
	}
	
	return $contenido;
}	
	


/**
 * clean '" from a given string
 *
 * @param string $string to clean
 * @return string cleaned
 */

function clean($string){
	return strtr( $string, array (
	"'" => "",
	'"' => ""));
	
}

/**
 * Filter a string, replacing \n by br and html <> by identities.
 *
 * @param string $string to clean
 * @return string cleaned
 */

function filter($s){
if ( strpos($s,"NO_FILTER_HTML:") ===0) {
	return str_replace("\n", "<br>", substr($s,15));
}
return strtr( $s, array (
	">" => "&gt;", 
	"<" => "&lt;", 
	"\n"=>"<br>"));
}


/**
 * extract table from a SQL SELECT 
 *
 * @param string $SQL Select query
 * @return string table
 */

function sql_extract_table ($SQL){
	if ( preg_match("/^SELECT .* FROM\s+([^ ]+)/i",$SQL, $resul)) {
		return $resul[1];
	}
	return false;
}


/**
 * get the primary key of a given table
 *
 * @param string $table to exam
 * @return string field or false
 */

function sql_get_primary_key($table){		
	$rs = mysql_query("SHOW COLUMNS FROM $table");
	while ( $field = mysql_fetch_array($rs) ){
		if ( $field["Key"] == "PRI" ) {
			return $field["Field"];
		}		
	}
	return false;
}


/**
 * add a record to a given table, reading field values from $_POST
 *
 * @param string $table 
 * @return boolean if added true, elser false
 */

function sql_add_record ( $table ){
	
	// cleaning
	$table= mysql_real_escape_string($table);
	
	foreach ($_POST as $key=>$value){
		if ( substr($key,0,1)!="_") {
			$fields[]=$key;
			$values[]=addslashes($value); 
		}		 
	}
	
	$sql = sprintf( "INSERT INTO $table (%s) VALUES ('%s') ",
		implode(","  , $fields),
		implode("','", $values) );  
	return 	mysql_query($sql);
}


/**
 * save record to a given table and id, reading field values from $_POST
 *
 * @param string $table 
 * @param string $id value
 * @return boolean if saved true, elser false
 */	
		
function sql_save_record ( $table, $id ){
	
	// cleaning
	$table= mysql_real_escape_string($table);
	$id   = mysql_real_escape_string($id);
	
	foreach ($_POST as $key=>$value){
		if ( substr($key,0,1)!="_") {
			$pairs[] = "$key= '". addslashes($value). "'";
		}		 		
	}
	
	$primaryKey = sql_get_primary_key($table);
	if ( $primaryKey ) {		
		$sql = sprintf( 
			"UPDATE $table SET %s WHERE $primaryKey='$id' ",
			implode(","  , $pairs));
		return	mysql_query($sql);	
	}
	return false;
 }	
	

/**
 * clone a record for given table and id
 *
 * @param string $table 
 * @param string $id value
 * @return boolean if saved true, elser false
 */		

function sql_clone_record ( $table, $id ) {

   // cleaning
   $table= mysql_real_escape_string($table);
   $id= mysql_real_escape_string($id);

   // get field list
   $rsStructure = mysql_query("SHOW COLUMNS FROM $table");
   $fields= array();
   $primaryKey ="";
   while ( $field = mysql_fetch_array($rsStructure) ){

       if ( $field["Key"] == "PRI" ){
           $primaryKey = $field[0];
       }
       $fields[] =  $field["Key"] == "PRI" || $field["Key"] == "UNI" ? "NULL":    $field[0];
   }
   mysql_free_result ( $rsStructure );

   // clonar el registro mediante una SQL
   if ( $primaryKey && count($fields)>0 ) {
       $SQL = sprintf( "INSERT INTO $table ( SELECT %s FROM $table WHERE %s='%s' )",
           implode(",",$fields),
           $primaryKey,
           $id );
       mysql_query ($SQL);
       return mysql_affected_rows();
   }
   return false;
}


/**
 * show the record of the first row of a given SQL SELECT query.
 *
 * @param string $SQL
 * @param string $class
 * @return string html code
 */		

function sql_show_record($SQL, $class=""){
	$rs= mysql_query($SQL);
	if ( $record = mysql_fetch_assoc($rs) ){
		$table = "<table class='record" . ($class? " $class" : "" ) ."'>";
		foreach ($record as $field => $value ){
			$table .= "<tr><th>$field</th><td>". htmlspecialchars($value) ."</td></tr>\n";
		}		
		$table .= "</table>";
		return $table;		
	} 
	message("no record $SQL","warning");
	return "";		
}


/**
 * show a html form to edit a record of a table
 *
 * @param string $table
 * @param string $id value
 * @return string html code
 */		


function form_record($table, $id=NULL) {
	
	// obtain structure and primary key
	$keyField = sql_get_primary_key($table);
	if ( !$keyField ) {
		return false;
	}
		
	$fields= array();	
	$rs = mysql_query("SHOW COLUMNS FROM $table");
	while ( $field = mysql_fetch_array($rs) ){		
		$fields[ $field["Field"] ]= $field;
	}
	mysql_free_result($rs);
		
	// obtain data 
	if ( is_null($id) ){
		// record;
		$record = array();
		foreach ($fields as $fieldname=>$field) {
			if ( $field["Default"] !="NULL" &&
				 strpos($field["Default"], "ON ") !==0 ){
				$record [ $fieldname ] = $field["Default"];
			}
		}		
	} else {
		$rs= mysql_query("SELECT * FROM $table WHERE $keyField='$id'");
		if ( ! $record= mysql_fetch_array($rs) ) {
			return "<div class='message error'>Record not found</div>";
		}
	}
	
	if ( is_null($id) ){
		$buttons= "<input type='submit' name='_action' class='button' value='Add record'>";
	} else {
		$buttons= "<input type='submit' name='_action' class='button' value='Save record'>".
			      "<input type='submit' name='_action' class='button' value='Save as new'>".
				  "<input type='submit' name='_action' class='button' value='Clone record'>";		
	}	
	
	return
		"<form action='". basename(__FILE__) . "' method='post' class='record edit'>".
		$buttons.
		"<input type='hidden' name='_table' value='$table'>\n".
		( !is_null($id) ? "<input type='hidden' name='_table_id' value='$id'>": "") ."\n".
		form_fields_record($fields,$record).
		$buttons.
		"</form>";	
}


/**
 * helper for form_record, that return input/textarea for each field
 *
 * @param string $table
 * @param string $id value
 * @return string html code
 */		

function form_fields_record( $fields, $record ){
	$ret="";
	foreach ($fields as $fieldname => $field ){
		$ret .= "<p><label for='$fieldname'>$fieldname</label>";
		$value = isset($record[$fieldname]) ? $record[$fieldname] : "" ;
		
		switch ( $field["Type"] ) {
			case "text":
			case "tinytext";
			case "mediumtext":
			case "longtext":
				$ret .= "<textarea id='$fieldname' name='$fieldname' rows='5' cols='60'>$value</textarea>";
				break;
			default :
				$ret .= "<input type='text' id='$fieldname' name='$fieldname' value='$value' >";
				break;
		}
		$ret .= "</p>\n";
	}
	return $ret;
}


/**
 * ECHO W3C border-radius css, with -moz and -webkit variants 
 *
 * @param string $borders
 * @return string css code
 */		

function border_radius($borders){
	echo "border-radius:$borders;-moz-border-radius:$borders;-webkit-border-radius:$borders;";
}


/***********************************************************
 * 
 * EMBEDDED CSS 
 *
 **********************************************************/
?>
<!DOCTYPE html>
<html>
<head>
<title>Aiki Tools</title>
<style type="text/css">

* {	margin:0; padding:0;}

body, table, textarea, form, input, select  {
font-family: sans-serif;
font-size:13px;
line-height:16px;}

body {
background-color:#333; }

h1{ font-size: 30px; line-height:40px; padding:4px 0px; color:#555;text-transform:uppercase;}
h1 em { display:block; font-size:15px; line-height:18px; font-style:normal; color: orange; text-transform:lowercase}
h2{ font-size: 20px; line-height:24px; padding:4px 0px; color:orange}

h3{ font-size: 13px; font-weight:bold  ; text-transform: uppercase;}
h4{ font-size: 13px; font-weight:normal; text-transform: uppercase; 
padding: 6x 0 7px 0;}

a { text-decoration:none; color: #569}
a:hover { color: #333}

#page { <?php border_radius('15px');?>
background-color:#fff;min-height:100%;padding: 1% 2% 9px 2%;position: relative;margin: 11px 6%;}

#header { padding-right:45%; min-height: 80px;}

#connection-data { <?php border_radius('0px 0px 0px 6px');?>
position: absolute; top: 0px; right: 0;
padding: 2px 4px 6px 24px;
background-color: #333;
color: #fff; font-weight:bold;
text-align:right;}
#connection-data a       { color: orange}
#connection-data a:hover { color: #777}
#connection-data a:before  {content: "\2192";padding-right: 0.25em}

#search-area { position: absolute;top: 33px;width: 55%;right: 2%;text-align:right;}
#search-area input[type='submit']{background-color: #fff; border:none;color: #569;}
#search-area input[type='submit']:hover {color:#333; cursor:pointer}
#search-area a + a { margin-left: 1em;}
#search-area a:before  {content: "\2192";padding-right: 0.25em}

#top {width:100%;background-color:#f0f0f0;height:31px;border-bottom:1px solid #333;}
#top li { display: block; float:left; width:110px; padding:10px 4px 4px 4px; height: 18px;
margin-bottom:16px;border-right: 1px solid #333}
#top li:hover { background-color: #999}
#top a { color: #333; font-weight: bold;}

#f_conection {width: 400px; margin: 15px auto;}

#menu {overflow: auto;}

#database {padding:0;margin: 9px 4% 9px 0;}

#aditional-info { padding-top:9px;color: #777; text-align:right}

form.pretty label { display:block; color:#666; margin:12px 0px 4px 0px; clear:both}	
form.pretty input.shortcut  { float: left;}	

input.button { margin-right: 4px}
input.unique-button { display: block; margin: 8px 0px}

ul.tables,	
table { border-collapse: collapse; color: #333; font-size:12px; margin:12px 0px;}
table theader tr { border-bottom: 1px solid #000 }

ul.tables li {
display: block;
border-bottom: 1px dotted #999 }
ul.tables li:hover { border-bottom-color: orange; color: orange }
ul.tables li.active { background-color:#f6f6f6;}
ul.tables li a { float: right; margin-right: 1em}
ul.tables li a:hover { color: orange }

table tr { border-bottom: 1px dotted #999 }
table th,
table td { vertical-align:top; padding: 1px 2px; }

table th+th,
table td+td{ border-left: 1px dotted #999 }

table tr.odd { background-color:#f0f0f0;}

table.record td { width:80%}
table.record th { text-align: right;  }
table.record th.action { text-align: left;  }
table.record th.action a { background: #f0f0ff; padding: 5px 10px 2px 10px;}
table.record th.action a:hover { background: #ddd}

table.record+table.record{margin-top:2em}

table.widget th { border-right: 1px solid #AAA;padding-right:4px}

#results {width:100%; overflow:auto}

div.col-tables    { background-color: #f6f6f6 }
div.col-list	  { background-color: #fff}
div.col-container { overflow:auto;}
div.col-container > div { float:left; }
div.col-container > div + div { margin-left: 4%;}
div.two-cols > div { width:55%}
#page div.col-list { width:35%;}
div.col-data h3 { margin: 16px 0 15px 0px; border-bottom:1px solid #333;}
div.col-data table { width: 100%}

div.message { margin: 1em 0; padding: 0.5em 8px; background-color: #f0f0f0; width:50%;
<?php border_radius("8px");?>}
div.message-error   { color: #900; font-weight:bold; }
div.message-error em  { color: #333; font-weight:normal; font-style:normal; }
div.message-warning { color: orange; font-weight:bold; }

form.record label, form.record textarea, form.record input, form.record select {
font-size:14px;}

form.record p {  margin: 2px 0px; border-top:1px dotted #999;  }
form.record label { float:left; clear:left;width:20%;color: #333; padding:2px 1em 0 0;text-align:right;  }
form.record textarea:focus{ height: 30em}

#widget_link { margin:8px 0px;overflow: auto; clear:both}
#widget_link a { margin-right:1em; }
#widget_link a:before {content: "\2192";padding-right: 0.25em}

#widget_link a.button { background-color: #ddd; padding:1px 1.5em 1px 0.5em}

</style>
</head>
<?php
/***********************************************************
 * 
 * EMBEDDED HTML BODY
 *
 *********************************************************/
 ?>
<body>

<div id="page">

	<div id="header">
		<h1><img src="http://www.aikiframework.org/assets/images/bannerlogo.png" alt="aiki framework"><em> recovery tool for aiki framework</em></h1>
		<?php if ( $aikiDB ) { ?>
		   <div id="connection-data">
		   <?php echo "<strong>Server:</strong> ",$_SESSION['db_host']," <strong>Database:</strong> ", $_SESSION['db_name'];?>
			| <a href="?_action=logout">Logout</a>
		   </div>
		<?php
			echo show_search_bar();
		 }
		?>	
	</div>

		
	<div id="menu">
	   <?php 
		  
	   if ( $aikiDB ) {?>
		<div id="top">
			<ul>
			<li><a href="?_action=show+widgets">Widgets</a></li>
			<li><a href="?_action=show+tables">Tables</a></li>
			<li><a href="?_action=query">SQL</a></li>
			</ul>
		</div>		
		<?php
		show_search_bar();
		} // from if !$aikiDB
		?>	
	</div>	

	<?php echo message(); ?>

	<div id="results">	
		<?php
		if ( $action == "phpinfo") {		
			echo "<iframe src='?_action=frame-phpinfo' width='100%' height='600px'></iframe>";	
		} else {
			echo $content;
		}?>
	</div>		

	<div id="aditional-info">Version <?php echo VERSION; printf(" | Generated in %.2f mseg", 1000*(microtime(true)-$start)); ?></div>
</div>	

</body>
</html>
