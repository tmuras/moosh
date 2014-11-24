#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -f testform_form.php
$MOOSHCMD generate-form testform
if ls | grep "testform_form.php" ; then
  exit 0
else
  exit 1
fi

