#!/bin/bash
source config.sh

function install_db {
  #echo "DROP DATABASE $DBNAME" | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"
  #echo "CREATE DATABASE $DBNAME" | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"
  mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" < "$DBNAME".sql
}
