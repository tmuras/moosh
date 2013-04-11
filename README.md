Introduction
============

Moosh stands for MOOdle SHell. It is a commandline tool that will allow you to perform most common Moodle tasks. It's inspired by Drush - a similar tool for Drupal.
moosh is licenced under GNU GPL v3 or any later.

I've created it when I realized how much time I waste each time I debug/test some Moodle issue and need to setup my environment.
Here is for example how you can create 5 Moodle user accounts with moosh:

    cd /moodle/root/installation
    moosh user-create user_{1..5}


Requirements
============

PHP 5.3+, Moodle 2.3 or 2.4.

Installation
============

Installation from Moodle package
--------------------------------

Download moosh package from Moodle: https://moodle.org/plugins/view.php?id=522, unpack and cd into the directory.
Follow "common steps" below.

Installation from Moodle git
----------------------------

Install composer - see http://getcomposer.org/download .

    git clone git://github.com/tmuras/moosh.git
    cd moosh
    composer update

Common steps
------------

Link to a location that is set in your $PATH, eg:

    ln -s $PWD/moosh.php ~/bin/moosh

Or system-wide:

    sudo ln -s $PWD/moosh.php /usr/local/bin/moosh


Commands
========

user-create
-----------

Create a new Moodle user. Provide one or more arguments to create one or more users.

Example 1: create user "testuser" with the all default profile fields.

    moosh user-create testuser

Example 2: create user "testuser" with the all the optional values

    moosh user-create --password pass --email me@example.com --city Szczecin --country PL --firstname "first name" --lastname name testuser

Example 3: use bash/zsh expansion to create 10 users

    moosh user-create testuser{1..10}

The users will have unique email addresses based on the user name (testuser1, testuser2, testuser3...).


user-mod
--------

Modify user(s) account.

Example 1: change admin's user password and email

    moosh user-mod --email my@email.com --password newpwd admin

Example 2: change authentication method for users with ids 17,20,22

    moosh user-mod -i --auth manual 17 20 22


Example 3: use bash/zsh expansion to change password for users with ID between 100 and 200

    moosh user-mod -i --password x {100..200}

Example 4: update all users

    moosh user-mod --email my@email.com --password newpwd --auth manual --all


user-list
--------

List user accounts.

Example 1: list 10 user accounts

    moosh user-list -n 10


sql-run
-------

Run any custom SQL against bootstrapped Moodle instance DB. If query start with SELECT then matched rows will be displayed.

Example 1: Set the country of all the users to Poland

    moosh sql-run "update {user} set country='PL'"


Example 2: Count the number of rows is log table

    moosh sql-run "select count(*) from {log}"


course-create
-------------

Create a new course(s).

Example 1: Create 10 new courses using bash/zim expansion

    moosh course-create newcourse{1..10}

Example 2: Create new course

    moosh course-create --category 1 --fullname "full course name" --description "course description" shortname


course-enrol
------------

Enrol user(s) into a course id provided. First argument is a course ID, then put one or more user names.
Use -i for providing username IDs.

Example 1: Enroll username1 and username2 into course ID 21 as students.

    moosh course-enrol 21 username1 username2

Example 2: Enroll user with id 21 into the course with id 31 as a non-editing teacher.

    moosh course-enrol -r teacher -i 31 21


course-backup
-------------

Backup course with provided id.

Example 1: Backup course id=3 into default .mbz file in current directory:

    moosh course-backup 3

Example 2: Backup course id=3 and save it as /tmp/mybackup..mbz:

    moosh course-backup -f /tmp/mybackup.mbz 3


course-restore
--------------

Restore course from path/to/backup.mbz to category with a given id.

Example 1: Restore backup.mbz into category with id=1

    moosh course-restore backup_file category_id


role-create
-----------

Create new role, optionally provide description, archetype and name. Role id is returned.

Example 1: Create role with short name "newstudentrole" a description, name an archetype

    moosh role-create -d "Role description" -a student -n "Role name" newstudentrole


role-delete
-----------

Delete role by ID or shortname.

Example 1: Delete role "newstudentrole"

    moosh role-delete newstudentrole

Example 2: Delete role id 10.

    moosh role-delete -i 10


config-plugins
--------------

Shows all plugins that have at least one entry in the config_plugins table. Optionally provide an argument to match plugin name.

Example 1: Show all plugins from config_plugins table.

    moosh config-plugins

Example 2: Show all themes that have some settings.

    moosh config-plugins theme_


config-get
----------

Get config variable from config or config_plugins table. The syntax is based on get_config($plugin,$name) API function. Both arguments are optional.

Example 1: Show all core config variables.

    moosh config-get

Example 2: Show all config variables for "user"

    moosh config-get user

Example 3: Show core setting "dirroot"

    moosh config-get core dirroot


config-set
----------

Set config variable. The syntax of the command is based on the set_config() Moodle API:

    moosh config-set name value <plugin>

If third argument (plugin) is not provided then the variable is set in the core Moodle configuration table.

Example 1: Enable debug.

    moosh config-set debug 32767

Example 2: Set URL to logo for Sky High theme.

    moosh config-set logo http://example.com/logo.png theme_sky_high


file-list
---------

Search and list files from mdl_files table. The argument should be a valid SQL WHERE statement. Interesting columns of possible search criterias are:
contextid, component, filearea, itemid, filepath, filename, userid, filesize, mimetype, status, timecreated, timemodified.

The output will contain some defaults or nearly all possible file information if "-a|--all" flag is provided. The meaning of the flags column is (in order):

 * mdl_files.status
 * lowercase letter "d" if entry is a dicrectory
 * "e" if external file
 * "i" if a valid image
 * "m" if time created and time modified differ

With "-i" option only IDs are returned. This can be used when pipe-ing into other file-related commands.


Example 1: Show all legacy files for a course, which context id is 15

    moosh file-list "contextid=15 AND component='course' AND filearea='legacy'"

Example 2: Display full information on file with ID 17

    moosh file-list -a id=162


file-delete
-----------

Delete Moodle files from DB and possibly move them to trash. File IDs can be provided as arguments or on the standard input (with moosh file-delete -s).
--flush option will remove the trashcan directory.

Example 1: Remove files with IDs 10,20 and 30.

    moosh file-delete 10 20 30

Example 2: Remove all files with size greater than 100 bytes

    moosh file-list -i 'filesize>1000' | moosh file-delete -s

Example 3: Flush trashcan

    moosh file-delete --flush


file-path
---------

Show full path in the filesystem to a Moodle file. Files can be identified by ID or hash (auto-detected) as arguments or on stdin (-s option).

Example 1: Show path to a file with contenthash da39a3ee5e6b4b0d3255bfef95601890afd80709

    moosh file-path da39a3ee5e6b4b0d3255bfef95601890afd80709

Example 2: Show paths to files with ID bewteen 100 and 200

    moosh file-list -i 'id>100 AND id<200' | moosh file-path -s

Example 3: Like above but with no duplicates

    moosh file-list -i 'id>100 AND id<200' | moosh file-path -s | sort | uniq


clear-cache
-----------

The same as "purge all caches" page.

    moosh clear-cache


debug-on
--------

Turns on full debug - all the options in debugging section of the settings plus enables theme designer mode.

    moosh debug-on


debug-off
---------

Turns off full debug and disables theme designer mode.

    moosh debug-off


generate-module
---------------

Creates new module based on the NEWMODULE template from Moodle HQ.

    moosh generate-module module_name

Example: Create new module under mod/flashcard

    moosh generate-module flashcard


generate-form
-------------

Creates a new file with the form class code. Will display on the screen a boilerplate code to use the form. If the form
file already exists, both form & form usage code will only be displayed on the standard output.
moosh will try to figure out what plugin are you currently working on, based on your current working directory, and prefix
the form accordingly.

    moosh generate-form form_name

Example: Assuming you are in mod/flashcard directory, the command will create edit_form.php containing mod_flashcard_edit_form
class. It will also display a boilerplate code on how can you use the form.

    moosh generate-form edit


form-add
--------

Adds an element to the form. If there is a form in your current working directory, that you have recently worked on with
moosh (e.g. you have generated it with moosh generate-form), moosh will inject the code into that file. If moosh is not
 able to figure out where you would like the code added, it will display it on the standard output.

    moosh form-add type name

Example 1: Display list of all available element templates.

    moosh form-add

Example 2: Add (or display) the code for advanced checkbox element for Moodle form.

    moosh form-add advcheckbox checkboxid


generate-lang
-------------

Scan files given as arguments or currently remembered file, extract language strings and add them to the lang file if
necessary.

    moosh generate-lang [file1] [file2] [file3]...

Example 1: Extract lang strings from edit_form.php.

    moosh generate-lang edit_form.php


generate-qtype
--------------

Creates new question type based on the NEWMODULE template from https://github.com/jamiepratt/moodle-qtype_TEMPLATE.

    moosh generate-qtype qtype_name

Example: Create new module under question/type/myqtype

    moosh generate-qtype myqtype


generate-gradereport
--------------

Creates new grade report under grade/report based on the template from https://github.com/danielneis/moodle-gradereport_newgradereport.

    moosh generate-gradereport report_name

Example: Create new module under grade/report/beststudents

    moosh generate-gradereport beststudents


generate-filepicker
-------------------

Shows how to code filepicker, based on https://github.com/AndyNormore/filemanager. Takes no arguments.

    moosh generate-filepicker


download-moodle
---------------

Download latest Moodle version from the latest branch (default) or previous one if -v given.

Example 1: Download latest Moodle.

    moosh download-moodle

Example 2: Download latest Moodle 2.3.

        moosh download-moodle -v 23


Contributing to moosh
=====================

1. Fork the project on github.
2. Follow "installation from Moodle git" section.
3. Look at existing plugins to see how they are done.
4. Create new plugin/update existing one.
5. Send me pull request.
