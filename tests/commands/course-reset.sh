#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD course-reset 2

# Check if user got un-enrolled from the course reset
if echo "SELECT userid FROM mdl_user_enrolments WHERE enrolid=1 and userid=4\G" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" | grep "userid: 4"; then
  exit 1
else
  exit 0
fi
