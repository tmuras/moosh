#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

cache_old=$(mysql --skip-column-names -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT cacherev FROM mdl_course WHERE id = 2")

$MOOSHCMD cache-course-rebuild --all
cache_new=$(mysql --skip-column-names -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT cacherev FROM mdl_course WHERE id = 2")

if [ $cache_old != $cache_new ] ; then
  exit 0
else
  exit 1
fi
