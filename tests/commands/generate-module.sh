#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf mod/testmodule/
$MOOSHCMD generate-module testmodule
rm -rf mod/testmodule/
if ls mod/testmodule/ ; then
rm -rf mod/testmodule/
  exit 0
else
 exit 1
rm -rf mod/testmodule/
fi

