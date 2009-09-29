<?php
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
<p>Hi there, <br /> my name is aiki installer and I will guide you throw this ONE STEP installer
<br />
before we start doing the hard work ( filling the form bellow ) I need you to check the following for me:
<br />
<br />
1- please make sure that <b>'.$system_folder.'</b> has script write permissions that is: 777 ( I need to create config.php and .htaccess inside )
<br />
<br />
2- please create an empty database, it will be great if you set the connection collation to utf8_general_ci.
<br />
<br />
3- make sure you are using php 5.1 or above ( I am alpha and not fully tested on other versions )
<br />
<br />
4- please enable mod_rewrite inside your apache2 httpd.conf (do not say you are still using apache 1) 
<br />
<br />
5- I am not tested on windows machines so if you have problems during the one step installation, please consider Linux alternative
<br />
<br />
6- I told you that I\'m still in alpha mode. while you are installing this some one is either pushing changes to lp:~aikiframework.admins/aikiframework/release-1.0.0 or playing xmoto ;) so if you have problems please join #aiki on freenode 
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


$config["widget_cache"] = false;
$config["widget_cache_dir"] = "widgets";

$config["css_cache"] = true;
$config["css_cache_timeout"] = 24;
$config["css_cache_file"] = "";

$config["javascript_cache"] = true;
$config["javascript_cache_timeout"] = 24;
$config["javascript_cache_file"] = "";

//$config["html_cache"] = "assets/var/cache/widgets";
$config["html_cache"] = false;
$config["cache_timeout"] = "24";


$config["error_404"] = "<h1>404 Page Not Found</h1>

<p>This page is not found</p>
<p>Please visit <a href=\"'.$pageURL.'\">Home page</a> so you may find what you are looking for.</p>"; 

?>
';	



	$config_file_name = "config.php";
	$FileHandle = fopen($config_file_name, 'w') or die("Sorry, but I don't have write permissions to create config file");
	fwrite($FileHandle, $config_file);
	fclose($FileHandle);


	$htaccess_file = 'Options +FollowSymLinks
RewriteEngine on
RewriteBase '.$_SERVER["REQUEST_URI"].'
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

	$conn = mysql_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_pass']) or die ('Error connecting to mysql');
	mysql_select_db($_POST['db_name']);

	$sql = '

CREATE TABLE IF NOT EXISTS `aiki_apps` (
  `id` int(11) NOT NULL auto_increment,
  `app_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--------------------------------------------------------

INSERT INTO `aiki_apps` (`id`, `app_name`) VALUES
(1, \'Admin Panel\');

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_config` (
  `config_id` int(11) unsigned NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `config_type` varchar(255) default NULL,
  `config_data` mediumtext,
  PRIMARY KEY  (`config_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--------------------------------------------------------

INSERT INTO `aiki_config` (`config_id`, `app_id`, `config_type`, `config_data`) VALUES
(1, 0, \'global_settings\', \'a:9:{s:4:"site";s:7:"default";s:3:"url";s:'.$page_strlen.':"'.$pageURL.'";s:13:"cookie_domain";s:0:"";s:13:"default_chmod";s:4:"0777";s:11:"pretty_urls";s:1:"1";s:16:"default_language";s:7:"english";s:19:"default_time_format";s:9:"d - m - Y";s:8:"site_dir";s:3:"ltr";s:19:"language_short_name";s:2:"en";}\'),
(2, 0, \'database_settings\', \'a:6:{s:7:"db_type";s:5:"mysql";s:10:"disk_cashe";s:1:"1";s:13:"cache_timeout";s:2:"24";s:9:"cache_dir";s:7:"somedir";s:13:"cache_queries";s:1:"1";s:16:"charset_encoding";s:4:"utf8";}\'),
(3, 0, \'paths_settings\', \'a:1:{s:10:"top_folder";s:'.$system_folder_strlen.':"'.$system_folder.'";}\'),
(4, 0, \'metatags_settings\', \'a:5:{s:10:"site_title";s:11:"[[AikiCms]]";s:13:"dynamic_metas";s:1:"1";s:13:"static_author";s:7:"Aikicms";s:15:"static_keywords";s:0:"";s:18:"static_description";s:0:"";}\'),
(5, 0, \'feed_settings\', \'a:10:{s:10:"feed_title";s:18:"[[discover_syria]]";s:16:"feed_description";s:0:"";s:9:"feed_link";s:29:"http://www.discover-syria.com";s:13:"feed_language";s:2:"ar";s:16:"feed_image_title";s:14:"Discover Syria";s:14:"feed_image_url";s:41:"images/skins/discover_syria/watermark.png";s:15:"feed_image_link";s:29:"http://www.discover-syria.com";s:16:"feed_image_width";s:3:"144";s:17:"feed_image_height";s:2:"50";s:15:"feed_def_module";s:8:"articles";}\'),
(6, 0, \'images_settings\', \'a:4:{s:7:"max_res";s:3:"650";s:20:"default_photo_module";s:21:"modules_photo_archive";s:23:"store_native_extensions";s:4:"true";s:13:"new_extension";s:5:".news";}\'),
(7, 0, \'admin_settings\', \'a:1:{s:17:"show_edit_widgets";s:1:"0";}\');

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
(1, 0, \'html, body, div, span, applet, object, h1, h2, h3, h4, h5, h6, p, a, em, img, strong, ol, ul, li, dl, dd, dt, form, label\', \'default\', \'\', \'\', \'  margin: 0;\r\n    padding: 0;\r\n    border: 0;\r\n    outline: 0;\r\n    font-style: inherit;\r\n    font-size: 100%;\r\n    font-family: inherit;\r\n    vertical-align: baseline;\r\n    background: transparent;\', \'\', \'\', 1),
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_filed_types` (
  `type_id` int(11) NOT NULL auto_increment,
  `type_name` varchar(255) NOT NULL,
  `type_full_name` varchar(255) NOT NULL,
  `type_father` int(11) NOT NULL,
  `sons_number` int(11) NOT NULL,
  `type_static` text NOT NULL,
  PRIMARY KEY  (`type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--------------------------------------------------------

INSERT INTO `aiki_filed_types` (`type_id`, `type_name`, `type_full_name`, `type_father`, `sons_number`, `type_static`) VALUES
(1, \'selection\', \'Selection Menu\', 0, 0, \'\'),
(2, \'textinput\', \'Text filed\', 0, 0, \'\'),
(3, \'iaselection\', \'Interactive selection\', 0, 0, \'\'),
(4, \'textblock\', \'Text Block\', 0, 0, \'\'),
(5, \'datetime\', \'Date and Time\', 0, 0, \'\'),
(6, \'staticselect\', \'Static Selection\', 0, 2, \'\'),
(7, \'imagefolderupload\', \'Upload Images Folder\', 0, 0, \'\'),
(8, \'arabicletters\', \'Arabic Letters Selection\', 6, 0, \'<option value="1">أ</option>\r\n			<option value="2">ب</option>\r\n			<option value="3">ت</option>\r\n			<option value="4">ث</option>\r\n			<option value="5">ج</option>\r\n			<option value="6">ح</option>\r\n			<option value="7">خ</option>\r\n			<option value="8">د</option>\r\n			<option value="9">ذ</option>\r\n			<option value="10">ر</option>\r\n			<option value="11">ز</option>\r\n			<option value="12">س</option>\r\n			<option value="13">ش</option>\r\n			<option value="14">ص</option>\r\n			<option value="15">ض</option>\r\n			<option value="16">ط</option>\r\n			<option value="17">ظ</option>\r\n			<option value="18">ع</option>\r\n			<option value="19">غ</option>\r\n			<option value="20">ف</option>\r\n			<option value="21">ق</option>\r\n			<option value="22">ك</option>\r\n			<option value="23">ل</option>\r\n			<option value="24">م</option>\r\n			<option value="25">ن</option>\r\n			<option value="26">ه</option>\r\n			<option value="27">و</option>\r\n			<option value="28">ي</option>\'),
(9, \'englishletters\', \'English Letters Selection\', 6, 0, \'<option value="1">a</option>\r\n			<option value="2">b</option>\r\n			<option value="3">c</option>\r\n			<option value="4">d</option>\r\n			<option value="5">e</option>\r\n			<option value="6">f</option>\r\n			<option value="7">g</option>\r\n			<option value="8">h</option>\r\n			<option value="9">i</option>\r\n			<option value="10">j</option>\r\n			<option value="11">k</option>\r\n			<option value="12">l</option>\r\n			<option value="13">m</option>\r\n			<option value="14">n</option>\r\n			<option value="15">o</option>\r\n			<option value="16">p</option>\r\n			<option value="17">q</option>\r\n			<option value="18">r</option>\r\n			<option value="19">s</option>\r\n			<option value="20">t</option>\r\n			<option value="21">u</option>\r\n			<option value="22">v</option>\r\n			<option value="23">w</option>\r\n			<option value="24">x</option>\r\n			<option value="25">y</option>\r\n			<option value="26">z</option>\'),
(10, \'upload\', \'Upload an image\', 0, 0, \'\'),
(11, \'no_yes\', \'No Yes\', 6, 0, \'<option value="1">yes</option>\r\n<option value="0" selected>no</option>\');

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_forms` (
  `id` int(11) NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `form_method` varchar(5) NOT NULL,
  `form_action` varchar(255) NOT NULL,
  `form_dir` varchar(155) NOT NULL,
  `form_name` varchar(255) NOT NULL,
  `form_array` text NOT NULL,
  `form_html` text NOT NULL,
  `form_query` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--------------------------------------------------------

INSERT INTO `aiki_forms` (`id`, `app_id`, `form_method`, `form_action`, `form_dir`, `form_name`, `form_array`, `form_html`, `form_query`) VALUES
(1, 0, \'\', \'\', \'\', \'new_user\', \'a:16:{s:9:"tablename";s:10:"aiki_users";s:4:"pkey";s:6:"userid";s:10:"send_email";s:315:"[email]|اكتشف سورية <info@discover-syria.com>|أهلاً بك في اكتشف سورية|شكراً لك يا [full_name] <br /> وأهلاً بك في اكتشف سورية<br /> الرجاء تفعيل الحساب بالضغط على الوصلة:<br /> http://www.discover-syria.com/activate/[randkey]";s:16:"unique_textinput";s:45:"username||true:اسم المستخدم:unique";s:9:"password1";s:47:"password:كلمة المرور:password:md5|md5";s:10:"textinput1";s:39:"full_name||true:الاسم الكامل";s:10:"textinput2";s:20:"country:الدولة";s:13:"staticselect3";s:94:"sex:الجنس:custome:بدون تحديد>بدون تحديد&ذكر>ذكر&أنثى>أنثى";s:10:"textinput4";s:14:"job:العمل";s:13:"static_input1";s:36:"usergroup:الصلاحيات:value:5";s:19:"textinput_if_valid1";s:51:"email||true:البريد الإلكتروني:email";s:19:"textinput_if_valid2";s:46:"homepage:الموقع الإلكتروني:url";s:10:"autofiled1";s:25:"first_ip:الآي بي:ip";s:10:"autofiled2";s:48:"first_login:تاريخ التسجيل:uploaddate";s:13:"staticselect5";s:83:"maillist:الاشتراك بالقائمة البريدية:custome:نعم>1&لا>0";s:10:"rand_value";s:38:"randkey:مفتاح التفعيل:rand";}\', \'\', \'\'),
(2, 0, \'\', \'\', \'ltr\', \'edit_widgets\', \'a:19:{s:9:"tablename";s:12:"aiki_widgets";s:4:"pkey";s:2:"id";s:10:"textinput1";s:33:"widget_name|SystemGOD:Widget name";s:10:"textinput2";s:28:"style_id|SystemGOD:Css Class";s:16:"normaltextblock1";s:27:"display_urls|SystemGOD:Urls";s:16:"normaltextblock2";s:29:"kill_urls|SystemGOD:Kill Urls";s:16:"normaltextblock3";s:27:"normal_select|SystemGOD:SQL";s:16:"normaltextblock4";s:37:"if_no_results|SystemGOD:if no results";s:10:"textinput3";s:32:"display_in_row_of|SystemGOD:Rows";s:10:"textinput4";s:33:"records_in_page|SystemGOD:Columns";s:10:"textinput5";s:38:"link_example|SystemGOD:Pagination link";s:13:"bigtextblock1";s:21:"widget|SystemGOD:HTML";s:16:"normaltextblock8";s:30:"pagetitle|SystemGOD:Page title";s:16:"normaltextblock9";s:43:"output_modifiers|SystemGOD:Output modifiers";s:10:"textinput6";s:44:"widget_cache_timeout|SystemGOD:Cache Timeout";s:14:"staticselect10";s:58:"custome_output|SystemGOD:Custome Output:custome:Yes>1&No>0";s:17:"normaltextblock11";s:45:"custome_header|SystemGOD:Custome http headers";s:10:"selection6";s:62:"javascript|SystemGOD:Javascript:aiki_javascript:id:script_name";s:14:"staticselect20";s:48:"is_active|SystemGOD:Is Active:custome:Yes>1&No>0";}\', \'<form method="post" name=\'\'editor\'\' id=\'\'addwidget\'\' class="widget_form" action="http://localhost/aikiframework/admin_tools/edit/widgets/19">\r\n<h2>Widget name</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<input type="text" name="widget_name" dir="" value="">\r\n<h2>Css Class</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<input type="text" name="style_id" dir="" value="">\r\n<h2>Urls</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<textarea rows=4" dir="" cols="50" name="display_urls">\r\n</textarea>\r\n<h2>Kill Urls</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<textarea rows="4" dir="" cols="50" name="kill_urls"></textarea>\r\n<h2>SQL</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<div style="border: 1px solid black; padding: 3px; background-color: #F8F8F8">\r\n<textarea id="normal_select" rows="4" dir="" cols="50" name="normal_select"></textarea>\r\n</div>\r\n<h2>if no results</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<textarea rows="4" dir="" cols="50" name="if_no_results"></textarea>\r\n<h2>Rows</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<input type="text" name="display_in_row_of" dir="" value="0">\r\n<h2>Columns</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<input type="text" name="records_in_page" dir="" value="0">\r\n<h2>Pagination link</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<input type="text" name="link_example" dir="" value="">\r\n<h2>HTML</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<div style="border: 1px solid black; padding: 3px; background-color: #F8F8F8">\r\n<textarea id="widget" cols="120" rows="30" name="widget"></textarea>\r\n</div>\r\n<h2>Page title</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<textarea rows="2" dir="" cols="50" name="pagetitle">\r\n\r\n</textarea>\r\n<h2>Output modifiers</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<textarea rows="4" dir="" cols="50" name="output_modifiers"></textarea>\r\n<h2>Cache Timeout</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<input type="text" name="widget_cache_timeout" dir="" value="0">\r\n<h2>Custome Output</h2>\r\n<select name="custome_output" dir="">\r\n<option value="1">Yes</option>\r\n<option value="0" selected>No</option>\r\n</select>\r\n<h2>Custome http headers</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<textarea rows="7" dir="" cols="50" name="custome_header"></textarea>\r\n<h2>Javascript</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<select name="javascript" dir="">\r\n	<option value="0">Please Select</option>\r\n	<option value=\'\'1\'\' >jQuery</option>\r\n	<option value=\'\'5\'\' >jquery_ui</option><option value=\'\'8\'\' >language</option>\r\n	<option value=\'\'6\'\' >resize_functions</option>\r\n	<option value=\'\'2\'\' >splitter</option>\r\n	<option value=\'\'3\'\' >tree_component</option>\r\n</select>\r\n<h2>Is Active</h2>\r\n<p class="codetext">The name of your Widget</p>\r\n<select name="is_active" dir="">\r\n	<option value="1" selected>Yes</option>\r\n	<option value="0">No</option>\r\n</select>\r\n<p class="form-buttons"> <input class="button" type="submit" name="add" value="Save"></p>\r\n</form>\', \'\'),
(3, 0, \'\', \'\', \'ltr\', \'edit_urls\', \'a:4:{s:9:"tablename";s:9:"aiki_urls";s:4:"pkey";s:2:"id";s:10:"textinput1";s:17:"url|SystemGOD:URL";s:10:"textinput2";s:19:"site|SystemGOD:Site";}\', \'<form method="post" enctype="multipart/form-data" name=\'\'editor\'\' id=\'\'addwidget\'\' class="widget_form" action="http://www.aikiframework.org/aikidev/admin_tools/new/urls">\r\n<h2>URL</h2>\r\n<p class="codetext">[root]</p>\r\n<input type="text" name="url" dir="" value="">\r\n<h2>Site</h2>\r\n<p class="codetext"></p>\r\n<input type="text" name="site" dir="" value="">\r\n<p class="form-buttons"> <input class="button" type="submit" name="add" value="Save"></p>\r\n</form>\', \'\');

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_guests` (
  `userid` int(9) unsigned NOT NULL auto_increment,
  `first_login` datetime NOT NULL,
  `last_hit` datetime NOT NULL,
  `last_hit_unix` int(11) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `last_ip` varchar(40) NOT NULL,
  `username` varchar(255) NOT NULL,
  `guest_session` varchar(255) NOT NULL,
  `hits` int(11) NOT NULL,
  `is_online` int(11) NOT NULL,
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--------------------------------------------------------

INSERT INTO `aiki_javascript` (`id`, `app_id`, `script_name`, `script_group`, `script_folder`, `script`, `father`, `global_use`, `is_active`) VALUES
(1, 0, \'jQuery\', \'aikiadmin\', \'\', \'<script type="text/javascript" src="[root]/system/jquery/jquery-1.3.2.min.js"></script>\', 0, 1, 1),
(2, 0, \'admin_panel\', \'aikiadmin\', \'\', \'<script type="text/javascript" src="[root]/system/jquery/plugins/jquery.layout.min.js"></script>\r\n<script type="text/javascript" src="[root]/system/jquery/plugins/jquery.form.js"></script> \r\n<script type="text/javascript" src="[root]system/jquery/plugins/css.js"></script>\r\n<script type="text/javascript" src="[root]system/jquery/plugins/jstree/tree_component.js"></script>\r\n<script type="text/javascript" src="[root]system/jquery/plugins/jstree/sarissa.js"></script>\r\n<script type="text/javascript" src="[root]system/jquery/plugins/jstree/sarissa_ieemu_xpath.js"></script>\r\n<script type="text/javascript" src="[root]system/jquery/plugins/jquery.xslt.js"></script>\r\n<script type="text/javascript" src="[root]assets/apps/admin/control_panel.js"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]system/jquery/plugins/jstree/tree_component.css" />\r\n<script type="text/javascript" src="[root]system/jquery/jquery-ui-1.7.2.custom.min.js"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]system/jquery/css/smoothness/jquery-ui-1.7.1.custom.css" />\r\n   <script src="[root]/assets/plugins/codemirror/js/codemirror.js" type="text/javascript"></script>\r\n    <link rel="stylesheet" type="text/css" href="[root]/assets/plugins/codemirror/css/docs.css"/>\r\n     <style type="text/css">\r\n      .CodeMirror-line-numbers {\r\n        width: 2.2em;\r\n        color: #aaa;\r\n        background-color: #eee;\r\n        text-align: right;\r\n        padding-right: .3em;\r\n        font-size: 10pt;\r\n        font-family: monospace;\r\n        padding-top: .4em;\r\n      }\r\n    </style>\', 0, 0, 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--------------------------------------------------------

INSERT INTO `aiki_languages` (`id`, `name`, `sys_name`, `short_name`, `dir`, `align`, `is_default`) VALUES
(1, \'عربي\', \'arabic\', \'ar\', \'rtl\', \'right\', 0),
(2, \'English\', \'english\', \'en\', \'ltr\', \'left\', 1),
(5, \'German\', \'german\', \'de\', \'ltr\', \'left\', 0);

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_linker_tags` (
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

INSERT INTO `aiki_linker_tags` (`name`, `tagstart`, `tagend`, `parlset`, `linkexample`, `dbtable`, `namecolumn`, `idcolumn`, `extrasql`, `is_extrasql_loop`) VALUES
(\'wikilinks\', \'(+(\', \')+)\', \'\', \'bank\', \'modules_wiki_text\', \'title\', \'id\', \'\', 0);

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_plugins` (
  `id` int(11) NOT NULL auto_increment,
  `app_id` int(11) NOT NULL,
  `modifiers_name` varchar(255) NOT NULL,
  `modifiers_group` varchar(255) NOT NULL,
  `modifiers_type` varchar(255) NOT NULL,
  `plugin_filename` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--------------------------------------------------------

INSERT INTO `aiki_plugins` (`id`, `app_id`, `modifiers_name`, `modifiers_group`, `modifiers_type`, `plugin_filename`) VALUES
(1, 0, \'linkTags\', \'aiki_defaults\', \'output_modifier\', \'\'),
(2, 0, \'doImages\', \'aiki_defaults\', \'output_modifier\', \'\'),
(3, 0, \'external_links\', \'aiki_defaults\', \'output_modifier\', \'\'),
(4, 0, \'getRemotePages\', \'aiki_defaults\', \'output_modifier\', \'\'),
(5, 0, \'doQuotes\', \'wikipedia_markup\', \'output_modifier\', \'\'),
(7, 0, \'doTables\', \'aiki_defaults\', \'output_modifier\', \'\'),
(8, 0, \'ajax\', \'aiki_defaults\', \'output_modifier\', \'\');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--------------------------------------------------------

INSERT INTO `aiki_template` (`id`, `app_id`, `template_name`, `template_input`, `template_output`) VALUES
(1, 0, \'Coin image box 2 singles\', \'{{Coin image box 2 singles\n| header =\n| image_left =\n| image_right =\n| caption_left =\n| caption_right =\n| width_left =\n| width_right =\n| footer =\n| position =\n| margin =\n| background =\n}}\', \'<table id="Coin Image Box" style="" align="(position)">\n    <tbody>\n        <tr>\n            <th colspan="2" style="padding: 2px; width: 176px;">(header)</th>\n        </tr>\n        <tr>\n            <td colspan="2" style="border: 1px solid rgb(204, 204, 204); padding: 0px;">\n            <img alt="" src="(image_left)" border="0" height="93" width="90">\n            <img alt="" src="(image_right)" border="0" height="93" width="90">\n            </td>\n        </tr>\n        <tr style="font-size: 85%; vertical-align: top;">\n            <td style="border: 1px solid rgb(204, 204, 204); padding: 2px; width: 86px; line-height: 1.5em;">(caption_left)</td>\n            <td style="border: 1px solid rgb(204, 204, 204); padding: 2px 1px 2px 2px; width: 86px; line-height: 1.5em;">(caption_right)</td>\n        </tr>\n        <tr>\n            <td colspan="2" style="padding: 2px; width: 176px; font-size: 85%; line-height: 1.5em;">(footer)</td>\n        </tr>\n    </tbody>\n</table>\'),
(3, 0, \'redirect\', \'{{redirect\r\n| url = http://www.discover-syria.com/results/538}}\r\n\', \'(#(header:(url))#)\r\n<p align="center">\r\n المحتوى المطلوب انتقل إلى الرابط التالي:<br />\r\n<a href="(url)">(url)</a><br />\r\nاذا لم يتم تحويلك تلقائياً خلال 3 ثوان<br />\r\n<a href="(url)">الرجاء اضغط هنا</a>\r\n</p>\'),
(4, 0, \'Bank Titles\', \'{{Bank Titles\r\n|title = معلومات عن دمشق\r\n|position = right\r\n|term = دمشق\r\n}}\', \'<div id="sub_contents" style="clear: (position); float: (position); border-width: .5em 0 .8em 1.4em;  padding-left: 20px; padding-right: 10px; padding-top: 10px; padding-bottom: 10px; width: 320px">\r\n<div style="width: 100%; border: 1px solid #ccc; padding: 3px; background-color: #f9f9f9;font-size: 99%;text-align: right;overflow: hidden;">\r\n<p>(title)</p>\r\n<ul>\r\n\r\n(@(select id, title from dsyria_bank_text where title RLIKE "(term)"||<li><a href="(+(1)+)">(+(2)+)</a></li>)@)\r\n\r\n</ul>\r\n</div>\r\n</div>\'),
(9, 0, \'box\', \'{{box\r\n| contents =\r\n| position =\r\n}}\', \'<table id="box" style="" align="(position)">\r\n    <tbody>\r\n        <tr>\r\n            <td colspan="2" style="border: 1px solid rgb(204, 204, 204); padding: 0px;">\r\n		(contents)\r\n            </td>\r\n        </tr>\r\n    </tbody>\r\n</table>\'),
(11, 0, \'refresh\', \'{{refresh\r\n| url = http://www.discover-syria.com/results/538}}\r\n\', \'<p align="center">\r\n المحتوى المطلوب انتقل إلى الرابط التالي:<br />\r\n<a href="(url)">(url)</a><br />\r\nاذا لم يتم تحويلك تلقائياً خلال 3 ثوان<br />\r\n<a href="(url)">الرجاء اضغط هنا</a>\r\n</p>\r\n<meta HTTP-EQUIV="REFRESH" content="3; url=(url)">\'),
(12, 0, \'inline\', \'{{inline\r\n| id = 538\r\n}}\r\n\', \'(#(inline:aikicore->setting[url]/mini_articles/news|(id)?noheaders=1&nogui=1)#)\');

--------------------------------------------------------

CREATE TABLE IF NOT EXISTS `aiki_urls` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL,
  `cacheable` int(1) NOT NULL,
  `site` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--------------------------------------------------------

INSERT INTO `aiki_urls` (`id`, `url`, `cacheable`, `site`) VALUES
(1, \'admin\', 0, \'aikiadmin\'),
(2, \'admin_tools/edit/\', 0, \'aikiadmin\'),
(3, \'login\', 0, \'aikiadmin\'),
(6, \'admin_tools/new/\', 0, \'aikiadmin\'),
(7, \'admin_tools/delete/\', 0, \'aikiadmin\');

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
(2, \'admin\', \'System admin\', \'\', \'male\', \'\', \'c3284d0f94606de1fd2af172aba15bf3\', 1, \'\', \'\', \'\', \'\', \'0000-00-00 00:00:00\', \'2009-09-24 18:49:07\', \'127.0.0.1\', \'\', 0, 83, \'\', 0);

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
  `session_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `session_date` datetime NOT NULL,
  `user_session` varchar(255) NOT NULL,
  `user_ip` varchar(100) NOT NULL,
  PRIMARY KEY  (`session_id`)
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--------------------------------------------------------

INSERT INTO `aiki_widgets` (`id`, `app_id`, `widget_name`, `widget_site`, `widget_target`, `widget_type`, `display_order`, `style_id`, `is_father`, `father_widget`, `display_urls`, `kill_urls`, `normal_select`, `authorized_select`, `if_no_results`, `widget`, `css`, `nogui_widget`, `display_in_row_of`, `records_in_page`, `link_example`, `operators_order`, `dynamic_pagetitle`, `pagetitle`, `output_modifiers`, `is_admin`, `if_authorized`, `permissions`, `remove_container`, `edit_in_place`, `widget_cache_timeout`, `custome_output`, `custome_header`, `is_active`, `javascript`) VALUES
(1, 1, \'header\', \'default\', \'body\', \'div\', 1, \'\', \'0\', 6, \'admin\', \'\', \'\', \'\', \'\', \'(#(header:[root]/login)#)\', \'#header {\r\n    height: 28px;\r\n    background: #eeeeee;\r\n    position: relative;\r\n    border-bottom:1px solid #666666;\r\n    border-top:1px solid #666666;\r\n    text-align:center;\r\n}\r\n\r\n#main-navigation {\r\n    	position: relative;\r\n	float:left;\r\n	line-height:25px;\r\n}\r\n\r\n#main-navigation li{\r\n	float:left;\r\n	line-height:25px;\r\n	margin-right: 10px;\r\n	margin-left: 10px;\r\n}\r\n\r\n#main-navigation li a img{\r\n	margin-top:5px;\r\n}\r\n\r\n#main-navigation .aiki-icon {\r\n	margin-left:-5px;\r\n	margin-right:-10px;\r\n}\r\n\r\n#main-navigation a.active{\r\n	font-weight:bold;\r\n}\r\n\r\n#user-navigation {\r\n    	position: relative;\r\n	float:right;\r\n	line-height:25px;\r\n}\r\n\r\n#user-navigation li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#user-navigation li a, #user-navigation li strong{\r\n	margin-right: 10px;\r\n	margin-left: 10px;\r\n}\r\n\r\n#user-navigation li a img{\r\n	margin-top:5px;\r\n}\r\n\r\n#user-navigation .aiki-icon {\r\n	margin-left:-5px;\r\n	margin-right:-10px;\r\n}\r\n\r\n#user-navigation a.active{\r\n	font-weight:bold;\r\n}\r\n\', \'\', 0, 0, \'\', \'\', \'\', \'aiki AdminPanel\', \'[all]\', 1, \'	<ul id="main-navigation" class="clearfix">\r\n		<li><a href="#" class="aiki-icon" id="aiki-icon-button"><img src="[root]/assets/images/aiki-icon.png" /></a></li>\r\n 		<li><a href="#" id="structur_button" class="active">Structure</a></li>\r\n		<li><a href="#">Apps</a></li>\r\n		<li><a href="#" id="system_button">System</a></li>\r\n	</ul>\r\n\r\n(ajax_a(structur_button;\r\n[\'\'[root]/index.php?widget=widget_accordion\'\',\'\'#ui-layout-center\'\', \'\'widget_accordion()\'\'];\r\n[\'\'[root]/index.php?widget=structur_accordion\'\',\'\'#ui-layout-west\'\', \'\'structur_accordion()\'\']\r\n)ajax_a)\r\n\r\n(ajax_a(system_button;\r\n[\'\'[root]/language\'\',\'\'#ui-layout-center\'\'];\r\n[\'\'[root]/index.php?widget=system_accordion\'\',\'\'#ui-layout-west\'\', \'\'system_accordion()\'\']\r\n)ajax_a)\r\n\r\n	<ul id="user-navigation" class="clearfix">\r\n		<li><strong>[username] @ aikiframework.com</strong>|</li>\r\n 		<li><a href="#">Settings</a>|</li>\r\n		<li><a href="#" class="help-toggler">Help</a>|</li>\r\n		<li><a href="#">Signout</a></li>\r\n	</ul>\r\n\r\n<div id="dialog" title="About your aiki installation">\r\n	<p>\r\n		<img src="[root]/assets/images/logo-aikiframework.png" />\r\n		<br /><br />\r\n		<h2>aiki framework 1.0.0</h2>\r\n		<br />\r\n		<a href="http://www.aikiframework.org">http://www.aikiframework.org</a>\r\n		<br /><br />\r\n		<h2>Credits:</h2>	\r\n		Bassel Safadi (Code)<br />\r\n		Michi Krnac (GUI)<br />\r\n		Vera Lobatcheva (HTML/CSS)<br />	\r\n	</p>\r\n</div>\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 2),
(2, 1, \'search\', \'default\', \'body\', \'div\', 2, \'\', \'0\', 6, \'admin\', \'\', \'\', \'\', \'\', \'\', \'#search {\r\n    	height: 80px;\r\n    	position: relative;\r\n	text-align:left;\r\n}\r\n\r\n#search .logo{\r\n	position:relative;\r\n	float:left;\r\n	margin:5px;\r\n}\r\n\r\n#search form{\r\n	position:relative;\r\n	float:left;\r\n	margin-top:15px;\r\n}\r\n\r\ninput.oneLine{\r\n	border: 1px solid #999999;\r\n	background:url(shared/images/input_bg.png) repeat-x bottom;\r\n    	font-size:12pt;\r\n	padding:2px;\r\n	margin-left:10px;\r\n}\r\n\r\ninput.button{\r\n	background:url(shared/images/input_button.png) no-repeat;\r\n	width:80px;\r\n	height:25px;\r\n    	font-size:10pt;\r\n	font-weight:bold;\r\n	padding:2px;\r\n	margin-left:10px;\r\n        border:0;\r\n}\r\n\r\n#search input.button:hover{\r\n	background:url(shared/images/input_button_active.png) no-repeat;\r\n}\r\n\r\n#content_button {\r\n  position:absolute;\r\n  right:-5px;\r\n  top:5px;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'<a href="#" class="logo"><img src="[root]/assets/images/logo.png" /></a>\r\n<a href="[root]/content" ><img id="content_button" src="[root]/assets/images/content-button.png" /></a>\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(3, 1, \'structur_accordion\', \'default\', \'body\', \'div\', 6, \'\', \'0\', 7, \'admin\', \'\', \'\', \'\', \'\', \'\', \'#tree-menu {\r\n	border-bottom: 1px dashed #d3d7cf;\r\ndisplay:block;\r\nposition:relative;\r\n}\r\n\r\n#tree-menu li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#tree-menu li a{\r\n	margin-right: 5px;\r\n	margin-left: 5px;\r\n}\r\n\r\n#tree-menu li a img{\r\n	margin-top:5px;\r\n	height:12px;\r\n	margin-right:2px;\r\n}\r\n\r\n#widget-tree {\r\n	text-align:left;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'<h3><a href="#">Urls & Widgets</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'url\'\' }, data: { title : \'\'New URL\'\', icon : \'\'[root]/assets/images/icons/link_add.png\'\' } },-1);"><img src="[root]/assets/images/icons/link_add.png" />Add URL</a></li>\r\n		<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'son\'\' }, data: { title : \'\'New Widget\'\', icon : \'\'[root]/assets/images/icons/layout_add.png\'\' } },0);"><img src="[root]/assets/images/icons/layout_add.png" />Create Widget</a></li>\r\n	</ul>\r\n	<div id="widgettree" class="demo"></div>\r\n</div>\r\n\r\n<h3><a href="#">Databases & Forms</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'site\'\' }, data: { title : \'\'New Site\'\', icon : \'\'[root]/assets/images/icons/database.png\'\' } },-1);"><img src="[root]/assets/images/icons/database.png" />Create Table</a></li>\r\n		<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'site\'\' }, data: { title : \'\'New Site\'\', icon : \'\'[root]/assets/images/icons/application_form.png\'\' } },-1);"><img src="[root]/assets/images/icons/application_form.png" />Create Form</a></li>\r\n	</ul>\r\n</div>\r\n\r\n<h3><a href="#">Global JS & CSS</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'site\'\' }, data: { title : \'\'New Site\'\', icon : \'\'[root]/assets/images/icons/page_gear.png\'\' } },-1);"><img src="[root]/assets/images/icons/page_gear.png" />Add Javascrip</a></li>\r\n		<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'site\'\' }, data: { title : \'\'New Site\'\', icon : \'\'[root]/assets/images/icons/page_link.png\'\' } },-1);"><img src="[root]/assets/images/icons/page_link.png" />Add CSS</a></li>\r\n	</ul>\r\n</div>\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(4, 1, \'widget_accordion\', \'default\', \'body\', \'div\', 0, \'\', \'\', 8, \'admin\', \'\', \'\', \'\', \'\', \'\', \'#breadcrumbs li{\r\n	float:left;	\r\n}\r\n\r\n#breadcrumbs li a{\r\n	float:left;\r\n}\r\n\r\n#breadcrumbs li a img{\r\n	height:12px;\r\n	margin-right:4px;\r\n	top: 5px;\r\n}\r\n\r\n#breadcrumbs li img{\r\n	float:left;\r\n	position: relative; \r\n	top: 8px;\r\n	margin-left:10px;\r\n}\r\n\r\n.widget_form h2 {\r\n	margin:15px 15px 0 15px;\r\n	font-size:120%;\r\n	color:#2e3436;\r\n}\r\n.widget_form h3 {\r\n	margin:0 15px 0 15px;\r\n	font-size:110%;\r\n	color:#2e3436;\r\n}\r\n\r\n.codetext {\r\n	margin:0 15px 0 15px;\r\n	color:#555753;\r\n	font-size:80%;\r\n}\r\n\r\n.options-button {\r\n	background:#eeeeee;\r\n	margin:15px 15px 0 15px;\r\n	width:80px;\r\n	height:20px;\r\n	text-align:center;\r\n}\r\n\r\n.options-button a{\r\n	margin:5px;\r\n	color: #1b3b6b;\r\n}\r\n.options-button a:hover {\r\n    	text-decoration: none;\r\n}\r\n\r\n.options {\r\n	border:1px solid #eeeeee;\r\n	margin:0px 15px 0 15px;\r\n	padding:10px;\r\n	color: #1b3b6b;\r\n}\r\n#big_form {\r\n	margin:0px 15px 0 15px;\r\n}\r\n.widget_form textarea, .widget_form input, .widget_form select {\r\n	border:2px solid #c3c3c3;\r\n	font-family: "Courier New";\r\n	padding:3px;\r\n	color:#555753;\r\n	margin:0 15px 0 15px;\r\n	font-size:120%;\r\n	background:GhostWhite ;\r\n}\r\n\r\n.form-buttons {\r\n	text-align:right;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'<h3><a href="#">Widgets</a></h3>\r\n\r\n<div id="widget-form" class="accordeon-content">\r\nHere\r\n</div>\r\n\r\n<h3><a href="#">Structure</a></h3>\r\n<div>\r\n\r\n</div>\r\n\r\n<h3><a href="#">Permission</a></h3>\r\n<div>\r\n\r\n</div>\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(5, 1, \'edit_record\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/edit/(.*)/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'(#(form:edit:(!(2)!):(!(3)!))#)\', \'SystemGOD\', 0, \'\', 0, 1, \'\', 1, 0),
(9, 1, \'aikiadmin_login\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'login\', \'\', \'\', \'\', \'\', \'<h2>Sign in to aikiframework Admin-Panel</h2><br/><br/>\r\n<img src="[root]assets/images/logo.png" /><br /><br />\r\n<form method=\'\'post\'\'>\r\n<div><table border=0>\r\n  <tbody>\r\n    <tr>\r\n      <td>Name:</td>\r\n      <td><input type="text" name="username" dir=""></td>\r\n    </tr>\r\n    <tr>\r\n      <td>Password:</td>\r\n      <td><input type="password" name="password" dir=""></td>\r\n    </tr>\r\n    <tr>\r\n      <td><input type="hidden" name="process" value="login"></td>\r\n      <td><input class="button" type="submit" name="submit" value="Sign in"></td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n</div>\r\n<form>\', \'#aikiadmin_login {\r\n border:1px solid #c3c3c3;\r\n width:400px;\r\nmargin: 200px auto;\r\ntext-align:center;\r\nbackground:GhostWhite ;\r\npadding:30px;\r\n}\r\n\r\n#aikiadmin_login img{\r\nmargin:5px;\r\n}\r\n\r\n#aikiadmin_login div{\r\nwidth: 260px; \r\nmargin: 0 auto;\r\n}\r\n\r\n#aikiadmin_login table{\r\ntext-align:right;\r\nwidth: 100%;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'Login to Aiki-Admin Panel\', \'\', 1, \'(#(header:[root]/admin)#)\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(6, 1, \'ui-layout-north\', \'default\', \'body\', \'div\', 0, \'ui-layout-north\', \'1\', 0, \'admin\', \'\', \'\', \'\', \'\', \'\', \'.ui-layout-pane-north {\r\n/* OVERRIDE \'\'default styles\'\' */\r\npadding: 0 !important;\r\noverflow: hidden !important;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(7, 1, \'ui-layout-west\', \'default\', \'body\', \'div\', 3, \'ui-layout-west\', \'1\', 0, \'admin\', \'\', \'\', \'\', \'\', \'\', \'	.ui-layout-pane-west {\r\n		/* OVERRIDE \'\'default styles\'\' */\r\n		padding: 0 !important;\r\n		overflow: hidden !important;\r\n	}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(8, 1, \'ui-layout-center\', \'default\', \'body\', \'div\', 0, \'ui-layout-center\', \'1\', 0, \'admin\', \'\', \'\', \'\', \'\', \'\', \'	.ui-layout-pane-center {\r\n		/* OVERRIDE \'\'default styles\'\' */\r\n		padding: 0 !important;\r\n		overflow: hidden !important;\r\n	}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(10, 1, \'system_accordion\', \'default\', \'body\', \'div\', 6, \'\', \'0\', 0, \'\', \'\', \'\', \'\', \'\', \'\', \'#system_accordion h3.ui-state-active{\r\n	background:url(assets/images/accordeon_active.png) repeat-x bottom;\r\n	text-align:left;\r\n	height:15px;\r\n	padding:5px;\r\n	border-top: 1px solid #999999;\r\n	border-bottom: 1px solid #999999;\r\n}\r\n\r\n#system_accordion h3.ui-state-active a{\r\n	color: #000;\r\n	text-decoration:none;\r\n}\r\n\r\n#system_accordion h3.ui-state-default{\r\n	background:url(assets/images/accordeon_default.png) repeat-x bottom;\r\n	text-align:left;\r\n	height:15px;\r\n	padding:5px;\r\n	border-top: 1px solid #d3d7cf;\r\n}\r\n\r\n#system_accordion h3.ui-state-default a{\r\n	color: #888a85;\r\n	text-decoration:none;\r\n}\r\n\r\n#tree-menu {\r\n	border-bottom: 1px dashed #d3d7cf;\r\ndisplay:block;\r\nposition:relative;\r\n}\r\n\r\n#tree-menu li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#tree-menu li a{\r\n	margin-right: 5px;\r\n	margin-left: 5px;\r\n}\r\n\r\n#tree-menu li a img{\r\n	margin-top:5px;\r\n	height:12px;\r\n	margin-right:2px;\r\n}\r\n\r\n#widget-tree {\r\n	text-align:left;\r\n}\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'<h3><a href="#">Users</a></h3>\r\n			<div>\r\n			<ul id="tree-menu" class="clearfix">\r\n 				<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'site\'\' }, data: { title : \'\'New Site\'\', icon : \'\'[root]/assets/images/icons/group.png\'\' } },-1);"><img src="[root]/assets/images/icons/group.png" />Add Group</a></li>\r\n\r\n				<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'url\'\' }, data: { title : \'\'New URL\'\', icon : \'\'[root]/assets/images/icons/user.png\'\' } },0);"><img src="[root]/assets/images/icons/user.png" />Add User</a></li>\r\n\r\n			</ul>\r\n		\r\n			<div id="widgettree" class="demo"></div>\r\n\r\n\r\n\r\n		\r\n			</div>\r\n		\r\n			<h3><a href="#">Language</a></h3>\r\n			<div>\r\n			<ul id="tree-menu" class="clearfix">\r\n 				<li><a href="#" onclick="$.tree_reference(\'\'widgettree\'\').create({ attributes : { \'\'class\'\' : \'\'site\'\' }, data: { title : \'\'New Site\'\', icon : \'\'[root]/assets/images/icons/world.png\'\' } },-1);"><img src="[root]/assets/images/icons/world.png" />Add Language</a></li>\r\n\r\n			</ul>\r\n			</div>\r\n\r\n			<h3><a href="#">Configuration</a></h3>\r\n			<div>\r\n			<p>\r\n			Nam enim risus, molestie et, porta ac, aliquam ac, risus. Quisque lobortis.\r\n			Phasellus pellentesque purus in massa. Aenean in pede. Phasellus ac libero\r\n			ac tellus pellentesque semper. Sed ac felis. Sed commodo, magna quis\r\n			lacinia ornare, quam ante aliquam nisi, eu iaculis leo purus venenatis dui.\r\n			</p>\r\n		\r\n			</div>\', \'SystemGOD\', 0, \'\', 0, 0, \'\', 1, 0),
(11, 1, \'new_record\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/new/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'(#(form:add:(!(2)!))#)\', \'SystemGOD\', 0, \'\', 0, 1, \'\', 1, 0),
(12, 1, \'confirmations\', \'default\', \'body\', \'div\', 0, \'\', \'\', 0, \'admin\', \'\', \'\', \'\', \'\', \'<div id="deletewidgetdialog" title="Delete widget">\r\n	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This widget will be permanently deleted and cannot be recovered. Are you sure?</p>\r\n</div>\', \'\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 0, \'\', \'\', 0, \'\', 0, 0, \'\', 1, 0),
(13, 1, \'delete_record\', \'default\', \'body\', \'div\', 0, \'\', \'0\', 0, \'admin_tools/delete/(.*)/(.*)\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', 0, 0, \'\', \'\', \'\', \'\', \'\', 1, \'{#{delete_record}#}\', \'SystemGOD\', 0, \'\', 0, 1, \'\', 1, 0);

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
  `original_artist_id` int(11) NOT NULL,
  `original_artist_name` varchar(255) NOT NULL,
  `original_width` int(11) NOT NULL,
  `original_height` int(11) NOT NULL,
  `article_title` varchar(255) NOT NULL,
  `article_keywords` text NOT NULL,
  `article_source` varchar(255) NOT NULL,
  `article_pubdate` int(11) NOT NULL,
  `article_writer` varchar(255) NOT NULL,
  `article_id` int(11) NOT NULL,
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