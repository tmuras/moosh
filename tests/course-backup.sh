#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

export BACKUP=/tmp/mooshcoursebackup.mbz
rm -f $BACKUP
moosh course-backup -f $BACKUP 2
if ls $BACKUP; then
  rm coursebackup.mbz
  exit 0
else
  exit 1
fi
