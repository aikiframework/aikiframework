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

# The config files
aiki_conf_files = \
	$(PACKAGE)-defs.php \
	htaccess.inc \
	config.php

# The httpd config files
httpd_conf_files = \
	z-$(PACKAGE).conf

# Additional files to remove on dist-clean
DISTCLEANFILES = \
	htaccess \
	z-$(PACKAGE).conf \
	$(PACKAGE)-defs.php

# Additional files to distribute
EXTRA_DIST = \
	$(PACKAGE)-defs.inc \
	config.php \
	htaccess.inc \
	z-$(PACKAGE).in

# The directory to put the files
aiki_confdir = \
	$(sysconfdir)/$(PACKAGE)

# The directory to put the files
httpd_confdir = \
	$(HTTPD_CONF_DIR)

# The config data
aiki_conf_DATA = \
	$(aiki_conf_files)

# The config data
httpd_conf_DATA = \
	$(httpd_conf_files)
