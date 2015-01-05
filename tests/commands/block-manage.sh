#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD block-manage hide badges
if echo "SELECT * FROM mdl_block WHERE name = 'badges' AND visible='0'" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" | grep "badges"; then
  :
else
  exit 1
fi

$MOOSHCMD block-manage show badges
if echo "SELECT * FROM mdl_block WHERE name = 'badges' AND visible='1'" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" | grep "badges"; then
  exit 0
else
  exit 1
fi