#!/bin/bash
source config.sh

function install_db {
  cd ../data
  bzip2 -dk moodle.sql.bz2
  echo "DROP DATABASE $DBNAME" | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"
  echo "CREATE DATABASE $DBNAME" | mysql -u "$DBUSER" -p"$DBPASSWORD"
  mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" < "$DBNAME".sql
}

function install_data {
  DIR_PATH=$(readlink -f "${MOODLEDATA}")

  if [[ -d "${DIR_PATH}" ]] ; then
    rm -rf $MOODLEDATA/*
    tar xjf moodledata.tar.bz2
    mv $PWD/moodledata/* $MOODLEDATA
  else
    exit 1
  fi
}