#!/bin/bash
source functions.sh

install_db
install_data
cd $MOOSH_TEST_DIR

moosh course-enrol 2 testuser
if [ $(moosh sql-run "SELECT * FROM {enrol} LEFT JOIN {user_enrolments}\
 ON {enrol}.id = {user_enrolments}.enrolid WHERE courseid=2 AND userid=5"\
 | grep -c "userid\|(5)") ]; then
  exit 0
else
  exit 1
fi
