#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR/mod/chat



if $MOOSHCMD dev-versionbump | grep "Bumped"; then

  exit 0
else
  exit 1
fi
