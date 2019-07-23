#!/bin/bash 
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD role-reset 1
#if mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e "SELECT * FROM mdl_role WHERE name = 'testrole'"; then
 # exit 0
#else
#  exit 1
#fi

