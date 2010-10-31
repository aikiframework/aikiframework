
## Process this file with automake to produce Makefile.in

# The config files
aiki_conf_files = \
	$(PACKAGE)-defs.php \
	config.php

# The httpd config files
httpd_conf_files = \
	z-$(PACKAGE).conf

# Subdirectories to be built
SUBDIRS =

# Subdirectories to distribute
DIST_SUBDIRS =

# Additional files to distribute
EXTRA_DIST = \
	$(PACKAGE)-defs.inc \
	config.php \
	z-$(PACKAGE).in \
	CreateTables.in

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
