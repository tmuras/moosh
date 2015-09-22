#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR



$MOOSHCMD activity-delete 5


if  echo "SELECT * FROM mdl_forum WHERE name='testactivityname'"\
        | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"\
| grep testactivityname; then
  exit 1 
else
  exit 0
fi

