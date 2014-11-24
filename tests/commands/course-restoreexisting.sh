#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD course-backup -f testbackup.mbz 2
$MOOSHCMD course-restoreexisting testbackup.mbz 1
rm testbackup.mbz
if echo "SELECT * FROM mdl_course WHERE id = '1'" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"; then
  exit 0
else
  exit 1
fi
