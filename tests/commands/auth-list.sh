#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD auth-list | grep "List of enabled auth plugins:"; then
  exit 0
else 
  exit 1
fi 