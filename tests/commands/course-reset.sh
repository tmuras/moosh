#!/bin/bash -x
source functions.sh

install_db
install_data
cd $MOODLEDIR

$MOOSHCMD course-reset -s "reset_gradebook_grades=0" 3
if  !(mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT * FROM mdl_grade_grades WHERE rawgrade='50.00000'"\
    | grep 50.00000); then
exit 1

fi

$MOOSHCMD course-reset -s "reset_gradebook_grades=1" 3
if  mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT * FROM mdl_grade_grades WHERE rawgrade='50.00000'"\
    | grep 50.00000; then
exit 1

fi
$MOOSHCMD course-reset -s "reset_assign_submissions=1" 3
 if  mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT * FROM mdl_assign_submission WHERE status='submitted'"\
    | grep submitted; then
  exit 1
fi
$MOOSHCMD course-reset -s "unenrol_users=5," 3
if  mysql -u "$DBUSER" -p"$DBPASSWORD" "$DBNAME" -e \
    "SELECT * FROM mdl_user_enrolments WHERE id='3'"\
    | grep 3; then
  exit 1

fi