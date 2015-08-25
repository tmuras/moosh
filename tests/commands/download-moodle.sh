#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -f moodle-2.9.tgz
$MOOSHCMD download-moodle
if ls | grep "moodle-2.9.tgz"; then
rm -f moodle-2.9.tgz
  exit 0
else
  exit 1
fi
