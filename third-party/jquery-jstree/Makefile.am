# Process this file with automake to produce Makefile.in    -*-Makefile-*-

# Copyright (C) 2010-2011 Aiki Lab Pte Ltd

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

# The source files
aiki_src_files = \
	css.js

# Subdirectories to be built
SUBDIRS = \
	jstree

# Subdirectories to distribute
DIST_SUBDIRS = \
	jstree

# Additional files to distribute
EXTRA_DIST = \
	$(aiki_src_files)

# The suffix of the path to put the files
suffix_dir = assets/javascript/jquery/plugins
thirdlibdir = third-party/jquery-jstree

# The directory to put the files
aiki_srcdir = \
	$(pkgdatadir)/$(suffix_dir)

# The source data
aiki_src_DATA = \
	$(aiki_src_files)

# the default make target
all :
# attempt to make the target directory
# copy the source to the build source and remove some extras
	@if $(TEST) -d $(abs_top_srcdir)/_build; then \
		$(MKDIR) -p $(abs_top_srcdir)/_build/src/$(suffix_dir); \
		$(CHMOD) 0755 $(abs_top_srcdir)/_build/src/$(suffix_dir); \
    	for f in $(aiki_src_files); do \
        	$(CP) -a $(abs_top_srcdir)/$(thirdlibdir)/$$f \
        	$(abs_top_srcdir)/_build/src/$(suffix_dir)/; \
    	done; \
    	for d in $(SUBDIRS); do \
        	$(CP) -a $(abs_top_srcdir)/$(thirdlibdir)/$$d \
        	$(abs_top_srcdir)/_build/src/$(suffix_dir)/; \
    	done; \
    else \
    	if $(TEST) -d $(abs_top_srcdir)/build; then \
    		$(MKDIR) -p $(abs_top_srcdir)/build/src/$(suffix_dir); \
    		$(CHMOD) 0755 $(abs_top_srcdir)/build/src/$(suffix_dir); \
        	for f in $(aiki_src_files); do \
            	$(CP) -a $(abs_top_srcdir)/$(thirdlibdir)/$$f \
            	$(abs_top_srcdir)/build/src/$(suffix_dir)/; \
        	done; \
        	for d in $(SUBDIRS); do \
            	$(CP) -a $(abs_top_srcdir)/$(thirdlibdir)/$$d \
            	$(abs_top_srcdir)/build/src/$(suffix_dir)/; \
        	done; \
    	fi; \
	fi

# the distribution make target
dist :
	@if $(TEST) -d $(abs_top_srcdir)/_build; then \
		$(MKDIR) -p $(abs_top_srcdir)/_build/$(PACKAGE)-$(VERSION)/src/$(suffix_dir); \
		$(CHMOD) 0755 $(abs_top_srcdir)/_build/$(PACKAGE)-$(VERSION)/src/$(suffix_dir); \
    	for f in $(aiki_src_files); do \
        	$(CP) -a $(abs_top_srcdir)/$(thirdlibdir)/$$f \
        	$(abs_top_srcdir)/_build/$(PACKAGE)-$(VERSION)/src/$(suffix_dir)/; \
    	done; \
    	for d in $(SUBDIRS); do \
        	$(CP) -a $(abs_top_srcdir)/$(thirdlibdir)/$$d \
        	$(abs_top_srcdir)/_build/$(PACKAGE)-$(VERSION)/src/$(suffix_dir)/; \
    	done; \
    else \
    	if $(TEST) -d $(abs_top_srcdir)/build; then \
    		$(MKDIR) -p $(abs_top_srcdir)/build/$(PACKAGE)-$(VERSION)/src/$(suffix_dir); \
    		$(CHMOD) 0755 $(abs_top_srcdir)/build/$(PACKAGE)-$(VERSION)/src/$(suffix_dir); \
        	for f in $(aiki_src_files); do \
            	$(CP) -a $(abs_top_srcdir)/$(thirdlibdir)/$$f \
            	$(abs_top_srcdir)/build/$(PACKAGE)-$(VERSION)/src/$(suffix_dir)/; \
        	done; \
        	for d in $(SUBDIRS); do \
            	$(CP) -a $(abs_top_srcdir)/$(thirdlibdir)/$$d \
            	$(abs_top_srcdir)/build/$(PACKAGE)-$(VERSION)/src/$(suffix_dir)/; \
        	done; \
    	fi; \
	fi
