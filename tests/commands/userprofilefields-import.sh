#!/bin/bash -x
source functions.sh

install_db
install_data
cd $MOODLEDIR


if $MOOSHCMD userprofilefields-import user_profile_fields.csv; then
  exit 0
else
  exit 1
fi
