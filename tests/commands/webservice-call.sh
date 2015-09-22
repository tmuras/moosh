#!/bin/bash -x
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD webservice-call --token 4ac42118db3ee8d4b1ae78f2c1232afd --params userid=3 core_enrol_get_users_courses
exit 1
#if echo "SELECT * FROM mdl_forum WHERE name='forumtest1'"\
#       | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME"; then
#  exit 0
#else
#  exit 1
#fi
                                                                                  
                                                                                       
                                                                    
