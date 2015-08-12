#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR



if $MOOSHCMD activity-delete 2 \
    | grep "Deleted activity"; then
  exit 0
else
  exit 1
fi


