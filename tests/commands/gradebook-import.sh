#!/bin/bash
source functions.sh

install_db
install_data

cd $MOODLEDIR

$MOOSHCMD gradebook-import "$MOODLEDATA/grades.csv" 1

#if echo "SELECT * FROM mdl_grade_grades \
#   WHERE name='testimport'" \
# | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" ; then
#   exit 0
#else
#    exit 1
#fi