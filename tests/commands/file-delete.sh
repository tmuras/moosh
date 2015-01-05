#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD file-delete 1

if SELECT * FROM mdl_files where id='1'; then
  echo 0
else
  echo 1
fi
