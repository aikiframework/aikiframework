
## Process this file with automake to produce Makefile.in

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
