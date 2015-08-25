#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf grade/report/testgradereport
$MOOSHCMD generate-gradereport testgradereport
if ls grade/report/testgradereport ; then
rm -rf grade/report/testgradereport
  exit 0
else
  exit 1
fi

