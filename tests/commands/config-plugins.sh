#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD config-plugins quiz | grep -w "quiz"; then
  exit 0
else
  exit 1
fi
