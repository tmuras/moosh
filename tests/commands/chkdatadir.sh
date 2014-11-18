#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh chkdatadir | grep "Checked dir" ; then
  echo 0
else
  echo 1
fi
