#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

moosh course-backup -f ${MOOSH_TEST_DIR}/coursebackup.mbz 2
if moosh course-restore coursebackup.mbz 1; then
  rm coursebackup.mbz
  exit 0
else
  exit 1
fi
