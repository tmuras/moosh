#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh user-list "id = 6" | grep testteacher ; then
  :
else
  exit 1
fi

if moosh user-list | grep testteacher ; then
  :
else
  exit 1
fi