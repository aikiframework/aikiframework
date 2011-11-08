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

# The desktop entry files
aiki_entry_files = \
	$(PACKAGE).desktop \
	$(PACKAGE)-create-db.desktop \
	$(PACKAGE)-create-usr.desktop \
	$(PACKAGE)-drop-db.desktop \
	$(PACKAGE)-drop-usr.desktop

# The icon files
aiki_icon_files = \
	$(PACKAGE).svg

# Additional files to distribute
EXTRA_DIST = \
	$(PACKAGE).svg \
	$(PACKAGE)-in.desktop \
	$(PACKAGE)-create-db-in.desktop \
	$(PACKAGE)-create-usr-in.desktop \
	$(PACKAGE)-drop-db-in.desktop \
	$(PACKAGE)-drop-usr-in.desktop \
	$(PACKAGE)-in.directory \
	$(PACKAGE)-in.menu

# Additional files to remove on dist-clean
DISTCLEANFILES = \
	$(aiki_entry_files) \
	$(PACKAGE).directory \
	$(PACKAGE).menu

# The directory to put the files
aiki_menudir = \
	$(PKG_MENU_DIR)

# The directory to put the files
aiki_desktopdir = \
	$(PKG_DESKTOP_DIR)

# The directory to put the files
aiki_entrydir = \
	$(PKG_ENTRY_DIR)

# The directory to put the files
aiki_icondir = \
	$(PKG_ICON_DIR)

# The desktop menu data
aiki_menu_DATA = \
	$(PACKAGE).menu

# The desktop directory data
aiki_desktop_DATA = \
	$(PACKAGE).directory

# The desktop entry data
aiki_entry_DATA = \
	$(aiki_entry_files)

# The icon data
aiki_icon_DATA = \
	$(aiki_icon_files)
