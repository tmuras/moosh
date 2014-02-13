#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

output=$(moosh data-stats | head -1)
if [[ "$output" -gt 6500 && "$output" -lt 10000 ]] ; then
  exit 0
else
  exit 1
fi
