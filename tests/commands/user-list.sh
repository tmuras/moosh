#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD user-list "id = 6" | grep testteacher ; then
  :
else
  exit 1
fi

if $MOOSHCMD user-list | grep testteacher ; then
  :
else
  exit 1
fi