#!/bin/bash
source functions.sh

 install_db
 install_data
cd $MOODLEDIR
IMPORTQUESTION="$MOODLEDATA/importquestion.xml"

if $MOOSHCMD question-import $IMPORTQUESTION | grep "question" ; then
  exit 0
else
  exit 1
fi