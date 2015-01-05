#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD chkdatadir | grep "Checked dir" ; then
  echo 0
else
  echo 1
fi
