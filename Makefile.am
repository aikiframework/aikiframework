# Make aiki                                                 -*-Makefile-*-

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

# The doc files
aiki_doc_files = \
	AUTHORS \
	ChangeLog \
	INSTALL \
	LICENSE \
	README \
	README.fedora

# Subdirectories to be built
SUBDIRS = \
	src \
	configs \
	scripts \
	man \
	desktop \
	rpm

# Sub directories to pass to the dist build
DIST_SUBDIRS = \
	src \
	configs \
	scripts \
	man \
	desktop \
	rpm

# Configure flags to pass to the distcheck build
DISTCHECK_CONFIGURE_FLAGS =

# Configure flags to pass to the RPM build
RPM_CONFIG =

# Additional files to distribute
EXTRA_DIST = \
	$(aiki_doc_files) \
	.bzr/branch/last-revision \
	PACKAGE-LICENSING.in \
	VERSION.in

# The directory to put the files
aiki_docdir = \
	$(docdir)-$(VERSION)

# The source data
aiki_doc_DATA = \
	$(aiki_doc_files) \
	PACKAGE-LICENSING \
	VERSION

# Additional files to remove on dist-clean
DISTCLEANFILES = \
	$$($(FIND) . -name $(distdir).\*) \
	$$($(FIND) . -name $(distdir)-\*)

# Additional files and directories to remove on clean
clean-local :
	@$(RM) -rf \
	$(abs_top_srcdir)/build/$(PACKAGE) \
	$(abs_top_srcdir)/build/$(distdir) \
	$$($(FIND) . -name $(distdir).\*) \
	$$($(FIND) . -name $(distdir)-\*) \
	$$($(FIND) . -name $(PACKAGE)-rev-\*)
	
# Additional files and directories to remove on distclean
distclean-local :
	@$(RM) -rf \
	$(distdir)/build \
	$(abs_top_srcdir)/build/*

# Additional things to do on dist
dist-hook:
	$(MKDIR) -vp $(distdir)/build

# Additional things to do on install
install-mysql-database:
	@$(ECHO) "***********************************"
	@$(ECHO) "Attempting installation of database"
	@$(ECHO) "***********************************"
	@$(MYSQL) -v -u $(DB_USER) -p$(DB_PASS) \
	-e "CREATE DATABASE IF NOT EXISTS $(DB_NAME) \
	DEFAULT CHARACTER SET $(DB_ENCODE) COLLATE utf8_general_ci;" \
	&& $(MYSQL) -B -u $(DB_USER) -p$(DB_PASS) $(DB_NAME) \
	< $(abs_top_builddir)/configs/CreateTables.sql

# Additional things to do on install
install-mysql-user:
	@$(ECHO) "****************************************"
	@$(ECHO) "Attempting installation of database user"
	@$(ECHO) "****************************************"
	@if $(TEST) "$$($(MYSQL) -u $(DB_SUPER_USER) -p$(DB_SUPER_USER_PASS) \
	-e "SELECT IF(user.user='$(DB_USER)','TRUE','') AS USER_EXISTS \
	FROM mysql.user WHERE user.user='$(DB_USER)'\\G" \
	| $(GREP) USER_EXISTS | $(AWK) '{print $$2}')" == "TRUE"; then \
	$(ECHO) "Failed to create database user name $(DB_USER). The user name already exists."; \
	else $(ECHO) "Creating database user name $(DB_USER)." \
	&& $(MYSQL) -v -u $(DB_SUPER_USER) -p$(DB_SUPER_USER_PASS) \
	-e "CREATE USER '$(DB_USER)'@'$(DB_HOST)' IDENTIFIED BY '$(DB_PASS)'; \
	GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER \
	ON $(DB_NAME).* TO '$(DB_USER)'@'$(DB_HOST)'; \
	SHOW GRANTS FOR '$(DB_USER)'@'$(DB_HOST)';"; \
	fi

# Additional things to do on uninstall
uninstall-mysql-user:
	@$(MYSQL) -v -u $(DB_SUPER_USER) -p$(DB_SUPER_USER_PASS) \
	-e "DROP USER '$(DB_USER)'@'$(DB_HOST)';"

# Additional things to do on uninstall
uninstall-mysql-database:
	@$(MYSQL) -v -u $(DB_SUPER_USER) -p$(DB_SUPER_USER_PASS) \
	-e "DROP DATABASE IF EXISTS $(DB_NAME);"

if ENABLE_RPM
DISTCHECK_CONFIGURE_FLAGS += \
	--enable-rpm
RPM_CONFIG += \
	--enable rpm
srpm :
	@$(MAKE) -C rpm srpm
rpm :
	@$(MAKE) -C rpm rpm
endif

if ENABLE_DESKTOP
# Additional things to do on install
install-desktop:
	$(UPDATE_DESKTOP_DATABASE) -q $(DESTDIR)$(PKG_ENTRY_DIR)
else
DISTCHECK_CONFIGURE_FLAGS += \
	--disable-desktop
RPM_CONFIG += \
	--disable desktop
install-desktop:
endif

# Additional things to do on install
install-data-hook : install-desktop
	@for d in $$($(FIND) $(DESTDIR)$(sysconfdir)/$(PACKAGE) -type d); do $(CHMOD) 0755 $$d; done
	@for f in $$($(FIND) $(DESTDIR)$(sysconfdir)/$(PACKAGE) -type f); do $(CHMOD) 0644 $$f; done
	@for d in $$($(FIND) $(DESTDIR)$(pkgdatadir) -type d); do $(CHMOD) 0755 $$d; done
	@for f in $$($(FIND) $(DESTDIR)$(pkgdatadir) -type f); do $(CHMOD) 0644 $$f; done
	@for d in $$($(FIND) $(DESTDIR)$(docdir)-$(VERSION) -type d); do $(CHMOD) 0755 $$d; done
	@for f in $$($(FIND) $(DESTDIR)$(docdir)-$(VERSION) -type f); do $(CHMOD) 0644 $$f; done
	@$(MKDIR) -p $(DESTDIR)$(pkgdatadir)/configs
	@$(LN_S) -f $(sysconfdir)/$(PACKAGE)/aiki-defs.php \
	$(DESTDIR)$(pkgdatadir)/configs/aiki-defs.php
	@$(LN_S) -f $(sysconfdir)/$(PACKAGE)/config.php \
	$(DESTDIR)$(pkgdatadir)/configs/config.php
	@/sbin/service httpd condrestart &> /dev/null || :

# Additional things to do on uninstall
uninstall-hook :
	@$(RM) -f $(DESTDIR)$(pkgdatadir)/configs/aiki-defs.php
	@$(RM) -f $(DESTDIR)$(pkgdatadir)/configs/config.php
	@if $(TEST) -z "$$($(FIND) $(DESTDIR)$(pkgdatadir) -type f)"; then \
	$(RM) -rf $(DESTDIR)$(pkgdatadir); fi
	@if $(TEST) -z "$$($(FIND) $(DESTDIR)$(docdir)-$(VERSION) -type f)"; then \
	$(RM) -rf $(DESTDIR)$(docdir)-$(VERSION); fi
	@if $(TEST) -z "$$($(FIND) $(DESTDIR)$(sysconfdir)/$(PACKAGE) -type f)"; then \
	$(RM) -rf $(DESTDIR)$(sysconfdir)/$(PACKAGE); fi
	@/sbin/service httpd condrestart &> /dev/null || :

# Added the following line to work around tar uid_t range error
am__tar = ${AMTAR} chf - "$$tardir"
# previously was defined in aclocal.m4: am__tar = ${AMTAR} chof - "$$tardir"

# get source revision number
REVNFILE = $$($(FIND) $(srcdir) -name last-revision)
REVISION = $$($(AWK) '{print $$1; exit}' $(REVNFILE))

# generate source revision packages
all : distsum rev
rev :
	@$(TAR) -xpzf $(distdir).tar.gz \
	--exclude="*.ac" \
	--exclude="*.am" \
	--exclude="*.in" \
	--exclude="*.m4" \
	--exclude="README.fedora" \
	--exclude="VERSION.in" \
	--exclude="$(distdir)/.bzr" \
	--exclude="$(distdir)/build" \
	--exclude="$(distdir)/build-aux" \
	--exclude="$(distdir)/configs" \
	--exclude="$(distdir)/desktop" \
	--exclude="$(distdir)/scripts" \
	--exclude="$(distdir)/rpm" \
	--exclude="$(distdir)/man" \
	--exclude="configure"
	@$(MV) $(distdir)/src/* $(distdir)/ && $(RM) -rf $(distdir)/src
	@$(RM) -rf $(abs_top_srcdir)/build/$(PACKAGE)
	@$(MV) $(distdir) $(PACKAGE)
	@$(CP) -vf VERSION $(PACKAGE)/
	@for d in $$($(FIND) $(PACKAGE) -type d); do $(CHMOD) 0755 $$d; done
	@for f in $$($(FIND) $(PACKAGE) -type f); do $(CHMOD) 0644 $$f; done
	@$(ZIP) -rq $(PACKAGE)-$@-$(REVISION).zip $(PACKAGE)
	@$(TAR) -cpzf $(PACKAGE)-$@-$(REVISION).tar.gz $(PACKAGE)
	@$(TAR) -cpjf $(PACKAGE)-$@-$(REVISION).tar.bz2 $(PACKAGE)
	@$(MV) $(PACKAGE) $(distdir)
# generate sum for source revision packages
	@$(SHASUM) -a $(ALGORITHM) $(PACKAGE)-$@-$(REVISION).zip > $(PACKAGE)-$@-$(REVISION).zip.shasum-$(ALGORITHM).txt
	@$(SHASUM) -a $(ALGORITHM) $(PACKAGE)-$@-$(REVISION).tar.gz > $(PACKAGE)-$@-$(REVISION).tar.gz.shasum-$(ALGORITHM).txt
	@$(SHASUM) -a $(ALGORITHM) $(PACKAGE)-$@-$(REVISION).tar.bz2 > $(PACKAGE)-$@-$(REVISION).tar.bz2.shasum-$(ALGORITHM).txt
	@for f in $$($(FIND) . -name $(PACKAGE)-$@-$(REVISION).\*); do $(CHMOD) 0644 $$f; done
	@$(ECHO) "*****************************************************************"
	@$(ECHO) "$(PACKAGE) revision $(REVISION) archives ready for distribution:"
	@$(LS) -1 $(PACKAGE)-$@-$(REVISION).*
	@$(ECHO) "*****************************************************************"

# to check: shasum -a 256 -c aiki-rev-429.tar.gz.shasum-256.txt

# generate sum for distribution packages
distsum : dist summod
distchecksum : distcheck summod
summod :
	@$(SHASUM) -a $(ALGORITHM) $(distdir).zip > $(distdir).zip.shasum-$(ALGORITHM).txt
	@$(SHASUM) -a $(ALGORITHM) $(distdir).tar.gz > $(distdir).tar.gz.shasum-$(ALGORITHM).txt
	@$(SHASUM) -a $(ALGORITHM) $(distdir).tar.bz2 > $(distdir).tar.bz2.shasum-$(ALGORITHM).txt
	@for f in $$($(FIND) . -name $(distdir).\*); do $(CHMOD) 0644 $$f; done

# declare phony targets
.PHONY : bone-clean rev summod distsum distchecksum install-desktop rpm srpm

# Remove ALL Autotools generated files
# WARNING: Use this with CAUTION
# Only for build system MAINTAINERS
bone-clean : maintainer-clean
	@$(RM) -rvf \
	$$($(FIND) $(srcdir)/ -name \*.o) \
	$$($(FIND) $(srcdir)/ -name \*.a) \
	$$($(FIND) $(srcdir)/ -name \*.so) \
	$$($(FIND) $(srcdir)/ -name \*.la) \
	$$($(FIND) $(srcdir)/ -name \*.lo) \
	$$($(FIND) $(srcdir)/ -name \*~) \
	$$($(FIND) $(srcdir)/ -name COPYING) \
	$$($(FIND) $(srcdir)/ -name ABOUT-NLS) \
	$$($(FIND) $(srcdir)/ -name autoscan.log) \
	$$($(FIND) $(srcdir)/ -name configure.scan) \
	$$($(FIND) $(srcdir)/ -name Makefile) \
	$$($(FIND) $(srcdir)/ -name Makefile.in) \
	$$($(FIND) $(srcdir)/ -name aclocal.m4) \
	$$($(FIND) $(srcdir)/ -name acinclude.m4) \
	$$($(FIND) $(srcdir)/ -name configure) \
	$$($(FIND) $(srcdir)/ -name depcomp) \
	$$($(FIND) $(srcdir)/ -name install-sh) \
	$$($(FIND) $(srcdir)/ -name missing) \
	$$($(FIND) $(srcdir)/ -name ltmain.sh) \
	$$($(FIND) $(srcdir)/ -name config.h) \
	$$($(FIND) $(srcdir)/ -name config.h.in) \
	$$($(FIND) $(srcdir)/ -name config.h.in~) \
	$$($(FIND) $(srcdir)/ -name config.sub) \
	$$($(FIND) $(srcdir)/ -name config.log) \
	$$($(FIND) $(srcdir)/ -name config.guess) \
	$$($(FIND) $(srcdir)/ -name config.status) \
	$$($(FIND) $(srcdir)/ -name stamp-h1) \
	$$($(FIND) $(srcdir)/ -name mkinstalldirs) \
	$$($(FIND) $(srcdir)/ -type d | $(GREP) stage) \
	$$($(FIND) $(srcdir)/ -type d | $(GREP) \.libs) \
	$$($(FIND) $(srcdir)/ -type d | $(GREP) \.deps) \
	$$($(FIND) $(srcdir)/ -type d | $(GREP) autom4te.cache) \
	$(abs_top_srcdir)/build/*

