#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR



if $MOOSHCMD event-list | grep "assignsubmission"; then
  echo 0
else
  echo 1
fi
