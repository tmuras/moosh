#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD course-enrolbyname -c course1 -f student2 -l student2
if echo "SELECT userid FROM mdl_enrol LEFT JOIN mdl_user_enrolments \
    ON mdl_enrol.id = mdl_user_enrolments.enrolid \
    WHERE courseid=2 AND userid=5\G" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" \
    | grep -c "userid: 5" ; then
    exit 0
else
  exit 1
fi
