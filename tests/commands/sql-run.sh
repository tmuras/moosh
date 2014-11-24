#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD sql-run "SELECT * FROM {user} WHERE username='mooshtest'" | grep "test@example.com"; then
  exit 0
else
  exit 1
fi
