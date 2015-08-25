#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR
#rm -rf moodleplugins/mahara/
if $MOOSHCMD plugin-install assignfeedback_mahara 2.8 | grep "Done"; then
  exit 0
else
  exit 1
fi