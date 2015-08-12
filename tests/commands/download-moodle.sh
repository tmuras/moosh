#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -f moodle-latest-29.tgz
$MOOSHCMD download-moodle -v 29
if ls | grep "moodle-latest-29"; then
  exit 0
else
  exit 1
fi
