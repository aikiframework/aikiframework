# Process this file with automake to produce Makefile.in    -*-Makefile-*-

# Copyright (C) 2010 Aikilab

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
	index.php \
	mssql.php \
	mysql.php \
	oracle8_9.php \
	pdo.php \
	postgresql.php \
	sqlite.php

# Subdirectories to be built
SUBDIRS =

# Subdirectories to distribute
DIST_SUBDIRS =

# Additional files to distribute
EXTRA_DIST = \
	$(aiki_src_files)

# The directory to put the files
aiki_srcdir = \
	$(pkgdatadir)/system/database

# The source data
aiki_src_DATA = \
	$(aiki_src_files)
