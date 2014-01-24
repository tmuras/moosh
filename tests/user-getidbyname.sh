#!/bin/bash
source functions.sh

install_db
cd /var/www

userid=`moosh user-getidbyname testuser`
if moosh user-list | grep "$userid"; then
  exit 0
else
  exit 1
fi
