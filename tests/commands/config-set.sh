#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD config-set newconfig test user 

if $MOOSHCMD config-get user | grep newconfig ; then
  exit 0
else
  exit 1
fi