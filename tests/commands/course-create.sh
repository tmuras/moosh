#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD course-create newcourse
if mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT * FROM mdl_course WHERE fullname = 'newcourse'"; then
  exit 0
else
  exit 1
fi
