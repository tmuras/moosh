#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf mod/testmodule
$MOOSHCMD generate-module testmodule
if ls mod/testmodule ; then
  exit 0
else
  exit 1
fi

