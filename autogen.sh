#!/bin/sh

#       File: autogen.sh
#    Summary: Auto generate a configure script and others
# Created on: Sep 15, 2008
#     Author: Steven Garcia http://webwhammy.com
#      Usage: ./autogen.sh
#       Note: Use this if the configure script is missing or broken

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

# Generate a build directories
if test ! -d build ; then mkdir -vp build ;fi
if test ! -d build-aux ; then mkdir -vp build-aux ;fi

# Generate a Makefile.am
if test ! -e Makefile.am ; then
  echo "Generating empty Makefile.am"
  echo >> Makefile.am
  echo "## Process this file with automake to produce Makefile.in" >> Makefile.am
  echo >> Makefile.am
fi

# Generate a README and other files
if test ! -e README ; then touch README ; fi
if test ! -e AUTHORS ; then touch AUTHORS ; fi
if test ! -e ChangeLog ; then touch ChangeLog ; fi

# Generate a configure.ac
if test ! -e configure.ac ; then
  echo "executing 'autoscan && cp -av configure.scan configure.ac'"
  autoscan && cp -av configure.scan configure.ac
fi

echo "executing 'autoreconf --force --install'"
autoreconf --force --install

echo "Finished executing $0"
echo
echo "Now try:
  cd build \\
  && ../configure \\
      --prefix=/usr \\
  --sysconfdir=/etc \\
  && sudo make install"
echo
echo "Or try:
  cd build \\
  && ../configure --help"
echo "Or try:
  ./configure && make"
echo

