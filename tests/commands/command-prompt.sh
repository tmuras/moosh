#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD activity-add assign | grep 1 ; then
  :
else
  exit 1
fi
# What `$MOOSHCMD act assign 2` should actually do?

if $MOOSHCMD user-c | grep "Not enough arguments" ; then
  :
else
  exit 1
fi

$MOOSHCMD user-c -f john -l doe johndoe
if  $MOOSHCMD user-list | grep johndoe ; then
  :
else
  exit 1
fi

if $MOOSHCMD --verbose user-c | grep "Top Moodle dir" ; then
  :
else
  exit 1
fi

if $MOOSHCMD asd | grep "No command provided" ; then
  :
else
  exit 1
fi

exit 0