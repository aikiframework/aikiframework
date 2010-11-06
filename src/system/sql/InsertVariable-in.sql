
INSERT INTO `aiki_config` (`config_id`, `app_id`, `config_type`, `config_data`) VALUES
(1, 0, 'global_settings', 'a:9:{s:4:"site";s:7:"default";s:3:"url";s:@AIKI_SITE_URL_LEN@:"@AIKI_SITE_URL@";s:13:"cookie_domain";s:0:"";s:13:"default_chmod";s:4:"0777";s:11:"pretty_urls";s:1:"1";s:16:"default_language";s:7:"english";s:19:"default_time_format";s:9:"d - m - Y";s:8:"site_dir";s:3:"ltr";s:19:"language_short_name";s:2:"en";}'),
(2, 0, 'database_settings', 'a:6:{s:7:"db_type";s:5:"mysql";s:10:"disk_cache";s:1:"1";s:13:"cache_timeout";s:2:"24";s:9:"cache_dir";s:5:"cache";s:13:"cache_queries";s:1:"1";s:16:"charset_encoding";s:4:"utf8";}'),
(3, 0, 'paths_settings', 'a:1:{s:10:"top_folder";s:@PKG_DATA_DIR_LEN@:"@PKG_DATA_DIR@";}'),
(4, 0, 'images_settings', 'a:4:{s:7:"max_res";s:3:"650";s:20:"default_photo_module";s:18:"apps_photo_archive";s:23:"store_native_extensions";s:4:"true";s:13:"new_extension";s:5:".aiki";}'),
(5, 0, 'admin_settings', 'a:1:{s:17:"show_edit_widgets";s:1:"0";}'),
(6, 0, 'upload_settings', 'a:4:{s:18:"allowed_extensions";s:20:"jpg|gif|png|jpeg|svg";s:11:"upload_path";s:15:"assets/uploads/";s:22:"plupload_max_file_size";s:4:"10mb";s:13:"plupload_path";s:15:"assets/uploads/";}');

-- ------------------------------------------------------

INSERT INTO `aiki_users` (`userid`, `username`, `full_name`, `country`, `sex`, `job`, `password`, `usergroup`, `email`, `avatar`, `homepage`, `first_ip`, `first_login`, `last_login`, `last_ip`, `user_permissions`, `maillist`, `logins_number`, `randkey`, `is_active`) VALUES
(1, 'guest', 'guest', '', '', '', '', 3, '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', 0, 0, '', 0),
(2, 'admin', 'admin', '', '', '', '74be16979710d4c4e7c6647856088456', 1, '', '', '', '', '', '', '', '', 0, 0, '', 0);
