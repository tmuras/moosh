#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

export BACKUP=/tmp/mooshactivitybackup.mbz
rm -f $BACKUP
$MOOSHCMD activity-backup -f $BACKUP 1
if ls $BACKUP; then
#  rm $BACKUP
  exit 0
else
  exit 1
fi
