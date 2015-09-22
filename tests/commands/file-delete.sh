#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD file-delete 1

if mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
"SELECT * FROM mdl_files WHERE id='1'"; then
  echo 0
else
  echo 1
fi
