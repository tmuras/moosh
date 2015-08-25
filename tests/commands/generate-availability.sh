#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf availability/condition/test
$MOOSHCMD generate-availability test
if ls availability/condition/test ; then
rm -rf availability/condition/test

  exit 0
else
 exit 1
fi

