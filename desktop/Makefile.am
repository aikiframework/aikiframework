
## Process this file with automake to produce Makefile.in

# The desktop entry files
aiki_entry_files = \
	$(PACKAGE).desktop

# The icon files
aiki_icon_files = \
	$(PACKAGE).svg

# Additional files to distribute
EXTRA_DIST = \
	$(PACKAGE).svg \
	desktop.in

# Additional files to remove on dist-clean
DISTCLEANFILES = \
	$(PACKAGE).desktop

# The directory to put the files
aiki_entrydir = \
	$(PKG_ENTRY_DIR)

# The directory to put the files
aiki_icondir = \
	$(PKG_ICON_DIR)

# The desktop entry data
aiki_entry_DATA = \
	$(aiki_entry_files)

# The icon data
aiki_icon_DATA = \
	$(aiki_icon_files)
