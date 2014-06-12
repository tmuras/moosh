SELECT * FROM mooshtest_26.mdl_cohort_members WHERE userid = 7;
#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

moosh cohort-create testcohotr
moosh cohort-enrol -u "testcohotr"
moosh cohort-unenrol 1 7

if ! echo "SELECT * FROM mdl_cohort_members WHERE userid = 7" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" | grep "cohortid"; then
  exit 0
else
  exit 1
fi