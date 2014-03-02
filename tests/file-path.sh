#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh file-path da39a3ee5e6b4b0d3255bfef95601890afd80709 | grep "filedir/da/39"; then
  echo 0
else
  echo 1
fi
