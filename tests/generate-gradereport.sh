#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf grade/report/testgradereport
moosh generate-gradereport testgradereport
if ls grade/report/testgradereport ; then
  exit 0
else
  exit 1
fi

