#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

moosh course-create newcourse
if moosh course-list newcourse; then
  exit 0
else
  exit 1
fi
