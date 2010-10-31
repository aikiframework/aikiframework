#                                               -*- Autoconf -*-
# Process this file with autoconf to produce a configure script.

# Autoconf minimum version required
AC_PREREQ(2.59)

# Initialize autoconf, set the name of this package and version
AC_INIT(aiki, 0.4.5, [https://bugs.launchpad.net/aikiframework],,[http://aikiframework.org])
PACKAGE=aiki

# Search for auxiliary scripts in the build-aux directory
AC_CONFIG_AUX_DIR(build-aux)

######################
# Checks for programs.
######################
# Set the variable INSTALL to the name of a BSD-compatible install program
AC_PROG_INSTALL
# Check for gawk, mawk, nawk, and awk, in that order, and set output variable AWK
AC_PROG_AWK
# Set output variable SED to a Sed implementation that conforms to Posix
AC_PROG_SED
# Look for the best available grep
AC_PROG_GREP
# Set output variable MKDIR_P
AC_PROG_MKDIR_P
# Set the output variable LN_S to ln -s
AC_PROG_LN_S
# Set the output variable MAKE to make
AC_PROG_MAKE_SET

# Define variables for summary of checks, features and packages.
featurelist=
packagelist=
fcheckslist=
programlist=
failurelist=
variablelist=

## Add a program to the summary checklist
# This should be called right after AC_CHECK_PROG()
# The first parameter is the name of the variable
# The second parameter is the variable set by AC_CHECK_PROG()
# Example: PROG_LIST_ADD DATE $DATE
PROG_LIST_ADD()
{
  programlist="$programlist $1=$($ECHO $2 | $SED s/" "//g)"
  fcheckslist="$fcheckslist $($ECHO $2 | $SED s/" "//g)"
}
VAR_LIST_ADD()
{
  variablelist="$variablelist $1=$($ECHO $2 | $SED s/" "//g)"
}
#### END PROG_LIST_ADD()

## Check for some minimum required programs commonly used
# If variable was already set, AC_CHECK_PROG does nothing.

AC_CHECK_PROG(ECHO,echo,echo,MISSING)
PROG_LIST_ADD ECHO $ECHO

AC_CHECK_PROG(SED,sed,sed,MISSING)
PROG_LIST_ADD SED $SED

AC_CHECK_PROG(MKDIR,mkdir,mkdir,MISSING)
PROG_LIST_ADD MKDIR $MKDIR

AC_CHECK_PROG(MYSQL,mysql,mysql,MISSING)
PROG_LIST_ADD MYSQL $MYSQL

AC_CHECK_PROG(CHMOD,chmod,chmod,MISSING)
PROG_LIST_ADD CHMOD $CHMOD

AC_CHECK_PROG(CHOWN,chown,chown,MISSING)
PROG_LIST_ADD CHOWN $CHOWN

AC_CHECK_PROG(UNAME,uname,uname,MISSING)
PROG_LIST_ADD UNAME $UNAME

AC_CHECK_PROG(MAKE,make,make,MISSING)
PROG_LIST_ADD MAKE $MAKE

AC_CHECK_PROG(GREP,grep,grep,MISSING)
PROG_LIST_ADD GREP $GREP

AC_CHECK_PROG(DATE,date,date,MISSING)
PROG_LIST_ADD DATE $DATE

AC_CHECK_PROG(FIND,find,find,MISSING)
PROG_LIST_ADD FIND $FIND

AC_CHECK_PROG(TEST,test,test,test)
PROG_LIST_ADD TEST $TEST

AC_CHECK_PROG(EXPR,expr,expr,MISSING)
PROG_LIST_ADD EXPR $EXPR

AC_CHECK_PROG(EVAL,eval,eval,eval)
PROG_LIST_ADD EVAL $EVAL

AC_CHECK_PROG(HEAD,head,head,MISSING)
PROG_LIST_ADD HEAD $HEAD

AC_CHECK_PROG(AWK,awk,awk,MISSING)
PROG_LIST_ADD AWK $AWK

AC_CHECK_PROG(TAR,tar,tar,MISSING)
PROG_LIST_ADD TAR $TAR

AC_CHECK_PROG(ZIP,zip,zip,MISSING)
PROG_LIST_ADD ZIP $ZIP

AC_CHECK_PROG(CD,cd,cd,cd)
PROG_LIST_ADD CD $CD

AC_CHECK_PROG(MV,mv,mv,MISSING)
PROG_LIST_ADD MV $MV

AC_CHECK_PROG(RM,rm,rm,MISSING)
PROG_LIST_ADD RM $RM

AC_CHECK_PROG(CP,cp,cp,MISSING)
PROG_LIST_ADD CP $CP

AC_CHECK_PROG(TR,tr,tr,MISSING)
PROG_LIST_ADD TR $TR

AC_CHECK_PROG(LN,ln,ln,MISSING)
PROG_LIST_ADD LN $LN

AC_CHECK_PROG(LS,ls,ls,MISSING)
PROG_LIST_ADD LS $LS

AC_CHECK_PROG(SHASUM,shasum,shasum,MISSING)
PROG_LIST_ADD SHASUM $SHASUM
#### END CHECKS FOR PROGRAMS

###################
# Optional features
###################
AC_ARG_ENABLE([runtime-installer],
  [AS_HELP_STRING([--enable-runtime-installer],
    [Enable runtime installer for configuration and database [default=no]])],
  [enable_runtime_installer=$enableval],
  [enable_runtime_installer=no])
if $TEST "x$enable_runtime_installer" == "xyes" ; then
  AC_SUBST(ENABLE_RUNTIME_INSTALLER,[TRUE])
else
  AC_SUBST(ENABLE_RUNTIME_INSTALLER,[FALSE])
fi
AM_CONDITIONAL(ENABLE_RUNTIME_INSTALLER, $TEST x$enable_runtime_installer = xyes)
featurelist="$featurelist ENABLE_RUNTIME_INSTALLER=$enable_runtime_installer"
#### END OPTIONAL FEATURES

#############################
# Initialize automake support
#############################
# The foreign option allows omittance of
# some GNU-style files like: NEWS, README, etc ...
# Emit all warnings and as errors while using bzip2 compression for distribution
AM_INIT_AUTOMAKE([1.9 -Wall -Werror foreign dist-bzip2 dist-zip])

#############################
# Define useful substitutions
#############################
AC_ARG_VAR(ALGORITHM,[Set the shasum algorithm where valid values are: 256, 224, 384 and 512. [default: ALGORITHM=256]])
AM_CONDITIONAL(ALGORITHM_NOT_SET, [$TEST -z $ALGORITHM])
AM_COND_IF([ALGORITHM_NOT_SET],[AC_SUBST(ALGORITHM,[256])])
VAR_LIST_ADD ALGORITHM $ALGORITHM

AC_ARG_VAR(DB_TYPE,[Set the database type [default: DB_TYPE=mysql]])
AM_CONDITIONAL(DB_TYPE_NOT_SET, [$TEST -z $DB_TYPE])
AM_COND_IF([DB_TYPE_NOT_SET],[AC_SUBST(DB_TYPE,[mysql])])
VAR_LIST_ADD DB_TYPE $DB_TYPE

AC_ARG_VAR(DB_HOST,[Set the database host name [default: DB_HOST=localhost]])
AM_CONDITIONAL(DB_HOST_NOT_SET, [$TEST -z $DB_HOST])
AM_COND_IF([DB_HOST_NOT_SET],[AC_SUBST(DB_HOST,[localhost])])
VAR_LIST_ADD DB_HOST $DB_HOST

AC_ARG_VAR(DB_NAME,[Set the database name [default: DB_NAME=aiki]])
AM_CONDITIONAL(DB_NAME_NOT_SET, [$TEST -z $DB_NAME])
AM_COND_IF([DB_NAME_NOT_SET],[AC_SUBST(DB_NAME,[aiki])])
VAR_LIST_ADD DB_NAME $DB_NAME

AC_ARG_VAR(DB_USER,[Set the database user [default: DB_USER=aiki]])
AM_CONDITIONAL(DB_USER_NOT_SET, [$TEST -z $DB_USER])
AM_COND_IF([DB_USER_NOT_SET],[AC_SUBST(DB_USER,[aiki])])
VAR_LIST_ADD DB_USER $DB_USER

DEFAULT_DB_PASS=
if $TEST -e /dev/urandom; then
  DEFAULT_DB_PASS=$(< /dev/urandom $TR -dc _A-Z-a-z-0-9 | $HEAD -c8)
else
  DEFAULT_DB_PASS=$($DATE +%s%N | $SHASUM -a $ALGORITHM | $HEAD -c8)
fi
AC_ARG_VAR(DB_PASS,[Set the database password [default: DB_PASS=/dev/urandom]])
AM_CONDITIONAL(DB_PASS_NOT_SET, [$TEST -z $DB_PASS])
AM_COND_IF([DB_PASS_NOT_SET],[AC_SUBST(DB_PASS,[$DEFAULT_DB_PASS])])
VAR_LIST_ADD DB_PASS $DB_PASS

AC_ARG_VAR(DB_ENCODE,[Set the database encoding [default: DB_ENCODE=utf8]])
AM_CONDITIONAL(DB_ENCODE_NOT_SET, [$TEST -z $DB_ENCODE])
AM_COND_IF([DB_ENCODE_NOT_SET],[AC_SUBST(DB_ENCODE,[utf8])])
VAR_LIST_ADD DB_ENCODE $DB_ENCODE

AC_ARG_VAR(DB_SUPER_USER,[Set the database super user [default: DB_SUPER_USER=root]])
AM_CONDITIONAL(DB_SUPER_USER_NOT_SET, [$TEST -z $DB_SUPER_USER])
AM_COND_IF([DB_SUPER_USER_NOT_SET],[AC_SUBST(DB_SUPER_USER,[root])])
VAR_LIST_ADD DB_SUPER_USER $DB_SUPER_USER

AC_ARG_VAR(DB_SUPER_USER_PASS,[Set the database super user password [default: DB_SUPER_USER_PASS=]])
AM_CONDITIONAL(DB_SUPER_USER_PASS_NOT_SET, [$TEST -z $DB_SUPER_USER_PASS])
AM_COND_IF([DB_SUPER_USER_PASS_NOT_SET],[AC_SUBST(DB_SUPER_USER_PASS,[''])])
VAR_LIST_ADD DB_SUPER_USER_PASS $DB_SUPER_USER_PASS

AC_ARG_VAR(DOMAIN,[Set the domain name where aiki will be installed [default: DOMAIN=localhost]])
AM_CONDITIONAL(DOMAIN_NOT_SET, [$TEST -z $DOMAIN])
AM_COND_IF([DOMAIN_NOT_SET],[AC_SUBST(DOMAIN,[localhost])])
VAR_LIST_ADD DOMAIN $DOMAIN

AC_ARG_VAR(HTTPD_CONF_DIR,[Set the HTTPD configuration directory [default: HTTPD_CONF_DIR=$sysconfdir/httpd/conf.d]])
AM_CONDITIONAL(HTTPD_CONF_DIR_NOT_SET, [$TEST -z $HTTPD_CONF_DIR])
AM_COND_IF([HTTPD_CONF_DIR_NOT_SET],[AC_SUBST(HTTPD_CONF_DIR,[$sysconfdir/httpd/conf.d])])
VAR_LIST_ADD HTTPD_CONF_DIR $HTTPD_CONF_DIR

VAR_LIST_ADD prefix $prefix
VAR_LIST_ADD docdir $docdir
VAR_LIST_ADD datarootdir $datarootdir
VAR_LIST_ADD sysconfdir $sysconfdir

# use eval to force variable substitution/expansion
AIKI_DATA_DIR=$($EVAL $EVAL $EVAL $ECHO $datadir/$PACKAGE)
AC_SUBST(AIKI_DATA_DIR,$AIKI_DATA_DIR)
VAR_LIST_ADD AIKI_DATA_DIR $AIKI_DATA_DIR

AIKI_DATA_DIR_LEN=$($ECHO $AIKI_DATA_DIR | $AWK '{print length}')
AC_SUBST(AIKI_DATA_DIR_LEN,$AIKI_DATA_DIR_LEN)

AIKI_SITE_URL=$($ECHO "http://$DOMAIN/$PACKAGE/")
AC_SUBST(AIKI_SITE_URL,$AIKI_SITE_URL)
VAR_LIST_ADD AIKI_SITE_URL $AIKI_SITE_URL

AIKI_SITE_URL_LEN=$($ECHO "$AIKI_SITE_URL" | $AWK '{print length}')
AC_SUBST(AIKI_SITE_URL_LEN,$AIKI_SITE_URL_LEN)

PRINT_CONFIG_SUMMARY()
{
  $ECHO
  AC_MSG_NOTICE([ENABLED FEATURES])
  AC_MSG_NOTICE([****************])
  for feature in $featurelist ; do
    AC_MSG_NOTICE([$feature])
  done
  $ECHO
  AC_MSG_NOTICE([WITH PACKAGES])
  AC_MSG_NOTICE([*************])
  for package in $packagelist ; do
    AC_MSG_NOTICE([$package])
  done
  $ECHO
  AC_MSG_NOTICE([REQUIRED PROGRAMS])
  AC_MSG_NOTICE([*****************])
  for program in $programlist ; do
    AC_MSG_NOTICE([$program])
  done
  $ECHO
  AC_MSG_NOTICE([VARIABLE INFO])
  AC_MSG_NOTICE([*************])
  for variable in $variablelist ; do
    AC_MSG_NOTICE([$($EVAL $EVAL $EVAL $ECHO $variable)])
  done
  $ECHO
  # Checks for failed checks
  # This allows the user to run all checks
  # and summarize failed checks at the end.
  for check in $fcheckslist ; do
    if $TEST "x$check" == "xMISSING" ; then
      failurelist=yes
    fi
  done
  if $TEST "x$failurelist" == "xyes" ; then
    AC_MSG_ERROR(
    [
    * Please try installing any missing packages or
      adjusting the path then re-execute configure.])
  fi
}
#### END PRINT_CONFIG_SUMMARY()

# Usually, makefiles are created
AC_CONFIG_FILES([ \
  Makefile \
  src/Makefile \
  src/assets/Makefile \
  src/assets/apps/Makefile \
  src/assets/apps/admin/Makefile \
  src/assets/apps/admin/images/Makefile \
  src/assets/apps/admin/images/icons/Makefile \
  src/assets/images/Makefile \
  src/assets/javascript/Makefile \
  src/assets/javascript/codemirror/Makefile \
  src/assets/javascript/codemirror/css/Makefile \
  src/assets/javascript/codemirror/js/Makefile \
  src/assets/javascript/jquery/Makefile \
  src/assets/javascript/jquery/css/Makefile \
  src/assets/javascript/jquery/css/smoothness/Makefile \
  src/assets/javascript/jquery/css/smoothness/images/Makefile \
  src/assets/javascript/jquery/plugins/Makefile \
  src/assets/javascript/jquery/plugins/jstree/Makefile \
  src/assets/javascript/jquery/plugins/jstree/themes/Makefile \
  src/assets/javascript/jquery/plugins/jstree/themes/default/Makefile \
  src/assets/javascript/plupload/Makefile \
  src/assets/javascript/plupload/img/Makefile \
  src/assets/uploads/Makefile \
  src/system/Makefile \
  src/system/database/Makefile \
  src/system/libraries/Makefile \
  configs/aiki-defs.php:configs/aiki-defs.inc \
  configs/z-aiki.conf:configs/z-aiki.in \
  configs/CreateTables.sql:configs/CreateTables.in \
  configs/Makefile \
])

# Generate config.status and launch it
AC_OUTPUT

# print a summary of the enabled features, packages and requirements
PRINT_CONFIG_SUMMARY
