#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh course-list testcourse1 | grep testcourse ; then
  exit 0
else
  exit 1
fi

