#!/bin/bash
source functions.sh

install_db
cd $MOOSH_TEST_DIR

moosh cohort-create testcohort
if [ $(moosh sql-run "select * from {cohort} where name = 'testcohort'" | grep -o 'testcohort') ]; then
  exit 0
else
  exit 1
fi
