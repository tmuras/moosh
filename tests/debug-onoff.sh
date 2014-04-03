#!/bin/bash
source functions.sh

install_db
install_data

cd $MOODLEDIR

moodle_url=$(echo $MOODLEDIR | grep -oP "[^\/]+$")

moosh debug-on
if curl http://127.0.0.1/$moodle_url/ | grep "core/databasemeta"; then
  :
else
 exit 1
fi

moosh debug-off
if ! curl http://127.0.0.1/$moodle_url/ | grep "core/databasemeta"; then
  exit 0 
else
  exit 1
fi

