---
title: moosh Development
layout: default
---


Behat testing with docker
=======================

The idea is to use Moodle Behat integration to test moosh functionality.
To run Behat tests, Moodle plugin is needed so a [local plugin mooshtest](https://github.com/tmuras/moosh/tree/master/tests/mooshtest) was created.

It runs external moosh command with 

    And I run moosh...
    
And can check the output of the moosh command with

    Then moosh command "..." contains "..."

See [existing feature files](https://github.com/tmuras/moosh/tree/master/tests/mooshtest/tests/behat) for examples.

Few hacks are needed in order to run the testing like that. Normally when Moodle runs behat tests, it is in "behat mode".
It uses a copy of DB and data (by default with a b_ prefix). If we run external command (moosh), it bootstrap Moodle in "normal mode".
Below are the instructions on how to set up mooshtest local plugin + vanilla Moodle 3.7 + moosh.

####Do the following steps to install and run moosh tests:

Create new folder:

    mkdir ~/moosh-testing 
     
Enter into the folder: 

    cd ~/moosh-testing


Clone Moodle HQ docker repository and Moodle code into  ~/moosh-testing:

    git clone https://github.com/moodlehq/moodle-docker.git  
    git clone -b MOODLE_37_STABLE git://git.moodle.org/moodle.git

Go into the moodle directory and download the moosh code:

    cd ~/moosh-testing/moodle
    git clone git://github.com/tmuras/moosh.git

Go into ~/moosh-testing/moodle/moosh and install moosh dependencies:

    cd ~/moosh-testing/moodle/moosh
    composer install

In the moodle directory, **move** tests from ~/moodle/moosh/tests/mooshtest into ~/moodle/local:


    cd  ~/moosh-testing/moodle
    mv moosh/tests/mooshtest local


Initialize environment for behat:

    export MOODLE_DOCKER_WWWROOT=~/moosh-testing/moodle
    export MOODLE_DOCKER_DB=mysql

copy template file as config to moodle:

    cd ~/moosh-testing/moodle-docker
    cp config.docker-template.php $MOODLE_DOCKER_WWWROOT/config.php


Run Docker container:

    bin/moodle-docker-compose up -d


Disable Moodle check for the DB prefix. In ~/moosh-testing/moodle/lib/behat/lib.php, comment out those 2 lines:

    //behat_error(BEHAT_EXITCODE_CONFIG,
    //'$CFG->behat_prefix in config.php must be different from $CFG->prefix');


Edit ~/moosh-testing/moodle/config.php directory and change:


    $CFG->prefix = 'm_';

To

    $CFG->prefix = 'b_';


Get into the web container:

    cd ~/moosh-testing/moodle-docker
    bin/moodle-docker-compose exec webserver bash

In your console you should be logged in as root (you should see something like this root@b5ba7a659e83:/var/www/html#), run the following commands:


    apt-get update
    apt-get install sudo
    chown -R www-data /var/www
    sudo -u www-data -E -H bash


Then you should be logged in as www-data (and see something like this www-data@b5ba7a659e83:~/html$)


Run then:

    cd /var/www/html
    php admin/tool/behat/cli/init.php


At the end of the installation, it should show local_mooshtest being installed:

-->local_mooshtest

++ Success ++
______________
When you get warning like this one:

PHP Warning:  PHP Startup: Unable to load dynamic library '/usr/local/lib/php/extensions/no-debug-non-zts-20160303/oci8.so' - libmql1.so: cannot open shared object file: No such file or directory in Unknown on line 0

Run this command:

    export LD_LIBRARY_PATH=/usr/local/instantclient_12_1/


Now you can run any moosh test with command:

    php admin/tool/behat/cli/run.php --format pretty --tags=@moosh


Or test any command:

    moosh/moosh.php moosh-command

Example:

    moosh/moosh.php course-list


####Exit, stop and remove container:

To exit from the interactive container, type:

    exit    

To stop the container:

    bin/moodle-docker-compose stop

To stop and remove te container:

    bin/moodle-docker-compose down
_____________________________________
####Quick steps to rerun the tests:

    export MOODLE_DOCKER_WWWROOT=~/moosh-testing/moodle
    export MOODLE_DOCKER_DB=mysql

    cd $MOODLE_DOCKER_WWWROOT
    bin/moodle-docker-compose up -d
    bin/moodle-docker-compose exec webserver bash

    apt-get update
    apt-get install sudo

    chown -R www-data /var/www
    sudo -u www-data -E -H bash

    php admin/tool/behat/cli/init.php
    php admin/tool/behat/cli/run.php --format pretty --tags=@moosh



Vagrant
=======

moosh comes with vagrant setup, which will give you the following moosh development environment:
 
 * Ubuntu 16.04 with PHP 7
 * Apache configured to run as user vagrant (so no problems with the file permissions)
 * MySQL
 * Latest Moodle 3.1 installed
 * composer and moosh installed from source
 
Simply:

    % git clone https://github.com/tmuras/moosh
    % vagrant up
 
Your Moodle 3.1 is now available at http://192.168.33.10/moodle/ (login "admin", password "a").
 PhpMyAdmin URL is  http://192.168.33.10/phpmyadmin (MySQL login "root" and "mypassword).
 
Once you SSH into your box with:
 
     % vagrant ssh
     
You can run moosh inside the Moodle 3.1 installation:
 
     % cd /var/www/html/moodle
     % moosh user-list
     
The source code of moosh is under vagrant's home in /home/vagrant/moosh-src and calling "moosh" 
 command will actually call /home/vagrant/moosh-src/moosh.php.
  
The directory /home/vagrant/moosh-src is shared with your host machine as "moosh-src", in directory
 where you have cloned the git repository. This is so you can use your favourite IDE on your host PC.

So - no excuses now - use pre-configured environment, develop some awesome moosh commands and send 
 them to me! 

Performance information
=======================

You can use global option -t (or long version --performance) to show extra performance information collected while the command runs:

    % mooshdev -t course-backup 2
    ... <output cut> ...
    *** PERFORMANCE INFORMATION ***
    Run from 2014-11-26T11:21:15+01:00 to 2014-11-26T11:21:16+01:00
    Real time run 0.667 seconds
    Server load before running the command: 0.14 0.16 0.16 1/584 6180
    Server load after: 0.14 0.16 0.16 1/584 6180
    Ticks: 67 user: 36 sys: 3 cuser: 0 csys: 0
    Memory use before command run (internal/real): 19058864/19136512 (18.18 MB/18.25 MB)
    Memory use after:  42252496/44040192 (40.30 MB/42.00 MB)
    Memory peak: 43679224/45088768  (41.66 MB/43.00 MB)
    *******************************



Functional tests
================

There are no unit tests implemented for testing moosh at the moment. Instead, a set of functional tests have been developed.
They are basically very simple bash scripts located in tests directory and named after command they test, e.g.:

    tests/file-list.sh

is used to test moosh file-list command.

All tests start with some common boilerplate:

    #!/bin/bash
    source functions.sh

    install_db
    install_data
    cd $MOODLEDIR

and then the test of the commmand is performed. Script should return (exit) with 0 if test is a success, with 1 otherwise. Here is test from file-list.sh:

    if moosh file-list id=6 | grep -w "grumpycat"; then
      exit 0
    else
      exit 1
    fi

All tests are then run with run-tests.php script, which in turn will generate status on the <a href="http://moosh-online.com/ci/">continues integration</a> page.


Environment
-----------

Notice in the test above that test suite assumes there is Moodle instance already setup and it contains a file called "grumpycat".
All commands will be run in a known, prepared environment with users, courses pre-created. “install moodle” means restoring Moodle DB and data from prepared snapshot.

Set up & run functional tests
--------------------------------

Some scripts use sudo chown command to operate on Moodle data, so to let them run without prompting for password add something like this to /etc/sudoers (use visudo to edit):

    %adm    ALL = NOPASSWD: /bin/chown, /bin/chmod

Then make sure your shell user is in group adm.

Create 2 directories for Moodle data, eg: ~/data/moosh-test/moodledata25 and ~/data/moosh-test/moodledata26. Give apache user write access to Moodle data dirs.

Create 2 empty databases: mooshtest_25 and mooshtest_26.

    #Get Moodle source code for 2.6 and 2.7
    cd ~/www/html/moosh/
    git clone https://github.com/moodle/moodle.git moodle25
    cd moodle25
    git checkout 3d176316cc1791e258a7c1b2118fd35976c9bcae
    cp config-dist.php config.php
    #configure settings in config.php down to & including $CFG->dataroot

    cd ~/www/html/moosh/
    cp -r moodle25 moodle26
    cd moodle26
    git checkout ba05f57
    cp config-dist.php config.php
    #configure settings in config.php down to & including $CFG->dataroot

    git clone https://github.com/tmuras/moosh
    cd moosh/tests
    #Configure DATA,DB,DBUSER and DBPASSWORD in restore_all.sh and run it
    ./restore_all.sh

Login to Moodle instances (e.g. http://localhost/moosh/moodle26/) as 'admin' using password 'a' and check if it works OK after restore.

    cp config-template.sh config25.sh
    cp config-template.sh config26.sh
    #configure variables in config25.sh and config26.sh

    #run tests, several should pass but some eventually fail:
    php run-tests.php


Contributing to moosh
=====================

1. Fork the project on github.
2. Follow "installation from Moodle git" section.
3. Look at existing plugins to see how they are done.
4. Create new plugin/update existing one. You can use moosh itself to generate a new command from a template for you:

    moosh generate-moosh category-command

5. Update this README.md file with the example on how to use your plugin.
6. For the extra bonus create a functional test to cover your command.
7. Send me pull request.


Local commands
==============

You can add your own, local commands to moosh by storing them in the same structure as moosh does but under ~/.moosh.
For example, to create your custom command dev-mytest that works with any Moodle version, you would put it under:

    ~/.moosh/Moosh/Command/Generic/Dev/MyTest.php



