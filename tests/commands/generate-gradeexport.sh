#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf grade/export/test
$MOOSHCMD generate-gradeexport test
if ls grade/export/test ; then
rm -rf grade/export/test
  exit 0
else
  exit 1
fi

