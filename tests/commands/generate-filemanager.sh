#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR
 
if $MOOSHCMD generate-filemanager | grep "Code template for managing the form with filepicker" ; then
  exit 0
else
  exit 1
fi

