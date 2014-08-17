#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh file-upload config.php | grep "successfully" ; then
  echo 0
else
  echo 1
fi
