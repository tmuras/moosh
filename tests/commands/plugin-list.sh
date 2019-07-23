#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD plugin-list | head -n 1 | grep "assignfeedback_mahara,2.3,2.4,2.5,2.6,2.7,2.8,https://moodle.org/plugins/download.php/7927/assignfeedback_mahara_moodle28_2014111000.zip
"; then
  exit 0
else
  exit 1
fi