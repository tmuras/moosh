#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD activity-add assign 2

if $MOOSHCMD activity-delete 1 \
    | grep "Deleted activity"; then
  exit 0
else
  exit 1
fi


