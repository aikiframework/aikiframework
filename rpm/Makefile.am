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

# Generate sum
sum :
	@$(CD) SPECS \
	&& $(SHASUM) -a $(ALGORITHM) $(PACKAGE).spec \
	> $(PACKAGE).spec.shasum-$(ALGORITHM).txt
	@if $(TEST) -e "$$($(FIND) SRPMS -name \*.src.rpm)"; then \
	  $(CD) SRPMS && $(SHASUM) -a $(ALGORITHM) $$($(LS) *.src.rpm) \
	  > $$($(LS) *.src.rpm).shasum-$(ALGORITHM).txt; \
	fi
	@if $(TEST) -e "$$($(FIND) RPMS -name \*.rpm)"; then \
	  $(CD) RPMS/noarch \
	  && $(SHASUM) -a $(ALGORITHM) $$($(LS) *.rpm) \
	  > $$($(LS) *.rpm).shasum-$(ALGORITHM).txt; \
	fi

# Run rpmlint
lint : sum
	@$(ECHO) "********************************" \
	&& $(CD) .. \
	&& $(FIND) rpm -name \*.rpm* \
	&& $(FIND) rpm -name $(PACKAGE).spec* \
	&& $(RPMLINT) rpm/SPECS/$(PACKAGE).spec \
	$$($(FIND) rpm -name \*.rpm) $$($(FIND) rpm -name \*.src.rpm) \
	&& $(ECHO) "********************************"

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
.PHONY : srpm rpm dirs src dist lint sum
