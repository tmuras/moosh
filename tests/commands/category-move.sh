#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD category-move 1 2 
if mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT * FROM mdl_course_categories WHERE parent = 2"\
    |grep Miscellaneous; then
  exit 0
else
  exit 1
fi
