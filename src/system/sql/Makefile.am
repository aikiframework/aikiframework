
## Process this file with automake to produce Makefile.in

# The sql files
aiki_sql_files = \
	CreateDatabase.sql \
	CreateTables.sql \
	CreateUser.sql \
	DropDatabase.sql \
	DropUser.sql \
	InsertDefaults.sql \
	InsertVariable.sql \
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
	$(pkgdatadir)/system/sql

# The config data
aiki_sql_DATA = \
	$(aiki_sql_files)
