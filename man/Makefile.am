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
	gzip -f $(DESTDIR)$(mandir)/man1/$(PACKAGE).1

# Additional things to do on uninstall
uninstall-hook :
	@$(RM) -f $(DESTDIR)$(mandir)/man1/$(PACKAGE).1.gz
