#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

moosh course-enrol 2 testuser
if mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e "SELECT * FROM mdl_enrol LEFT JOIN\
    mdl_user_enrolments ON mdl_enrol.id = mdl_user_enrolments.enrolid WHERE courseid='2' AND userid='5'"; then
  exit 0
else
  exit 1
fi
