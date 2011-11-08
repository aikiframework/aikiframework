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

INSERT IGNORE INTO `aiki_dictionary` (`term_id`, `app_id`, `short_term`, `lang_english`, `lang_arabic`, `lang_german`, `lang_chinese`) VALUES
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

INSERT IGNORE INTO `aiki_forms` (`id`, `app_id`, `form_method`, `form_action`, `form_dir`, `form_table`, `form_name`, `form_array`, `form_html`, `form_query`) VALUES
(1, 0, '', '', '', 'aiki_widgets', 'widgets_simple_editor', 'a:16:{s:9:"tablename";s:12:"aiki_widgets";s:4:"pkey";s:2:"id";s:10:"textinput2";s:26:"widget_name|SystemGOD:Name";s:7:"hidden3";s:47:"widget_site|SystemGOD:widget site:value:default";s:13:"staticselect4";s:61:"widget_target|SystemGOD:Target:custom:body>body&header>header";s:7:"hidden5";s:43:"widget_type|SystemGOD:widget type:value:div";s:13:"staticselect6";s:47:"is_father|SystemGOD:Is Father:custom:No>0&Yes>1";s:10:"selection7";s:123:"father_widget|SystemGOD:Father Widget:aiki_widgets:id:widget_name:where display_urls NOT RLIKE (admin) and is_father != (0)";s:10:"textinput8";s:36:"display_order|SystemGOD:Render Order";s:10:"textblock9";s:36:"display_urls|SystemGOD:Address (url)";s:11:"textblock10";s:36:"normal_select|SystemGOD:Select Query";s:11:"textblock11";s:24:"widget|SystemGOD:Content";s:11:"textblock12";s:17:"css|SystemGOD:CSS";s:11:"textinput13";s:42:"records_in_page|SystemGOD:Records per page";s:14:"staticselect14";s:44:"is_active|SystemGOD:Active:custom:Yes>1&No>0";s:6:"submit";s:3:"Add";}', '', ''),
(4, 0, '', '', '', 'aiki_dictionary', 'aiki_dictionary', 'a:7:{s:9:"tablename";s:15:"aiki_dictionary";s:4:"pkey";s:7:"term_id";s:10:"textinput1";s:23:"app_id|SystemGOD:app id";s:10:"textinput2";s:31:"short_term|SystemGOD:short term";s:10:"textblock3";s:35:"lang_english|SystemGOD:lang english";s:10:"textblock4";s:33:"lang_arabic|SystemGOD:lang arabic";s:10:"textblock5";s:33:"lang_german|SystemGOD:lang german";}', '', ''),
(6, 0, '', '', '', 'aiki_forms', 'aiki_forms', 'a:11:{s:9:"tablename";s:10:"aiki_forms";s:4:"pkey";s:2:"id";s:10:"textinput1";s:23:"app_id|SystemGOD:app id";s:10:"textinput2";s:33:"form_method|SystemGOD:form method";s:10:"textinput3";s:33:"form_action|SystemGOD:form action";s:10:"textinput4";s:27:"form_dir|SystemGOD:form dir";s:10:"textinput5";s:31:"form_table|SystemGOD:form table";s:10:"textinput6";s:29:"form_name|SystemGOD:form name";s:10:"textblock7";s:31:"form_array|SystemGOD:form array";s:10:"textblock8";s:29:"form_html|SystemGOD:form html";s:10:"textblock9";s:31:"form_query|SystemGOD:form query";}', '', ''),
(9, 0, '', '', '', 'aiki_languages', 'aiki_languages', 'a:8:{s:9:"tablename";s:14:"aiki_languages";s:4:"pkey";s:2:"id";s:10:"textinput1";s:19:"name|SystemGOD:name";s:10:"textinput2";s:27:"sys_name|SystemGOD:sys name";s:10:"textinput3";s:31:"short_name|SystemGOD:short name";s:10:"textinput4";s:17:"dir|SystemGOD:dir";s:10:"textinput5";s:21:"align|SystemGOD:align";s:10:"textinput6";s:31:"is_default|SystemGOD:is default";}', '', ''),
(12, 0, '', '', '', 'aiki_redirects', 'aiki_redirects', 'a:4:{s:9:"tablename";s:14:"aiki_redirects";s:10:"textinput1";s:17:"url|SystemGOD:url";s:10:"textinput2";s:27:"redirect|SystemGOD:redirect";s:10:"textinput3";s:19:"hits|SystemGOD:hits";}', '', ''),
(13, 0, '', '', '', 'aiki_sites', 'aiki_sites', 'a:6:{s:9:"tablename";s:10:"aiki_sites";s:4:"pkey";s:7:"site_id";s:10:"textinput1";s:29:"site_name|SystemGOD:site name";s:10:"textinput2";s:37:"site_shortcut|SystemGOD:site shortcut";s:10:"textinput3";s:29:"is_active|SystemGOD:is active";s:10:"textblock4";s:43:"if_closed_output|SystemGOD:if closed output";}', '', ''),
(17, 0, '', '', '', 'aiki_users', 'aiki_users', 'a:6:{s:9:"tablename";s:10:"aiki_users";s:4:"pkey";s:6:"userid";s:10:"textinput2";s:27:"username|SystemGOD:username";s:9:"password4";s:44:"password|SystemGOD:password:password:md5|md5";s:10:"textinput6";s:21:"email|SystemGOD:email";s:10:"textinput3";s:29:"is_active|SystemGOD:is active";}', '', ''),
(18, 0, '', '', '', 'aiki_users_groups', 'aiki_users_groups', 'a:6:{s:9:"tablename";s:17:"aiki_users_groups";s:4:"pkey";s:2:"id";s:10:"textinput1";s:23:"app_id|SystemGOD:app id";s:10:"textinput2";s:19:"name|SystemGOD:name";s:10:"textinput3";s:45:"group_permissions|SystemGOD:group permissions";s:10:"textinput4";s:33:"group_level|SystemGOD:group level";}', '', ''),
(20, 0, '', '', '', 'aiki_widgets', 'aiki_widgets', 'a:31:{s:9:"tablename";s:12:"aiki_widgets";s:4:"pkey";s:2:"id";s:10:"textinput2";s:26:"widget_name|SystemGOD:Name";s:10:"selection3";s:61:"widget_site|SystemGOD:Site:aiki_sites:site_shortcut:site_name";s:13:"staticselect4";s:61:"widget_target|SystemGOD:Target:custom:body>body&header>header";s:13:"staticselect5";s:212:"widget_type|SystemGOD:Type:custom:div>div&none>0&span>span&paragraph>p&link>a&---html 5--->0&header>header&nav>nav&article>article&aside>aside&figure>figure&footer>footer&section>section&address>address&abbr>abbr";s:10:"textinput6";s:36:"display_order|SystemGOD:Render Order";s:10:"textinput7";s:32:"style_id|SystemGOD:Style (class)";s:13:"staticselect8";s:47:"is_father|SystemGOD:Is Father:custom:No>0&Yes>1";s:10:"selection9";s:123:"father_widget|SystemGOD:Father Widget:aiki_widgets:id:widget_name:where display_urls NOT RLIKE (admin) and is_father != (0)";s:11:"textblock10";s:36:"display_urls|SystemGOD:Address (URL)";s:11:"textblock11";s:29:"kill_urls|SystemGOD:Kill urls";s:11:"textblock12";s:36:"normal_select|SystemGOD:Select Query";s:11:"textblock13";s:51:"authorized_select|SystemGOD:Authorized Select Query";s:11:"textblock14";s:40:"if_no_results|SystemGOD:No Results Error";s:11:"textblock15";s:24:"widget|SystemGOD:Content";s:11:"textblock16";s:17:"css|SystemGOD:CSS";s:11:"textblock17";s:36:"nogui_widget|SystemGOD:nogui Content";s:11:"textinput18";s:53:"display_in_row_of|SystemGOD:Display results in row of";s:11:"textinput19";s:42:"records_in_page|SystemGOD:Records per page";s:11:"textinput20";s:46:"link_example|SystemGOD:Pagination Link Example";s:11:"textblock21";s:30:"pagetitle|SystemGOD:Page title";s:14:"staticselect22";s:64:"is_admin|SystemGOD:Require special permissions:custom:No>0&Yes>1";s:11:"textblock23";s:45:"if_authorized|SystemGOD:If authorized content";s:11:"textblock24";s:39:"permissions|SystemGOD:Permissions Group";s:14:"staticselect25";s:61:"remove_container|SystemGOD:Remove Container:custom:No>0&Yes>1";s:11:"textinput26";s:44:"widget_cache_timeout|SystemGOD:Cache Timeout";s:14:"staticselect27";s:55:"custom_output|SystemGOD:Custom Output:custom:No>0&Yes>1";s:11:"textblock28";s:47:"custom_header|SystemGOD:Send Custom http header";s:14:"staticselect29";s:44:"is_active|SystemGOD:Active:custom:Yes>1&No>0";s:6:"submit";s:4:"Save";}', '', '');

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
