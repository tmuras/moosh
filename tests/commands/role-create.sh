#!/bin/bash 
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD role-create -d "test description" -n "testrole" newstudent
if mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e "SELECT * FROM mdl_role WHERE name = 'testrole'"; then
  exit 0
else
  exit 1
fi

