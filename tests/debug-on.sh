#!/bin/bash
source functions.sh

install_db
install_data

cd $MOODLEDIR

moosh debug-on
if echo "SELECT * FROM mdl_config WHERE name='debug' AND value='0'" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" | grep "debug"; then
  exit 1
else
 exit 0
fi

