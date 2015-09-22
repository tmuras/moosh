#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD plugin-uninstall assignfeedback_mahara; then
  exit 1
else
  exit 1
fi