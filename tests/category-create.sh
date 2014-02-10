#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

moosh category-create hyperion
if moosh category-list | grep hyperion; then
  exit 0
else
  exit 1
fi
