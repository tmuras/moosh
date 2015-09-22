#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR


$MOOSHCMD generate-moosh 
#if  ; then

#  exit 0
#else
#  exit 1
#fi

