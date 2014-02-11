#!/bin/bash
source functions.sh

install_db
install_data
cd $MOOSH_TEST_DIR

moosh sql-run "SELECT * FROM {role} WHERE shortname = 'delete'"
moosh role-delete -i 9 
if ! [ $(moosh sql-run "select * from {role} where shortname = 'delete'" | grep -o 'delete') ]; then
  exit 0
else
  exit 1
fi
