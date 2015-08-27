#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR


if $MOOSHCMD user-c | grep "Not enough arguments" ; then
  :
else
  exit 1
fi


if $MOOSHCMD --verbose user-c | grep "Top Moodle dir" ; then
  :
else
  exit 2
fi

if $MOOSHCMD asd | grep "No command provided" ; then
  :
else
  exit 3
fi

if $MOOSHCMD -t user-list | grep "PERFORMANCE INFORMATION" ; then
  :
else
  exit 4
fi

if ($MOOSHCMD cohort | grep -v "cohort"="") ; then
  :
else
  exit 5
fi

exit 0