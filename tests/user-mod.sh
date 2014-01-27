#!/bin/bash
source functions.sh

install_db
cd $MOOSH_TEST_DIR

moosh user-mod --email newemail@example.com testuser
if moosh user-list | grep newemail@example.com; then
  exit 0
else
  exit 1
fi
