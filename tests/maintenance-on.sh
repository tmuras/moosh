#!/bin/bash
source functions.sh

install_db
install_data

cd $MOODLEDIR

moodle_url=$(echo $MOODLEDIR | grep -oP "[^\/]+$")

moosh maintenance-on
if curl http://127.0.0.1/$moodle_url/ | grep "The site is undergoing maintenance and is currently not available"; then
 exit 0
else
 exit 1
fi