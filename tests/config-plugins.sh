#!/bin/bash
source functions.sh

install_db
install_data
cd $MOOSH_TEST_DIR

if moosh config-plugins quiz | grep -w "quiz"; then
  exit 0
else
  exit 1
fi
