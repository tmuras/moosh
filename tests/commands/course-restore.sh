#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD course-backup -f testbackup.mbz 2
$MOOSHCMD course-restore testbackup.mbz 3
rm testbackup.mbz
if echo "SELECT * FROM mdl_course WHERE category = '3'" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"; then
  exit 0
else
  exit 1
fi
