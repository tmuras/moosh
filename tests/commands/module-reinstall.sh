#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD module-reinstall assign | grep "Success" ; then
  exit 0
else
  exit 1
fi

