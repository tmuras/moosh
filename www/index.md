---
title: moosh - Moodle commandline helper
layout: default
---

Introduction
============

Moosh stands for MOOdle SHell. It is a commandline tool that will allow you to perform most common Moodle tasks. It's inspired by Drush - a similar tool for Drupal.
moosh is licenced under GNU GPL v3 or any later.

I've created it when I realized how much time I waste each time I debug/test some Moodle issue and need to setup my environment.
Here is for example how you can create 5 Moodle user accounts with moosh:

    cd /moodle/root/installation
    moosh user-create user_{1..5}

Basic usage
===========

Basically cd into your Moodle installation & run moosh and one of the many commands it implements. Some (few) commands will work when not in Moodle directory - for example:

    moosh download-moodle

If you don't know the exact name of the command you want to run but know the part of it, run moosh with the substring:

    moosh user

As a result you will get a list of all commands that contain string "user":

    course-enrolleduser
    user-create
    user-delete
    user-getidbyname
    user-list
    user-mod

Requirements
============

PHP 5.3+, Moodle 1.9, 2.2 or higher.

Installation
============

Installation from Ubuntu package
--------------------------------

     sudo apt-add-repository 'deb http://ppa.launchpad.net/zabuch/ppa/ubuntu precise main'
     sudo apt-get update
     sudo apt-get install moosh

Installation from Moodle package
--------------------------------

Download moosh package from Moodle: https://moodle.org/plugins/view.php?id=522, unpack and cd into the directory.
Follow "common steps" below.

Installation from Moodle git
----------------------------

Install composer - see http://getcomposer.org/download .

    git clone git://github.com/tmuras/moosh.git
    cd moosh
    ./composer.phar update

Common steps for Moodle package and git
---------------------------------------

Link to a location that is set in your $PATH, eg:

    ln -s $PWD/moosh.php ~/bin/moosh

Or system-wide:

    sudo ln -s $PWD/moosh.php /usr/local/bin/moosh


Local commands
==============

You can add your own, local commands to moosh by storing them in the same structure as moosh does but under ~/.moosh.
For example, to create your custom command dev-mytest that works with any Moodle version, you would put it under:

    ~/.moosh/Moosh/Command/Generic/Dev/MyTest.php

xdotool integration
===================

You can automate some of the manual tasks (like refreshing browser page after adding a form) by using xdotool. First, install xdotool:

    apt-get install xdotool

Then go to ~/.mooshrc.php and add these flags:

    $defaultOptions['global']['xdotool'] = false;
    $defaultOptions['global']['browser_string'] = 'Mozilla Firefox';

Change Mozilla Firefox to your preferred browser and you're good to go. Commands that currently support xdotool:

    form-add

Contributing to moosh
=====================

1. Fork the project on github.
2. Follow "installation from Moodle git" section.
3. Look at existing plugins to see how they are done.
4. Create new plugin/update existing one. You can use moosh itself to generate a new command from a template for you:

    moosh generate-moosh category-command

5. Update this README.md file with the example on how to use your plugin.
6. Send me pull request.

moosh praise
============

_Fan-effing-tastic! Thank you. I've used Drush and it is so incredibly
helpful. I just got this running on win 2k8 (not my choice) and it is
useful as hell. Thanks!_

_Jeff Masiello_



_Soooo beautiful :-) Thank you!_

_Nadav Kavalerchik_
