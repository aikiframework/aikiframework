#!/bin/sh

#       File: bone-clean.sh
#    Summary: Make project bone-clean
# Created on: Sep 15, 2008
#     Author: Steven Garcia http://webwhammy.com
#      Usage: ./scripts/bone-clean.sh
#       Note: Use with caution, only for maintainers

# Copyright (C) 2010 Aikilab
#
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

if test -e build/Makefile ; then
  cd build
  make bone-clean
  cd ..
else
  rm -rvf \
  $(find . -name \*.o) \
  $(find . -name \*.a) \
  $(find . -name \*.so) \
  $(find . -name \*.la) \
  $(find . -name \*.lo) \
  $(find . -name \*~) \
  $(find . -name COPYING) \
  $(find . -maxdepth 1 -name "*.in" | sed 's/.in//') \
  $(find . -name ABOUT-NLS) \
  $(find . -name autoscan.log) \
  $(find . -name configure.scan) \
  $(find . -name Makefile) \
  $(find . -name Makefile.in) \
  $(find . -name aclocal.m4) \
  $(find . -name acinclude.m4) \
  $(find . -name configure) \
  $(find . -name depcomp) \
  $(find . -name install-sh) \
  $(find . -name missing) \
  $(find . -name ltmain.sh) \
  $(find . -name config.h) \
  $(find . -name config.h.in) \
  $(find . -name config.h.in~) \
  $(find . -name config.sub) \
  $(find . -name config.log) \
  $(find . -name config.guess) \
  $(find . -name config.status) \
  $(find . -name stamp-h1) \
  $(find . -name mkinstalldirs) \
  $(find . -type d | grep stage) \
  $(find . -type d | grep \\.libs) \
  $(find . -type d | grep \\.deps) \
  $(find . -type d | grep autom4te.cache)
fi
