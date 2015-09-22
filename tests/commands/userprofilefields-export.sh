#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD userprofilefields-export
if ls $MOODLEDIR | grep "user_profile_fields.csv"; then
  exit 0
else
  exit 1
fi
