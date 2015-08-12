#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD user-mod --email newemail@example.com testteacher
if mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
  "SELECT * FROM mdl_user WHERE email = 'newemail@example.com'"; then
  exit 0
else
  exit 1
fi
