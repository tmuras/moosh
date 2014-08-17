#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if  moosh role-update-contextlevel --system-on student | grep "successfuly"; then
  exit 0
else
  exit 1
fi
