#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD config-get user | grep enablecourseinstances ; then
  exit 0
else
  exit 1
fi