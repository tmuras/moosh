#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD role-update-capability student mod/forumn:viewrating allow 1

if echo "SELECT * FROM mdl_role_capabilities WHERE contextid='1' \
        AND capability='mod/forumn:viewrating' AND permission='1' AND roleid='5'"  \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" ; then
  exit 0
else
  exit 1
fi

