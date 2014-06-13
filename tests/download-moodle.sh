#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -f moodle-latest-23.tgz
moosh download-moodle -v 23
if ls | grep "moodle-latest-23"; then
  exit 0
else
  exit 1
fi
