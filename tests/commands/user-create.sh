#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD user-create --password pass1234 --email me@example.com --city Szczecin\
 --country PL --firstname bruce --lastname wayne batman
if echo "SELECT * FROM mdl_user WHERE email='me@example.com'"\
	| mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"; then
  exit 0
else
  exit 1
fi
