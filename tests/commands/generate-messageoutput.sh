#!/bin/bash -x
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf message/output/extest/
$MOOSHCMD generate-messageoutput extest

if ls message/output/extest ; then
rm -rf message/output/extest/
  exit 0
else
  exit 1
fi

