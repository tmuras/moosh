#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD course-config-set course 2 shortname test_shortname
if mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT * FROM mdl_course WHERE shortname = 'test_shortname'"; then
  :
else
  exit 1
fi

$MOOSHCMD course-config-set category 1 format topics

if mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT * FROM mdl_course WHERE category = '1' AND format = 'topics'"; then
  exit 0
else
  exit 1
fi