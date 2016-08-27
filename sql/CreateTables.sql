/* Create default table structures for aiki

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

CREATE TABLE IF NOT EXISTS aiki_config (
  config_id int(11) unsigned NOT NULL AUTO_INCREMENT,
  app_id int(11) NOT NULL,
  config_type varchar(255) default NULL,
  config_data mediumtext,
  PRIMARY KEY (config_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_settings (
  setting_id int(11) NOT NULL AUTO_INCREMENT,
  setting_name varchar(32) NOT NULL,
  setting_description text NOT NULL,
  setting_group text NOT NULL,
  setting_edit text NOT NULL,
  setting_autoload int(11) NOT NULL,
  PRIMARY KEY (setting_id),
  KEY setting_name (setting_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_configs (
  config_id int(11) NOT NULL AUTO_INCREMENT,
  config_name varchar(32) NOT NULL,
  config_value text NOT NULL,
  config_selector varchar(94) NOT NULL,
  config_important int(11) NOT NULL,
  config_weight int(11) NOT NULL,
  PRIMARY KEY (config_id),
  KEY config_name (config_name),
  KEY config_important (config_important),
  KEY config_weight (config_weight)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_dictionary (
  term_id int(11) NOT NULL AUTO_INCREMENT,
  app_id int(11) NOT NULL,
  short_term varchar(255) NOT NULL,
  lang_en text NOT NULL,
  lang_ar text NOT NULL,
  lang_de text NOT NULL,
  lang_zh text NOT NULL,
  PRIMARY KEY (term_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_forms (
  id int(11) NOT NULL AUTO_INCREMENT,
  app_id int(11) NOT NULL,
  form_method varchar(5) NOT NULL,
  form_action varchar(255) NOT NULL,
  form_dir varchar(155) NOT NULL,
  form_table varchar(255) NOT NULL,
  form_name varchar(255) NOT NULL,
  form_array text NOT NULL,
  form_html text NOT NULL,
  form_query text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_events (
  id int(11) NOT NULL AUTO_INCREMENT,
  event text NOT NULL,
  username varchar(255) NOT NULL,
  widgetid int(11) NOT NULL,
  timestarted timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_flow (
  id int(11) NOT NULL AUTO_INCREMENT,
  table_name varchar(255) NOT NULL,
  record_number int(11) NOT NULL,
  data text NOT NULL,
  date varchar(255) NOT NULL,
  username varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS apps_wiki_links (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(250) NOT NULL default '',
  tagstart varchar(250) NOT NULL default '',
  tagend varchar(250) NOT NULL default '',
  parlset varchar(250) NOT NULL default '',
  linkexample varchar(250) NOT NULL default '',
  dbtable varchar(250) NOT NULL default '',
  namecolumn varchar(250) NOT NULL default '',
  idcolumn varchar(250) NOT NULL default '',
  extrasql varchar(255) NOT NULL default '',
  is_extrasql_loop int(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_languages (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  sys_name varchar(255) NOT NULL,
  short_name varchar(9) NOT NULL,
  dir varchar(9) NOT NULL,
  align varchar(10) NOT NULL,
  is_default int(1) NOT NULL default '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_dictionaries (
  app_id int(11) NOT NULL,
  term varchar(120) NOT NULL,
  translatefrom varchar(5) NOT NULL,
  translateto varchar(5) NOT NULL,
  translation varchar(120) NOT NULL,
  KEY term (term),
  KEY translatefrom (translatefrom),
  KEY translateto (translateto)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_redirects (
  id int(11) NOT NULL AUTO_INCREMENT,
  url varchar(255) NOT NULL,
  redirect varchar(255) NOT NULL,
  hits int(11) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY url (url)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_views (
  view_id int(11) NOT NULL AUTO_INCREMENT,
  view_name varchar(32) NOT NULL,
  view_site varchar(32) NOT NULL,
  view_active int(11) NOT NULL,
  view_prefix varchar(32) NOT NULL,
  view_use_prefix int(11) NOT NULL,
  view_url varchar(255) NOT NULL,
  view_short_description text NOT NULL,
  view_description text NOT NULL,
  PRIMARY KEY (view_id),
  KEY view_prefix (view_prefix),
  KEY view_site (view_site)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_plugins (
  plugin_id int(11) NOT NULL AUTO_INCREMENT,
  plugin_name varchar(64) NOT NULL,
  plugin_class_name varchar(64) NOT NULL,
  plugin_short_description text NOT NULL,
  plugin_description text NOT NULL,
  plugin_author varchar(64) NOT NULL,
  plugin_version varchar(32) NOT NULL,
  plugin_file varchar(96) NOT NULL,
  plugin_state varchar(12) NOT NULL,
  plugin_default_values text NOT NULL,
  PRIMARY KEY (plugin_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_plugin_configurations (
  plconf_id int(11) NOT NULL AUTO_INCREMENT,
  plconf_active varchar(11) NOT NULL,
  plconf_plugin_id int(11) NOT NULL,
  plconf_routes text NOT NULL,
  plconf_priority int(11) NOT NULL,
  plconf_values text NOT NULL,
  PRIMARY KEY (plconf_id),
  KEY plconf_plugin_id (plconf_plugin_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_sites (
  site_id int(11) NOT NULL AUTO_INCREMENT,
  site_name varchar(255) NOT NULL,
  site_shortcut varchar(255) NOT NULL,
  is_active int(1) NOT NULL,
  if_closed_output text NOT NULL,
  site_default_language varchar(5) NOT NULL DEFAULT '',
  site_languages text NOT NULL,
  widget_language varchar(5) NOT NULL,
  site_prefix varchar(80) NOT NULL DEFAULT '',
  site_default_view varchar(32) NOT NULL,
  site_engine varchar(32) NOT NULL DEFAULT 'aiki',
  site_engine_parameters text NOT NULL,      
  PRIMARY KEY (site_id),
  KEY site_prefix (site_prefix)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS apps_wiki_templates (
  id int(11) NOT NULL AUTO_INCREMENT,
  app_id int(11) NOT NULL,
  template_name varchar(255) NOT NULL,
  template_input text NOT NULL,
  template_output text NOT NULL,
  PRIMARY KEY (id),
  KEY template_name (template_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_revisions (
  id int(11) NOT NULL AUTO_INCREMENT,
  table_name varchar(255) NOT NULL,
  record_number int(11) NOT NULL,
  data text NOT NULL,
  changes text NOT NULL,
  date varchar(255) NOT NULL,
  username varchar(255) NOT NULL,
  revision int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_users (
  userid int(9) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(100) NOT NULL default '',
  full_name varchar(255) NOT NULL,
  country varchar(255) NOT NULL,
  sex varchar(25) NOT NULL,
  job varchar(255) NOT NULL,
  password varchar(100) NOT NULL default '',
  usergroup int(10) NOT NULL default '0',
  email varchar(100) NOT NULL default '',
  avatar varchar(255) NOT NULL,
  homepage varchar(100) NOT NULL default '',
  first_ip varchar(40) NOT NULL default '0',
  first_login datetime NOT NULL,
  last_login datetime NOT NULL,
  last_ip varchar(40) NOT NULL,
  user_permissions text NOT NULL,
  maillist int(1) NOT NULL,
  logins_number int(11) NOT NULL,
  randkey varchar(255) NOT NULL,
  is_active int(5) NOT NULL default '1',
  PRIMARY KEY (userid),
  UNIQUE KEY username (username)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_users_groups (
  id int(3) NOT NULL AUTO_INCREMENT,
  app_id int(11) NOT NULL,
  name varchar(255) NOT NULL,
  group_permissions varchar(255) NOT NULL,
  group_level int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_users_sessions (
  session_id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  user_name varchar(255) NOT NULL,
  session_start int(11) NOT NULL,
  last_hit int(11) NOT NULL,
  user_session varchar(255) NOT NULL,
  hits int(11) NOT NULL,
  user_ip varchar(100) NOT NULL,
  last_ip varchar(100) NOT NULL,
  PRIMARY KEY (session_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_sessions (
  session_id varchar(255) NOT NULL,
  session_data varchar(255) NOT NULL,
  session_time int(11) NOT NULL,
  PRIMARY KEY (session_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_widgets (
  id int(11) NOT NULL AUTO_INCREMENT,
  app_id int(11) default 0,
  widget_name varchar(128),
  widget_site varchar(255),
  widget_target varchar(128),
  widget_type varchar(255),
  display_order int(11) default 0,
  style_id varchar(255) default '',
  is_father int(1),
  father_widget int(11),
  display_urls text,
  kill_urls text,
  normal_select text,
  authorized_select text,
  if_no_results text,
  widget text,
  css text,
  nogui_widget text,
  display_in_row_of int(11),
  records_in_page int(11),
  link_example varchar(255),
  pagetitle text,
  is_admin tinyint(1) NOT NULL default 0,
  if_authorized text,
  permissions text,
  remove_container int(1) NOT NULL default 0,
  widget_cache_timeout int(11),
  custom_output int(1) NOT NULL default 0,
  custom_header text,
  is_active int(1) NOT NULL default 1,
  widget_owner int(9) unsigned NOT NULL default 2,
  widget_privilege varchar(1) NOT NULL default 'w',
  last_change int(11) NOT NULL default 0,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS apps_photo_archive (
  id int(11) NOT NULL AUTO_INCREMENT,
  categorie int(11) NOT NULL,
  mime_type varchar(255) NOT NULL,
  title varchar(255) NOT NULL,
  colored_label varchar(10) NOT NULL,
  rating float NOT NULL,
  ratings_num int(11) NOT NULL,
  upload_file_name varchar(255) NOT NULL,
  upload_file_size varchar(255) NOT NULL,
  checksum_sha1 varchar(255) NOT NULL,
  checksum_md5 varchar(255) NOT NULL,
  width int(11) NOT NULL,
  height int(11) NOT NULL,
  alt_text varchar(255) NOT NULL,
  keywords text NOT NULL,
  date_of_shot int(11) NOT NULL,
  copyright text NOT NULL,
  description text NOT NULL,
  current_owner varchar(255) NOT NULL,
  photographer varchar(255) NOT NULL,
  event varchar(255) NOT NULL,
  event_date int(11) NOT NULL,
  published_by varchar(255) NOT NULL,
  right_term varchar(255) NOT NULL,
  people_in_photo varchar(255) NOT NULL,
  scene varchar(255) NOT NULL,
  full_path varchar(255) NOT NULL default 'assets/uploads/',
  resolution varchar(255) NOT NULL,
  depth varchar(255) NOT NULL,
  color_space varchar(255) NOT NULL,
  compression varchar(255) NOT NULL,
  source_url varchar(255) NOT NULL,
  source_device varchar(255) NOT NULL,
  exif_data text NOT NULL,
  capture_date int(11) NOT NULL,
  aperture varchar(255) NOT NULL,
  shutter_speed varchar(255) NOT NULL,
  focal_length varchar(255) NOT NULL,
  iso_speed varchar(255) NOT NULL,
  location varchar(255) NOT NULL,
  longitude varchar(255) NOT NULL,
  altitude varchar(255) NOT NULL,
  available_sizes varchar(255) NOT NULL,
  watermark varchar(255) NOT NULL,
  no_watermark_under int(11) NOT NULL,
  filename varchar(255) NOT NULL,
  is_missing int(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS apps_wiki_text (
  id int(11) NOT NULL AUTO_INCREMENT,
  cat int(11) NOT NULL,
  title varchar(255) NOT NULL,
  text text NOT NULL,
  infobox text NOT NULL,
  keywords text NOT NULL,
  richable int(1) NOT NULL default '1',
  edit_by text NOT NULL,
  insert_date int(11) NOT NULL,
  insert_by varchar(255) NOT NULL,
  hits_counter int(11) NOT NULL,
  is_editable varchar(255) default NULL,
  PRIMARY KEY (id),
  FULLTEXT KEY title (title),
  FULLTEXT KEY text (text)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ------------------------------------------------------

CREATE TABLE IF NOT EXISTS aiki_databases (
  id int(11) NOT NULL AUTO_INCREMENT,
  db_type varchar(255) NOT NULL,
  db_name varchar(255) NOT NULL,
  db_user varchar(255) NOT NULL,
  db_pass varchar(255) NOT NULL,
  db_host varchar(255) NOT NULL,
  db_encoding varchar(255) NOT NULL,
  db_use_mysql_set_charset varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
