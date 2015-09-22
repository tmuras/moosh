#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

rm -rf question/type/testqtype
$MOOSHCMD generate-qtype testqtype
if ls question/type/testqtype ; then
rm -rf question/type/testqtype
rm -rf mod/testqtype
  exit 0
else
  exit 1
fi

