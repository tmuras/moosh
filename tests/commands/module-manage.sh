#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD module-manage hide assign

if echo "SELECT * FROM mdl_modules WHERE name='assign' AND visible='0'" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" ; then
  :
else
  exit 1
fi

$MOOSHCMD module-manage show assign

if echo "SELECT * FROM mdl_modules WHERE name='assign' AND visible='1'" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" ; then
  exit 0
else
  exit 1
fi