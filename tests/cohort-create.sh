#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

moosh cohort-create testcohort
if echo "SELECT * FROM mdl_cohort WHERE name = 'testcohort'"\
	| mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"; then
  exit 0
else
  exit 1
fi

