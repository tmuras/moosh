#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf question/type/testqtype
moosh generate-module testqtype
if ls question/type/testqtype ; then
  exit 0
else
  exit 1
fi

