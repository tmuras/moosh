#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD course-delete 2
if ! mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT * FROM mdl_course WHERE fullname = 'test'"\
    | grep test; then
  exit 0
else
  exit 1
fi
