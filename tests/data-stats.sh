#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

output=$(moosh data-stats | head -1 | egrep '[-+]?([0-9]*\.[0-9]+|[0-9]+)' -o)
if [[ "$output" > 6.0 && "$output" < 10.0 ]] ; then
  exit 0
else
  exit 1
fi
