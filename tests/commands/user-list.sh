#!/bin/bash -x
source functions.sh

install_db
install_data
cd $MOODLEDIR



if $MOOSHCMD user-list | grep testteacher ; then
  :
else
  exit 1
fi
