#!/bin/bash
source functions.sh

install_db
install_data
cd $MOODLEDIR

if moosh form-add tag testtag | grep "$mform->addElement('tag', 'testtag' get_string('langkey', 'unknown'), $options);"; then
  echo 0
else
  echo 1
fi
