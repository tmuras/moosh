#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR


$MOOSHCMD course-unenrol 2 3
if ! echo "SELECT courseid, userid FROM mdl_enrol LEFT JOIN mdl_user_enrolments \
    ON mdl_enrol.id = mdl_user_enrolments.enrolid \
    WHERE courseid=2 AND userid=3\G" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" \
    | grep -c "userid: 3" ; then
    exit 0
else
    exit 1
fi
