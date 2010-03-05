<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
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
	type="text" name="db_encoding" value="utf8" /></fieldset>

<button type="submit">Next..</button>
</form>';

}else{

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

$config["widget_cache"] = false;
$config["widget_cache_dir"] = "widgets";

$config["css_cache"] = true;
$config["css_cache_timeout"] = 24;
$config["css_cache_file"] = "";

$config["javascript_cache"] = true;
$config["javascript_cache_timeout"] = 24;
$config["javascript_cache_file"] = "";

$config["html_cache"] = false; //full path to cache directory
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
	@mysql_select_db($_POST['db_name']) or die ("Unable to select database");


	$config_file_name = "config.php";
	$FileHandle = fopen($config_file_name, 'w') or die("Sorry, but I don't have write permissions to create config file");
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
	$FileHandle = fopen($htaccess_file_name, 'w') or die("Sorry, but I don't have write permissions to create .htaccess file");
	fwrite($FileHandle, $htaccess_file);
	fclose($FileHandle);



	$sql = '

CREATE TABLE IF NOT EXISTS `aiki_config` (
  `config_id` int(11) unsigned NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `config_type` varchar(255) default NULL,
  `config_data` mediumtext,
  PRIMARY KEY  (`config_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--------------------------------------------------------

INSERT INTO `aiki_config` (`config_id`, `app_id`, `config_type`, `config_data`) VALUES
(1, 0, \'global_settings\', \'a:9:{s:4:"site";s:7:"default";s:3:"url";s:'.$page_strlen.':"'.$pageURL.'";s:13:"cookie_domain";s:0:"";s:13:"default_chmod";s:4:"0777";s:11:"pretty_urls";s:1:"1";s:16:"default_language";s:7:"english";s:19:"default_time_format";s:9:"d - m - Y";s:8:"site_dir";s:3:"ltr";s:19:"language_short_name";s:2:"en";}\'),
(2, 0, \'database_settings\', \'a:6:{s:7:"db_type";s:5:"mysql";s:10:"disk_cashe";s:1:"1";s:13:"cache_timeout";s:2:"24";s:9:"cache_dir";s:5:"cache";s:13:"cache_queries";s:1:"1";s:16:"charset_encoding";s:4:"utf8";}\'),
(3, 0, \'paths_settings\', \'a:1:{s:10:"top_folder";s:'.$system_folder_strlen.':"'.$system_folder.'";}\'),
(4, 0, \'images_settings\', \'a:4:{s:7:"max_res";s:3:"650";s:20:"default_photo_module";s:18:"apps_photo_archive";s:23:"store_native_extensions";s:4:"true";s:13:"new_extension";s:5:".aiki";}\'),
(5, 0, \'admin_settings\', \'a:1:{s:17:"show_edit_widgets";s:1:"0";}\');

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_css` (
  `id` int(11) NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `css_name` varchar(255) NOT NULL,
  `css_group` varchar(255) NOT NULL,
  `css_folder` varchar(128) NOT NULL,
  `father_module` varchar(255) NOT NULL,
  `style_sheet` text NOT NULL,
  `if_ie` text NOT NULL,
  `media` varchar(255) NOT NULL,
  `is_active` int(1) NOT NULL default \'1\',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--------------------------------------------------------

INSERT INTO `aiki_css` (`id`, `app_id`, `css_name`, `css_group`, `css_folder`, `father_module`, `style_sheet`, `if_ie`, `media`, `is_active`) VALUES
(1, 0, \'html, body, div, span, applet, object, p, a, em, img, strong, ol, ul, li, dl, dd, dt, label, h1, h2, h3, h4, h5, h6\', \'default\', \'\', \'\', \'  margin: 0;\r\n    padding: 0;\r\n    border: 0;\r\n    outline: 0;\r\n    font-style: inherit;\r\n    font-size: 100%;\r\n    font-family: inherit;\r\n    vertical-align: baseline;\r\n    background: transparent;\', \'\', \'\', 1),
(2, 0, \':focus\', \'default\', \'\', \'\', \'outline: 0;\', \'\', \'\', 1),
(3, 0, \'ul\', \'default\', \'\', \'\', \' list-style: none;\', \'\', \'\', 1),
(4, 0, \'ol\', \'default\', \'\', \'\', \'list-style-position: inside; \', \'\', \'\', 1),
(5, 0, \'body, html\', \'default\', \'\', \'\', \'   padding: 0;\r\n    margin: 0;\r\n    font-family: "Bitstream Vera Sans", Tahoma, sans-serif;\r\n    font-size: 9pt;\r\n    color: #000;\r\n    height: 100%;\r\n    background: #FFF;\r\n    line-height: 1;\', \'\', \'\', 1),
(6, 0, \'* html #container\', \'default\', \'\', \'\', \'height: 100%;\', \'\', \'\', 1),
(7, 0, \'img\', \'default\', \'\', \'\', \'    border: 0;\', \'\', \'\', 1),
(8, 0, \'a:link, a:visited\', \'default\', \'\', \'\', \' outline: none;\r\n    text-decoration: none;\r\n    color: #1b3b6b;\', \'\', \'\', 1),
(9, 0, \'a:hover, a:active\', \'default\', \'\', \'\', \'    text-decoration: underline;\', \'\', \'\', 1),
(10, 0, \'.clear\', \'default\', \'\', \'\', \'    clear: both;\r\n    font-size: 0.3pt;\', \'\', \'\', 1),
(11, 0, \'.clearfix:after\', \'default\', \'\', \'\', \'	content: ".";\r\n	display: block;\r\n	clear: both;\r\n	visibility: hidden;\r\n	line-height: 0;\r\n	height: 0;\', \'\', \'\', 1),
(12, 0, \'.clearfix\', \'default\', \'\', \'\', \'	display: inline-block;\', \'\', \'\', 1),
(13, 0, \'* html .clearfix\', \'default\', \'\', \'\', \'height: 1\', \'\', \'\', 1),
(14, 0, \'.tree-context\', \'default\', \'\', \'\', \'z-index:999; \', \'\', \'\', 1);

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
(3, 0, \'\', \'\', \'\', \'aiki_css\', \'aiki_css\', \'a:11:{s:9:"tablename";s:8:"aiki_css";s:4:"pkey";s:2:"id";s:10:"textinput2";s:23:"app_id|SystemGOD:app id";s:10:"textinput3";s:27:"css_name|SystemGOD:css name";s:10:"textinput4";s:29:"css_group|SystemGOD:css group";s:10:"textinput5";s:31:"css_folder|SystemGOD:css folder";s:10:"textinput6";s:37:"father_module|SystemGOD:father module";s:10:"textblock7";s:33:"style_sheet|SystemGOD:style sheet";s:10:"textblock8";s:21:"if_ie|SystemGOD:if ie";s:10:"textinput9";s:21:"media|SystemGOD:media";s:11:"textinput10";s:29:"is_active|SystemGOD:is active";}\', \'\', \'\'),
(4, 0, \'\', \'\', \'\', \'aiki_dictionary\', \'aiki_dictionary\', \'a:7:{s:9:"tablename";s:15:"aiki_dictionary";s:4:"pkey";s:7:"term_id";s:10:"textinput1";s:23:"app_id|SystemGOD:app id";s:10:"textinput2";s:31:"short_term|SystemGOD:short term";s:10:"textblock3";s:35:"lang_english|SystemGOD:lang english";s:10:"textblock4";s:33:"lang_arabic|SystemGOD:lang arabic";s:10:"textblock5";s:33:"lang_german|SystemGOD:lang german";}\', \'\', \'\'),
(6, 0, \'\', \'\', \'\', \'aiki_forms\', \'aiki_forms\', \'a:11:{s:9:"tablename";s:10:"aiki_forms";s:4:"pkey";s:2:"id";s:10:"textinput1";s:23:"app_id|SystemGOD:app id";s:10:"textinput2";s:33:"form_method|SystemGOD:form method";s:10:"textinput3";s:33:"form_action|SystemGOD:form action";s:10:"textinput4";s:27:"form_dir|SystemGOD:form dir";s:10:"textinput5";s:31:"form_table|SystemGOD:form table";s:10:"textinput6";s:29:"form_name|SystemGOD:form name";s:10:"textblock7";s:31:"form_array|SystemGOD:form array";s:10:"textblock8";s:29:"form_html|SystemGOD:form html";s:10:"textblock9";s:31:"form_query|SystemGOD:form query";}\', \'\', \'\'),
(8, 0, \'\', \'\', \'\', \'aiki_javascript\', \'aiki_javascript\', \'a:10:{s:9:"tablename";s:15:"aiki_javascript";s:4:"pkey";s:2:"id";s:10:"textinput1";s:23:"app_id|SystemGOD:app id";s:10:"textinput2";s:33:"script_name|SystemGOD:script name";s:10:"textinput3";s:35:"script_group|SystemGOD:script group";s:10:"textinput4";s:37:"script_folder|SystemGOD:script folder";s:10:"textblock5";s:23:"script|SystemGOD:script";s:10:"textinput6";s:23:"father|SystemGOD:father";s:10:"textinput7";s:31:"global_use|SystemGOD:global use";s:10:"textinput8";s:29:"is_active|SystemGOD:is active";}\', \'\', \'\'),
(9, 0, \'\', \'\', \'\', \'aiki_languages\', \'aiki_languages\', \'a:8:{s:9:"tablename";s:14:"aiki_languages";s:4:"pkey";s:2:"id";s:10:"textinput1";s:19:"name|SystemGOD:name";s:10:"textinput2";s:27:"sys_name|SystemGOD:sys name";s:10:"textinput3";s:31:"short_name|SystemGOD:short name";s:10:"textinput4";s:17:"dir|SystemGOD:dir";s:10:"textinput5";s:21:"align|SystemGOD:align";s:10:"textinput6";s:31:"is_default|SystemGOD:is default";}\', \'\', \'\'),
(12, 0, \'\', \'\', \'\', \'aiki_redirects\', \'aiki_redirects\', \'a:4:{s:9:"tablename";s:14:"aiki_redirects";s:10:"textinput1";s:17:"url|SystemGOD:url";s:10:"textinput2";s:27:"redirect|SystemGOD:redirect";s:10:"textinput3";s:19:"hits|SystemGOD:hits";}\', \'\', \'\'),
(13, 0, \'\', \'\', \'\', \'aiki_sites\', \'aiki_sites\', \'a:6:{s:9:"tablename";s:10:"aiki_sites";s:4:"pkey";s:7:"site_id";s:10:"textinput1";s:29:"site_name|SystemGOD:site name";s:10:"textinput2";s:37:"site_shortcut|SystemGOD:site shortcut";s:10:"textinput3";s:29:"is_active|SystemGOD:is active";s:10:"textblock4";s:43:"if_closed_output|SystemGOD:if closed output";}\', \'\', \'\'),
(15, 0, \'\', \'\', \'\', \'aiki_template\', \'aiki_template\', \'a:6:{s:9:"tablename";s:13:"aiki_template";s:4:"pkey";s:2:"id";s:10:"textinput1";s:23:"app_id|SystemGOD:app id";s:10:"textinput2";s:37:"template_name|SystemGOD:template name";s:10:"textblock3";s:39:"template_input|SystemGOD:template input";s:10:"textblock4";s:41:"template_output|SystemGOD:template output";}\', \'\', \'\'),
(16, 0, \'\', \'\', \'\', \'aiki_urls\', \'aiki_urls\', \'a:5:{s:9:"tablename";s:9:"aiki_urls";s:4:"pkey";s:2:"id";s:10:"textinput1";s:17:"url|SystemGOD:url";s:10:"textinput2";s:29:"cacheable|SystemGOD:cacheable";s:10:"textinput3";s:19:"site|SystemGOD:site";}\', \'\', \'\'),
(17, 0, \'\', \'\', \'\', \'aiki_users\', \'aiki_users\', \'a:21:{s:9:"tablename";s:10:"aiki_users";s:4:"pkey";s:6:"userid";s:10:"textinput2";s:27:"username|SystemGOD:username";s:10:"textinput3";s:29:"full_name|SystemGOD:full name";s:10:"textinput4";s:25:"country|SystemGOD:country";s:10:"textinput5";s:17:"sex|SystemGOD:sex";s:10:"textinput6";s:17:"job|SystemGOD:job";s:9:"password7";s:44:"password|SystemGOD:password:password:md5|md5";s:10:"textinput8";s:29:"usergroup|SystemGOD:usergroup";s:10:"textinput9";s:21:"email|SystemGOD:email";s:11:"textinput10";s:23:"avatar|SystemGOD:avatar";s:11:"textinput11";s:27:"homepage|SystemGOD:homepage";s:11:"textinput12";s:27:"first_ip|SystemGOD:first ip";s:8:"hidden13";s:33:"first_login|SystemGOD:first login";s:8:"hidden14";s:31:"last_login|SystemGOD:last login";s:11:"textinput15";s:25:"last_ip|SystemGOD:last ip";s:11:"textblock16";s:43:"user_permissions|SystemGOD:user permissions";s:11:"textinput17";s:27:"maillist|SystemGOD:maillist";s:11:"textinput18";s:37:"logins_number|SystemGOD:logins number";s:11:"textinput19";s:25:"randkey|SystemGOD:randkey";s:11:"textinput20";s:29:"is_active|SystemGOD:is active";}\', \'\', \'\'),
(18, 0, \'\', \'\', \'\', \'aiki_users_groups\', \'aiki_users_groups\', \'a:6:{s:9:"tablename";s:17:"aiki_users_groups";s:4:"pkey";s:2:"id";s:10:"textinput1";s:23:"app_id|SystemGOD:app id";s:10:"textinput2";s:19:"name|SystemGOD:name";s:10:"textinput3";s:45:"group_permissions|SystemGOD:group permissions";s:10:"textinput4";s:33:"group_level|SystemGOD:group level";}\', \'\', \'\'),
(20, 0, \'\', \'\', \'\', \'aiki_widgets\', \'aiki_widgets\', \'a:33:{s:9:"tablename";s:12:"aiki_widgets";s:4:"pkey";s:2:"id";s:10:"textinput3";s:33:"widget_name|SystemGOD:widget name";s:10:"textinput4";s:33:"widget_site|SystemGOD:widget site";s:13:"staticselect5";s:69:"widget_target|SystemGOD:widget target:custome:body>body&header>header";s:13:"staticselect6";s:220:"widget_type|SystemGOD:widget type:custome:div>div&none>0&span>span&paragraph>p&link>a&---html 5--->0&header>header&nav>nav&article>article&aside>aside&figure>figure&footer>footer&section>section&address>address&abbr>abbr";s:10:"textinput7";s:37:"display_order|SystemGOD:display order";s:10:"textinput8";s:27:"style_id|SystemGOD:style id";s:13:"staticselect9";s:48:"is_father|SystemGOD:is father:custome:No>0&Yes>1";s:11:"selection10";s:65:"father_widget|SystemGOD:father widget:aiki_widgets:id:widget_name";s:11:"textblock11";s:35:"display_urls|SystemGOD:display urls";s:11:"textblock12";s:29:"kill_urls|SystemGOD:kill urls";s:11:"textblock13";s:37:"normal_select|SystemGOD:normal select";s:11:"textblock14";s:45:"authorized_select|SystemGOD:authorized select";s:11:"textblock15";s:37:"if_no_results|SystemGOD:if no results";s:11:"textblock16";s:23:"widget|SystemGOD:widget";s:11:"textblock17";s:17:"css|SystemGOD:css";s:11:"textblock18";s:35:"nogui_widget|SystemGOD:nogui widget";s:11:"textinput19";s:45:"display_in_row_of|SystemGOD:display in row of";s:11:"textinput20";s:41:"records_in_page|SystemGOD:records in page";s:11:"textinput21";s:35:"link_example|SystemGOD:link example";s:11:"textinput23";s:45:"dynamic_pagetitle|SystemGOD:dynamic pagetitle";s:11:"textblock24";s:29:"pagetitle|SystemGOD:pagetitle";s:11:"textblock25";s:43:"output_modifiers|SystemGOD:output modifiers";s:14:"staticselect26";s:64:"is_admin|SystemGOD:Require special permission:custome:No>0&Yes>1";s:11:"textblock27";s:37:"if_authorized|SystemGOD:if authorized";s:11:"textblock28";s:33:"permissions|SystemGOD:permissions";s:11:"textinput29";s:43:"remove_container|SystemGOD:remove container";s:11:"textinput31";s:51:"widget_cache_timeout|SystemGOD:widget cache timeout";s:14:"staticselect32";s:58:"custome_output|SystemGOD:custome output:custome:No>0&Yes>1";s:11:"textblock33";s:39:"custome_header|SystemGOD:custome header";s:11:"selection34";s:62:"javascript|SystemGOD:javascript:aiki_javascript:id:script_name";s:14:"staticselect35";s:48:"is_active|SystemGOD:is active:custome:Yes>1&No>0";}\', \'\', \'\');

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_internal_links` (
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

INSERT INTO `aiki_internal_links` (`name`, `tagstart`, `tagend`, `parlset`, `linkexample`, `dbtable`, `namecolumn`, `idcolumn`, `extrasql`, `is_extrasql_loop`) VALUES
(\'wikilinks\', \'(+(\', \')+)\', \'\', \'wiki\', \'apps_wiki_text\', \'title\', \'id\', \'\', 0);

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_javascript` (
  `id` int(11) NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `script_name` varchar(255) NOT NULL,
  `script_group` varchar(128) NOT NULL,
  `script_folder` varchar(255) NOT NULL,
  `script` text NOT NULL,
  `father` int(11) NOT NULL,
  `global_use` int(1) NOT NULL,
  `is_active` int(1) NOT NULL default \'1\',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--------------------------------------------------------

INSERT INTO `aiki_javascript` (`id`, `app_id`, `script_name`, `script_group`, `script_folder`, `script`, `father`, `global_use`, `is_active`) VALUES
(1, 0, \'jQuery\', \'aikiadmin\', \'\', \'<script type="text/javascript" src="[root]/assets/javascript/jquery/jquery-1.4.2.min.js"></script>\', 0, 1, 1),
(2, 0, \'admin_panel\', \'aikiadmin\', \'\', \'<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jquery.layout.min.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jquery.form.js"></script> \r\n<script type="text/javascript" src="[root]assets/javascript/jquery/plugins/css.js"></script>\r\n<script type="text/javascript" src="[root]assets/javascript/jquery/plugins/jstree/tree_component.js"></script>\r\n<script type="text/javascript" src="[root]assets/javascript/jquery/plugins/jstree/sarissa.js"></script>\r\n<script type="text/javascript" src="[root]assets/javascript/jquery/plugins/jstree/sarissa_ieemu_xpath.js"></script>\r\n<script type="text/javascript" src="[root]assets/javascript/jquery/plugins/jquery.xslt.js"></script>\r\n<script type="text/javascript" src="[root]assets/apps/admin/control_panel.js"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]assets/javascript/jquery/plugins/jstree/tree_component.css" />\r\n<script type="text/javascript" src="[root]assets/javascript/jquery/jquery-ui-1.7.2.custom.min.js"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]assets/javascript/jquery/css/smoothness/jquery-ui-1.7.1.custom.css" />\r\n   <script src="[root]/assets/javascript/codemirror/js/codemirror.js" type="text/javascript"></script>\r\n    <link rel="stylesheet" type="text/css" href="[root]/assets/javascript/codemirror/css/docs.css"/>\r\n     <style type="text/css">\r\n      .CodeMirror-line-numbers {\r\n        width: 2.2em;\r\n        color: #aaa;\r\n        background-color: #eee;\r\n        text-align: right;\r\n        padding-right: .3em;\r\n        font-size: 10pt;\r\n        font-family: monospace;\r\n        padding-top: .4em;\r\n      }\r\n    </style>\', 0, 0, 1);

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
(1, \'عربي\', \'arabic\', \'ar\', \'rtl\', \'right\', 0),
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

CREATE TABLE IF NOT EXISTS `aiki_synchronization` (
  `id` int(11) NOT NULL auto_increment,
  `table_name` varchar(255) NOT NULL,
  `record_id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `date_and_time` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_template` (
  `id` int(11) NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `template_input` text NOT NULL,
  `template_output` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `template_name` (`template_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_urls` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL,
  `cacheable` int(1) NOT NULL,
  `site` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--------------------------------------------------------

INSERT INTO `aiki_urls` (`id`, `url`, `cacheable`, `site`) VALUES
(1, \'login\', 0, \'aikiadmin\'),
(2, \'homepage\', 0, \'default\');

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
(2, \'admin\', \'System admin\', \'\', \'male\', \'\', \'c3284d0f94606de1fd2af172aba15bf3\', 1, \'\', \'\', \'\', \'\', \'0000-00-00 00:00:00\', \'2009-10-13 15:39:56\', \'::1\', \'\', 0, 112, \'\', 0);

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
  `id` int(11) NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `widget_name` varchar(128) NOT NULL,
  `widget_site` varchar(255) NOT NULL,
  `widget_target` varchar(128) NOT NULL,
  `widget_type` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL,
  `style_id` varchar(255) NOT NULL,
  `is_father` varchar(3) NOT NULL,
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
  `operators_order` varchar(255) NOT NULL,
  `dynamic_pagetitle` varchar(3) NOT NULL,
  `pagetitle` text NOT NULL,
  `output_modifiers` text NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `if_authorized` text NOT NULL,
  `permissions` text NOT NULL,
  `remove_container` int(11) NOT NULL,
  `edit_in_place` varchar(3) NOT NULL,
  `widget_cache_timeout` int(11) NOT NULL,
  `custome_output` int(11) NOT NULL,
  `custome_header` text NOT NULL,
  `is_active` int(1) NOT NULL,
  `javascript` int(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--------------------------------------------------------

INSERT INTO `aiki_widgets` (`id`, `app_id`, `widget_name`, `widget_site`, `widget_target`, `widget_type`, `display_order`, `style_id`, `is_father`, `father_widget`, `display_urls`, `kill_urls`, `normal_select`, `authorized_select`, `if_no_results`, `widget`, `css`, `nogui_widget`, `display_in_row_of`, `records_in_page`, `link_example`, `operators_order`, `dynamic_pagetitle`, `pagetitle`, `output_modifiers`, `is_admin`, `if_authorized`, `permissions`, `remove_container`, `edit_in_place`, `widget_cache_timeout`, `custome_output`, `custome_header`, `is_active`, `javascript`) VALUES
(1, 1, \'header\', \'default\', \'body\', \'div\', 1, \'\', \'0\', 6, \'admin\', \'\', \'\', \'\', \'\', \'(#(header:Location: [root]/login|false|301)#)\', \'#header {\r\n    height: 28px;\r\n    background: #eeeeee;\r\n    position: relative;\r\n    border-bottom:1px solid #666666;\r\n    border-top:1px solid #666666;\r\n    text-align:center;\r\n}\r\n\r\n#main-navigation {\r\n    	position: relative;\r\n	float:left;\r\n	line-height:25px;\r\n}\r\n\r\n#main-navigation li{\r\n	float:left;\r\n	line-height:25px;\r\n	margin-right: 10px;\r\n	margin-left: 10px;\r\n}\r\n\r\n#main-navigation li a img{\r\n	margin-top:5px;\r\n}\r\n\r\n#main-navigation .aiki-icon {\r\n	margin-left:-5px;\r\n	margin-right:-10px;\r\n}\r\n\r\n#main-navigation a.active{\r\n	font-weight:bold;\r\n}\r\n\r\n#user-navigation {\r\n    	position: relative;\r\n	float:right;\r\n	line-height:25px;\r\n}\r\n\r\n#user-navigation li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#user-navigation li a, #user-navigation li strong{\r\n	margin-right: 10px;\r\n	margin-left: 10px;\r\n}\r\n\r\n#user-navigation li a img{\r\n	margin-top:5px;\r\n}\r\n\r\n#user-navigation .aiki-icon {\r\n	margin-left:-5px;\r\n	margin-right:-10px;\r\n}\r\n\r\n#user-navigation a.active{\r\n	font-weight:bold;\r\n}\r\n\', \'\', 0, 0, \'\', \'\', \'\', \'aiki AdminPanel\', \'markup_ajax\', 1, \'	<ul id="main-navigation" class="clearfix">\r\n		<li><a href="#" class="aiki-icon" id="aiki-icon-button"><img src="[root]/assets/apps/admin/images/aiki-icon.png" /></a></li>\r\n 		<li><a href="#" id="structur_button" class="active">Structure</a></li>\r\n		<li><a href="#">Apps</a></li>\r\n		<li><a href="#" id="system_button">System</a></li>\r\n	</ul>\r\n\r\n(ajax_a(structur_button;\r\n[\'\'[root]/index.php?widget=widget_accordion\'\',\'\'#ui-layout-center\'\', \'\'widget_accordion()\'\'];\r\n[\'\'[root]/index.php?widget=structur_accordion\'\',\'\'#ui-layout-west\'\', \'\'structur_accordion()\'\']\r\n)ajax_a)\r\n\r\n(ajax_a(system_button;\r\n[\'\'[root]/language\'\',\'\'#ui-layout-center\'\'];\r\n[\'\'[root]/index.php?widget=system_accordion\'\',\'\'#ui-layout-west\'\', \'\'system_accordion()\'\']\r\n)ajax_a)\r\n\r\n	<ul id="user-navigation" class="clearfix">\r\n		<li><strong>[username] @ [root]</strong>|</li>\r\n 		<li><a href="#widget-form" rel="[root]/admin_tools/edit/17/[userid]" ajax="true">Settings</a>|</li>\r\n		<li><a href="#" class="help-toggler">Help</a>|</li>\r\n		<li><a href="#">Signout</a></li>\r\n	</ul>\r\n\r\n<div id="dialog" title="About your aiki installation">\r\n	<p>\r\n		<img src="[root]/assets/apps/admin/images/logo-aikiframework.png" />\r\n		<br /><br />\r\n		<h2>aiki framework 1.0.0</h2>\r\n		<br />\r\n		<a href="http://www.aikiframework.org">http://www.aikiframework.org</a>\r\n		<br /><br />\r\n		<h2>Credits:</h2>	\r\n		Bassel Safadi (Code)<br />\r\n	 Jon Phillips (Code)<br />\r\n 	Michi Krnac (GUI)<br />\r\n		Vera Lobatcheva (HTML/CSS)<br />	\r\n	</p>\r\n</div>\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 2),
(2, 1, \'search\', \'default\', \'body\', \'div\', 2, \'\', \'0\', 6, \'admin\', \'\', \'\', \'\', \'\', \'\', \'#search {\r\n    	height: 80px;\r\n    	position: relative;\r\n	text-align:left;\r\n}\r\n\r\n#search .logo{\r\n	position:relative;\r\n	float:left;\r\n	margin:5px;\r\n}\r\n\r\n#search form{\r\n	position:relative;\r\n	float:left;\r\n	margin-top:15px;\r\n}\r\n\r\ninput.oneLine{\r\n	border: 1px solid #999999;\r\n	background:url(assets/apps/admin/images/input_bg.png) repeat-x bottom;\r\n    	font-size:12pt;\r\n	padding:2px;\r\n	margin-left:10px;\r\n}\r\n\r\ninput.button{\r\n	background:url(assets/apps/admin/images/input_button.png) no-repeat;\r\n	width:80px;\r\n	height:25px;\r\n    	font-size:10pt;\r\n	font-weight:bold;\r\n	padding:2px;\r\n	margin-left:10px;\r\n        border:0;\r\n}\r\n\r\n#search input.button:hover{\r\n	background:url(assets/apps/admin/images/input_button_active.png) no-repeat;\r\n}\r\n\r\n#content_button {\r\n  position:absolute;\r\n  right:-5px;\r\n  top:5px;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'<a href="#" class="logo"><img src="[root]/assets/apps/admin/images/logo.png" /></a>\r\n<a href="[root]/content" ><img id="content_button" src="[root]/assets/apps/admin/images/content-button.png" /></a>\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(3, 1, \'structur_accordion\', \'default\', \'body\', \'div\', 6, \'\', \'0\', 7, \'admin\', \'\', \'\', \'\', \'\', \'\', \'#tree-menu {\r\n	border-bottom: 1px dashed #d3d7cf;\r\ndisplay:block;\r\nposition:relative;\r\n}\r\n\r\n#tree-menu li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#tree-menu li a{\r\n	margin-right: 5px;\r\n	margin-left: 5px;\r\n}\r\n\r\n#tree-menu li a img{\r\n	margin-top:5px;\r\n	height:12px;\r\n	margin-right:2px;\r\n}\r\n\r\n#widget-tree {\r\n	text-align:left;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'<h3><a href="#" id="urls_widgets">Urls & Widgets</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" id="create_new_url"><img src="[root]/assets/apps/admin/images/icons/link_add.png" />Add URL</a></li>\r\n		<li><a href="#" id="create_new_widget"><img src="[root]/assets/apps/admin/images/icons/layout_add.png" />Create Widget</a></li>\r\n	</ul>\r\n	<div id="widgettree" class="demo"></div>\r\n</div>\r\n\r\n<h3><a href="#" id="database_forms">Databases & Forms</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" id="create_new_table"><img src="[root]/assets/apps/admin/images/icons/database.png" />Create Table</a></li>\r\n		<li><a href="#" id="create_new_form"><img src="[root]/assets/apps/admin/images/icons/application_form.png" />Create Form</a></li>\r\n	</ul>\r\n<div id="databaseformstree" class="demo"></div>\r\n</div>\r\n\r\n<h3><a href="#" id="javascript">Javascript</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" id="create_new_javascript"><img src="[root]/assets/apps/admin/images/icons/page_gear.png" />Add Javascript</a></li>\r\n	</ul>\r\n<div id="javascripttree" class="demo"></div>\r\n</div>\r\n\r\n\r\n<h3><a href="#" id="css">Global CSS</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" id="create_new_css"><img src="[root]/assets/apps/admin/images/icons/page_link.png" />Add CSS</a></li>\r\n	</ul>\r\n<div id="csstree" class="demo"></div>\r\n</div>\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(4, 1, \'widget_accordion\', \'default\', \'body\', \'div\', 0, \'\', \'\', 8, \'admin\', \'\', \'\', \'\', \'\', \'\', \'#breadcrumbs li{\r\n	float:left;	\r\n}\r\n\r\n#breadcrumbs li a{\r\n	float:left;\r\n}\r\n\r\n#breadcrumbs li a img{\r\n	height:12px;\r\n	margin-right:4px;\r\n	top: 5px;\r\n}\r\n\r\n#breadcrumbs li img{\r\n	float:left;\r\n	position: relative; \r\n	top: 8px;\r\n	margin-left:10px;\r\n}\r\n\r\n.codetext {\r\n	margin:0 15px 0 15px;\r\n	color:#555753;\r\n	font-size:80%;\r\n}\r\n\r\n.options-button {\r\n	background:#eeeeee;\r\n	margin:15px 15px 0 15px;\r\n	width:80px;\r\n	height:20px;\r\n	text-align:center;\r\n}\r\n\r\n.options-button a{\r\n	margin:5px;\r\n	color: #1b3b6b;\r\n}\r\n.options-button a:hover {\r\n    	text-decoration: none;\r\n}\r\n\r\n.options {\r\n	border:1px solid #eeeeee;\r\n	margin:0px 15px 0 15px;\r\n	padding:10px;\r\n	color: #1b3b6b;\r\n}\r\n#big_form {\r\n	margin:0px 15px 0 15px;\r\n}\r\ntextarea, input, select {\r\n	border:2px solid #c3c3c3;\r\n	font-family: "Courier New";\r\n	padding:3px;\r\n	color:#555753;\r\n	margin:0 15px 0 15px;\r\n	font-size:120%;\r\n	background:GhostWhite ;\r\n}\r\n\r\n.form-buttons {\r\n	text-align:right;\r\n}\r\n\r\n#widget_container, #normal_select_container, #if_authorized_container{\r\nborder: 1px solid black;\r\npadding: 3px;\r\nbackground-color: #F8F8F8\r\n}\r\n\r\n#widget-form h2{\r\nborder-color:#CCCCCC;\r\nborder-style:dotted none;\r\nborder-width:1px 0 0;\r\ndisplay:block;\r\nmargin-top:16px;\r\npadding-bottom:6px;\r\npadding-top:4px;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'<h3><a href="#">Widgets</a></h3>\r\n\r\n<div id="widget-form" class="accordeon-content">\r\nYou can start building your cms from the left menu.\r\n</div>\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(5, 1, \'edit_record\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/edit/(.*)/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'(#(form:edit:(!(2)!):(!(3)!))#)\', \'SystemGOD\', 0, \'\', 0, 1, \'\', 1, 0),
(6, 1, \'ui-layout-north\', \'default\', \'body\', \'div\', 0, \'ui-layout-north\', \'1\', 0, \'admin\', \'\', \'\', \'\', \'\', \'\', \'.ui-layout-pane-north {\r\n/* OVERRIDE \'\'default styles\'\' */\r\npadding: 0 !important;\r\noverflow: hidden !important;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(7, 1, \'ui-layout-west\', \'default\', \'body\', \'div\', 3, \'ui-layout-west\', \'1\', 0, \'admin\', \'\', \'\', \'\', \'\', \'\', \'	.ui-layout-pane-west {\r\n		/* OVERRIDE \'\'default styles\'\' */\r\n		padding: 0 !important;\r\n		overflow: hidden !important;\r\n	}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(8, 1, \'ui-layout-center\', \'default\', \'body\', \'div\', 0, \'ui-layout-center\', \'1\', 0, \'admin\', \'\', \'\', \'\', \'\', \'\', \'	.ui-layout-pane-center {\r\n		/* OVERRIDE \'\'default styles\'\' */\r\n		padding: 0 !important;\r\n		overflow: hidden !important;\r\n	}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(9, 1, \'aikiadmin_login\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'login\', \'\', \'\', \'\', \'\', \'<h2>Sign in to aikiframework Admin-Panel</h2><br/><br/>\r\n<img src="[root]assets/apps/admin/images/logo.png" /><br /><br />\r\n<form method=\'\'post\'\'>\r\n<div><table border=0>\r\n  <tbody>\r\n    <tr>\r\n      <td>Name:</td>\r\n      <td><input type="text" name="username" dir=""></td>\r\n    </tr>\r\n    <tr>\r\n      <td>Password:</td>\r\n      <td><input type="password" name="password" dir=""></td>\r\n    </tr>\r\n    <tr>\r\n      <td><input type="hidden" name="process" value="login"></td>\r\n      <td><input class="button" type="submit" name="submit" value="Sign in"></td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n</div>\r\n<form>\', \'#aikiadmin_login {\r\n border:1px solid #c3c3c3;\r\n width:400px;\r\nmargin: 200px auto;\r\ntext-align:center;\r\nbackground:GhostWhite ;\r\npadding:30px;\r\n}\r\n\r\n#aikiadmin_login img{\r\nmargin:5px;\r\n}\r\n\r\n#aikiadmin_login div{\r\nwidth: 260px; \r\nmargin: 0 auto;\r\n}\r\n\r\n#aikiadmin_login table{\r\ntext-align:right;\r\nwidth: 100%;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'Login to Aiki-Admin Panel\', \'\', 1, \'(#(header:Location: [root]/admin|false|301)#)\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(10, 1, \'system_accordion\', \'default\', \'body\', \'div\', 6, \'\', \'0\', 0, \'\', \'\', \'\', \'\', \'\', \'\', \'#system_accordion h3.ui-state-active{\r\n	background:url(assets/apps/admin/images/accordeon_active.png) repeat-x bottom;\r\n	text-align:left;\r\n	height:15px;\r\n	padding:5px;\r\n	border-top: 1px solid #999999;\r\n	border-bottom: 1px solid #999999;\r\n}\r\n\r\n#system_accordion h3.ui-state-active a{\r\n	color: #000;\r\n	text-decoration:none;\r\n}\r\n\r\n#system_accordion h3.ui-state-default{\r\n	background:url(assets/apps/admin/images/accordeon_default.png) repeat-x bottom;\r\n	text-align:left;\r\n	height:15px;\r\n	padding:5px;\r\n	border-top: 1px solid #d3d7cf;\r\n}\r\n\r\n#system_accordion h3.ui-state-default a{\r\n	color: #888a85;\r\n	text-decoration:none;\r\n}\r\n\r\n#tree-menu {\r\n	border-bottom: 1px dashed #d3d7cf;\r\ndisplay:block;\r\nposition:relative;\r\n}\r\n\r\n#tree-menu li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#tree-menu li a{\r\n	margin-right: 5px;\r\n	margin-left: 5px;\r\n}\r\n\r\n#tree-menu li a img{\r\n	margin-top:5px;\r\n	height:12px;\r\n	margin-right:2px;\r\n}\r\n\r\n#widget-tree {\r\n	text-align:left;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'<h3><a href="#">Users</a></h3>\r\n			<div>\r\n			<ul id="tree-menu" class="clearfix">\r\n 				<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'site\'\' }, data: { title : \'\'New Site\'\', icon : \'\'[root]/assets/apps/admin/images/icons/group.png\'\' } },-1);"><img src="[root]/assets/apps/admin/images/icons/group.png" />Add Group</a></li>\r\n\r\n				<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'url\'\' }, data: { title : \'\'New URL\'\', icon : \'\'[root]/assets/apps/admin/images/icons/user.png\'\' } },0);"><img src="[root]/assets/apps/admin/images/icons/user.png" />Add User</a></li>\r\n\r\n			</ul>\r\n		\r\n			<div id="widgettree" class="demo"></div>\r\n\r\n\r\n\r\n		\r\n			</div>\r\n		\r\n			<h3><a href="#">Language</a></h3>\r\n			<div>\r\n			<ul id="tree-menu" class="clearfix">\r\n 				<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'site\'\' }, data: { title : \'\'New Site\'\', icon : \'\'[root]/assets/apps/admin/images/icons/world.png\'\' } },-1);"><img src="[root]/assets/apps/admin/images/icons/world.png" />Add Language</a></li>\r\n\r\n			</ul>\r\n			</div>\r\n\r\n			<h3><a href="#">Configuration</a></h3>\r\n			<div>\r\n			<p>\r\n			Nam enim risus, molestie et, porta ac, aliquam ac, risus. Quisque lobortis.\r\n			Phasellus pellentesque purus in massa. Aenean in pede. Phasellus ac libero\r\n			ac tellus pellentesque semper. Sed ac felis. Sed commodo, magna quis\r\n			lacinia ornare, quam ante aliquam nisi, eu iaculis leo purus venenatis dui.\r\n			</p>\r\n		\r\n			</div>\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(11, 1, \'new_record\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/new/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'(#(form:add:(!(2)!))#)\', \'SystemGOD\', 0, \'\', 0, 1, \'\', 1, 0),
(12, 1, \'confirmations\', \'default\', \'body\', \'div\', 0, \'\', \'\', 0, \'admin\', \'\', \'\', \'\', \'\', \'<div id="deletewidgetdialog" title="Delete widget">\r\n	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This widget will be permanently deleted and cannot be recovered. Are you sure?</p>\r\n</div>\', \'\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 0, \'\', \'\', 0, \'\', 0, 0, \'\', 1, 0),
(13, 1, \'delete_record\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/delete/(.*)/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'(#(form:delete:(!(2)!):(!(3)!))#)\', \'SystemGOD\', 0, \'\', 0, 1, \'\', 1, 0),
(14, 1, \'aiki_home\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'homepage\', \'\', \'\', \'\', \'\', \'<h2>Welcome to aikiframework</h2><br/><br/>\r\n<img src="[root]assets/apps/admin/images/logo.png" />\r\n<br/><br/>\r\nYou\'\'ve successfully installed your aiki..\r\n<br/><br/>\r\nPlease go to <a href=\'\'admin\'\'>admin panel</a> to start creating your own cms and to change this default page \r\n<br/><br/>\r\nfor documentation please visit <a target=\'\'_blank\'\' href=\'\'http://www.aikiframework.org\'\'>aikiframework.org</a>\', \'\r\n#aiki_home {\r\n border:1px solid #c3c3c3;\r\n width:400px;\r\nmargin: 200px auto;\r\ntext-align:center;\r\nbackground:GhostWhite ;\r\npadding:30px;\r\n}\r\n\r\n#aiki_home img{\r\nmargin:5px;\r\n}\r\n\r\n#aiki_home div{\r\nwidth: 260px; \r\nmargin: 0 auto;\r\n}\r\n\r\n#aiki_home table{\r\ntext-align:right;\r\nwidth: 100%;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'Aikiframework\', \'\', 0, \'\', \'\', 0, \'\', 0, 0, \'\', 1, 0),
(15, 1, \'edit_array\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/array/(.*)/(.*)/(.*)/(.*)/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'(#(array:edit:(!(2)!):(!(3)!):(!(4)!):(!(5)!):(!(6)!))#)\', \'SystemGOD\', 0, \'\', 0, 1, \'\', 1, 0),
(16, 1, \'auto_generate\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/auto_generate/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'(#(form:auto_generate:(!(2)!))#)\', \'SystemGOD\', 0, \'\', 0, 1, \'\', 1, 0);

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
  `full_path` varchar(255) NOT NULL default \'upload/dsyria/\',
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


	echo '<h1>Great success, aiki framework installed</h1>';
	echo '<a href="admin/">Click here to login and start creating a cms</a>';
	echo '<br />';
	echo 'Username: admin';
	echo '<br />';
	echo 'Password: admin';


}
echo '</div>
</div>
</body>
</html>';
?>