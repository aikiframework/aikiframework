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
	index.php \
	bootstrap.php \
	style.php

# Subdirectories to be built
SUBDIRS = \
	assets \
	configs \
	libs \
	sql

# Subdirectories to distribute
DIST_SUBDIRS = \
	assets \
	configs \
	libs \
	sql

# Additional files to remove on dist-clean
DISTCLEANFILES = .htaccess

# Additional files to distribute
EXTRA_DIST = \
	bootstrap.php \
	index.php \
	style.php

# The directory to put the files
aiki_srcdir = \
	$(pkgdatadir)

# The source data
aiki_src_DATA = \
	$(aiki_src_files)
