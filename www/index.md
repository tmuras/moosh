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

Moosh will always try to use Moodle's superuser. But you can change on what user you want to execute specific command. For example:

    moosh -u testuser course-backup 1 

will execute command `course-backup` for user with name `testuser`

Requirements
============

PHP 5.3+, Moodle 1.9, 2.2 or higher.

# <a name="installation"></a>Installation

Installation from Ubuntu package
--------------------------------

     sudo apt-add-repository 'deb http://ppa.launchpad.net/zabuch/ppa/ubuntu trusty main'
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
    composer install

Common steps for Moodle package and git
---------------------------------------

Link to a location that is set in your $PATH, eg:

    ln -s $PWD/moosh.php ~/bin/moosh

Or system-wide:

    sudo ln -s $PWD/moosh.php /usr/local/bin/moosh


xdotool integration
===================

You can automate some of the manual tasks (like refreshing browser page after adding a form) by using xdotool. First, install xdotool:

    apt-get install xdotool

Then go to ~/.mooshrc.php and add these flags:

    $defaultOptions['global']['xdotool'] = true;
    $defaultOptions['global']['browser_string'] = 'Mozilla Firefox';

Change Mozilla Firefox to your preferred browser and you're good to go. Commands that currently support xdotool:

    form-add

<span class="anchor" id="cfg-auto-completion"></span>
$CFG auto-completion
====================

You can use moosh to generate fake class moodle_config which will contain public properties extracted from your current
 Moodle. Properties will have PHP doc based on Moodle's documentation. If you're lazy, simply 
 <a href="https://raw.githubusercontent.com/tmuras/moosh/master/includes/config.class.php">download</a> <a href="https://github.com/tmuras/moosh/blob/master/includes/config.class.php">config class for Moodle 2.9</a>.
  
To get it to work with PHP Storm, simply drop that file somewhere into your Moodle project files.
![$CFG autocompletion in PHP Storm](/images/cfg_autocompletion_phpstorm.png)

With NetBeans, add line in your code:
     
     /* @var $CFG moodle_config */
     
![$CFG autocompletion in PHP Storm](/images/cfg_autocompletion_netbeans.png)
     
# <a name="praise"></a>moosh praise


> _Fan-effing-tastic! Thank you. I've used Drush and it is so incredibly
> helpful. I just got this running on win 2k8 (not my choice) and it is
> useful as hell. Thanks!_
>
> Jeff Masiello

<br />

> _Soooo beautiful :-) Thank you!_
>
> Nadav Kavalerchik

<br />

> _Using moosh, we have cut the number of hours required to prepare for each quarter from 120 to about 12.  Thanks for the awesome tool!_
>
> Kevin Metcalf
