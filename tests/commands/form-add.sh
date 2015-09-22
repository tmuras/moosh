#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR
$MOOSHCMD form-add tag testtag
if find -cmin 1 | grep "testform_form.php" ; then
  echo 0
else
  echo 1
fi
