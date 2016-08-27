/* Insert default table records for aiki

 * Copyright (C) 2010 Aikilab

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

INSERT IGNORE INTO `aiki_dictionary` (`term_id`, `app_id`, `short_term`, `lang_en`, `lang_ar`, `lang_de`, `lang_zh`) VALUES
(1, 0, 'encoding', 'utf-8', 'utf-8', 'utf-8', ''),
(2, 0, 'added_successfully', 'Added successfully', '', '', ''),
(3, 0, 'error_inserting_into_database', 'Error inserting into database', '', '', ''),
(4, 0, 'next', 'Next >', '', '', ''),
(5, 0, 'previous', '< Previous', '', '', ''),
(6, 0, 'move_to_page', 'Move to page:', '', '', ''),
(7, 0, 'first_page', '<< First page', '', '', ''),
(8, 0, 'last_page', 'Last page >>', '', '', ''),
(9, 0, 'content', 'Content', '', '', ''),
(10, 0, 'please_fill', 'Please fill', '', '', ''),
(11, 0, 'please_enter_a_password', 'Please enter a password', '', '', ''),
(12, 0, 'the_email_address_is_not_valid', 'The email address is not valid', '', '', ''),
(13, 0, 'this_value_is_already_in_use', 'This value is already in use', '', '', ''),
(14, 0, 'error_while_uploading', 'an error occurred while uploading 0 byte file size, please go back and try again', '', '', ''),
(15, 0, 'not_valid_filename', 'Not a valid filename', '', '', ''),
(16, 0, 'file_is_already_uploaded', 'Sorry the same file is already uploaded', '', '', ''),
(17, 0, 'new_directory_created', 'new directory created:', '', '', ''),
(18, 0, 'folder_not_found', 'cannot upload file. folder not found', '', '', ''),
(19, 0, 'sorry', 'Sorry', '', '', ''),
(20, 0, 'the_file', 'the file', '', '', ''),
(21, 0, 'already_exists', 'already exists.', '', '', ''),
(22, 0, 'please_choose_a_file_to_upload', 'Please choose a file to upload', '', '', ''),
(23, 0, 'file_already_exists', '(File already exists)', '', '', ''),
(24, 0, 'not_allowed_file_name', '(Not allowed file name)', '', '', ''),
(25, 0, 'file_upload_fail', '(File upload fail)', '', '', ''),
(26, 0, 'uploaded', 'uploaded', '', '', ''),
(27, 0, 'files_out_of', 'files out of', '', '', ''),
(28, 0, 'selected_files', 'selected files', '', '', ''),
(29, 0, 'uploaded_files', 'Uploaded files:', '', '', ''),
(30, 0, 'not_uploaded_files', 'NOT uploaded files:', '', '', ''),
(31, 0, 'filename', 'Filename:', '', '', ''),
(32, 0, 'no_primary_key', 'Fatal Error. No primary key. There is nothing to do.', '', '', ''),
(33, 0, 'yes', 'Yes', '', '', ''),
(34, 0, 'no', 'No', '', '', ''),
(35, 0, 'record', 'Record', '', '', ''),
(36, 0, 'deleted_from', 'Deleted from', '', '', ''),
(37, 0, 'faild_to_edit_record', 'Faild to edit record', '', '', ''),
(38, 0, 'in', 'in', '', '', ''),
(39, 0, 'wrong_table_name', 'error: wrong table name', '', '', '');

-- ------------------------------------------------------

INSERT IGNORE INTO `aiki_forms` VALUES (1,0,'','','','aiki_widgets','widgets_simple_editor','a:16:{s:9:\"tablename\";s:12:\"aiki_widgets\";s:4:\"pkey\";s:2:\"id\";s:10:\"textinput2\";s:26:\"widget_name|SystemGOD:Name\";s:7:\"hidden3\";s:47:\"widget_site|SystemGOD:widget site:value:default\";s:13:\"staticselect4\";s:61:\"widget_target|SystemGOD:Target:custom:body>body&header>header\";s:7:\"hidden5\";s:43:\"widget_type|SystemGOD:widget type:value:div\";s:13:\"staticselect6\";s:47:\"is_father|SystemGOD:Is Father:custom:No>0&Yes>1\";s:10:\"selection7\";s:123:\"father_widget|SystemGOD:Father Widget:aiki_widgets:id:widget_name:where display_urls NOT RLIKE (admin) and is_father != (0)\";s:10:\"textinput8\";s:38:\"display_order|SystemGOD:Render Order:0\";s:10:\"textblock9\";s:36:\"display_urls|SystemGOD:Address (url)\";s:11:\"textblock10\";s:36:\"normal_select|SystemGOD:Select Query\";s:11:\"textblock11\";s:24:\"widget|SystemGOD:Content\";s:11:\"textblock12\";s:17:\"css|SystemGOD:CSS\";s:11:\"textinput13\";s:47:\"records_in_page|SystemGOD:Records per page:null\";s:14:\"staticselect14\";s:44:\"is_active|SystemGOD:Active:custom:Yes>1&No>0\";s:8:\"submit15\";s:3:\"Add\";}','',''),
(4,0,'','','','aiki_dictionary','aiki_dictionary','a:7:{s:9:\"tablename\";s:15:\"aiki_dictionary\";s:4:\"pkey\";s:7:\"term_id\";s:10:\"textinput1\";s:23:\"app_id|SystemGOD:app id\";s:10:\"textinput2\";s:31:\"short_term|SystemGOD:short term\";s:10:\"textblock3\";s:35:\"lang_english|SystemGOD:lang english\";s:10:\"textblock4\";s:33:\"lang_arabic|SystemGOD:lang arabic\";s:10:\"textblock5\";s:33:\"lang_german|SystemGOD:lang german\";}','',''),
(6,0,'','','','aiki_forms','aiki_forms','a:11:{s:9:\"tablename\";s:10:\"aiki_forms\";s:4:\"pkey\";s:2:\"id\";s:10:\"textinput1\";s:23:\"app_id|SystemGOD:app id\";s:10:\"textinput2\";s:33:\"form_method|SystemGOD:form method\";s:10:\"textinput3\";s:33:\"form_action|SystemGOD:form action\";s:10:\"textinput4\";s:27:\"form_dir|SystemGOD:form dir\";s:10:\"textinput5\";s:31:\"form_table|SystemGOD:form table\";s:10:\"textinput6\";s:29:\"form_name|SystemGOD:form name\";s:10:\"textblock7\";s:31:\"form_array|SystemGOD:form array\";s:10:\"textblock8\";s:29:\"form_html|SystemGOD:form html\";s:10:\"textblock9\";s:31:\"form_query|SystemGOD:form query\";}','',''),
(9,0,'','','','aiki_languages','aiki_languages','a:8:{s:9:\"tablename\";s:14:\"aiki_languages\";s:4:\"pkey\";s:2:\"id\";s:10:\"textinput1\";s:19:\"name|SystemGOD:name\";s:10:\"textinput2\";s:27:\"sys_name|SystemGOD:sys name\";s:10:\"textinput3\";s:31:\"short_name|SystemGOD:short name\";s:10:\"textinput4\";s:17:\"dir|SystemGOD:dir\";s:10:\"textinput5\";s:21:\"align|SystemGOD:align\";s:10:\"textinput6\";s:31:\"is_default|SystemGOD:is default\";}','',''),
(12,0,'','','','aiki_redirects','aiki_redirects','a:4:{s:9:\"tablename\";s:14:\"aiki_redirects\";s:10:\"textinput1\";s:17:\"url|SystemGOD:url\";s:10:\"textinput2\";s:27:\"redirect|SystemGOD:redirect\";s:10:\"textinput3\";s:19:\"hits|SystemGOD:hits\";}','',''),
(13,0,'','','','aiki_sites','aiki_sites','a:6:{s:9:\"tablename\";s:10:\"aiki_sites\";s:4:\"pkey\";s:7:\"site_id\";s:10:\"textinput1\";s:29:\"site_name|SystemGOD:site name\";s:10:\"textinput2\";s:37:\"site_shortcut|SystemGOD:site shortcut\";s:10:\"textinput3\";s:29:\"is_active|SystemGOD:is active\";s:10:\"textblock4\";s:43:\"if_closed_output|SystemGOD:if closed output\";}','',''),
(17,0,'','','','aiki_users','aiki_users','a:6:{s:9:\"tablename\";s:10:\"aiki_users\";s:4:\"pkey\";s:6:\"userid\";s:10:\"textinput2\";s:27:\"username|SystemGOD:username\";s:9:\"password4\";s:44:\"password|SystemGOD:password:password:md5|md5\";s:10:\"textinput6\";s:21:\"email|SystemGOD:email\";s:10:\"textinput3\";s:29:\"is_active|SystemGOD:is active\";}','',''),(18,0,'','','','aiki_users_groups','aiki_users_groups','a:6:{s:9:\"tablename\";s:17:\"aiki_users_groups\";s:4:\"pkey\";s:2:\"id\";s:10:\"textinput1\";s:23:\"app_id|SystemGOD:app id\";s:10:\"textinput2\";s:19:\"name|SystemGOD:name\";s:10:\"textinput3\";s:45:\"group_permissions|SystemGOD:group permissions\";s:10:\"textinput4\";s:33:\"group_level|SystemGOD:group level\";}','',''),(20,0,'','','','aiki_widgets','aiki_widgets','a:31:{s:9:\"tablename\";s:12:\"aiki_widgets\";s:4:\"pkey\";s:2:\"id\";s:10:\"textinput2\";s:26:\"widget_name|SystemGOD:Name\";s:10:\"selection3\";s:61:\"widget_site|SystemGOD:Site:aiki_sites:site_shortcut:site_name\";s:13:\"staticselect4\";s:61:\"widget_target|SystemGOD:Target:custom:body>body&header>header\";s:13:\"staticselect5\";s:212:\"widget_type|SystemGOD:Type:custom:div>div&none>0&span>span&paragraph>p&link>a&---html 5--->0&header>header&nav>nav&article>article&aside>aside&figure>figure&footer>footer&section>section&address>address&abbr>abbr\";s:10:\"textinput6\";s:38:\"display_order|SystemGOD:Render Order:0\";s:10:\"textinput7\";s:32:\"style_id|SystemGOD:Style (class)\";s:13:\"staticselect8\";s:47:\"is_father|SystemGOD:Is Father:custom:No>0&Yes>1\";s:10:\"selection9\";s:123:\"father_widget|SystemGOD:Father Widget:aiki_widgets:id:widget_name:where display_urls NOT RLIKE (admin) and is_father != (0)\";s:11:\"textblock10\";s:36:\"display_urls|SystemGOD:Address (URL)\";s:11:\"textblock11\";s:29:\"kill_urls|SystemGOD:Kill urls\";s:11:\"textblock12\";s:36:\"normal_select|SystemGOD:Select Query\";s:11:\"textblock13\";s:51:\"authorized_select|SystemGOD:Authorized Select Query\";s:11:\"textblock14\";s:40:\"if_no_results|SystemGOD:No Results Error\";s:11:\"textblock15\";s:24:\"widget|SystemGOD:Content\";s:11:\"textblock16\";s:17:\"css|SystemGOD:CSS\";s:11:\"textblock17\";s:36:\"nogui_widget|SystemGOD:nogui Content\";s:11:\"textinput18\";s:58:\"display_in_row_of|SystemGOD:Display results in row of:null\";s:11:\"textinput19\";s:47:\"records_in_page|SystemGOD:Records per page:null\";s:11:\"textinput20\";s:46:\"link_example|SystemGOD:Pagination Link Example\";s:11:\"textblock21\";s:30:\"pagetitle|SystemGOD:Page title\";s:14:\"staticselect22\";s:64:\"is_admin|SystemGOD:Require special permissions:custom:No>0&Yes>1\";s:11:\"textblock23\";s:45:\"if_authorized|SystemGOD:If authorized content\";s:11:\"textblock24\";s:39:\"permissions|SystemGOD:Permissions Group\";s:14:\"staticselect25\";s:61:\"remove_container|SystemGOD:Remove Container:custom:No>0&Yes>1\";s:11:\"textinput26\";s:49:\"widget_cache_timeout|SystemGOD:Cache Timeout:null\";s:14:\"staticselect27\";s:55:\"custom_output|SystemGOD:Custom Output:custom:No>0&Yes>1\";s:11:\"textblock28\";s:47:\"custom_header|SystemGOD:Send Custom http header\";s:14:\"staticselect29\";s:44:\"is_active|SystemGOD:Active:custom:Yes>1&No>0\";s:8:\"submit30\";s:4:\"Save\";}','','');

-- ------------------------------------------------------

INSERT IGNORE INTO `apps_wiki_links` (`id`, `name`, `tagstart`, `tagend`, `parlset`, `linkexample`, `dbtable`, `namecolumn`, `idcolumn`, `extrasql`, `is_extrasql_loop`) VALUES
(1, 'wikilinks', '(+(', ')+)', '', 'wiki', 'apps_wiki_text', 'title', 'id', '', 0);

-- ------------------------------------------------------

INSERT IGNORE INTO `aiki_languages` (`id`, `name`, `sys_name`, `short_name`, `dir`, `align`, `is_default`) VALUES
(1, 'Arabic', 'arabic', 'ar', 'rtl', 'right', 0),
(2, 'English', 'english', 'en', 'ltr', 'left', 1),
(3, 'German', 'german', 'de', 'ltr', 'left', 0),
(4, 'Chinese', 'chinese', 'ch', 'ltr', 'left', 0);

-- ------------------------------------------------------

INSERT IGNORE INTO `aiki_sites` (`site_id`, `site_name`, `site_shortcut`, `is_active`, `if_closed_output`) VALUES
(1, 'default', 'default', 1, '');

-- ------------------------------------------------------

INSERT IGNORE INTO `aiki_users_groups` (`id`, `app_id`, `name`, `group_permissions`, `group_level`) VALUES
(1, 0, 'System Administrators', 'SystemGOD', 1),
(2, 0, 'Modules Administrators', 'ModulesGOD', 2),
(3, 0, 'Guests', 'ViewPublished', 100),
(4, 0, 'Banned users', 'ViewPublished', 101),
(5, 0, 'Normal User', 'normal', 3);

-- ------------------------------------------------------

INSERT INTO `aiki_dictionaries` (`app_id`, `term`, `translatefrom`, `translateto`, `translation`) VALUES
(0, 'utf-8', 'en', '', ''),
(0, 'Added successfully', 'en', 'es', 'Añadido con éxito'),
(0, 'Error inserting into database', 'en', 'es', 'Error al insertar en la base de datos'),
(0, 'Next >', 'en', 'es', 'Siguiente >'),
(0, '< Previous', 'en', 'es', '< Anterior'),
(0, 'Move to page:', 'en', 'es', 'Ir a la página'),
(0, '<< First page', 'en', 'es', '<< Primera página'),
(0, 'Last page >>', 'en', 'es', 'Última página >>'),
(0, 'Content', 'en', 'es', 'Contenido'),
(0, 'Please fill', 'en', 'es', 'Por favor, rellene'),
(0, 'Please enter a password', 'en', 'es', 'Por favor, introduzca una contraseña'),
(0, 'The email address is not valid', 'en', 'es', 'El mail no es válido'),
(0, 'This value is already in use', 'en', 'es', 'Este valor ya esta en uso'),
(0, 'an error occurred while uploading 0 byte file size, please go back and try again', 'en', 'es', 'ha ocurrido un error descargando un archivo de 0 byte. Por favor,  intentelo de nuevo'),
(0, 'Not a valid filename', 'en', 'es', 'No es un fichero válido'),
(0, 'Sorry the same file is already uploaded', 'en', 'es', 'Lo siento, e mismo  fichero ya ha sido subido'),
(0, 'new directory created:', 'en', 'es', 'nuevo directorio creado'),
(0, 'cannot upload file. folder not found', 'en', 'es', 'no se puede subir el fichero, ya que no se encuentra la carpeta'),
(0, 'Sorry', 'en', 'es', 'Lo siento'),
(0, 'the file', 'en', 'es', 'el archivo'),
(0, 'already exists.', 'en', 'es', 'ya existe'),
(0, 'Please choose a file to upload', 'en', 'es', 'Por favor, seleccione el archivo a subir'),
(0, '(File already exists)', 'en', 'es', '(el fichero ya existe)'),
(0, '(Not allowed file name)', 'en', 'es', '(nombre de archivo no permitido)'),
(0, '(File upload fail)', 'en', 'es', '(error al subir fichero)'),
(0, 'uploaded', 'en', 'es', 'subido'),
(0, 'files out of', 'en', 'es', 'archivo fuera de '),
(0, 'selected files', 'en', 'es', 'archivos seleccionados'),
(0, 'Uploaded files:', 'en', 'es', 'Archivos subidos:'),
(0, 'NOT uploaded files:', 'en', 'es', 'Archivo NO subidos:'),
(0, 'Filename:', 'en', 'es', 'Archivo:'),
(0, 'Fatal Error. No primary key. There is nothing to do.', 'en', 'es', 'Error. No hay clave primaria. No hya nada que hacer'),
(0, 'Yes', 'en', 'es', 'Si'),
(0, 'No', 'en', 'es', 'No'),
(0, 'Record', 'en', 'es', 'Registro'),
(0, 'Deleted from', 'en', 'es', 'Borrado'),
(0, 'Faild to edit record', 'en', 'es', 'Error al editar registro'),
(0, 'in', 'en', 'es', 'en'),
(0, 'error: wrong table name', 'en', 'es', 'error: nombre de tabla no válido'),
(0, 'Nobody is online', 'en', 'es', 'No hay nadie conectado'),
(0, 'Logged out.', 'en', 'es', 'Desconectado.'),
(0, 'You are already logged out.', 'en', 'es', 'Ya estás desconectado'),
(0, 'To reset your password please click this link:', 'en', 'es', 'Para resetear tu contraseña pulsa sobre este enlace:'),
(0, 'An email has been sent to your address. Please follow the link to reset your password.', 'en', 'es', 'Ha sido enviado un correo electrónico a tu dirección. Por favor pulsa sobre el enlace para resetear tu contraseña. '),
(0, 'The email address and username do not match what we have on file.', 'en', 'es', 'La dirección de correo y el nombre de usuarios no están en nuestros archivos.'),
(0, 'Wrong username or password.', 'en', 'es', 'Usuario o contraseña incorrectos'),
(0, 'New Password', 'en', 'es', 'Nueva contraseña'),
(0, 'Confirm New Password', 'en', 'es', 'Confirma nueva contraseña'),
(0, 'Set Password', 'en', 'es', 'Establecer contraseña'),
(0, 'Your password has been reset. You can now log in to your account.', 'en', 'es', 'Tu contraseña ha sido guardada. Ahora puedes conectarte con tu cuenta'),
(0, 'The two passwords do not match. Please try again.', 'en', 'es', 'Las dos contraseñas no coinciden. Intántalo de nuevo.'),
(0, 'The key was incorrect or has expired.', 'en', 'es', 'La clave es incorrecta o ha expirado'),
(0, 'You must provide your username in order to reset your password.', 'en', 'es', 'Debe proporcionar un nombre de usuario para poder resetear tu contraseña'),
(0, 'You must enter the email address you used to sign up for the account.', 'en', 'es', 'Debes introducir la cuenta de correo que usas para activar  tu cuenta'),
(0, 'The user %s doesn''t exist. Make sure you typed the name correctly', 'en', 'es', 'El usuario %s no existe. Asegúrate de teclear el nombre correctamente.');

-- ------------------------------------------------------
INSERT IGNORE INTO `aiki_widgets` (`id`, `app_id`, `widget_name`, `widget_site`, `widget_target`, `widget_type`, `display_order`, `style_id`, `is_father`, `father_widget`, `display_urls`, `kill_urls`, `normal_select`, `authorized_select`, `if_no_results`, `widget`, `css`, `nogui_widget`, `display_in_row_of`, `records_in_page`, `link_example`, `pagetitle`, `is_admin`, `if_authorized`, `permissions`, `remove_container`, `widget_cache_timeout`, `custom_output`, `custom_header`, `is_active`, `widget_owner`, `widget_privilege`) VALUES
(1, 1, 'header', 'default', 'body', 'div', 1, '', '0', 6, 'admin', '', '', '', '', '(#(header:Location: [root]/login|false|301)#)', '#header {\r\n    height: 28px;\r\n    background: #eeeeee;\r\n    position: relative;\r\n    border-bottom:1px solid #666666;\r\n    border-top:1px solid #666666;\r\n    text-align:center;\r\n}\r\n\r\n#main-navigation {\r\n    	position: relative;\r\n	float:left;\r\n	line-height:25px;\r\n}\r\n\r\n#main-navigation li{\r\n	float:left;\r\n	line-height:25px;\r\n	margin-right: 10px;\r\n	margin-left: 10px;\r\n}\r\n\r\n#main-navigation li a img{\r\n	margin-top:5px;\r\n}\r\n\r\n#main-navigation .aiki-icon {\r\n	margin-left:-5px;\r\n	margin-right:-10px;\r\n}\r\n\r\n#main-navigation a.active{\r\n	font-weight:bold;\r\n}\r\n\r\n#user-navigation {\r\n    	position: relative;\r\n	float:right;\r\n	line-height:25px;\r\n}\r\n\r\n#user-navigation li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#user-navigation li a, #user-navigation li strong{\r\n	margin-right: 10px;\r\n	margin-left: 10px;\r\n}\r\n\r\n#user-navigation li a img{\r\n	margin-top:5px;\r\n}\r\n\r\n#user-navigation .aiki-icon {\r\n	margin-left:-5px;\r\n	margin-right:-10px;\r\n}\r\n\r\n#user-navigation a.active{\r\n	font-weight:bold;\r\n}\r\n\r\n#tree-menu {\r\n	border-bottom: 1px dashed #d3d7cf;\r\ndisplay:block;\r\nposition:relative;\r\n}\r\n\r\n#tree-menu li{\r\n	float:left;\r\n	line-height:25px;\r\n	\r\n}\r\n\r\n#tree-menu li a{\r\n	margin-right: 5px;\r\n	margin-left: 5px;\r\n}\r\n\r\n#tree-menu li a img{\r\n	margin-top:5px;\r\n	height:12px;\r\n	margin-right:2px;\r\n}\r\n\r\n#widget-tree {\r\n	text-align:left;\r\n}', '', 0, 0, '', 'aiki AdminPanel', 1, "	<ul id='main-navigation' class='clearfix'>\r\n		<li><a href='#' class='aiki-icon' id='aiki-icon-button'><img src='[root]/assets/apps/admin/images/aiki-icon.png' alt='Aiki'/></a></li>\r\n 		<li><a href='#' id='structur_button'>Structure</a></li>\r\n<li><a href='#' id='system_button'>System</a></li>\r\n	</ul>\r\n\r\n(ajax_a(structur_button;\r\n['[root]/index.php?widget=structur_accordion','#ui-layout-west', 'structur_accordion()']\r\n)ajax_a)\r\n\r\n(ajax_a(system_button;\r\n['[root]/index.php?widget=system_accordion','#ui-layout-west', 'system_accordion()']\r\n)ajax_a)\r\n\r\n	<ul id='user-navigation' class='clearfix'>\r\n		<li><a rev='#widget-form' href='[root]/admin_tools/edit/17/[userid]' rel='edit_record'>[username]</a>@<a href='[root]'>[root]</a><a href='http://bugs.launchpad.net/aikiframework/+filebug'>bugs?</a><a href='http://blueprints.launchpad.net/aikiframework/+addspec'>blueprints</a></li> \r\n <li><a href='http://www.aikiframework.org/'>aiki @VERSION@.@REVISION@</a></li>\r\n<li><a href='[root]/admin_tools/logout'>Logout</a></li>\r\n</ul>\r\n\r\n<div id='dialog' title='About Aikiframework'>\r\n<p>\r\n<img src='[root]/assets/apps/admin/images/logo.png' alt='Logo'/>\r\n<br /><br />\r\n<h2>Aiki Framework @VERSION@.@REVISION@</h2>\r\n<br />\r\n<a href='http://www.aikiframework.org'>http://www.aikiframework.org</a>\r\n<br /><br />\r\n<h2>Credits</h2>\r\n<p>@AUTHORS@</p></p>\r\n</div>", 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(2, 1, 'terminal', 'default', 'body', 'div', 2, '', 0, 6, 'admin', '', '', '', '', '', '#terminal {\r\n    height: 300px;\r\n    left: 1px;\r\n    overflow: auto;\r\n    position: absolute;\r\n    width: 100%;\r\n}\r\n#terminal p{\r\npadding: 2px;\r\n}', '', 0, 0, '', '', 1, '', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(3, 1, 'structur_accordion', 'default', 'body', 'div', 6, '', '0', 7, 'admin', '', '', '', '', '', '', '', 0, 0, '', '', 1, '<h3><a href="#" id="urls_widgets">Urls & Widgets</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" id="create_new_widget"><img src="[root]/assets/apps/admin/images/icons/layout_add.png" alt="Create Widget"/>Create Widget</a></li>\r\n	</ul>\r\n	<div id="widgettree" class="demo"></div>\r\n</div>\r\n\r\n<h3><a href="#" id="database_forms">Databases & Forms</a></h3>\r\n<div>\r\n	<ul id="tree-menu" class="clearfix">\r\n		<li><a href="#" id="create_new_table"><img src="[root]/assets/apps/admin/images/icons/database.png" alt="Database"/>Create Table</a></li>\r\n		<li><a href="#" id="create_new_form"><img src="[root]/assets/apps/admin/images/icons/application_form.png" alt="Create Widget"/>Create Form</a></li>\r\n	</ul>\r\n<div id="databaseformstree" class="demo"></div>\r\n</div>\r\n', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(4, 1, 'widget_accordion', 'default', 'body', 'div', 0, '', '', 8, 'admin', '', '', '', '', '', '#breadcrumbs li{\r\n	float:left;	\r\n}\r\n\r\n#breadcrumbs li a{\r\n	float:left;\r\n}\r\n\r\n#breadcrumbs li a img{\r\n	height:12px;\r\n	margin-right:4px;\r\n	top: 5px;\r\n}\r\n\r\n#breadcrumbs li img{\r\n	float:left;\r\n	position: relative; \r\n	top: 8px;\r\n	margin-left:10px;\r\n}\r\n\r\n.codetext {\r\n	margin:0 15px 0 15px;\r\n	color:#555753;\r\n	font-size:80%;\r\n}\r\n\r\n.options-button {\r\n	background:#eeeeee;\r\n	margin:15px 15px 0 15px;\r\n	width:80px;\r\n	height:20px;\r\n	text-align:center;\r\n}\r\n\r\n.options-button a{\r\n	margin:5px;\r\n	color: #1b3b6b;\r\n}\r\n.options-button a:hover {\r\n    	text-decoration: none;\r\n}\r\n\r\n.options {\r\n	border:1px solid #eeeeee;\r\n	margin:0px 15px 0 15px;\r\n	padding:10px;\r\n	color: #1b3b6b;\r\n}\r\n#big_form {\r\n	margin:0px 15px 0 15px;\r\n}\r\ntextarea, input, select {\r\n	border:2px solid #c3c3c3;\r\n	font-family: "Courier New";\r\n	padding:3px;\r\n	color:#555753;\r\n	margin:0;\r\n	font-size:120%;\r\n	background:GhostWhite ;\r\n}\r\n\r\nfieldset {\r\n	border: 0;\r\n}\r\n\r\n.buttons {\r\n	text-align:right;\r\n}\r\n\r\n#widget_container, #authorized_select_container, #normal_select_container, #css_container, #if_authorized_container{\r\nborder: 1px solid black;\r\npadding: 3px;\r\nbackground-color: #F8F8F8\r\n}\r\n\r\n#widget-form label{\r\nborder-color:#CCCCCC;\r\nborder-style:dotted none;\r\nborder-width:1px 0 0;\r\ndisplay:block;\r\nmargin-top:16px;\r\npadding-bottom:6px;\r\npadding-top:4px;\r\nfont-weight: bold;}', '', 0, 0, '', '', 1, '<h3><a></a></h3>\r\n\r\n<div id="widget-form" class="accordion-content">\r\nYou can start building your CMS from the left menu.\r\n</div>', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(5, 1, 'edit_record', 'default', 'body', 'div', 0, '', '0', 0, 'admin_tools/edit/(.*)/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '(#(form:edit:(!(2)!):(!(3)!))#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(6, 1, 'ui-layout-north', 'default', 'body', 'div', 0, 'ui-layout-north', '1', 0, 'admin', '', '', '', '', '', '.ui-layout-pane-north {\r\n/* OVERRIDE "default styles" */\r\npadding: 0 !important;\r\noverflow: hidden !important;\r\n}', '', 0, 0, '', '', 1, '', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(7, 1, 'ui-layout-west', 'default', 'body', 'div', 3, 'ui-layout-west', '1', 0, 'admin', '', '', '', '', '', '	.ui-layout-pane-west {\r\n padding: 0 !important;\r\n		overflow: hidden !important;\r\n	}', '', 0, 0, '', '', 1, '', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(8, 1, 'ui-layout-center', 'default', 'body', 'div', 0, 'ui-layout-center', '1', 0, 'admin', '', '', '', '', '', '	.ui-layout-pane-center {\r\n		padding: 0 !important;\r\n		overflow: hidden !important;\r\n	}', '', 0, 0, '', '', 1, '', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(9, 0, 'aikiadmin_login', 'default', 'body', 'div', 0, '', 0, 0, 'login', '', '', '', '', '<script type="text/javascript" >$(function(){$("#username").focus();});</script>\r\n\r\n<h1>Sign in to <em>Aiki Framework Admin-Panel</em></h1>\r\n\r\n<form method="post">\r\n(script($aiki->message->get_login_error())script)\r\n\r\n\r\n<p><label for="username">Name</label>\r\n<input type="text" name="username" id="username" class=''user-input''>\r\n\r\n<p><label for="password">Password</label>\r\n<input type="password" name="password" id="password" class=''user-input''>\r\n\r\n<input type="hidden" name="process" value="login">\r\n\r\n<p><input class="button" type="submit" name="submit" value="Sign in">\r\n</form> \r\n\r\n<div id="footer">\r\nAiki Framework is licensed under the GNU AGPL 3.0.<a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html"><img src="[root]/assets/images/agpl.png" alt="AGPL"/></a>\r\n</div>', '', '', 0, 0, '', 'Login to Aiki-Admin Panel', 1, '(#(header:Location: [root]/admin|false|301)#)', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(10, 1, 'system_accordion', 'default', 'body', 'div', 6, '', '0', 0, '', '', '', '', '', '', '', '', 0, 0, '', '', 1, '<h3><a href="#" id="config">Config</a></h3>\r\n\r\n<div id="configtree" class="demo"></div>', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(11, 1, 'new_record', 'default', 'body', 'div', 0, '', '0', 0, 'admin_tools/new/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '(#(form:add:(!(2)!))#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(12, 1, 'confirmations', 'default', 'body', 'div', 0, '', '', 0, 'admin', '', '', '', '', '<div id="deletewidgetdialog" title="Delete widget">\r\n	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This widget will be permanently deleted and cannot be recovered. Are you sure?</p>\r\n</div>\r\n\r\n<div id="deleteformdialog" title="Delete Form">\r\n	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This form will be permanently deleted and cannot be recovered. Are you sure?</p>\r\n</div>', '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(13, 1, 'delete_record', 'default', 'body', 'div', 0, '', '0', 0, 'admin_tools/delete/(.*)/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '(#(form:delete:(!(2)!):(!(3)!))#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(14, 0, 'aiki_home', 'default', 'body', 'div', 0, '', '0', 0, 'homepage', '', '', '', '', '<h2>Welcome to Aiki Framework</h2><br/><br/>\r\n\r\n<img src="[root]assets/apps/admin/images/logo.png" alt="Logo" />\r\n\r\n<br/><br/>\r\n\r\nYou have successfully installed your Aiki.\r\n\r\n<br/><br/>\r\n\r\nPlease use the <a href="[root]/admin">admin panel</a> to start creating your own CMS and to change this default page.\r\n\r\n<br/><br/>\r\n\r\nFor documentation please visit <a target="_blank" href="http://www.aikiframework.org">aikiframework.org</a>.\r\n\r\n<br/><br/>\r\n\r\nAiki Framework is licensed under the GNU AGPL 3.0.<br /><a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html"><img src="[root]/assets/images/agpl.png" alt="AGPL"/></a>', '#aiki_home {\r\n\r\n border:1px solid #c3c3c3;\r\n\r\n width:450px;\r\n\r\nmargin: auto;\r\n\r\ntext-align:center;\r\n\r\nbackground:GhostWhite ;\r\n\r\npadding:30px;\r\n\r\nposition: absolute;\r\ntop: 0;\r\nleft: 0;\r\nright: 0;\r\nbottom: 0;\r\nheight: 395px;\r\n}\r\n\r\n\r\n\r\n#aiki_home img{\r\n\r\nmargin:5px;\r\n\r\n}\r\n\r\n\r\n\r\n#aiki_home div{\r\n\r\nwidth: 260px; \r\n\r\nmargin: 0 auto;\r\n\r\n}\r\n\r\n\r\n\r\n#aiki_home table{\r\n\r\ntext-align:right;\r\n\r\nwidth: 100%;\r\n\r\n}', '', 0, 0, '', 'Aikiframework', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(15, 1, 'edit_array', 'default', 'body', 'div', 0, '', '0', 0, 'admin_tools/array/(.*)/(.*)/(.*)/(.*)/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '(#(array:edit:(!(2)!):(!(3)!):(!(4)!):(!(5)!):(!(6)!))#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(16, 1, 'auto_generate', 'default', 'body', 'div', 2, '', '0', 0, 'admin_tools/auto_generate/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '<php $aiki->Bot->ShowTableStructure( (!(2)!) ); php>', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(17, 1, 'admin_javascript', 'default', 'header', '0', 3, '', '0', 0, 'admin', '', '', '', '', '', '', '', 0, 0, '', '', 1, '<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jquery.layout.min.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/css.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jstree/tree_component.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jstree/sarissa.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jstree/sarissa_ieemu_xpath.js"></script>\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/plugins/jquery.xslt.js"></script>\r\n<script type="text/javascript" src="[root]assets/apps/admin/control_panel.js"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]/assets/javascript/jquery/plugins/jstree/tree_component.css" />\r\n<script type="text/javascript" src="[root]/assets/javascript/jquery/jquery-ui-1.7.2.custom.min.js"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]/assets/javascript/jquery/css/smoothness/jquery-ui-1.7.1.custom.css" />\r\n<script src="[root]/assets/javascript/codemirror/js/codemirror.js" type="text/javascript"></script>\r\n<link rel="stylesheet" type="text/css" href="[root]/assets/javascript/codemirror/css/docs.css"/>\r\n<style type="text/css">\r\n.CodeMirror-line-numbers {width: 2.2em;color: #aaa;background-color: #eee;text-align: right;padding-right: .3em;font-size: 10pt;font-family: monospace;padding-top: .4em;}\r\n</style>', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(18, 0, 'global_javascript', 'default', 'header', '0', 2, '', '', 0, '*', 'admin', '', '', '', "<script type='text/javascript'  src='[root]/assets/javascript/jquery/jquery-1.4.2.min.js'></script>\r\n<script type='text/javascript' src='[root]/assets/javascript/jquery/plugins/jquery.form.js'></script>\r\n<script type='text/javascript' src='[root]/assets/javascript/aiki.js'></script>", '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(19, 0, 'style.css', 'default', 'body', '0', 0, '', '0', 0, 'globals/admin_style.css', '', '\n', '', '', 'html, body, div, span, applet, object, p, a, em, img, strong, ol, ul, li, dl, dd, dt, label, h1, h2, h3, h4, h5, h6{\n  margin: 0;\n    padding: 0;\n    border: 0;\n    outline: 0;\n    font-style: inherit;\n    font-size: 100%;\n    font-family: inherit;\n    vertical-align: baseline;\n    background: transparent;\n}\n:focus{\noutline: 0;\n}\nul{\n list-style: none;\n}\nol{\nlist-style-position: inside; \n}\nbody, html{\n   padding: 0;\n    margin: 0;\n    font-family: "Bitstream Vera Sans", Tahoma, sans-serif;\n    font-size: 9pt;\n    color: #000;\n    height: 100%;\n    background: #FFF;\n    line-height: 1;\n}\n* html #container{\nheight: 100%;\n}\nimg{\n    border: 0;\n}\na:link, a:visited{\n outline: none;\n    text-decoration: none;\n    color: #1B3B6B;\n}\na:hover, a:active{\n    text-decoration: underline;\n    color: #1B3B6B;\n}\n.clear{\n    clear: both;\n    font-size: 0.3pt;\n}\n.clearfix:after{\n  content: ".";\n  display: block;\n  clear: both;\n  visibility: hidden;\n  line-height: 0;\n  height: 0;\n}\n.clearfix{\n  display: inline-block;\n}\n* html .clearfix{\n\n}\n.tree-context{\nz-index:999; \n}\n\n\n.edit_button{\n-moz-border-radius:2px 2px 2px 2px;\nbackground:none repeat scroll 0 0 #F57900;\nborder:0 none;\nbottom:0;\ncolor:#FFFFFF;\nfont-weight:bold;\nposition:fixed;\nright:3%;\nwidth:8%;\nheight:35px;\nopacity:0.8;\nfilter:alpha(opacity=80);\nfont-weight:16pt;\ncursor:pointer;\n}\n\n.edit_button a:hover {text-decoration: underline;}\n.edit_button:hover { opacity:1.0; filter:alpha(opacity=100);}\n#log-content {\n\tline-height: normal;\n}\n.log-content-line-error {\n\tbackground-color: #fdd;\n}\n.log-content-line-warn {\n\tbackground-color: #ffc;\n}\n.log-content-line-info {\n\tbackground-color: #d4ffd4;\n}\n.log-content-line-debug {\n\tbackground-color: #f4f4ff;\n}\n.log-content-tag-error {\n\tcolor: #800;\n}\n.log-content-tag-warn {\n\tcolor: #770;\n}\n.log-content-tag-info {\n\tcolor: #070;\n}\n.log-content-tag-debug {\n\tcolor: #008;\n}\n', '', '', 0, 0, '', '', 0, '\n', '', 0, 0, 1, 'Content-type: text/css', 1, 2, 'w'),
(20, 0, 'admin_css', 'default', 'header', '0', 1, '', '0', 0, 'admin', '', '', '', '', '<link rel="stylesheet" type="text/css" href="[root]/globals/admin_style.css" />', '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(21, 0, 'logout', 'default', 'body', 'div', 0, '', '0', 0, 'admin_tools/logout', '', '', '', '', '(#(header:Location: [root]/admin)#)', '', '', 0, 0, '', 'Aikiframework', 1, '<php $aiki->membership->LogOut(); php> <p>Logging out please wait...</p> <meta http-equiv="refresh" content="2;url=[root]/admin"> <p><a href="[root]/admin">Click here if your browser does not support redirect</a>.</p>', 'SystemGOD', 0, 0, 0, '', 1, 2, 'w'),
(22, 0, 'global_css', 'default', 'header', '0', 2, '', '', 0, '*', 'admin', '', '', '', '', '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(23, 0, 'admin_javascript', 'default', 'header', '0', 2, '', '', 0, 'admin', '', '', '', '', "<script type='text/javascript'  src='[root]/assets/javascript/jquery/jquery-1.4.2.min.js'></script>\r\n<script type='text/javascript' src='[root]/assets/javascript/jquery/plugins/jquery.form.js'></script>\r\n<script type='text/javascript' src='[root]/assets/javascript/aiki.js'></script>", '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(24, 1, 'generate_form', 'default', 'body', 'div', 0, '', 0, 0, 'admin_tools/generate_form/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '(#(form:auto_generate:(!(2)!))#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(25, 1, 'table_tools', 'default', 'body', 'div', 1, '', 0, 0, 'admin_tools/auto_generate/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, 'table: (!(2)!)\r\n<p style="padding: 10px"><a href="[root]/admin_tools/generate_form/(!(2)!)" rel="generate_form" rev="#table_information_container">Generate Form</a> - <a href="[root]/admin_tools/auto_generate/(!(2)!)" rel="auto_generate" rev="#table_information_container">Structure</a> - <a href="[root]/admin_tools/datagrid/(!(2)!)" rel="table_datagrid" rev="#table_information_container">Browse</a> - <a href="[root]/admin_tools/newfromtable/(!(2)!)" rel="new_record_from_tablename" rev="#table_information_container">Insert</a></p>', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(26, 1, 'table_datagrid', 'default', 'body', 'div', 0, '', 0, 0, 'admin_tools/datagrid/(.*)', '', '', '', '', '', '', '', 0, 0, '', '', 1, '<php $aiki->Bot->DataGrid( (!(2)!) ); php>', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(27, 1, 'new_record_from_tablename', 'default', 'body', 'div', 0, '', 0, 0, 'admin_tools/newfromtable/(.*)', '', '', 'select id from aiki_forms where form_table like ''(!(2)!)''', '', '', '', '', 0, 0, '', '', 1, '(#(form:add:((id)):ajax)#)', 'SystemGOD', 0, 0, 1, '', 1, 2, 'w'),
(28, 0, 'error_404', 'default', 'body', 'div', 0, '', 0, 0, 'error_404', '', '', '', '', '<h1>404 Page Not Found</h1><p>Please visit <a href="[root]">Home page</a> so you may find what you are looking for.</p>', '',  '', 0, 0, '', 'Aikiframework', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(29, 0, 'aikiadmin_login_theme', 'default', 'header', '', 0, '', 0, 0, 'login', '', '\r\n', '', '', '<link rel=''stylesheet'' type=''text/css''\r\nhref=''[root]/assets/themes/default/login.css''>\r\n', '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 1, 2, 'w'),
(100, 1, 'fake', 'default', 'body', 'div', 0, '', 0, 0, '', '', '', '', '', '', '', '', 0, 0, '', '', 0, '', '', 0, 0, 0, '', 0, 0, 'w');
