#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh activity-add assign | grep 1 ; then
  :
else
  exit 1
fi
# What `moosh act assign 2` should actually do?

if moosh user-c | grep "Not enough arguments" ; then
  :
else
  exit 1
fi

moosh user-c -f john -l doe johndoe
if  moosh user-list | grep johndoe ; then
  :
else
  exit 1
fi

if moosh --verbose user-c | grep "Top Moodle dir" ; then
  :
else
  exit 1
fi

if moosh asd | grep "No command provided" ; then
  :
else
  exit 1
fi

exit 0