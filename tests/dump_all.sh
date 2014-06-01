#!/bin/sh

#set -x
set -e

# Create or restore all data directories from git repository to test locations.
# Run from "tests directory"
export GITCHECKOUT=$(cd .. && pwd -P)
export VERSIONS="25 26"

export DATA="/home/tomasz/data/moosh-test"
export DB="mooshtest_"
export DBUSER=root
export DBPASSWORD=
export SHELLUSER=
#export WWW="~/www/moosh-test"

for V in $VERSIONS; do
    rm $GITCHECKOUT/data/moodledata$V.tar.bz2
    echo Dumping Moodle $V files
    cd $DATA/moodledata$V/ || exit 1
    sudo chown -R $SHELLUSER .
    rm -rf cache localcache  muc  sessions  temp
    cd ..
    tar -cjf moodledata$V.tar.bz2 moodledata$V
    mv moodledata$V.tar.bz2 $GITCHECKOUT/data

    rm $GITCHECKOUT/data/moodle$V.sql.bz2
    echo Dumping Moodle $V database
    mysqldump -u "$DBUSER" -p"$DBPASSWORD" "$DB$V" | bzip2 -c > moodle$V.sql.bz2
    mv moodle$V.sql.bz2 $GITCHECKOUT/data

done
