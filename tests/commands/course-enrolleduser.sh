#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD course-enrol 2 testuser
if $MOOSHCMD course-enrolleduser student 2 \
    | grep "5"; then
  exit 0
else
  exit 1
fi
