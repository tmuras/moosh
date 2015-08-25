#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf blocks/testblock
$MOOSHCMD generate-block testblock
if ls blocks/testblock ; then
rm -rf blocks/testblock
  exit 0
else
  exit 1
fi

