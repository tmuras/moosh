#!/bin/bash
source functions.sh

install_db
install_data

cd $MOODLEDIR

if $MOOSHCMD category-import "$MOODLEDATA/categoryimport.xml" | grep -w "TestCategory"; then
  exit 0
else
  exit 1
fi
