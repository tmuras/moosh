#!/bin/bash -x
source functions.sh

install_db
install_data

cd $MOODLEDIR

if $MOOSHCMD apache-parse-missing-files "$MOODLEDATA/apachelog.log" | grep -w "1,1234"; then
  exit 0
else
  exit 1
fi
