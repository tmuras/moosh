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
#export WWW="~/www/moosh-test"

for V in $VERSIONS; do
    echo Restoring $V

    #clean existing
    rm -rf $DATA/moodledata$V
    echo "DROP DATABASE IF EXISTS $DB$V" | mysql -u "$DBUSER" -p"$DBPASSWORD"

    #unpack
    cd $GITCHECKOUT/data
    tar -xf moodledata$V.tar.bz2
    mv moodledata$V $DATA
    chmod 777 -R $DATA/moodledata$V

    #apply
    echo "CREATE DATABASE $DB$V" | mysql -u "$DBUSER" -p"$DBPASSWORD"
    bzip2 -dc moodle$V.sql.bz2 | mysql -u "$DBUSER" -p"$DBPASSWORD" "$DB$V"

    #clean up
done
