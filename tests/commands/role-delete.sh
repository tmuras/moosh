#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD role-delete -i 9 
if ! mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e "SELECT * FROM mdl_role WHERE shortname = 'delete'" | grep delete; then
  exit 0
else
  exit 1
fi

