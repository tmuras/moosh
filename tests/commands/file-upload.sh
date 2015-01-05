#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD file-upload config.php | grep "successfully" ; then
  echo 0
else
  echo 1
fi
