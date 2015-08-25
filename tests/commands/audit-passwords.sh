#!/bin/bash -x

source functions.sh

install_db
install_data
cd $MOODLEDIR



if $MOOSHCMD audit-passwords -u 10 | grep "User with known (easily crackable) password: weakpassuser"
 then
  exit 0
else
  exit 1
fi
