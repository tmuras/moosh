#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

moosh course-reset 2
if echo "SELECT * FROM mdl_course WHERE id='2' and shortname='tc1';" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" | grep "testcourse1"; then
  exit 0
else
  exit 1
fi
