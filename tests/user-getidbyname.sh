#!/bin/bash
source config.sh

mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" < "$DBNAME".sql
cd /var/www

userid=`moosh user-getidbyname testuser`
if moosh user-list | grep "$userid"; then
  echo 1
else
  echo 0
fi
