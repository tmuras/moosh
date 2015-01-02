#!/bin/bash
source functions.sh

install_db
install_data

cd $MOODLEDIR

$MOOSHCMD debug-off
if echo "SELECT * FROM mdl_config WHERE name='debug' AND value='0'\G" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" | grep "name: debug"; then
  exit 0
else
 exit 1
fi