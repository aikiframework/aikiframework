#!/bin/sh

#       File: stage.sh
#    Summary: Make a test install
# Created on: Mar 29, 2009
#     Author: Steven Garcia http://webwhammy.com
#      Usage: ./scripts/stage.sh
#       Note: none

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

mkdir -vp stage && make DESTDIR=$(pwd)/stage install

