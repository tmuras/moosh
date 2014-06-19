#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf blocks/testblock
moosh generate-block testblock
if ls blocks/testblock ; then
  exit 0
else
  exit 1
fi

