#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

moosh role-create -d "test description" -n "testrole" newstudent
if [ $(moosh sql-run "select * from {role} where name = 'testrole'" | grep -o 'testrole') ]; then
  exit 0
else
  exit 1
fi
