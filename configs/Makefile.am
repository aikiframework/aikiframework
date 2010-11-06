
## Process this file with automake to produce Makefile.in

# The config files
aiki_conf_files = \
	$(PACKAGE)-defs.php \
	config.php

# The httpd config files
httpd_conf_files = \
	z-$(PACKAGE).conf

# Additional files to remove on dist-clean
DISTCLEANFILES = \
	z-$(PACKAGE).conf \
	$(PACKAGE)-defs.php

# Additional files to distribute
EXTRA_DIST = \
	$(PACKAGE)-defs.inc \
	config.php \
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
