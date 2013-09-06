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

PHP 5.3+, Moodle 1.9, 2.2 or higher.

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
    ./composer.phar update

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

    moosh user-create --password pass --email me@example.com --digest 2 --city Szczecin --country PL --firstname "first name" --lastname name testuser

Example 3: use bash/zsh expansion to create 10 users

    moosh user-create testuser{1..10}

The users will have unique email addresses based on the user name (testuser1, testuser2, testuser3...).

Example 4: create a user with LDAP authentication

    moosh user-create --auth ldap --password NONE  --email joe.blogs@domain.tld --city "Some City" --country IE --firstname "Joe" --lastname "Blogs" jblogs


user-delete
-----------

Delete user(s) from Moodle. Provide one ore more usernames as arguments.

Example 1: delete user testuser

    moosh user-delete testuser

Example 2: delete user testuser1 and user testuser2
    
    moosh user delete testuser1 testuser2



user-getidbyname
----------------

Returns the userid of users. The parameter can be the first and last name of a user, or one or more username(s). (When using first and last name may be ambiguous. If more than one user with the same first and last name is found, it returns an error message: Multiple records found, only one record expected.)

Example 1: Returns the userid of the user "test user"

    moosh user-getidbyname --fname test --lname user

Example 2: Returns the userid of the user with username "testuser"

    moosh user-getidbyname testuser

Example 3: Returns the userid of the users with username testuser{1..10}

    moosh user-getidbyname testuser{1..5}

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

Example 2: list 100 accounts sorted on email address in descending order and showing idnumber column

    moosh user-list --limit 100 --idnumber --sort email --descending

sql-run
-------

Run any custom SQL against bootstrapped Moodle instance DB. If query start with SELECT then matched rows will be displayed.

Example 1: Set the country of all the users to Poland

    moosh sql-run "update {user} set country='PL'"


Example 2: Count the number of rows is log table

    moosh sql-run "select count(*) from {log}"


category-create
---------------

Create new category.

Example 1: Add new top level category "mycat", invisible with no description.

    moosh category-create mycat

Example 2: Add category "mycat" under category id 6, set to visible and description to "My category".

    moosh category-create -p 6 -v 1 -d "My category" mycat


category-export
-------------

Export category structure to XML.

Example 1: Export all categories to XML.

    moosh category-export 1


category-list
-------------

List all categories or those that match search string(s).

Example 1: List all categories

    moosh category-list

Example 2: List all categories with name "test" OR "foobar"

    moosh category-list test foobar


category-move
---------------

Move one category to another category

Example 1: Move the category with id 5 to be in the category with id 7

    moosh category-move 5 7

Example 2: Make the category with id 3 a top-level category

    moosh category-move 3 0


cohort-create
-------------

Create new cohort.

Example 1: Create two system cohorts "mycohort1" and "mycohort2".

    moosh cohort-create mycohort1 mycohort2

Example 2: Create cohort "my cohort18" with id "cohort18" under category id 2, with description "Long description".

    moosh cohort-create -d "Long description" -i cohort18 -c 2 "my cohort18"


cohort-enrol
------------

Add user to cohort or enroll cohort to a course.

Example 1: Add user id 17 to cohort named "my cohort18"

    moosh cohort-enrol -u 17 "my cohort18"

Example 2: Enroll cohort "my cohort18" to course id 4.

    moosh cohort-enrol -c 4 "my cohort18"


cohort-unenrol
--------------

Remove user(s) from a cohort (by cohort id)

Example 1: Remove users 20,30,40 from cohort id=7.

    moosh cohort-unenrol 7 20 30 40


course-create
-------------

Create a new course(s).

Example 1: Create 10 new courses using bash/zim expansion

    moosh course-create newcourse{1..10}

Example 2: Create new course

    moosh course-create --category 1 --fullname "full course name" --description "course description" --idnumber "course idnumber" shortname


course-enableselfenrol
----------------------

Enable self enrolment on one or more courses given a list of course IDs. By default self enrolment is enabled without an enrolment key, but one can be passed as an option.

Example 1: Enable self enrolment on a course without an enrolment key

    moosh course-enableselfenrol 3
    
Example 2: Enable self enrolment on a course with an enrolment key

    moosh course-enableselfenrol --key "an example enrolment key" 3


course-enrol
------------

Enrol user(s) into a course id provided. First argument is a course ID, then put one or more user names.
Use -i for providing username IDs.

Example 1: Enroll username1 and username2 into course ID 21 as students.

    moosh course-enrol 21 username1 username2

Example 2: Enroll user with id 21 into the course with id 31 as a non-editing teacher.

    moosh course-enrol -r teacher -i 31 21

course-unenrol
------------

Unerol user(s) from a course id provided. First argument is a course ID, possible options:

--roles : comma separated list of user roles
--cohort: boolean 1 remove all cohort sync enrolments

Example 1:

    moosh course-unenrol --role editingteacher --cohort 1 144


course-enrolbyname
------------------

Is similar to course-enrol function. But it can also be used the first- and lastname of the user and the course shortname.

Example 1: Enroll user with firstname test42 and lastname user42 into the course with shortname T12345 as an editing teacher.

    moosh course-enrolbyname -r editingteacher -f test42 -l user42 -c T12345


course-enrolleduser
-------------------

Returns all enrolled user in a course, which have a specific role. First argument is the shortname of a role, second argument is a course ID.

Example 1:

    moosh course-enrolleduser student 4


course-backup
-------------

Backup course with provided id.

Example 1: Backup course id=3 into default .mbz file in current directory:

    moosh course-backup 3

Example 2: Backup course id=3 and save it as /tmp/mybackup.mbz:

    moosh course-backup -f /tmp/mybackup.mbz 3


course-restore
--------------

Restore course from path/to/backup.mbz to category with a given id.

Example 1: Restore backup.mbz into category with id=1

    moosh course-restore backup.mbz 1


course-reset
--------------

Reset course by ID, using default settings.

Example 1: Reset course with id=17

    moosh course-reset 17


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


role-update-capability
----------------------

Update role capabilities on any context.

Use: -i "roleid" or "role_short_name" with "role capability" and "capability setting" (inherit|allow|prevent|prohibit)
and finally, "contextid" (where 1 is system wide)

Example 1: update "student" role (roleid=5) "mod/forumng:grade" capability, system wide (contextid=1)
    moosh student mod/forumng:grade allow 1

Example 2: update "editingteacher" role (roleid=3) "mod/forumng:grade" capability, system wide (contextid=1)
    moosh -i 3 mod/forumng:grade prevent 1

role-update-contextlevel
------------------------

Update the context level upon a role can be updated.

Use: "short role name" or -i "roleid" with relevant context level (system|user|category|course|activity|block)
and add "-on" or "-off" to the caontext level name to turn it on or off.

Example 1: Allow "student" role to be set on block level
    moosh student -block-on

Example 1: Prevent "manager" role to be set on course level
    moosh manager -course-off


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

maintenance-on
--------------

Enable maintenance mode.

    moosh maintenance-on
    
A maintenance message can also be set:

    moosh maintenace-on -m "Example message"
    
maintenance-off
---------------

Disable maintenance mode.

    moosh maintenance-off


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


info
---------------

Show information about plugin in current directory.

Example 1:

    moosh info


info-plugins
---------------

List all possible plugins in this version of Moodle and directory for each.

Example 1: Show all plugin types.

    moosh info-plugins


block-add
---------------

Add a new block instance to any system context (front page, category, course, module ...)
Can add a block instance to a single course or to all courses in a category
Can add a block to the category itself which will appear in all it's sub categories and courses
(use "moosh block-add -h" for more help)

Example:

    moosh block-add category 2 calendar_month admin-course-category side-pre -1
    moosh block-add -s category 2 calendar_month admin-course-category side-pre -1
    moosh block-add categorycourses 2 calendar_month course-view-* side-post 0
    moosh block-add course 32 calendar_month course-view-* side-post 0


activity-add
------------

Adds an activity instance to the specified course. The activity is specified by it's component name
without the plugin type prefix, so "forum", "assign" or "data" for example, and the course is specified
by it's id.

Example:

    moosh activity-add assign 2
    moosh activity-add --section 3 forum 4
    moosh activity-add --name "General course forum" --section 2 forum 3
    moosh activity-add --name "Easy assignent" --section 2 --idnumber "ASD123" assign 2

random-label
------------

Add a label with random text to random section of course id provided.

Example 1: Add 5 labels to course id 17.

    for i in {1..5}; do moosh random-label 17; done

Example 2: Add label that will contain string " uniquetext " inside.

    moosh random-label -i ' uniquetext ' 17


block-manage
----------------

Show or Hide blocks, system wide (Will also delete, in the future)

Example:

    moosh block-manage hide calendar
    moosh block-manage show calendar


module-manage
----------------

Show or Hide moudles, system wide (Will also delete, in the future)

Example:

    moosh module-manage hide scorm
    moosh module-manage show scorm


module-config
----------------

Set or Get any plugin's settings values

Example:

    moosh module-config set dropbox dropbox_secret 123
    moosh module-config get dropbox dropbox_secret ?


forum-newdiscussion
-------------------

Adds a new discussion to an existing forum. You should provide a course id, a forum id
and an user id in this order. If no name or message is specified it defaults to the data
generator one.

Example:

    moosh forum-newdiscussion 3 7 2
    moosh forum-newdiscussion --subject "Forum Name" --message "I am a long text" 3 7 2


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
