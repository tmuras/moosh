#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh report-concurrency -f 2014-07-27 -t 2014-07-29 | grep "2014-07-28 10:05:00 users online: 1" ; then
  exit 0
else
  exit 1
fi