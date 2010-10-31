#!/bin/sh

#       File: stage.sh
#    Summary: Make a test install
# Created on: Mar 29, 2009
#     Author: Steven Garcia http://webwhammy.com
#      Usage: ./scripts/stage.sh
#       Note: none

mkdir -vp stage && make DESTDIR=$(pwd)/stage install

