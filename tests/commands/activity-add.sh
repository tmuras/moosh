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

$MOOSHCMD activity-add activity-add -n 'more emails' -o="--intro=\"polite orders.\" --grade=3 --subnet=192.168.2.2" quiz 2

if echo "SELECT * FROM mdl_quiz WHERE name='more emails' AND intro='polite orders' AND grade=3 AND subnet='192.168.2.2' AND course=2"\
        | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"; then
  exit 0
else
  exit 1
fi
