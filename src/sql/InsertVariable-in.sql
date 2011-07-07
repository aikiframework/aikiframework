/* Insert default table records with variable fields for aiki

 * Copyright (C) 2010-2011 Aiki Lab Pte Ltd

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.

 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * Written by Bassel Safadi, Steven Garcia
 * 
 * IMPORTANT in between each pair of SQL statements there must
 * exist a comment line with a precise series of dashes.
 * These are delimiters used by libs/installer.php
 * For more info see SQL_DELIMIT in libs/installer.php.
 */

INSERT IGNORE INTO `aiki_config` (`config_id`, `app_id`, `config_type`, `config_data`) VALUES
(1, 0, 'global_settings', 'a:9:{s:4:"site";s:7:"default";s:3:"url";s:@AIKI_SITE_URL_LEN@:"@AIKI_SITE_URL@";s:13:"cookie_domain";s:0:"";s:13:"default_chmod";s:4:"0777";s:11:"pretty_urls";s:1:"1";s:16:"default_language";s:7:"english";s:19:"default_time_format";s:9:"d - m - Y";s:8:"site_dir";s:3:"ltr";s:19:"language_short_name";s:2:"en";}'),
(2, 0, 'database_settings', 'a:6:{s:7:"db_type";s:5:"mysql";s:10:"disk_cache";s:1:"1";s:13:"cache_timeout";s:2:"24";s:9:"cache_dir";s:5:"cache";s:13:"cache_queries";s:1:"1";s:16:"charset_encoding";s:4:"utf8";}'),
(3, 0, 'paths_settings', 'a:1:{s:10:"top_folder";s:@PKG_DATA_DIR_LEN@:"@PKG_DATA_DIR@";}'),
(4, 0, 'images_settings', 'a:4:{s:7:"max_res";s:3:"650";s:20:"default_photo_module";s:18:"apps_photo_archive";s:23:"store_native_extensions";s:4:"true";s:13:"new_extension";s:5:".aiki";}'),
(5, 0, 'admin_settings', 'a:1:{s:17:"show_edit_widgets";s:1:"0";}'),
(6, 0, 'upload_settings', 'a:4:{s:18:"allowed_extensions";s:20:"jpg|gif|png|jpeg|svg";s:11:"upload_path";s:15:"assets/uploads/";s:22:"plupload_max_file_size";s:4:"10mb";s:13:"plupload_path";s:15:"assets/uploads/";}'),
(7, 0, 'revisions_settings', 'a:1:{s:24:"send_email_notifications";s:5:"false";}');

-- ------------------------------------------------------

INSERT IGNORE INTO `aiki_users` (`userid`, `username`, `full_name`, `country`, `sex`, `job`, `password`, `usergroup`, `email`, `avatar`, `homepage`, `first_ip`, `first_login`, `last_login`, `last_ip`, `user_permissions`, `maillist`, `logins_number`, `randkey`, `is_active`) VALUES
(1, 'guest', 'guest', '', '', '', '', 3, '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', 0, 0, '', 0),
(2, '@ADMIN_USER@', '@ADMIN_NAME@', '', '', '', '@ADMIN_PASS@', 1, '@ADMIN_MAIL@', '', '', '', '', '', '', '', 0, 0, '', 0);

-- ------------------------------------------------------

INSERT IGNORE INTO `aiki_widgets` (`id`, `app_id`, `widget_name`, `widget_site`, `widget_target`, `widget_type`, `display_order`, `style_id`, `is_father`, `father_widget`, `display_urls`, `kill_urls`, `normal_select`, `authorized_select`, `if_no_results`, `widget`, `css`, `nogui_widget`, `display_in_row_of`, `records_in_page`, `link_example`, `pagetitle`, `is_admin`, `if_authorized`, `permissions`, `remove_container`, `widget_cache_timeout`, `custom_output`, `custom_header`, `is_active`, `widget_owner`, `widget_privilege`) VALUES
(1, 1, 'header', 'default', 'body', 'div', 1, '', '0', 6, 'admin', '', '', '', '', '(#(header:Location: [root]/login|false|301)#)', '#header {\r\n    height: 28px;\r\n    background: #eeeeee;\r\n    position: relative;\r\n    border-bottom:1px solid #666666;\r\n    border-top:1px solid #666666;\r\n    text-align:center;\r\n}\r\n\r\n#main-navigation {\r\n    	position: relative;\r\n	float:left;\r\n	line-height:25px;\r\n}\r\n\r\n#main-navigation li{\r\n	float:left;\r\n	line-height:25px;\r\n	margin-right: 10px;\r\n	margin-left: 10px;\r\n}\r\n\r\n#main-navigation li a img{\r\n	margin-top:5px;\r\n}\r\n\r\n#main-navigation .aiki-icon {\r\n	margin-left:-5px;\r\n	margin-right:-10px;\r\n}\r\n\r\n#main-navigation a.active{\r\n	font-weight:bold;\r\n}\r\n\r\n#user-navigation {\r\n    	position: relative;\r\n	float:right;\r\n	line-height:25px;\r\n}\r\n\r\n#user-navigation li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#user-navigation li a, #user-navigation li strong{\r\n	margin-right: 10px;\r\n	margin-left: 10px;\r\n}\r\n\r\n#user-navigation li a img{\r\n	margin-top:5px;\r\n}\r\n\r\n#user-navigation .aiki-icon {\r\n	margin-left:-5px;\r\n	margin-right:-10px;\r\n}\r\n\r\n#user-navigation a.active{\r\n	font-weight:bold;\r\n}\r\n\r\n#tree-menu {\r\n	border-bottom: 1px dashed #d3d7cf;\r\ndisplay:block;\r\nposition:relative;\r\n}\r\n\r\n#tree-menu li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#tree-menu li a{\r\n	margin-right: 5px;\r\n	margin-left: 5px;\r\n}\r\n\r\n#tree-menu li a img{\r\n	margin-top:5px;\r\n	height:12px;\r\n	margin-right:2px;\r\n}\r\n\r\n#widget-tree {\r\n	text-align:left;\r\n}', '', 0, 0, '', 'aiki AdminPanel', 1, "	<ul id='main-navigation' class='clearfix'>\r\n		<li><a href='#' class='aiki-icon' id='aiki-icon-button'><img src='[root]/assets/apps/admin/images/aiki-icon.png' alt='Aiki'/></a></li>\r\n 		<li><a href='#' id='structur_button'>Structure</a></li>\r\n<li><a href='#' id='system_button'>System</a></li>\r\n	</ul>\r\n\r\n(ajax_a(structur_button;\r\n['[root]/index.php?widget=structur_accordion','#ui-layout-west', 'structur_accordion()']\r\n)ajax_a)\r\n\r\n(ajax_a(system_button;\r\n['[root]/index.php?widget=system_accordion','#ui-layout-west', 'system_accordion()']\r\n)ajax_a)\r\n\r\n	<ul id='user-navigation' class='clearfix'>\r\n		<li><a rev='#widget-form' href='[root]/admin_tools/edit/17/[userid]' rel='edit_record'>[username]</a>@<a href='[root]'>[root]</a>| </li> \r\n <li><a href='http://www.aikiframework.org/'>aiki framework @VERSION@.@REVISION@</a>|</li>\r\n<li><a href='[root]/admin_tools/logout'>Logout</a></li>\r\n</ul>\r\n\r\n<div id='dialog' title='About Aikiframework'>\r\n<p>\r\n<img src='[root]/assets/apps/admin/images/logo.png' alt='Logo'/>\r\n<br /><br />\r\n<h2>Aiki Framework @VERSION@.@REVISION@</h2>\r\n<br />\r\n<a href='http://www.aikiframework.org'>http://www.aikiframework.org</a>\r\n<br /><br />\r\n<h2>Credits:</h2>\r\n@AUTHORS@</p>\r\n</div>", 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(2, 1, 'terminal', 'default', 'body', 'div', 2, '', 0, 6, 'admin', '', '', '', '', '', '#terminal {\r\n    height: 300px;\r\n    left: 1px;\r\n    overflow: auto;\r\n    position: absolute;\r\n    width: 100%;\r\n}\r\n#terminal p{\r\npadding: 2px;\r\n}', '', 0, 0, '', '', 1, '', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(3, 1, 'structur_accordion', 'default', 'body', 'div', 6, '', '0', 7, 'admin', '', '', '', '', '', '', '', 0, 0, '', '', 1, '<h3><a href="#" id="urls_widgets">Urls & Widgets</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" id="create_new_widget"><img src="[root]/assets/apps/admin/images/icons/layout_add.png" alt="Create Widget"/>Create Widget</a></li>\r\n	</ul>\r\n	<div id="widgettree" class="demo"></div>\r\n</div>\r\n\r\n<h3><a href="#" id="database_forms">Databases & Forms</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" id="create_new_table"><img src="[root]/assets/apps/admin/images/icons/database.png" alt="Database"/>Create Table</a></li>\r\n		<li><a href="#" id="create_new_form"><img src="[root]/assets/apps/admin/images/icons/application_form.png" alt="Create Widget"/>Create Form</a></li>\r\n	</ul>\r\n<div id="databaseformstree" class="demo"></div>\r\n</div>\r\n', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(4, 1, 'widget_accordion', 'default', 'body', 'div', 0, '', '', 8, 'admin', '', '', '', '', '', '#breadcrumbs li{\r\n	float:left;	\r\n}\r\n\r\n#breadcrumbs li a{\r\n	float:left;\r\n}\r\n\r\n#breadcrumbs li a img{\r\n	height:12px;\r\n	margin-right:4px;\r\n	top: 5px;\r\n}\r\n\r\n#breadcrumbs li img{\r\n	float:left;\r\n	position: relative; \r\n	top: 8px;\r\n	margin-left:10px;\r\n}\r\n\r\n.codetext {\r\n	margin:0 15px 0 15px;\r\n	color:#555753;\r\n	font-size:80%;\r\n}\r\n\r\n.options-button {\r\n	background:#eeeeee;\r\n	margin:15px 15px 0 15px;\r\n	width:80px;\r\n	height:20px;\r\n	text-align:center;\r\n}\r\n\r\n.options-button a{\r\n	margin:5px;\r\n	color: #1b3b6b;\r\n}\r\n.options-button a:hover {\r\n    	text-decoration: none;\r\n}\r\n\r\n.options {\r\n	border:1px solid #eeeeee;\r\n	margin:0px 15px 0 15px;\r\n	padding:10px;\r\n	color: #1b3b6b;\r\n}\r\n#big_form {\r\n	margin:0px 15px 0 15px;\r\n}\r\ntextarea, input, select {\r\n	border:2px solid #c3c3c3;\r\n	font-family: "Courier New";\r\n	padding:3px;\r\n	color:#555753;\r\n	margin:0 15px 0 15px;\r\n	font-size:120%;\r\n	background:GhostWhite ;\r\n}\r\n\r\nfieldset {\r\n	border: 0;\r\n}\r\n\r\n.buttons {\r\n	text-align:right;\r\n}\r\n\r\n#widget_container, #authorized_select_container, #normal_select_container, #css_container, #if_authorized_container{\r\nborder: 1px solid black;\r\npadding: 3px;\r\nbackground-color: #F8F8F8\r\n}\r\n\r\n#widget-form label{\r\nborder-color:#CCCCCC;\r\nborder-style:dotted none;\r\nborder-width:1px 0 0;\r\ndisplay:block;\r\nmargin-top:16px;\r\npadding-bottom:6px;\r\npadding-top:4px;\r\nfont-weight: bold;}', '', 0, 0, '', '', 1, '<h3><a></a></h3>\r\n\r\n<div id="widget-form" class="accordion-content">\r\nYou can start building your CMS from the left menu.\r\n</div>', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(5, 1, 'edit_record', 'default', 'body', 'div', 0, '', '0', 0, 'admin_tools/edit/(.*)/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '(#(form:edit:(!(2)!):(!(3)!))#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(6, 1, 'ui-layout-north', 'default', 'body', 'div', 0, 'ui-layout-north', '1', 0, 'admin', '', '', '', '', '', '.ui-layout-pane-north {\r\n/* OVERRIDE "default styles" */\r\npadding: 0 !important;\r\noverflow: hidden !important;\r\n}', '', 0, 0, '', '', 1, '', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(7, 1, 'ui-layout-west', 'default', 'body', 'div', 3, 'ui-layout-west', '1', 0, 'admin', '', '', '', '', '', '	.ui-layout-pane-west {\r\n padding: 0 !important;\r\n		overflow: hidden !important;\r\n	}', '', 0, 0, '', '', 1, '', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(8, 1, 'ui-layout-center', 'default', 'body', 'div', 0, 'ui-layout-center', '1', 0, 'admin', '', '', '', '', '', '	.ui-layout-pane-center {\r\n		padding: 0 !important;\r\n		overflow: hidden !important;\r\n	}', '', 0, 0, '', '', 1, '', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(9, 0, 'aikiadmin_login', 'default', 'body', 'div', 0, '', '0', 0, 'login', '', '', '', '', '<script type="text/javascript" >$(function(){$("#username").focus();});</script><h2>Sign in to Aiki Framework Admin-Panel</h2><br/><br/>\r\n<img src="[root]assets/apps/admin/images/logo.png" alt="Logo" /><br /><br />\r\n<form method="post">\r\n<div><table>\r\n  <tbody>\r\n    <tr>\r\n      <td>Name:</td>\r\n      <td><input type="text" name="username" id="username"></td>\r\n    </tr>\r\n    <tr>\r\n      <td>Password:</td>\r\n      <td><input type="password" name="password" id="password"></td>\r\n    </tr>\r\n    <tr>\r\n      <td><input type="hidden" name="process" value="login"></td>\r\n      <td><input class="button" type="submit" name="submit" value="Sign in"></td>\r\n    </tr>\r\n  </tbody>\r\n</table>\r\n</div>\r\n</form> \r\n\r\n<br/><br/>\r\n\r\nAiki Framework is licensed under the GNU AGPL 3.0.<br /><a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html"><img src="[root]/assets/images/agpl.png" alt="AGPL"/></a>', '#aikiadmin_login {\r\n border:1px solid #c3c3c3;\r\n width:450px;\r\nmargin: 200px auto;\r\ntext-align:center;\r\nbackground:GhostWhite ;\r\npadding:30px;\r\n}\r\n\r\n#aikiadmin_login img{\r\nmargin:5px;\r\n}\r\n\r\n#aikiadmin_login div{\r\nwidth: 260px; \r\nmargin: 0 auto;\r\n}\r\n\r\n#aikiadmin_login table{\r\ntext-align:right;\r\nwidth: 100%;\r\n}', '', 0, 0, '', 'Login to Aiki-Admin Panel', 1, '(#(header:Location: [root]/admin|false|301)#)', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(10, 1, 'system_accordion', 'default', 'body', 'div', 6, '', '0', 0, '', '', '', '', '', '', '', '', 0, 0, '', '', 1, '<h3><a href="#" id="config">Config</a></h3>\r\n\r\n<div id="configtree" class="demo"></div>', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(11, 1, 'new_record', 'default', 'body', 'div', 0, '', '0', 0, 'admin_tools/new/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '(#(form:add:(!(2)!))#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(12, 1, 'confirmations', 'default', 'body', 'div', 0, '', '', 0, 'admin', '', '', '', '', '<div id="deletewidgetdialog" title="Delete widget">\r\n	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This widget will be permanently deleted and cannot be recovered. Are you sure?</p>\r\n</div>\r\n\r\n<div id="deleteformdialog" title="Delete Form">\r\n	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This form will be permanently deleted and cannot be recovered. Are you sure?</p>\r\n</div>', '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(13, 1, 'delete_record', 'default', 'body', 'div', 0, '', '0', 0, 'admin_tools/delete/(.*)/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '(#(form:delete:(!(2)!):(!(3)!))#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(14, 0, 'aiki_home', 'default', 'body', 'div', 0, '', '0', 0, 'homepage', '', '', '', '', '<h2>Welcome to Aiki Framework</h2><br/><br/>\r\n\r\n<img src="[root]assets/apps/admin/images/logo.png" alt="Logo" />\r\n\r\n<br/><br/>\r\n\r\nYou have successfully installed your Aiki.\r\n\r\n<br/><br/>\r\n\r\nPlease use the <a href="[root]/admin">admin panel</a> to start creating your own CMS and to change this default page.\r\n\r\n<br/><br/>\r\n\r\nFor documentation please visit <a target="_blank" href="http://www.aikiframework.org">aikiframework.org</a>.\r\n\r\n<br/><br/>\r\n\r\nAiki Framework is licensed under the GNU AGPL 3.0.<br /><a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html"><img src="[root]/assets/images/agpl.png" alt="AGPL"/></a>', '#aiki_home {\r\n\r\n border:1px solid #c3c3c3;\r\n\r\n width:450px;\r\n\r\nmargin: 200px auto;\r\n\r\ntext-align:center;\r\n\r\nbackground:GhostWhite ;\r\n\r\npadding:30px;\r\n\r\n}\r\n\r\n\r\n\r\n#aiki_home img{\r\n\r\nmargin:5px;\r\n\r\n}\r\n\r\n\r\n\r\n#aiki_home div{\r\n\r\nwidth: 260px; \r\n\r\nmargin: 0 auto;\r\n\r\n}\r\n\r\n\r\n\r\n#aiki_home table{\r\n\r\ntext-align:right;\r\n\r\nwidth: 100%;\r\n\r\n}', '', 0, 0, '', 'Aikiframework', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(15, 1, 'edit_array', 'default', 'body', 'div', 0, '', '0', 0, 'admin_tools/array/(.*)/(.*)/(.*)/(.*)/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '(#(array:edit:(!(2)!):(!(3)!):(!(4)!):(!(5)!):(!(6)!))#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(16, 1, 'auto_generate', 'default', 'body', 'div', 2, '', '0', 0, 'admin_tools/auto_generate/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '<php $aiki->bot->ShowTableStructure( (!(2)!) ); php>', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(17, 1, 'admin_javascript', 'default', 'header', '0', 3, '', '0', 0, 'admin', '', '', '', '', '', '', '', 0, 0, '', '', 1, '<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jquery.layout.min.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/css.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jstree/tree_component.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jstree/sarissa.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jstree/sarissa_ieemu_xpath.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jquery.xslt.js"></script>\r\n<script type="text/javascript" src="[root]assets/apps/admin/control_panel.js"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]/assets/javascript/jquery/plugins/jstree/tree_component.css" />\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/jquery-ui-1.7.2.custom.min.js"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]/assets/javascript/jquery/css/smoothness/jquery-ui-1.7.1.custom.css" />\r\n<script src="[root]/assets/javascript/codemirror/js/codemirror.js" type="text/javascript"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]/assets/javascript/codemirror/css/docs.css"/>\r\n<style type="text/css">\r\n.CodeMirror-line-numbers {width: 2.2em;color: #aaa;background-color: #eee;text-align: right;padding-right: .3em;font-size: 10pt;font-family: monospace;padding-top: .4em;}\r\n</style>', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(18, 0, 'global_javascript', 'default', 'header', '0', 2, '', '', 0, '*', 'admin', '', '', '', "<script type='text/javascript'  src='[root]/assets/javascript/jquery/jquery-1.4.2.min.js'></script>\r\n<script type='text/javascript' src='[root]/assets/javascript/jquery/plugins/jquery.form.js'></script>\r\n<script type='text/javascript' src='[root]/assets/javascript/aiki.js'></script>", '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(19, 0, 'style.css', 'default', 'body', '0', 0, '', '0', 0, 'globals/admin_style.css', '', '\n', '', '', 'html, body, div, span, applet, object, p, a, em, img, strong, ol, ul, li, dl, dd, dt, label, h1, h2, h3, h4, h5, h6{\n  margin: 0;\n    padding: 0;\n    border: 0;\n    outline: 0;\n    font-style: inherit;\n    font-size: 100%;\n    font-family: inherit;\n    vertical-align: baseline;\n    background: transparent;\n}\n:focus{\noutline: 0;\n}\nul{\n list-style: none;\n}\nol{\nlist-style-position: inside; \n}\nbody, html{\n   padding: 0;\n    margin: 0;\n    font-family: "Bitstream Vera Sans", Tahoma, sans-serif;\n    font-size: 9pt;\n    color: #000;\n    height: 100%;\n    background: #FFF;\n    line-height: 1;\n}\n* html #container{\nheight: 100%;\n}\nimg{\n    border: 0;\n}\na:link, a:visited{\n outline: none;\n    text-decoration: none;\n    color: #1B3B6B;\n}\na:hover, a:active{\n    text-decoration: underline;\n    color: #1B3B6B;\n}\n.clear{\n    clear: both;\n    font-size: 0.3pt;\n}\n.clearfix:after{\n  content: ".";\n  display: block;\n  clear: both;\n  visibility: hidden;\n  line-height: 0;\n  height: 0;\n}\n.clearfix{\n  display: inline-block;\n}\n* html .clearfix{\n\n}\n.tree-context{\nz-index:999; \n}\n\n\n.edit_button{\n-moz-border-radius:2px 2px 2px 2px;\nbackground:none repeat scroll 0 0 #F57900;\nborder:0 none;\nbottom:10px;\ncolor:#FFFFFF;\nfont-weight:bold;\nposition:fixed;\nright:10px;\n}\n\n#log-content {\n\tline-height: normal;\n}\n.log-content-line-error {\n\tbackground-color: #fdd;\n}\n.log-content-line-warn {\n\tbackground-color: #ffc;\n}\n.log-content-line-info {\n\tbackground-color: #d4ffd4;\n}\n.log-content-line-debug {\n\tbackground-color: #f4f4ff;\n}\n.log-content-tag-error {\n\tcolor: #800;\n}\n.log-content-tag-warn {\n\tcolor: #770;\n}\n.log-content-tag-info {\n\tcolor: #070;\n}\n.log-content-tag-debug {\n\tcolor: #008;\n}\n', '', '', 0, 0, '', '', 0, '\n', '', 0, 0, 1, 'Content-type: text/css', 1, 2, 'w'),
(20, 0, 'admin_css', 'default', 'header', '0', 1, '', '0', 0, 'admin', '', '', '', '', '<link rel="stylesheet" type="text/css" href="[root]/globals/admin_style.css" />', '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(21, 0, 'logout', 'default', 'body', 'div', 0, '', '0', 0, 'admin_tools/logout', '', '', '', '', '(#(header:Location: [root]/admin)#)', '', '', 0, 0, '', 'Aikiframework', 1, '<php $aiki->membership->LogOut(); php> <p>Logging out please wait...</p> <meta http-equiv="refresh" content="2;url=[root]/admin"> <p><a href="[root]/admin">Click here if your browser does not support redirect</a>.</p>', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(22, 0, 'global_css', 'default', 'header', '0', 2, '', '', 0, '*', 'admin', '', '', '', '', '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(23, 0, 'admin_javascript', 'default', 'header', '0', 2, '', '', 0, 'admin', '', '', '', '', "<script type='text/javascript'  src='[root]/assets/javascript/jquery/jquery-1.4.2.min.js'></script>\r\n<script type='text/javascript' src='[root]/assets/javascript/jquery/plugins/jquery.form.js'></script>\r\n<script type='text/javascript' src='[root]/assets/javascript/aiki.js'></script>", '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(24, 1, 'generate_form', 'default', 'body', 'div', 0, '', 0, 0, 'admin_tools/generate_form/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '(#(form:auto_generate:(!(2)!))#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(25, 1, 'table_tools', 'default', 'body', 'div', 1, '', 0, 0, 'admin_tools/auto_generate/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, 'table: (!(2)!)\r\n<p style="padding: 10px"><a href="[root]/admin_tools/generate_form/(!(2)!)" rel="generate_form" rev="#table_information_container">Generate Form</a> - <a href="[root]/admin_tools/auto_generate/(!(2)!)" rel="auto_generate" rev="#table_information_container">Structure</a> - <a href="[root]/admin_tools/datagrid/(!(2)!)" rel="table_datagrid" rev="#table_information_container">Browse</a> - <a href="[root]/admin_tools/newfromtable/(!(2)!)" rel="new_record_from_tablename" rev="#table_information_container">Insert</a></p>', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(26, 1, 'table_datagrid', 'default', 'body', 'div', 0, '', 0, 0, 'admin_tools/datagrid/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '<php $aiki->bot->DataGrid( (!(2)!) ); php>', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(27, 1, 'new_record_from_tablename', 'default', 'body', 'div', 0, '', 0, 0, 'admin_tools/newfromtable/(.*)', '', '', 'select id from aiki_forms where form_table like ''(!(2)!)''', '', '', '', '', 0, 0, '', '', 1, '(#(form:add:((id)):ajax)#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w');
