#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR


$MOOSHCMD cohort-enrol -u 2 "testcohort"

if echo "SELECT * FROM mdl_cohort_members WHERE userid = 2" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" | grep "cohortid"; then
  exit 0
else
  exit 1
fi