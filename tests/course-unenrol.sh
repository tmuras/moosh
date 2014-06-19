#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

moosh course-enrol 2 testuser
if echo "SELECT courseid, userid FROM mdl_enrol LEFT JOIN mdl_user_enrolments \
    ON mdl_enrol.id = mdl_user_enrolments.enrolid \
    WHERE courseid=2 AND userid=5" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" \
    | grep -c "userid\|(5)" ; then
    :
else
    echo "Command 'course-enrol' failure."
    exit 1
fi

moosh course-unenrol 2
if ! echo "SELECT courseid, userid FROM mdl_enrol LEFT JOIN mdl_user_enrolments \
    ON mdl_enrol.id = mdl_user_enrolments.enrolid \
    WHERE courseid=2 AND userid=5" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" \
    | grep -c "userid\|(5)" ; then
    exit 0
else
    exit 1
fi
