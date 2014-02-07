#!/bin/bash
source functions.sh

install_db
install_data
cd $MOOSH_TEST_DIR

if moosh user-list "id = 6" | grep testteacher ; then
  exit 0
else
  exit 1
fi