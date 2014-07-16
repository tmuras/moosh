#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh plugin-fetchinfo --limit=1 | grep "Fetching"; then
  exit 0
else
  exit 1
fi

