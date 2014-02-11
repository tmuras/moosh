#!/bin/bash
source functions.sh

install_db
install_data
cd $MOOSH_TEST_DIR

if moosh user-list | grep testteacher ; then
  exit 0
else
  exit 1
fi