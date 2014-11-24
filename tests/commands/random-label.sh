#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD random-label 2

if echo "SELECT * FROM mdl_label WHERE course = '2'"  \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" ; then
  exit 0
else
  exit 1
fi

