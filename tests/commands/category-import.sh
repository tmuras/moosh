#!/bin/bash
source functions.sh

install_db
install_data

cd $MOODLEDIR

$MOOSHCMD category-import "$MOODLEDATA/categoryimport.xml" 

if echo "SELECT * FROM mdl_course_categories \
    WHERE name='testimport'" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" ; then
    exit 0
else
    exit 1
fi