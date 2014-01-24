#!/bin/bash
source config.sh

mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" < "$DBNAME".sql
cd /var/www

moosh category-create hyperion
if moosh category-list | grep hyperion; then
  echo 1
else
  echo 0
fi
