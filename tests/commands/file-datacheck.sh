#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR



if $MOOSHCMD file-datacheck | grep "The contents of moodledata appear to be OK."; then
  echo 0
else
  echo 1
fi
