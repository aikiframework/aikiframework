
## Process this file with automake to produce Makefile.in

# The source files
aiki_src_files = \
	index.php \
	aiki.php \
	style.php

# Subdirectories to be built
SUBDIRS = \
	assets \
	system

# Subdirectories to distribute
DIST_SUBDIRS = \
	assets \
	system

# Additional files to remove on dist-clean
DISTCLEANFILES = \
	aiki.php

# Additional files to distribute
EXTRA_DIST = \
	aiki-in.php \
	index.php \
	style.php

# The directory to put the files
aiki_srcdir = \
	$(pkgdatadir)

# The source data
aiki_src_DATA = \
	$(aiki_src_files)