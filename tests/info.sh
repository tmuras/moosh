#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh info | grep "Plugin type:"; then
  exit 0
else
  exit 1
fi
