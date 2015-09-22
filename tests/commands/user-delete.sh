#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD user-delete testteacher
if  !(mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT * FROM mdl_user WHERE email='testteacher@moodle.org'"\
    | grep testteacher@moodle.org); then
  exit 0
else
  exit 1
fi
