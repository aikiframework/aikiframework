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
(1, 0, 'global_settings', 'a:9:{s:4:"site";s:7:"default";s:3:"url";s:@AIKI_SITE_URL_LEN@:"@AIKI_SITE_URL@";s:13:"cookie_domain";s:0:"";s:13:"default_chmod";s:4:"0777";s:11:"pretty_urls";s:1:"1";s:16:"default_language";s:2:"en";s:19:"default_time_format";s:9:"d - m - Y";s:8:"site_dir";s:3:"ltr";s:19:"language_short_name";s:2:"en";}'),
(2, 0, 'database_settings', 'a:6:{s:7:"db_type";s:5:"mysql";s:10:"disk_cache";s:1:"1";s:13:"cache_timeout";s:2:"24";s:9:"cache_dir";s:5:"cache";s:13:"cache_queries";s:1:"1";s:16:"charset_encoding";s:4:"utf8";}'),
(3, 0, 'paths_settings', 'a:1:{s:10:"top_folder";s:@PKG_DATA_DIR_LEN@:"@PKG_DATA_DIR@";}'),
(4, 0, 'images_settings', 'a:4:{s:7:"max_res";s:3:"650";s:20:"default_photo_module";s:18:"apps_photo_archive";s:23:"store_native_extensions";s:4:"true";s:13:"new_extension";s:5:".aiki";}'),
(5, 0, 'admin_settings', 'a:1:{s:17:"show_edit_widgets";s:1:"0";}'),
(6, 0, 'upload_settings', 'a:4:{s:18:"allowed_extensions";s:20:"jpg|gif|png|jpeg|svg";s:11:"upload_path";s:15:"assets/uploads/";s:22:"plupload_max_file_size";s:4:"10mb";s:13:"plupload_path";s:15:"assets/uploads/";}'),
(7, 0, 'revisions_settings', 'a:1:{s:24:"send_email_notifications";s:5:"false";}'),
(8, 0, 'log_settings', 'a:3:{s:7:"log_dir";s:3:"log";s:8:"log_file";s:8:"aiki.log";s:9:"log_level";s:4:"NONE";}');

-- ------------------------------------------------------

DELETE FROM `aiki_users` WHERE username = '@ADMIN_USER@';

-- ------------------------------------------------------

INSERT IGNORE INTO `aiki_users` (`userid`, `username`, `full_name`, `country`, `sex`, `job`, `password`, `usergroup`, `email`, `avatar`, `homepage`, `first_ip`, `first_login`, `last_login`, `last_ip`, `user_permissions`, `maillist`, `logins_number`, `randkey`, `is_active`) VALUES
(1, 'guest', 'guest', '', '', '', '', 3, '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', 0, 0, '', 1),
(NULL, '@ADMIN_USER@', '@ADMIN_NAME@', '', '', '', '@ADMIN_PASS@', 1, '@ADMIN_MAIL@', '', '', '', '', '', '', '', 0, 0, '', 1);
