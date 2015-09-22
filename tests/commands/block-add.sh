#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR


$MOOSHCMD block-add category 1 test_block admin-course-category side-pre -1

if echo "SELECT * FROM mdl_block_instances WHERE blockname='test_block' \
        AND pagetypepattern='admin-course-category' \
        AND defaultregion='side-pre' AND defaultweight='-1';" \
    | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" | grep "test_block"; then
  exit 0
else
  exit 1
fi
