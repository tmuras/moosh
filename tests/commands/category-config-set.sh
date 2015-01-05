#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD category-config-set 2 visible 0
if echo "SELECT * FROM mdl_course_categories \
    WHERE id=2 AND visible=0" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" ; then
    exit 0
else
    exit 1
fi


