#!/bin/bash
source config.sh

if [ -z "${DBNAME}" ]; then
    echo "Moodle DB is not set up in \$DBNAME"
    exit 1
fi

if [[ ! $(echo select 1 | mysql -u $DBUSER -p$DBPASSWORD $DBNAME) ]]; then
    echo Could not connect to the database, check \$DBUSER, \$DBNAME and \$DBPASSWORD
fi

if [ -z "${MOODLEDIR}" ]; then
    echo "Moodle directory \$MOODLEDIR is not set"
    exit 1
fi

if [ -z "${MOODLEDATA}" ]; then
    echo "Moodle data directory \$MOODLEDATA is not set"
    exit 1
fi


exit 0
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