
## Process this file with automake to produce Makefile.in

# Additional files to distribute
EXTRA_DIST = \
	spec.in

# Additional files to remove on dist-clean
DISTCLEANFILES = \
	$(PACKAGE).spec \
	$$($(FIND) . -name $(PKG_DIST)) \
	$$($(FIND) . -name \*.rpm)
	
# Additional files and directories to remove on distclean
distclean-local :
	@$(RM) -rf \
	SOURCES \
	BUILD \
	SRPMS \
	RPMS

# dist target
dist :
	@if $(TEST) -z "$$($(FIND) $(abs_top_builddir) -name $(PKG_DIST))"; then \
	$(MAKE) -C $(abs_top_builddir) dist; \
	fi
	@if $(TEST) ! -e $(PKG_DIST); then \
	$(CP) -v $$($(FIND) $(abs_top_builddir) -name $(PKG_DIST)) .; \
	fi

# Make RPM-Build directories
dirs :
	@if $(TEST) ! -d SOURCES; then \
	$(MKDIR) -vp \
	SOURCES \
	BUILD \
	SRPMS \
	RPMS/noarch; \
	fi

# Install sources
src : dirs dist
	@if $(TEST) ! -e SOURCES/$(PKG_DIST); then \
	$(CP) -v $(PKG_DIST) SOURCES/; \
	fi

lint :
	@$(ECHO) "********" \
	&& $(CD) .. \
	&& $(FIND) rpm -name \*.rpm \
	&& $(RPMLINT) rpm/SPECS/$(PACKAGE).spec \
	$$($(FIND) rpm -name \*.rpm) $$($(FIND) rpm -name \*.src.rpm) \
	&& $(ECHO) "********"

# Build RPM target
rpm : src
	$(RPMBUILD) -bb \
	$(RPM_CONFIG) \
	--define="_topdir $(realpath .)" \
	SPECS/$(PACKAGE).spec \
	&& $(MAKE) lint

# Build Source-RPM target
srpm : src
	$(RPMBUILD) -bs \
	$(RPM_CONFIG) \
	--define="_topdir $(realpath .)" \
	SPECS/$(PACKAGE).spec \
	&& $(MAKE) lint

# declare phony targets
.PHONY : srpm rpm dirs src dist lint
