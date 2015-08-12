#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

export BACKUP=/tmp/mooshcoursebackup.mbz
rm -f $BACKUP
$MOOSHCMD course-backup -f $BACKUP 2
if ls $BACKUP; then
#  rm $BACKUP
  exit 0
else
  exit 1
fi
