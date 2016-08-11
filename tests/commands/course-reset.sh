#!/bin/bash -x
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD course-reset 3
if  mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT * FROM mdl_grade_grades WHERE rawgrade=''"; then
    :
else
    exit 1
fi
