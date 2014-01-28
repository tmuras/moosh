#!/bin/bash
source config.sh

function install_db {
  cd ../data
  echo "DROP DATABASE $DBNAME" | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"
  echo "CREATE DATABASE $DBNAME" | mysql -u "$DBUSER" -p"$DBPASSWORD"
  mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" < "$DBNAME".sql
}

function install_data {
  rm -rf $MOOSH_DATA_DIR/*
  tar xjf moodledata.tar.bz2
  mv $PWD/moodledata/* $MOOSH_DATA_DIR
}
