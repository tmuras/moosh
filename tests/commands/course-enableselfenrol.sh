#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD course-enableselfenrol 2
if mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT status FROM mdl_enrol WHERE enrol = 'self' AND courseid = 2"\
    | grep 0; then
  exit 0
else
  exit 1
fi
