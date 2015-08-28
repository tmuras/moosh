#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

CACHE_OLD=$(mysql --skip-column-names -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT cacherev FROM mdl_course WHERE id = 3")




if $MOOSHCMD cache-course-rebuild 3 | grep "Succesfully rebuilt cache for course 3"   ; then
  :
else
  exit 1
fi

CACHE_NEW=$(mysql --skip-column-names -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT cacherev FROM mdl_course WHERE id = 3")


if [ $CACHE_OLD -lt $CACHE_NEW ]  ; then
  exit 0
else
  exit 2
fi