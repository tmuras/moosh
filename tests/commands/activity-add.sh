#!/bin/bash -x
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD activity-add -n forumtest1 forum 2

if echo "SELECT * FROM mdl_forum WHERE name='forumtest1'"\
        | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"; then
  exit 0
else
  exit 1
fi
~                                                                                       
~                                                                                       
~                                                                    
