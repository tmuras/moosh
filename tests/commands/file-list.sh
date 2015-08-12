#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD file-list -a id=1 | grep "background"; then
  exit 0
else
  exit 1
fi
