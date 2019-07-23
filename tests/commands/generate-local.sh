#!/bin/bash -x
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf local/polpol
$MOOSHCMD generate-local polpol

if ls local/polpol ; then
rm -rf local/polpol
  exit 0
else
  exit 1
fi

