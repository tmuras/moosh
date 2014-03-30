#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

moosh course-backup -f ${MOODLEDIR}/coursebackup.mbz 2
if ls | grep coursebackup.mbz; then
  rm coursebackup.mbz
  exit 0
else
  exit 1
fi
