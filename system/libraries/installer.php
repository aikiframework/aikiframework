<?php

/**
 * Aiki framework (PHP)
 *
 * @author		Aikilab http://www.aikilab.com
 * @copyright  (c) 2008-2010 Aikilab
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>Aiki framework installer</title>

<style type="text/css">
* {
	padding: 0;
	margin: 0;
}

body {
	background-color: #E5E5E5;
}

legend {
	padding: 20px;
}

#content {
	float: left;
	width: 520px;
	padding: 6px 10px 0px 6px;
}

#content img {
	margin-right: 16px;
	margin-left: 12px;
	margin-top: 6px;
	margin-bottom: 6px;
	padding: 2px;
	float: left;
}

#content p {
	font-family: verdana, "Microsoft Sans Serif", Times, serif;
	font-size: 8pt;
	margin-top: 0px;
	margin-bottom: 10px;
	padding: 0px 10px;
	text-align: justify;
	line-height: 12pt;
}

#content h1 {
	font-family: Baskerville, Georgia, Times, serif;
	font-size: 15pt;
	font-style: normal;
	font-weight: normal;
	margin-top: 5px;
	padding: 10px 10px;
}

.myform {
	margin: 10px;
	padding: 14px;
}

#stylized {
	border: solid 2px #999;
	background: #F7F7F7;
}

#stylized h1 {
	font-size: 14px;
	font-weight: bold;
	margin-bottom: 0px;
}

#stylized p {
	font-size: 11px;
	color: #666666;
	margin-bottom: 20px;
	border-bottom: solid 1px #999;
	padding-bottom: 10px;
}

#stylized label {
	display: block;
	font-weight: bold;
	text-align: right;
	width: 200px;
	float: left;
}

#stylized .small {
	color: #666666;
	display: block;
	font-size: 11px;
	font-weight: normal;
	text-align: right;
	width: 140px;
}

#stylized input {
	float: left;
	font-size: 12px;
	padding: 4px 2px;
	border: solid 1px #999;
	width: 200px;
	margin: 2px 0 20px 10px;
}

#stylized select {
	float: left;
	font-size: 12px;
	padding: 4px 2px;
	border: solid 1px #999;
	width: 200px;
	margin: 2px 0 20px 10px;
}

#stylized button {
	clear: both;
	margin-left: 150px;
	width: 128px;
	height: 35px;
	background: #666666;
	text-align: center;
	line-height: 31px;
	color: #FFFFFF;
	font-size: 11px;
	font-weight: bold;
}
</style>

</head>

<body>

<div class="" id="content">
<h1>Aiki framework installer</h1>

<div id="stylized" class="myform">';
if (!isset($_POST['db_type']) or !isset($_POST['db_host']) or !isset($_POST['db_name']) or !isset($_POST['db_user'])){
	echo '
<p>This will guide you throw this ONE STEP installer
<br />
before we start you need to check the following for:
<br />
<br />
1- please make sure that <b>'.$system_folder.'</b> has script write permissions that is: 777 ( to create config.php and .htaccess inside otherwise you will have to create them manulay)
<br />
<br />
2- please create an empty database, it will be great if you set the connection collation to utf8_general_ci.
<br />
<br />
3- make sure you are using php 5.1 or above
<br />
<br />
4- please enable mod_rewrite inside your apache2 httpd.conf  
</p>	
	
<form method="post" id="form">
<fieldset><legend> Database Settings</legend> <label>Database type</label>
<select name="db_type">
<option name="mysql" selected>mysql</option>
<option name="mssql">mssql</option>
<option name="oracle">oracle 8 or higher</option>
<option name="pdo">pdo</option>
<option name="postgresql">postgresql</option>
<option name="sqlite">sqlite</option>
</select>
<label>Host Name</label><input
	type="text" name="db_host" value="localhost" /> <label>Database name</label><input
	type="text" name="db_name" value="" /> <label>Database username</label><input
	type="text" name="db_user" value="" /> <label>Database password</label><input
	type="text" name="db_pass" value="" /> <label>Database encoding</label><input
	type="text" name="db_encoding" value="utf8" />
	</fieldset>
	
<fieldset><legend>Admin Settings</legend>
<label>Username</label><input type="text" name="username" value="" /></label>
<label>Full Name</label><input type="text" name="full_name" value="" /></label>
<label>Email</label><input	type="text" name="email" value="" /></label>
	</fieldset>	

<button type="submit">Next..</button>
</form>';

}else{

	if ($_POST['username']){
		$username = $_POST['username'];
	}else{
		$username = "admin";
	}

	if ($_POST['full_name']){
		$full_name = $_POST['full_name'];
	}else{
		$full_name = "System Admin";
	}

	if ($_POST['email']){
		if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $_POST['email'])){
			$email = $_POST['email'];
		}
	}
	if (!isset($email)){
		$email = '';
	}

	$pageURL = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	$page_strlen =  strlen($pageURL);
	$system_folder_strlen =  strlen($system_folder);
	$_SERVER["REQUEST_URI"] = str_replace("index.php", '', $_SERVER["REQUEST_URI"]);


	$config_file = '<?php

$config = array();

$config["db_type"] = "'.$_POST['db_type'].'";
$config["db_name"] = "'.$_POST['db_name'].'";
$config["db_user"] = "'.$_POST['db_user'].'";
$config["db_pass"] = "'.$_POST['db_pass'].'";
$config["db_host"] = "'.$_POST['db_host'].'";
$config["db_encoding"] = "'.$_POST['db_encoding'].'";
$config["db_use_mysql_set_charset"] = false;

$config["db_path"] = ""; //SQLite and sqlite PDO
$config["db_dsn"] = ""; //sqlite PDO

$config["db_cache_timeout"] = 24;
$config["cache_dir"] = "";//db cacheing
$config["enable_query_cache"] = false;

$config["html_tidy"] = false;
$config["tidy_compress"] = false;
$config["html_tidy_config"] = array(
 \'indent\'         => true,
 \'output-xhtml\' =>    true,
 \'wrap\' =>    \'0\',
);

$config["compress_output"] = false;

//widget cache
$config["widget_cache"] = false;
$config["widget_cache_dir"] = ""; //full path to widgets cache directory

$config["css_cache"] = true;
$config["css_cache_timeout"] = 24;
$config["css_cache_file"] = "";

//full html cache
$config["html_cache"] = false; //full path to full html cache directory
$config["cache_timeout"] = "86400"; //ms

$config["session_timeout"] = 3600; //ms
$config["allow_multiple_sessions"] = false;
$config["allow_guest_sessions"] = true;

$config["register_errors"] = false;

$config["error_404"] = "<h1>404 Page Not Found</h1>

<p>This page is not found</p>
<p>Please visit <a href=\"'.$pageURL.'\">Home page</a> so you may find what you are looking for.</p>"; 

$config["debug"] = false;

?>';	


	$conn = @mysql_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_pass']) or die ('Error connecting to mysql');
	$select_db = @mysql_select_db($_POST['db_name']);

	if (!$select_db){
		echo "Can't select db, Trying to create database $_POST[db_name]";
		$create_db = mysql_query("CREATE DATABASE `$_POST[db_name]` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		$select_db = @mysql_select_db($_POST['db_name']);

		if (!$create_db and !$select_db){
			die ("<br />Can't create database $_POST[db_name]");
		}else{
			echo "<br />Success! Created database $_POST[db_name]";
		}
	}

	$config_file_name = "config.php";
	$FileHandle = fopen($config_file_name, 'w') or die("Sorry, no permissions to create config.php");
	fwrite($FileHandle, $config_file);
	fclose($FileHandle);

	$htaccess_file = 'Options +FollowSymLinks
RewriteEngine on
RewriteBase '.$_SERVER["REQUEST_URI"].'
RewriteRule ^image/(.*)px/(.*)/(.*) assets/apps/image_viewer.php?id=$3&size=$1&mode=$2
RewriteRule ^image/(.*)px/(.*) assets/apps/image_viewer.php?id=$2&size=$1
RewriteRule ^image/(.*) assets/apps/image_viewer.php?id=$1
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteRule ^(.*)$ index.php?pretty=$1 [L,QSA]';

	$htaccess_file_name = ".htaccess";
	$FileHandle = fopen($htaccess_file_name, 'w') or die("Sorry, no permissions to create .htaccess file");
	fwrite($FileHandle, $htaccess_file);
	fclose($FileHandle);

	$admin_password = substr(md5(uniqid(rand(),true)),1,8);
	$admin_password_md5_md5 = md5(md5($admin_password));

	$sql = '

CREATE TABLE IF NOT EXISTS `aiki_config` (
  `config_id` int(11) unsigned NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `config_type` varchar(255) default NULL,
  `config_data` mediumtext,
  PRIMARY KEY  (`config_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--------------------------------------------------------

INSERT INTO `aiki_config` (`config_id`, `app_id`, `config_type`, `config_data`) VALUES
(1, 0, \'global_settings\', \'a:9:{s:4:"site";s:7:"default";s:3:"url";s:'.$page_strlen.':"'.$pageURL.'";s:13:"cookie_domain";s:0:"";s:13:"default_chmod";s:4:"0777";s:11:"pretty_urls";s:1:"1";s:16:"default_language";s:7:"english";s:19:"default_time_format";s:9:"d - m - Y";s:8:"site_dir";s:3:"ltr";s:19:"language_short_name";s:2:"en";}\'),
(2, 0, \'database_settings\', \'a:6:{s:7:"db_type";s:5:"mysql";s:10:"disk_cashe";s:1:"1";s:13:"cache_timeout";s:2:"24";s:9:"cache_dir";s:5:"cache";s:13:"cache_queries";s:1:"1";s:16:"charset_encoding";s:4:"utf8";}\'),
(3, 0, \'paths_settings\', \'a:1:{s:10:"top_folder";s:'.$system_folder_strlen.':"'.$system_folder.'";}\'),
(4, 0, \'images_settings\', \'a:4:{s:7:"max_res";s:3:"650";s:20:"default_photo_module";s:18:"apps_photo_archive";s:23:"store_native_extensions";s:4:"true";s:13:"new_extension";s:5:".aiki";}\'),
(5, 0, \'admin_settings\', \'a:1:{s:17:"show_edit_widgets";s:1:"0";}\'),
(6, 0, \'upload_settings\', \'a:4:{s:18:"allowed_extensions";s:20:"jpg|gif|png|jpeg|svg";s:11:"upload_path";s:15:"assets/uploads/";s:8:"plupload";s:5:"html5";s:13:"plupload_path";s:15:"assets/uploads/";}\');

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_dictionary` (
  `term_id` int(11) NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `short_term` varchar(255) NOT NULL,
  `lang_english` text NOT NULL,
  `lang_arabic` text NOT NULL,
  `lang_german` text NOT NULL,
  PRIMARY KEY  (`term_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--------------------------------------------------------

INSERT INTO `aiki_dictionary` (`term_id`, `app_id`, `short_term`, `lang_english`, `lang_arabic`, `lang_german`) VALUES
(1, 0, \'encoding\', \'utf-8\', \'utf-8\', \'utf-8\'),
(2, 0, \'added_successfully\', \'Added successfully\', \'\', \'\'),
(3, 0, \'error_inserting_into_database\', \'Error inserting into database\', \'\', \'\');

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_forms` (
  `id` int(11) NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `form_method` varchar(5) NOT NULL,
  `form_action` varchar(255) NOT NULL,
  `form_dir` varchar(155) NOT NULL,
  `form_table` varchar(255) NOT NULL,
  `form_name` varchar(255) NOT NULL,
  `form_array` text NOT NULL,
  `form_html` text NOT NULL,
  `form_query` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--------------------------------------------------------

INSERT INTO `aiki_forms` (`id`, `app_id`, `form_method`, `form_action`, `form_dir`, `form_table`, `form_name`, `form_array`, `form_html`, `form_query`) VALUES
(1, 0, \'\', \'\', \'\', \'aiki_widgets\', \'widgets_simple_editor\', \'a:15:{s:9:"tablename";s:12:"aiki_widgets";s:4:"pkey";s:2:"id";s:10:"textinput3";s:26:"widget_name|SystemGOD:Name";s:7:"hidden4";s:47:"widget_site|SystemGOD:widget site:value:default";s:13:"staticselect5";s:62:"widget_target|SystemGOD:Target:custome:body>body&header>header";s:7:"hidden6";s:43:"widget_type|SystemGOD:widget type:value:div";s:13:"staticselect8";s:48:"is_father|SystemGOD:Is Father:custome:No>0&Yes>1";s:10:"selection9";s:123:"father_widget|SystemGOD:Father Widget:aiki_widgets:id:widget_name:where display_urls NOT RLIKE (admin) and is_father != (0)";s:10:"textinput6";s:36:"display_order|SystemGOD:Render Order";s:11:"textblock11";s:36:"display_urls|SystemGOD:Address (url)";s:11:"textblock13";s:36:"normal_select|SystemGOD:Select Query";s:11:"textblock16";s:24:"widget|SystemGOD:Content";s:11:"textblock17";s:17:"css|SystemGOD:CSS";s:11:"textinput20";s:42:"records_in_page|SystemGOD:Records per page";s:14:"staticselect35";s:45:"is_active|SystemGOD:Active:custome:Yes>1&No>0";}\', \'\', \'\'),
(4, 0, \'\', \'\', \'\', \'aiki_dictionary\', \'aiki_dictionary\', \'a:7:{s:9:"tablename";s:15:"aiki_dictionary";s:4:"pkey";s:7:"term_id";s:10:"textinput1";s:23:"app_id|SystemGOD:app id";s:10:"textinput2";s:31:"short_term|SystemGOD:short term";s:10:"textblock3";s:35:"lang_english|SystemGOD:lang english";s:10:"textblock4";s:33:"lang_arabic|SystemGOD:lang arabic";s:10:"textblock5";s:33:"lang_german|SystemGOD:lang german";}\', \'\', \'\'),
(6, 0, \'\', \'\', \'\', \'aiki_forms\', \'aiki_forms\', \'a:11:{s:9:"tablename";s:10:"aiki_forms";s:4:"pkey";s:2:"id";s:10:"textinput1";s:23:"app_id|SystemGOD:app id";s:10:"textinput2";s:33:"form_method|SystemGOD:form method";s:10:"textinput3";s:33:"form_action|SystemGOD:form action";s:10:"textinput4";s:27:"form_dir|SystemGOD:form dir";s:10:"textinput5";s:31:"form_table|SystemGOD:form table";s:10:"textinput6";s:29:"form_name|SystemGOD:form name";s:10:"textblock7";s:31:"form_array|SystemGOD:form array";s:10:"textblock8";s:29:"form_html|SystemGOD:form html";s:10:"textblock9";s:31:"form_query|SystemGOD:form query";}\', \'\', \'\'),
(9, 0, \'\', \'\', \'\', \'aiki_languages\', \'aiki_languages\', \'a:8:{s:9:"tablename";s:14:"aiki_languages";s:4:"pkey";s:2:"id";s:10:"textinput1";s:19:"name|SystemGOD:name";s:10:"textinput2";s:27:"sys_name|SystemGOD:sys name";s:10:"textinput3";s:31:"short_name|SystemGOD:short name";s:10:"textinput4";s:17:"dir|SystemGOD:dir";s:10:"textinput5";s:21:"align|SystemGOD:align";s:10:"textinput6";s:31:"is_default|SystemGOD:is default";}\', \'\', \'\'),
(12, 0, \'\', \'\', \'\', \'aiki_redirects\', \'aiki_redirects\', \'a:4:{s:9:"tablename";s:14:"aiki_redirects";s:10:"textinput1";s:17:"url|SystemGOD:url";s:10:"textinput2";s:27:"redirect|SystemGOD:redirect";s:10:"textinput3";s:19:"hits|SystemGOD:hits";}\', \'\', \'\'),
(13, 0, \'\', \'\', \'\', \'aiki_sites\', \'aiki_sites\', \'a:6:{s:9:"tablename";s:10:"aiki_sites";s:4:"pkey";s:7:"site_id";s:10:"textinput1";s:29:"site_name|SystemGOD:site name";s:10:"textinput2";s:37:"site_shortcut|SystemGOD:site shortcut";s:10:"textinput3";s:29:"is_active|SystemGOD:is active";s:10:"textblock4";s:43:"if_closed_output|SystemGOD:if closed output";}\', \'\', \'\'),
(17, 0, \'\', \'\', \'\', \'aiki_users\', \'aiki_users\', \'a:21:{s:9:"tablename";s:10:"aiki_users";s:4:"pkey";s:6:"userid";s:10:"textinput2";s:27:"username|SystemGOD:username";s:10:"textinput3";s:29:"full_name|SystemGOD:full name";s:10:"textinput4";s:25:"country|SystemGOD:country";s:10:"textinput5";s:17:"sex|SystemGOD:sex";s:10:"textinput6";s:17:"job|SystemGOD:job";s:9:"password7";s:44:"password|SystemGOD:password:password:md5|md5";s:10:"textinput8";s:29:"usergroup|SystemGOD:usergroup";s:10:"textinput9";s:21:"email|SystemGOD:email";s:11:"textinput10";s:23:"avatar|SystemGOD:avatar";s:11:"textinput11";s:27:"homepage|SystemGOD:homepage";s:11:"textinput12";s:27:"first_ip|SystemGOD:first ip";s:8:"hidden13";s:33:"first_login|SystemGOD:first login";s:8:"hidden14";s:31:"last_login|SystemGOD:last login";s:11:"textinput15";s:25:"last_ip|SystemGOD:last ip";s:11:"textblock16";s:43:"user_permissions|SystemGOD:user permissions";s:11:"textinput17";s:27:"maillist|SystemGOD:maillist";s:11:"textinput18";s:37:"logins_number|SystemGOD:logins number";s:11:"textinput19";s:25:"randkey|SystemGOD:randkey";s:11:"textinput20";s:29:"is_active|SystemGOD:is active";}\', \'\', \'\'),
(18, 0, \'\', \'\', \'\', \'aiki_users_groups\', \'aiki_users_groups\', \'a:6:{s:9:"tablename";s:17:"aiki_users_groups";s:4:"pkey";s:2:"id";s:10:"textinput1";s:23:"app_id|SystemGOD:app id";s:10:"textinput2";s:19:"name|SystemGOD:name";s:10:"textinput3";s:45:"group_permissions|SystemGOD:group permissions";s:10:"textinput4";s:33:"group_level|SystemGOD:group level";}\', \'\', \'\'),
(20, 0, \'\', \'\', \'\', \'aiki_widgets\', \'aiki_widgets\', \'a:30:{s:9:"tablename";s:12:"aiki_widgets";s:4:"pkey";s:2:"id";s:10:"textinput2";s:26:"widget_name|SystemGOD:Name";s:10:"selection3";s:61:"widget_site|SystemGOD:Site:aiki_sites:site_shortcut:site_name";s:13:"staticselect4";s:62:"widget_target|SystemGOD:Target:custome:body>body&header>header";s:13:"staticselect5";s:213:"widget_type|SystemGOD:Type:custome:div>div&none>0&span>span&paragraph>p&link>a&---html 5--->0&header>header&nav>nav&article>article&aside>aside&figure>figure&footer>footer&section>section&address>address&abbr>abbr";s:10:"textinput6";s:36:"display_order|SystemGOD:Render Order";s:10:"textinput7";s:32:"style_id|SystemGOD:Style (class)";s:13:"staticselect8";s:48:"is_father|SystemGOD:Is Father:custome:No>0&Yes>1";s:10:"selection9";s:123:"father_widget|SystemGOD:Father Widget:aiki_widgets:id:widget_name:where display_urls NOT RLIKE (admin) and is_father != (0)";s:11:"textblock10";s:36:"display_urls|SystemGOD:Address (URL)";s:11:"textblock11";s:29:"kill_urls|SystemGOD:Kill urls";s:11:"textblock12";s:36:"normal_select|SystemGOD:Select Query";s:11:"textblock13";s:51:"authorized_select|SystemGOD:Authorized Select Query";s:11:"textblock14";s:40:"if_no_results|SystemGOD:No Results Error";s:11:"textblock15";s:24:"widget|SystemGOD:Content";s:11:"textblock16";s:17:"css|SystemGOD:CSS";s:11:"textblock17";s:36:"nogui_widget|SystemGOD:nogui Content";s:11:"textinput18";s:53:"display_in_row_of|SystemGOD:Display results in row of";s:11:"textinput19";s:42:"records_in_page|SystemGOD:Records per page";s:11:"textinput20";s:46:"link_example|SystemGOD:Pagination Link Example";s:11:"textblock21";s:30:"pagetitle|SystemGOD:Page title";s:14:"staticselect22";s:65:"is_admin|SystemGOD:Require special permissions:custome:No>0&Yes>1";s:11:"textblock23";s:45:"if_authorized|SystemGOD:If authorized content";s:11:"textblock24";s:39:"permissions|SystemGOD:Permissions Group";s:14:"staticselect25";s:62:"remove_container|SystemGOD:Remove Container:custome:No>0&Yes>1";s:11:"textinput26";s:44:"widget_cache_timeout|SystemGOD:Cache Timeout";s:14:"staticselect27";s:57:"custome_output|SystemGOD:Custom Output:custome:No>0&Yes>1";s:11:"textblock28";s:48:"custome_header|SystemGOD:Send Custom http header";s:14:"staticselect29";s:45:"is_active|SystemGOD:Active:custome:Yes>1&No>0";}\', \'\', \'\');

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `apps_wiki_links` (
  `name` varchar(250) NOT NULL default \'\',
  `tagstart` varchar(250) NOT NULL default \'\',
  `tagend` varchar(250) NOT NULL default \'\',
  `parlset` varchar(250) NOT NULL default \'\',
  `linkexample` varchar(250) NOT NULL default \'\',
  `dbtable` varchar(250) NOT NULL default \'\',
  `namecolumn` varchar(250) NOT NULL default \'\',
  `idcolumn` varchar(250) NOT NULL default \'\',
  `extrasql` varchar(255) NOT NULL default \'\',
  `is_extrasql_loop` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--------------------------------------------------------

INSERT INTO `apps_wiki_links` (`name`, `tagstart`, `tagend`, `parlset`, `linkexample`, `dbtable`, `namecolumn`, `idcolumn`, `extrasql`, `is_extrasql_loop`) VALUES
(\'wikilinks\', \'(+(\', \')+)\', \'\', \'wiki\', \'apps_wiki_text\', \'title\', \'id\', \'\', 0);

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_languages` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `sys_name` varchar(255) NOT NULL,
  `short_name` varchar(9) NOT NULL,
  `dir` varchar(9) NOT NULL,
  `align` varchar(10) NOT NULL,
  `is_default` int(1) NOT NULL default \'0\',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--------------------------------------------------------

INSERT INTO `aiki_languages` (`id`, `name`, `sys_name`, `short_name`, `dir`, `align`, `is_default`) VALUES
(1, \'Arabic\', \'arabic\', \'ar\', \'rtl\', \'right\', 0),
(2, \'English\', \'english\', \'en\', \'ltr\', \'left\', 1),
(3, \'German\', \'german\', \'de\', \'ltr\', \'left\', 0);

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_redirects` (
  `url` varchar(255) NOT NULL,
  `redirect` varchar(255) NOT NULL,
  `hits` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_sites` (
  `site_id` int(11) NOT NULL auto_increment,
  `site_name` varchar(255) NOT NULL,
  `site_shortcut` varchar(255) NOT NULL,
  `is_active` int(1) NOT NULL,
  `if_closed_output` text NOT NULL,
  PRIMARY KEY  (`site_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--------------------------------------------------------


INSERT INTO `aiki_sites` (`site_id`, `site_name`, `site_shortcut`, `is_active`, `if_closed_output`) VALUES
(1, \'default\', \'default\', 1, \'\');

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `apps_wiki_templates` (
  `id` int(11) NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `template_input` text NOT NULL,
  `template_output` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `template_name` (`template_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_users` (
  `userid` int(9) unsigned NOT NULL auto_increment,
  `username` varchar(100) NOT NULL default \'\',
  `full_name` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `sex` varchar(25) NOT NULL,
  `job` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL default \'\',
  `usergroup` int(10) NOT NULL default \'0\',
  `email` varchar(100) NOT NULL default \'\',
  `avatar` varchar(255) NOT NULL,
  `homepage` varchar(100) NOT NULL default \'\',
  `first_ip` varchar(40) NOT NULL default \'0\',
  `first_login` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `last_ip` varchar(40) NOT NULL,
  `user_permissions` text NOT NULL,
  `maillist` int(1) NOT NULL,
  `logins_number` int(11) NOT NULL,
  `randkey` varchar(255) NOT NULL,
  `is_active` int(5) NOT NULL,
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--------------------------------------------------------

INSERT INTO `aiki_users` (`userid`, `username`, `full_name`, `country`, `sex`, `job`, `password`, `usergroup`, `email`, `avatar`, `homepage`, `first_ip`, `first_login`, `last_login`, `last_ip`, `user_permissions`, `maillist`, `logins_number`, `randkey`, `is_active`) VALUES
(1, \'guest\', \'guest\', \'\', \'\', \'\', \'\', 3, \'\', \'\', \'\', \'\', \'0000-00-00 00:00:00\', \'0000-00-00 00:00:00\', \'\', \'\', 0, 0, \'\', 0),
(2, \''.$username.'\', \''.$full_name.'\', \'\', \'\', \'\', \''.$admin_password_md5_md5.'\', 1, \''.$email.'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', 0);

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_users_groups` (
  `id` int(3) NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `group_permissions` varchar(255) NOT NULL,
  `group_level` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--------------------------------------------------------

INSERT INTO `aiki_users_groups` (`id`, `app_id`, `name`, `group_permissions`, `group_level`) VALUES
(1, 0, \'System Administrators\', \'SystemGOD\', 1),
(2, 0, \'Modules Administrators\', \'ModulesGOD\', 2),
(3, 0, \'Guests\', \'ViewPublished\', 100),
(4, 0, \'Banned users\', \'ViewPublished\', 101),
(5, 0, \'Normal User\', \'normal\', 4),
(6, 0, \'employees\', \'employees\', 3);

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_users_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `session_start` int(11) NOT NULL,
  `last_hit` int(11) NOT NULL,
  `user_session` varchar(255) NOT NULL,
  `hits` int(11) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  `last_ip` varchar(100) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `widget_name` varchar(128) NOT NULL,
  `widget_site` varchar(255) NOT NULL,
  `widget_target` varchar(128) NOT NULL,
  `widget_type` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL,
  `style_id` varchar(255) NOT NULL,
  `is_father` int(1) NOT NULL,
  `father_widget` int(11) NOT NULL,
  `display_urls` text NOT NULL,
  `kill_urls` text NOT NULL,
  `normal_select` text NOT NULL,
  `authorized_select` text NOT NULL,
  `if_no_results` text NOT NULL,
  `widget` text NOT NULL,
  `css` text NOT NULL,
  `nogui_widget` text NOT NULL,
  `display_in_row_of` int(11) NOT NULL,
  `records_in_page` int(11) NOT NULL,
  `link_example` varchar(255) NOT NULL,
  `pagetitle` text NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `if_authorized` text NOT NULL,
  `permissions` text NOT NULL,
  `remove_container` int(1) NOT NULL,
  `widget_cache_timeout` int(11) NOT NULL,
  `custome_output` int(1) NOT NULL,
  `custome_header` text NOT NULL,
  `is_active` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--------------------------------------------------------

INSERT INTO `aiki_widgets` (`id`, `app_id`, `widget_name`, `widget_site`, `widget_target`, `widget_type`, `display_order`, `style_id`, `is_father`, `father_widget`, `display_urls`, `kill_urls`, `normal_select`, `authorized_select`, `if_no_results`, `widget`, `css`, `nogui_widget`, `display_in_row_of`, `records_in_page`, `link_example`, `pagetitle`, `is_admin`, `if_authorized`, `permissions`, `remove_container`, `widget_cache_timeout`, `custome_output`, `custome_header`, `is_active`) VALUES
(1, 1, \'header\', \'default\', \'body\', \'div\', 1, \'\', \'0\', 6, \'admin\', \'\', \'\', \'\', \'\', \'(#(header:Location: [root]/login|false|301)#)\', \'#header {\r\n    height: 28px;\r\n    background: #eeeeee;\r\n    position: relative;\r\n    border-bottom:1px solid #666666;\r\n    border-top:1px solid #666666;\r\n    text-align:center;\r\n}\r\n\r\n#main-navigation {\r\n    	position: relative;\r\n	float:left;\r\n	line-height:25px;\r\n}\r\n\r\n#main-navigation li{\r\n	float:left;\r\n	line-height:25px;\r\n	margin-right: 10px;\r\n	margin-left: 10px;\r\n}\r\n\r\n#main-navigation li a img{\r\n	margin-top:5px;\r\n}\r\n\r\n#main-navigation .aiki-icon {\r\n	margin-left:-5px;\r\n	margin-right:-10px;\r\n}\r\n\r\n#main-navigation a.active{\r\n	font-weight:bold;\r\n}\r\n\r\n#user-navigation {\r\n    	position: relative;\r\n	float:right;\r\n	line-height:25px;\r\n}\r\n\r\n#user-navigation li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#user-navigation li a, #user-navigation li strong{\r\n	margin-right: 10px;\r\n	margin-left: 10px;\r\n}\r\n\r\n#user-navigation li a img{\r\n	margin-top:5px;\r\n}\r\n\r\n#user-navigation .aiki-icon {\r\n	margin-left:-5px;\r\n	margin-right:-10px;\r\n}\r\n\r\n#user-navigation a.active{\r\n	font-weight:bold;\r\n}\r\n\r\n#tree-menu {\r\n	border-bottom: 1px dashed #d3d7cf;\r\ndisplay:block;\r\nposition:relative;\r\n}\r\n\r\n#tree-menu li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#tree-menu li a{\r\n	margin-right: 5px;\r\n	margin-left: 5px;\r\n}\r\n\r\n#tree-menu li a img{\r\n	margin-top:5px;\r\n	height:12px;\r\n	margin-right:2px;\r\n}\r\n\r\n#widget-tree {\r\n	text-align:left;\r\n}\', \'\', 0, 0, \'\', \'aiki AdminPanel\', 1, "	<ul id=\'main-navigation\' class=\'clearfix\'>\r\n		<li><a href=\'#\' class=\'aiki-icon\' id=\'aiki-icon-button\'><img src=\'[root]/assets/apps/admin/images/aiki-icon.png\' /></a></li>\r\n 		<li><a href=\'#\' id=\'structur_button\'>Structure</a></li>\r\n<li><a href=\'#\' id=\'system_button\'>System</a></li>\r\n	</ul>\r\n\r\n(ajax_a(structur_button;\r\n[\'[root]/index.php?widget=structur_accordion\',\'#ui-layout-west\', \'structur_accordion()\']\r\n)ajax_a)\r\n\r\n(ajax_a(system_button;\r\n[\'[root]/index.php?widget=system_accordion\',\'#ui-layout-west\', \'system_accordion()\']\r\n)ajax_a)\r\n\r\n	<ul id=\'user-navigation\' class=\'clearfix\'>\r\n		<li><strong><a rev=\'#widget-form\' href=\'[root]/admin_tools/edit/17/[userid]\' rel=\'edit_record\'>[username]</a> @ [root]</strong>|</li> \r\n <li><a href=\'[root]/admin_tools/logout\'>Logout</a></li>\r\n	</ul>\r\n\r\n<div id=\'dialog\' title=\'About Aikiframework\'>\r\n	<p>\r\n		<img src=\'[root]/assets/apps/admin/images/logo.png\' />\r\n		<br /><br />\r\n		<h2>aiki framework 1.0.0</h2>\r\n		<br />\r\n		<a href=\'http://www.aikiframework.org\'>http://www.aikiframework.org</a>\r\n		<br /><br />\r\n		<h2>Credits:</h2>	\r\n		Bassel Safadi<br />\r\n	 Jon Phillips<br />\r\n 	Michi Krnac<br />\r\n Ronaldo Barbachano <br />\r\n 	Vera Lobatcheva<br />	\r\n	</p>\r\n</div>", \'SystemGOD\', 0, 0, 0, \'\', 1),
(2, 1, \'search\', \'default\', \'body\', \'div\', 2, \'\', \'0\', 6, \'admin\', \'\', \'\', \'\', \'\', \'\', \'#search {\r\n    	height: 80px;\r\n    	position: relative;\r\n	text-align:left;\r\n}\r\n\r\n#search .logo{\r\n	position:relative;\r\n	float:left;\r\n	margin:5px;\r\n}\r\n\r\n#search form{\r\n	position:relative;\r\n	float:left;\r\n	margin-top:15px;\r\n}\r\n\r\ninput.oneLine{\r\n	border: 1px solid #999999;\r\n	background:url(assets/apps/admin/images/input_bg.png) repeat-x bottom;\r\n    	font-size:12pt;\r\n	padding:2px;\r\n	margin-left:10px;\r\n}\r\n\r\ninput.button{\r\n	background:url(assets/apps/admin/images/input_button.png) no-repeat;\r\n	width:80px;\r\n	height:25px;\r\n    	font-size:10pt;\r\n	font-weight:bold;\r\n	padding:2px;\r\n	margin-left:10px;\r\n        border:0;\r\n}\r\n\r\n#search input.button:hover{\r\n	background:url(assets/apps/admin/images/input_button_active.png) no-repeat;\r\n}\r\n\r\n#content_button {\r\n  position:absolute;\r\n  right:-5px;\r\n  top:5px;\r\n}\', \'\', 0, 0, \'\', \'\', 1, \'<a href="#" class="logo"><img src="[root]/assets/apps/admin/images/logo.png" /></a>\', \'SystemGOD\', 0, 0, 0, \'\', 1),
(3, 1, \'structur_accordion\', \'default\', \'body\', \'div\', 6, \'\', \'0\', 7, \'admin\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', 1, \'<h3><a href="#" id="urls_widgets">Urls & Widgets</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" id="create_new_widget"><img src="[root]/assets/apps/admin/images/icons/layout_add.png" />Create Widget</a></li>\r\n	</ul>\r\n	<div id="widgettree" class="demo"></div>\r\n</div>\r\n\r\n<h3><a href="#" id="database_forms">Databases & Forms</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" id="create_new_table"><img src="[root]/assets/apps/admin/images/icons/database.png" />Create Table</a></li>\r\n		<li><a href="#" id="create_new_form"><img src="[root]/assets/apps/admin/images/icons/application_form.png" />Create Form</a></li>\r\n	</ul>\r\n<div id="databaseformstree" class="demo"></div>\r\n</div>\r\n\', \'SystemGOD\', 0, 0, 0, \'\', 1),
(4, 1, \'widget_accordion\', \'default\', \'body\', \'div\', 0, \'\', \'\', 8, \'admin\', \'\', \'\', \'\', \'\', \'\', \'#breadcrumbs li{\r\n	float:left;	\r\n}\r\n\r\n#breadcrumbs li a{\r\n	float:left;\r\n}\r\n\r\n#breadcrumbs li a img{\r\n	height:12px;\r\n	margin-right:4px;\r\n	top: 5px;\r\n}\r\n\r\n#breadcrumbs li img{\r\n	float:left;\r\n	position: relative; \r\n	top: 8px;\r\n	margin-left:10px;\r\n}\r\n\r\n.codetext {\r\n	margin:0 15px 0 15px;\r\n	color:#555753;\r\n	font-size:80%;\r\n}\r\n\r\n.options-button {\r\n	background:#eeeeee;\r\n	margin:15px 15px 0 15px;\r\n	width:80px;\r\n	height:20px;\r\n	text-align:center;\r\n}\r\n\r\n.options-button a{\r\n	margin:5px;\r\n	color: #1b3b6b;\r\n}\r\n.options-button a:hover {\r\n    	text-decoration: none;\r\n}\r\n\r\n.options {\r\n	border:1px solid #eeeeee;\r\n	margin:0px 15px 0 15px;\r\n	padding:10px;\r\n	color: #1b3b6b;\r\n}\r\n#big_form {\r\n	margin:0px 15px 0 15px;\r\n}\r\ntextarea, input, select {\r\n	border:2px solid #c3c3c3;\r\n	font-family: "Courier New";\r\n	padding:3px;\r\n	color:#555753;\r\n	margin:0 15px 0 15px;\r\n	font-size:120%;\r\n	background:GhostWhite ;\r\n}\r\n\r\n.form-buttons {\r\n	text-align:right;\r\n}\r\n\r\n#widget_container, #normal_select_container, #css_container, #if_authorized_container{\r\nborder: 1px solid black;\r\npadding: 3px;\r\nbackground-color: #F8F8F8\r\n}\r\n\r\n#widget-form h2{\r\nborder-color:#CCCCCC;\r\nborder-style:dotted none;\r\nborder-width:1px 0 0;\r\ndisplay:block;\r\nmargin-top:16px;\r\npadding-bottom:6px;\r\npadding-top:4px;\r\n}\', \'\', 0, 0, \'\', \'\', 1, \'<h3><a></a></h3>\r\n\r\n<div id="widget-form" class="accordeon-content">\r\nYou can start building your cms from the left menu.\r\n</div>\', \'SystemGOD\', 0, 0, 0, \'\', 1),
(5, 1, \'edit_record\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/edit/(.*)/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', 1, \'(#(form:edit:(!(2)!):(!(3)!))#)\', \'SystemGOD\', 0, 0, 1, \'\', 1),
(6, 1, \'ui-layout-north\', \'default\', \'body\', \'div\', 0, \'ui-layout-north\', \'1\', 0, \'admin\', \'\', \'\', \'\', \'\', \'\', \'.ui-layout-pane-north {\r\n/* OVERRIDE "default styles" */\r\npadding: 0 !important;\r\noverflow: hidden !important;\r\n}\', \'\', 0, 0, \'\', \'\', 1, \'\', \'SystemGOD\', 0, 0, 0, \'\', 1),
(7, 1, \'ui-layout-west\', \'default\', \'body\', \'div\', 3, \'ui-layout-west\', \'1\', 0, \'admin\', \'\', \'\', \'\', \'\', \'\', \'	.ui-layout-pane-west {\r\n padding: 0 !important;\r\n		overflow: hidden !important;\r\n	}\', \'\', 0, 0, \'\', \'\', 1, \'\', \'SystemGOD\', 0, 0, 0, \'\', 1),
(8, 1, \'ui-layout-center\', \'default\', \'body\', \'div\', 0, \'ui-layout-center\', \'1\', 0, \'admin\', \'\', \'\', \'\', \'\', \'\', \'	.ui-layout-pane-center {\r\n		padding: 0 !important;\r\n		overflow: hidden !important;\r\n	}\', \'\', 0, 0, \'\', \'\', 1, \'\', \'SystemGOD\', 0, 0, 0, \'\', 1),
(9, 0, \'aikiadmin_login\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'login\', \'\', \'\', \'\', \'\', \'<h2>Sign in to aikiframework Admin-Panel</h2><br/><br/>\r\n<img src="[root]assets/apps/admin/images/logo.png" /><br /><br />\r\n<form method="post">\r\n<div><table border=0>\r\n  <tbody>\r\n    <tr>\r\n      <td>Name:</td>\r\n      <td><input type="text" name="username" dir=""></td>\r\n    </tr>\r\n    <tr>\r\n      <td>Password:</td>\r\n      <td><input type="password" name="password" dir=""></td>\r\n    </tr>\r\n    <tr>\r\n      <td><input type="hidden" name="process" value="login"></td>\r\n      <td><input class="button" type="submit" name="submit" value="Sign in"></td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n</div>\r\n<form>\', \'#aikiadmin_login {\r\n border:1px solid #c3c3c3;\r\n width:400px;\r\nmargin: 200px auto;\r\ntext-align:center;\r\nbackground:GhostWhite ;\r\npadding:30px;\r\n}\r\n\r\n#aikiadmin_login img{\r\nmargin:5px;\r\n}\r\n\r\n#aikiadmin_login div{\r\nwidth: 260px; \r\nmargin: 0 auto;\r\n}\r\n\r\n#aikiadmin_login table{\r\ntext-align:right;\r\nwidth: 100%;\r\n}\', \'\', 0, 0, \'\', \'Login to Aiki-Admin Panel\', 1, \'(#(header:Location: [root]/admin|false|301)#)\', \'SystemGOD\', 0, 0, 0, \'\', 1),
(10, 1, \'system_accordion\', \'default\', \'body\', \'div\', 6, \'\', \'0\', 0, \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', 1, "<h3><a href=\"#\" id=\"config\">Config</a></h3>\n\r<div id=\"configtree\" class=\"demo\"></div><h3><a href=\'#\'>Sites</a></h3>\r\n			<div>\r\n			<ul id=\'tree-menu\' class=\'clearfix\'>\r\n 				<li><a href=\'#\' onclick=\'$.tree_reference(\'widgettree\').create({ attributes : { \'class\' : \'site\' }, data: { title : \'New Site\', icon : \'[root]/assets/apps/admin/images/icons/world.png\' } },-1);\'><img src=\'[root]/assets/apps/admin/images/icons/world.png\' />New site</a></li>\r\n\r\n			</ul>\r\n			</div>\r\n<h3><a href=\'#\'>Users & Permission</a></h3>\r\n			<div>\r\n			<ul id=\'tree-menu\' class=\'clearfix\'>\r\n 				<li><a href=\'#\' onclick=\'$.tree_reference(\'widgettree\').create({ attributes : { \'class\' : \'site\' }, data: { title : \'Add Group\', icon : \'[root]/assets/apps/admin/images/icons/group.png\' } },-1);\'><img src=\'[root]/assets/apps/admin/images/icons/group.png\' />New Group</a></li>\r\n\r\n				<li><a href=\'#\' onclick=\'$.tree_reference(\'widgettree\').create({ attributes : { \'class\' : \'url\' }, data: { title : \'New User\', icon : \'[root]/assets/apps/admin/images/icons/user.png\' } },0);\'><img src=\'[root]/assets/apps/admin/images/icons/user.png\' />New User</a></li>\r\n			</ul>\r\n			<div id=\'widgettree\' class=\'demo\'></div>\r\n		\r\n			</div>\r\n", \'SystemGOD\', 0, 0, 0, \'\', 1),
(11, 1, \'new_record\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/new/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', 1, \'(#(form:add:(!(2)!))#)\', \'SystemGOD\', 0, 0, 1, \'\', 1),
(12, 1, \'confirmations\', \'default\', \'body\', \'div\', 0, \'\', \'\', 0, \'admin\', \'\', \'\', \'\', \'\', \'<div id="deletewidgetdialog" title="Delete widget">\r\n	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This widget will be permanently deleted and cannot be recovered. Are you sure?</p>\r\n</div>\', \'\', \'\', 0, 0, \'\', \'\', 0, \'\', \'\', 0, 0, 0, \'\', 1),
(13, 1, \'delete_record\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/delete/(.*)/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', 1, \'(#(form:delete:(!(2)!):(!(3)!))#)\', \'SystemGOD\', 0, 0, 1, \'\', 1),
(14, 0, \'aiki_home\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'homepage\', \'\', \'\', \'\', \'\', \'<h2>Welcome to aikiframework</h2><br/><br/>\r\n\r\n<img src="[root]assets/apps/admin/images/logo.png" />\r\n\r\n<br/><br/>\r\n\r\nYou have successfully installed your aiki..\r\n\r\n<br/><br/>\r\n\r\nPlease go to <a href="admin">admin panel</a> to start creating your own cms and to change this default page \r\n\r\n<br/><br/>\r\n\r\nfor documentation please visit <a target="_blank" href="http://www.aikiframework.org">aikiframework.org</a>\', \'#aiki_home {\r\n\r\n border:1px solid #c3c3c3;\r\n\r\n width:400px;\r\n\r\nmargin: 200px auto;\r\n\r\ntext-align:center;\r\n\r\nbackground:GhostWhite ;\r\n\r\npadding:30px;\r\n\r\n}\r\n\r\n\r\n\r\n#aiki_home img{\r\n\r\nmargin:5px;\r\n\r\n}\r\n\r\n\r\n\r\n#aiki_home div{\r\n\r\nwidth: 260px; \r\n\r\nmargin: 0 auto;\r\n\r\n}\r\n\r\n\r\n\r\n#aiki_home table{\r\n\r\ntext-align:right;\r\n\r\nwidth: 100%;\r\n\r\n}\', \'\', 0, 0, \'\', \'Aikiframework\', 0, \'\', \'\', 0, 0, 0, \'\', 1),
(15, 1, \'edit_array\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/array/(.*)/(.*)/(.*)/(.*)/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', 1, \'(#(array:edit:(!(2)!):(!(3)!):(!(4)!):(!(5)!):(!(6)!))#)\', \'SystemGOD\', 0, 0, 1, \'\', 1),
(16, 1, \'auto_generate\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/auto_generate/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', 1, \'(#(form:auto_generate:(!(2)!))#)\', \'SystemGOD\', 0, 0, 1, \'\', 1),
(17, 1, \'admin_javascript\', \'default\', \'header\', \'0\', 3, \'\', \'0\', 0, \'admin\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', 1, \'<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jquery.layout.min.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/css.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jstree/tree_component.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jstree/sarissa.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jstree/sarissa_ieemu_xpath.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jquery.xslt.js"></script>\r\n<script type="text/javascript" src="[root]assets/apps/admin/control_panel.js"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]/assets/javascript/jquery/plugins/jstree/tree_component.css" />\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/jquery-ui-1.7.2.custom.min.js"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]/assets/javascript/jquery/css/smoothness/jquery-ui-1.7.1.custom.css" />\r\n<script src="[root]/assets/javascript/codemirror/js/codemirror.js" type="text/javascript"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]/assets/javascript/codemirror/css/docs.css"/>\r\n<style type="text/css">\r\n.CodeMirror-line-numbers {width: 2.2em;color: #aaa;background-color: #eee;text-align: right;padding-right: .3em;font-size: 10pt;font-family: monospace;padding-top: .4em;}\r\n</style>\', \'SystemGOD\', 0, 0, 0, \'\', 1),
(18, 0, \'global_javascript\', \'default\', \'header\', \'0\', 2, \'\', \'\', 0, \'*\', \'\', \'\', \'\', \'\', "<script type=\'text/javascript\'  src=\'[root]/assets/javascript/jquery/jquery-1.4.2.min.js\'></script>\r\n<script type=\'text/javascript\' src=\'[root]/assets/javascript/jquery/plugins/jquery.form.js\'></script>\r\n<script type=\'text/javascript\' src=\'[root]/assets/javascript/aiki.js\'></script>", \'\', \'\', 0, 0, \'\', \'\', 0, \'\', \'\', 0, 0, 0, \'\', 1),
(19, 0, \'style.css\', \'default\', \'body\', \'0\', 0, \'\', \'0\', 0, \'globals/admin_style.css\', \'\', \'\n\', \'\', \'\', \'html, body, div, span, applet, object, p, a, em, img, strong, ol, ul, li, dl, dd, dt, label, h1, h2, h3, h4, h5, h6{\n  margin: 0;\n    padding: 0;\n    border: 0;\n    outline: 0;\n    font-style: inherit;\n    font-size: 100%;\n    font-family: inherit;\n    vertical-align: baseline;\n    background: transparent;\n}\n:focus{\noutline: 0;\n}\nul{\n list-style: none;\n}\nol{\nlist-style-position: inside; \n}\nbody, html{\n   padding: 0;\n    margin: 0;\n    font-family: "Bitstream Vera Sans", Tahoma, sans-serif;\n    font-size: 9pt;\n    color: #000;\n    height: 100%;\n    background: #FFF;\n    line-height: 1;\n}\n* html #container{\nheight: 100%;\n}\nimg{\n    border: 0;\n}\na:link, a:visited{\n outline: none;\n    text-decoration: none;\n    color: #1B3B6B;\n}\na:hover, a:active{\n    text-decoration: underline;\n    color: #1B3B6B;\n}\n.clear{\n    clear: both;\n    font-size: 0.3pt;\n}\n.clearfix:after{\n  content: ".";\n  display: block;\n  clear: both;\n  visibility: hidden;\n  line-height: 0;\n  height: 0;\n}\n.clearfix{\n  display: inline-block;\n}\n* html .clearfix{\nheight: 1\n}\n.tree-context{\nz-index:999; \n}\n\n\n.edit_button{\n-moz-border-radius:2px 2px 2px 2px;\nbackground:none repeat scroll 0 0 #F57900;\nborder:0 none;\nbottom:10px;\ncolor:#FFFFFF;\nfont-weight:bold;\nposition:fixed;\nright:10px;\n}\n\', \'\', \'\', 0, 0, \'\', \'\', 0, \'\n\', \'\', 0, 0, 1, \'Content-type: text/css\', 1),
(20, 0, \'admin_css\', \'default\', \'header\', \'0\', 1, \'\', \'0\', 0, \'admin\', \'\', \'\', \'\', \'\', \'<link rel="stylesheet" type="text/css" href="[root]/globals/admin_style.css" />\', \'\', \'\', 0, 0, \'\', \'\', 0, \'\', \'\', 0, 0, 0, \'\', 1),
(21, 0, \'logout\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/logout\', \'\', \'\', \'\', \'\', \'(#(header:Location: [root]/admin)#)\', \'\', \'\', 0, 0, \'\', \'Aikiframework\', 1, \'<php $aiki->membership->LogOut(); php> <p>Logging out please wait...</p> <meta http-equiv="refresh" content="2;url=[root]/admin"> <p><a href="[root]/admin">click here if your browser does not support redirect</a></p>\', \'SystemGOD\', 0, 0, 0, \'\', 1);

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `apps_photo_archive` (
  `id` int(11) NOT NULL auto_increment,
  `categorie` int(11) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `colored_label` varchar(10) NOT NULL,
  `rating` float NOT NULL,
  `ratings_num` int(11) NOT NULL,
  `upload_file_name` varchar(255) NOT NULL,
  `upload_file_size` varchar(255) NOT NULL,
  `checksum_sha1` varchar(255) NOT NULL,
  `checksum_md5` varchar(255) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `alt_text` varchar(255) NOT NULL,
  `keywords` text NOT NULL,
  `date_of_shot` int(11) NOT NULL,
  `copyright` text NOT NULL,
  `description` text NOT NULL,
  `current_owner` varchar(255) NOT NULL,
  `photographer` varchar(255) NOT NULL,
  `event` varchar(255) NOT NULL,
  `event_date` int(11) NOT NULL,
  `published_by` varchar(255) NOT NULL,
  `right_term` varchar(255) NOT NULL,
  `people_in_photo` varchar(255) NOT NULL,
  `scene` varchar(255) NOT NULL,
  `full_path` varchar(255) NOT NULL default \'assets/uploads/\',
  `resolution` varchar(255) NOT NULL,
  `depth` varchar(255) NOT NULL,
  `color_space` varchar(255) NOT NULL,
  `compression` varchar(255) NOT NULL,
  `source_url` varchar(255) NOT NULL,
  `source_device` varchar(255) NOT NULL,
  `exif_data` text NOT NULL,
  `capture_date` int(11) NOT NULL,
  `aperture` varchar(255) NOT NULL,
  `shutter_speed` varchar(255) NOT NULL,
  `focal_length` varchar(255) NOT NULL,
  `iso_speed` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `altitude` varchar(255) NOT NULL,
  `available_sizes` varchar(255) NOT NULL,
  `watermark` varchar(255) NOT NULL,
  `no_watermark_under` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `is_missing` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `apps_wiki_text` (
  `id` int(11) NOT NULL auto_increment,
  `cat` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `infobox` text NOT NULL,
  `keywords` text NOT NULL,
  `richable` int(1) NOT NULL default \'1\',
  `edit_by` text NOT NULL,
  `insert_date` int(11) NOT NULL,
  `insert_by` varchar(255) NOT NULL,
  `hits_counter` int(11) NOT NULL,
  `is_editable` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;';

	$sql = explode('--------------------------------------------------------', $sql);

	foreach($sql as $sql_statment)
	{
	 mysql_query($sql_statment);
	}


	echo '<h1>Great success '.$full_name.'! aiki framework installed</h1>';
	echo '<a href="admin/">Click here to login and start creating a cms</a>';
	echo '<br />';
	echo 'Username: '.$username;
	echo '<br />';
	echo 'Password: '.$admin_password;

	if ($email){
		
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		$headers .= "From: noreply@aikiframework.org\r\n";

		$message = "Hello $full_name <br /> your new aiki installation is ready to be used <br />
			Go to: ".$pageURL."admin <br />
Username: $username <br />
Password: $admin_password
";

		mail($email,'Your new aiki installation',$message,$headers);
	}


}
echo '</div>
</div>
</body>
</html>';
?>
