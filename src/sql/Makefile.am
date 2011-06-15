# Process this file with automake to produce Makefile.in    -*-Makefile-*-

# Copyright (c) 2010-2011 Aiki Lab Pte Ltd

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

# The sql files
aiki_sql_files = \
	CreateDatabase.sql \
	CreateTables.sql \
	CreateUser.sql \
	DropDatabase.sql \
	DropUser.sql \
	InsertDefaults.sql \
	InsertVariable.sql \
	InsertVariable-in.sql \
	UserExists.sql

# Additional files to remove on dist-clean
DISTCLEANFILES = \
	CreateDatabase.sql \
	CreateUser.sql \
	DropDatabase.sql \
	DropUser.sql \
	InsertVariable.sql \
	UserExists.sql

# Additional files to distribute
EXTRA_DIST = \
	CreateDatabase-in.sql \
	CreateTables.sql \
	CreateUser-in.sql \
	DropDatabase-in.sql \
	DropUser-in.sql \
	InsertDefaults.sql \
	InsertVariable-in.sql \
	UserExists-in.sql

# The directory to put the files
aiki_sqldir = \
	$(pkgdatadir)/sql

# The config data
aiki_sql_DATA = \
	$(aiki_sql_files)
