#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD code-check | head -n 5 | grep "Registering sniffs in the moodle standard"; then
  :
else
  exit 1
fi

if $MOOSHCMD code-check -p webservice/lib.php  ; then
  :
else
  exit 2
fi