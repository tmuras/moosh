#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

moosh activity-add assign 2

if moosh activity-delete 1 \
    | grep "Deleted activity"; then
  exit 0
else
  exit 1
fi


