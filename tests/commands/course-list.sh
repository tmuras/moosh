#!/bin/bash -x
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD course-list | grep '"1","0","Courses","Courses","1"'; then
  exit 0
else
  exit 1
fi