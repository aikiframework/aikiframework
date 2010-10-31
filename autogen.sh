#!/bin/sh

#       File: autogen.sh
#    Summary: Auto generate a configure script and others
# Created on: Sep 15, 2008
#     Author: Steven Garcia http://webwhammy.com
#      Usage: ./autogen.sh
#       Note: Use this if the configure script is missing or broken

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
echo

