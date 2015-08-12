#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if $MOOSHCMD file-path 41cfeee5884a43a4650a851f4f85e7b28316fcc9 | grep "filedir/41/cf"; then
  echo 0
else
  echo 1
fi
