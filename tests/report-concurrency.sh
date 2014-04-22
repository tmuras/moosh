#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh report-concurrency -f 2014-01-01 -t 2014-03-01 | grep "users online" ; then
  exit 0
else
  exit 1
fi