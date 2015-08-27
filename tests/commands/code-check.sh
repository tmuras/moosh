#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD code-check -p mod/quiz/classes/output | grep "0 errors, 0 warnings"; then
  exit 0
else
  exit 1
fi
