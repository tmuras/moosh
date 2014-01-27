#!/bin/bash
source functions.sh

install_db
cd $MOOSH_TEST_DIR

moosh category-create hyperion
if moosh category-list | grep hyperion; then
  exit 0
else
  exit 1
fi
