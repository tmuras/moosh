#!/bin/bash 
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh activity-add assign 2 | grep 1; then
  exit 0
else
  exit 1
fi
