---
title: moosh Development
layout: default
---

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

Login to Moodle instances (e.g. http://localhost/moosh/moodle26/) as admin / a and check if it works OK after restore.

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