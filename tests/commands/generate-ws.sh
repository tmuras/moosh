#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf local/test_ws
$MOOSHCMD generate-ws test_ws
if ls local/test_ws;then
rm -rf local/test_ws
   exit 0
else
    exit 1
fi

