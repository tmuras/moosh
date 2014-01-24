#!/bin/bash
source config.sh

mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" < "$DBNAME".sql
cd /var/www

moosh user-mod --email newemail@example.com testuser
if moosh user-list | grep newemail@example.com; then
  echo 1 
else
  echo 0 
fi
