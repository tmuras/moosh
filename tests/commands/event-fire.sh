#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR



if $MOOSHCMD event-fire subscription_deleted 1 | grep "Array
(
    [0] => mod_forum\event\subscription_deleted
    [1] => tool_monitor\event\subscription_deleted
)
" ; then
  echo 0
else
  echo 1
fi
