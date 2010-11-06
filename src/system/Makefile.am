
## Process this file with automake to produce Makefile.in

# The source files
aiki_src_files = \
	core.php \
	widgets.php

# Subdirectories to be built
SUBDIRS = \
	database \
	libraries \
	sql

# Subdirectories to distribute
DIST_SUBDIRS = \
	database \
	libraries \
	sql

# Additional files to distribute
EXTRA_DIST = \
	$(aiki_src_files)

# The directory to put the files
aiki_srcdir = \
	$(pkgdatadir)/system

# The source data
aiki_src_DATA = \
	$(aiki_src_files)