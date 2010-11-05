
## Process this file with automake to produce Makefile.in

# Man pages go here
man1_MANS = \
	$(PACKAGE).1

# Additional files to distribute
EXTRA_DIST = \
	man.in

# Additional files to remove on dist-clean
DISTCLEANFILES = \
	$(PACKAGE).1

# Additional things to do on install
install-data-hook :
	gzip $(DESTDIR)$(mandir)/man1/$(PACKAGE).1

# Additional things to do on uninstall
uninstall-hook :
	@$(RM) -f $(DESTDIR)$(mandir)/man1/$(PACKAGE).1.gz
