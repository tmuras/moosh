#!/bin/bash 
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD activity-add assign 2 | grep 1; then
  exit 0
else
  exit 1
fi
