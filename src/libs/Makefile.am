# Process this file with automake to produce Makefile.in    -*-Makefile-*-

# Copyright (c) 2010-2011 Aiki Lab Pte Ltd

# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.

# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

# Written by Steven Garcia

# The source files
aiki_src_files = \
	aiki_array.php \
	aiki_curl.php \
	AikiException.php \
	aiki.php \
	AikiSession.php \
	Archive.php \
	Backup.php \
	bot.php \
	config.php \
	css_parser.php \
	dictionary.php \
	errors.php \
	File.php \
	forms.php \
	image.php \
	input.php \
	installer.php \
	languages.php \
	Log.php \
	membership.php \
	message.php \
	output.php \
	parser.php \
	php.php \
	plugins.php \
	records.php \
	security.php \
	site.php \
	sql_markup.php \
	text.php \
	Updater.php \
	Upgrade.php \
	url.php \
	view_parser.php \
	widgets.php \
	wiki.php


# Subdirectories to be built
SUBDIRS = \
	archive \
	backup \
	database \
	file \
	session \
	updater \
	upgrade

# Subdirectories to distribute
DIST_SUBDIRS = \
	archive \
	backup \
	database \
	file \
	session \
	updater \
	upgrade

# Additional files to distribute
EXTRA_DIST = \
	$(aiki_src_files)

# The directory to put the files
aiki_srcdir = \
	$(pkgdatadir)/libs

# The source data
aiki_src_DATA = \
	$(aiki_src_files)
