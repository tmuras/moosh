#!/bin/bash
source functions.sh

install_db
install_data

cd $MOODLEDIR


if $MOOSHCMD maintenance-on | grep "Maintenance Mode Enabled"; then
 exit 0
else
 exit 1
fi
