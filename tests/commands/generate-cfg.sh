#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR
 
if $MOOSHCMD generate-cfg | grep "class moodle_config" ; then
  exit 0
else
  exit 1
fi

