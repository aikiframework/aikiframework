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

# Find the source files of only the current source directory non-recursively
aiki_src_files = \
	$(shell find $(srcdir) -maxdepth 1 -name \*\.css -o -name \*\.png -o -name \*\.gif)

# Find the sub directories of only the current build directory non-recursively
export aiki_sub_dirs = \
	$(shell find -maxdepth 1 -type d | $(SED) s/\.\\/*//)

# Subdirectories to distribute and build
DIST_SUBDIRS = $(aiki_sub_dirs)
SUBDIRS = $(DIST_SUBDIRS)

# Additional files to distribute
EXTRA_DIST = \
	$(aiki_src_files)

# The directory to put the files
aiki_srcdir = \
	$(pkgdatadir)/assets/javascript/jquery/plugins/jstree/themes/default

# The source data
aiki_src_DATA = \
	$(aiki_src_files)
