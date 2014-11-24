#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD module-config set dropbox dropbox_secret 123
$MOOSHCMD module-config get dropbox dropbox_secret ?

if $MOOSHCMD theme-info | grep "Site themes" ; then
  exit 0
else
  exit 1
fi