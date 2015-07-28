---
title: commands
layout: command
---

Commands
========

<a class="command-name" name="activity-add">activity-add</a>
------------

Adds an activity instance to the specified course. The activity is specified by it's component name
without the plugin type prefix, so "forum", "assign" or "data" for example, and the course is specified
by it's id.

Example:

    moosh activity-add assign 2
    moosh activity-add --section 3 forum 4
    moosh activity-add --name "General course forum" --section 2 forum 3
    moosh activity-add --name "Easy assignent" --section 2 --idnumber "ASD123" assign 2

<a class="command-name" name="activity-delete">activity-delete</a>
---------------

Deletes activity with given module id.

Example

    moosh activity-delete 2

<a class="command-name" name="apache-parse-missing-files">apache-parse-missing-files</a>
---------------

Looks for missing files in apache log when moodle files are accessed and reports them

Example 1. Parse file `apache.log` and search for missing files

    moosh apache-parse-missing-files apache.log


<a class="command-name" name="apache-parse-perflog">apache-parse-perflog</a>
---------------

Parse log file, and construct query with performance log.

Example 1.

    moosh apache-parse-perflog apache.log


<a class="command-name" name="audit-passwords">audit-passwords</a>
---------------

Audit hashed passwords - check if any one matches top 10 000 known passwords. With -r also show password matched.

Example 1.

    moosh audit-passwords -r


<a class="command-name" name="auth-manage">auth-manage</a>
---------------

Allows to manage auth plugins. Disable, enable, moving up and down in order.

Example 1. Disable External database (db) auth plugin.

    moosh auth-manage disable db

Example 2. Move up Email-based self-registration (email).

    moosh auth-manage up email 

<a class="command-name" name="block-add">block-add</a>
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

<a class="command-name" name="block-manage">block-manage</a>
----------------

Show or Hide blocks, system wide (Will also delete, in the future)

Example:

    moosh block-manage hide calendar
    moosh block-manage show calendar

<a class="command-name" name="category-create">category-create</a>
---------------

Create new category.

Example 1: Add new top level category "mycat", invisible with no description.

    moosh category-create mycat

Example 2: Add category "mycat" under category id 6, set to visible and description to "My category".

    moosh category-create -p 6 -v 1 -d "My category" mycat

<a class="command-name" name="category-export">category-export</a>
-------------

Export category structure to XML.

Example 1: Export all categories to XML.

    moosh category-export 1

<a class="command-name" name="category-import">category-import</a>
-------------

Imports category structure from XML.

Example 1: Import all categories from XML.

    moosh category-import categor-to-import.xml


<a class="command-name" name="category-list">category-list</a>
-------------

List all categories or those that match search string(s).

Example 1: List all categories

    moosh category-list

Example 2: List all categories with name "test" OR "foobar"

    moosh category-list test foobar

<a class="command-name" name="category-move">category-move</a>
---------------

Move one category to another category

Example 1: Move the category with id 5 to be in the category with id 7

    moosh category-move 5 7

Example 2: Make the category with id 3 a top-level category

    moosh category-move 3 0

<a class="command-name" name="chkdatadir">chkdatadir</a>
----------

Check if every file and directory in Moodle data is writeable for the user that runs the command.
You usually want to run the check as the same user that runs web server.

Example:

    sudo -u www-data moosh chkdatadir

<a class="command-name" name="cache-clear">cache-clear</a>
-----------

The same as "purge all caches" page.

    moosh cache-clear

<a class="command-name" name="code-check">code-check</a>
-----------

Checks files if they are compatible with moodle code standards

Example 1:

    moosh code-check -p some/path/to/file.php

Example 2:

    moosh code-check -p some/path/to/dir

<a class="command-name" name="cohort-create">cohort-create</a>
-------------

Create new cohort.

Example 1: Create two system cohorts "mycohort1" and "mycohort2".

    moosh cohort-create mycohort1 mycohort2

Example 2: Create cohort "my cohort18" with id "cohort18" under category id 2, with description "Long description".

    moosh cohort-create -d "Long description" -i cohort18 -c 2 "my cohort18"

<a class="command-name" name="cohort-enrol">cohort-enrol</a>
------------

Add user to cohort or enroll cohort to a course.

Example 1: Add user id 17 to cohort named "my cohort18"

    moosh cohort-enrol -u 17 "my cohort18"

Example 2: Enroll cohort "my cohort18" to course id 4.

    moosh cohort-enrol -c 4 "my cohort18"

<a class="command-name" name="cohort-unenrol">cohort-unenrol</a>
--------------

Remove user(s) from a cohort (by cohort id)

Example 1: Remove users 20,30,40 from cohort id=7.

    moosh cohort-unenrol 7 20 30 40

<a class="command-name" name="config-get">config-get</a>
----------

Get config variable from config or config_plugins table. The syntax is based on get_config($plugin,$name) API function. Both arguments are optional.

Example 1: Show all core config variables.

    moosh config-get

Example 2: Show all config variables for "user"

    moosh config-get user

Example 3: Show core setting "dirroot"

    moosh config-get core dirroot

<a class="command-name" name="config-plugins">config-plugins</a>
--------------

Shows all plugins that have at least one entry in the config_plugins table. Optionally provide an argument to match plugin name.

Example 1: Show all plugins from config_plugins table.

    moosh config-plugins

Example 2: Show all themes that have some settings.

    moosh config-plugins theme_

<a class="command-name" name="config-set">config-set</a>
----------

Set config variable. The syntax of the command is based on the set_config() Moodle API:

    moosh config-set name value <plugin>

If third argument (plugin) is not provided then the variable is set in the core Moodle configuration table.

Example 1: Enable debug.

    moosh config-set debug 32767

Example 2: Set URL to logo for Sky High theme.

    moosh config-set logo http://example.com/logo.png theme_sky_high

<a class="command-name" name="course-backup">course-backup</a>
-------------

Backup course with provided id.

Example 1: Backup course id=3 into default .mbz file in current directory:

    moosh course-backup 3

Example 2: Backup course id=3 and save it as /tmp/mybackup.mbz:

    moosh course-backup -f /tmp/mybackup.mbz 3

<a class="command-name" name="course-create">course-create</a>
-------------

Create a new course(s).

Example 1: Create 10 new courses using bash/zim expansion

    moosh course-create newcourse{1..10}

Example 2: Create new course

    moosh course-create --category 1 --fullname "full course name" --description "course description" --idnumber "course idnumber" shortname

<a class="command-name" name="course-config-set">course-config-set</a>
-----------------

Update a field in the Moodle {course} table for a single course, or for all courses in a category.

Example 1: set the shortname of a single course with id=42

    moosh course-config-set course 42 shortname new_shortname
    
Example 2: set the format to topics for all courses in a category with id=7

    moosh course-config-set category 7 format topics

<a class="command-name" name="course-enrol">course-enrol</a>
------------

Enrol user(s) into a course id provided. First argument is a course ID, then put one or more user names.
Use -i for providing username IDs.

Example 1: Enroll username1 and username2 into course ID 21 as students.

    moosh course-enrol 21 username1 username2

Example 2: Enroll user with id 21 into the course with id 31 as a non-editing teacher.

    moosh course-enrol -r teacher -i 31 21

<a class="command-name" name="course-enrolbyname">course-enrolbyname</a>
------------------

Is similar to course-enrol function. But it can also be used the first- and lastname of the user and the course shortname.

Example 1: Enroll user with firstname test42 and lastname user42 into the course with shortname T12345 as an editing teacher.

    moosh course-enrolbyname -r editingteacher -f test42 -l user42 -c T12345

<a class="command-name" name="course-enrolleduser">course-enrolleduser</a>
-------------------

Returns all enrolled user in a course, which have a specific role. First argument is the shortname of a role, second argument is a course ID.

Example 1:

    moosh course-enrolleduser student 4

<a class="command-name" name="course-enableselfenrol">course-enableselfenrol</a>
----------------------

Enable self enrolment on one or more courses given a list of course IDs. By default self enrolment is enabled without an enrolment key, but one can be passed as an option.

Example 1: Enable self enrolment on a course without an enrolment key

    moosh course-enableselfenrol 3
    
Example 2: Enable self enrolment on a course with an enrolment key

    moosh course-enableselfenrol --key "an example enrolment key" 3


<a class="command-name" name="course-list">course-list</a>
----------------------

Lists courses with given short or full name.

Example 1: List all courses with shor or full name containing phrase 'student'

    moosh course-list student

Example 2: List all courses with shor or full name containing phrase 'course' and display only id's of courses.

    moosh course-list course -i

<a class="command-name" name="course-reset">course-reset</a>
--------------

Reset course by ID, using default settings.

Example 1: Reset course with id=17

    moosh course-reset 17

<a class="command-name" name="course-restore">course-restore</a>
--------------

Restore course from path/to/backup.mbz to category or existig course.

Example 1: Restore backup.mbz into category with id=1

    moosh course-restore backup.mbz 1

Example 2: Restore backup.mbz into existing course with id=3

    moosh course-restore -e backup.mbz 3

<a class="command-name" name="course-unenrol">course-unenrol</a>
------------

Unerol user(s) from a course id provided. First argument is a course ID, possible options:

--roles : comma separated list of user roles
--cohort: boolean 1 remove all cohort sync enrolments

Possible arguments:
userid

Example 1:

    moosh course-unenrol --role editingteacher --cohort 1 144

Example 2:
    moosh course-unenrol 2 7 9 12 16

This will unenrol users with id 7, 9, 12 and 16 from course with id 2.

<a class="command-name" name="data-stats">data-stats</a>
----------

Provides information on size of dataroot directory, dataroot/filedir subdirectory and total size of non-external files in moodle. Outpus data in json format when run using --json option.

    moosh data-stats

<a class="command-name" name="debug-off">debug-off</a>
---------

Turns off full debug and disables theme designer mode.

    moosh debug-off

<a class="command-name" name="debug-on">debug-on</a>
--------

Turns on full debug - all the options in debugging section of the settings plus enables theme designer mode.

    moosh debug-on

<a class="command-name" name="dev-versionbump">dev-versionbump</a>
---------------

Increase the version in module's version.php.

Example:

    cd <moodle_root>/mod/<your_module>
    moosh dev-versionbump

<a class="command-name" name="download-moodle">download-moodle</a>
---------------

Download latest Moodle version from the latest branch (default) or previous one if -v given.

Example 1: Download latest Moodle (as set up in default_options.php).

    moosh download-moodle

Example 2: Download latest Moodle 2.3.

        moosh download-moodle -v 2.3

<a class="command-name" name="file-delete">file-delete</a>
-----------

Delete Moodle files from DB and possibly move them to trash. File IDs can be provided as arguments or on the standard input (with moosh file-delete -s).
--flush option will remove the trashcan directory.

Example 1: Remove files with IDs 10,20 and 30.

    moosh file-delete 10 20 30

Example 2: Remove all files with size greater than 100 bytes

    moosh file-list -i 'filesize>1000' | moosh file-delete -s

Example 3: Flush trashcan

    moosh file-delete --flush

Example 4: Remove all automated backups and reclaim the space

    moosh file-list -i 'component="backup" AND filearea="automated"' | moosh file-delete -s
    moosh file-delete --flush

<a class="command-name" name="file-list">file-list</a>
---------

Search and list files from mdl_files table. The argument should be a valid SQL WHERE statement. Interesting columns of possible search criterias are:
contextid, component, filearea, itemid, filepath, filename, userid, filesize, mimetype, status, timecreated, timemodified.

You can also use some special values:

 * course=NNN to list all files that relate to a course

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

Example 3: Show all files from course 6

    moosh file-list course=6

Example 4: Super-combo. Get all course files and tar/bzip2 them up.

    moosh file-list -i course=2 | moosh file-path -s -r | tar -C $(moosh config-get core dataroot) -T - -cjf files.tar.bz2

<a class="command-name" name="file-path">file-path</a>
---------

Show full or relative path in the filesystem to Moodle file(s). Files can be identified by ID or hash (auto-detected) as arguments or on stdin (-s option).

Example 1: Show path to a file with contenthash da39a3ee5e6b4b0d3255bfef95601890afd80709

    moosh file-path da39a3ee5e6b4b0d3255bfef95601890afd80709

Example 2: Show paths to files with ID bewteen 100 and 200

    moosh file-list -i 'id>100 AND id<200' | moosh file-path -s

Example 3: Like above but with no duplicates and show path relative to data root (-r)

    moosh file-list -r -i 'id>100 AND id<200' | moosh file-path -s | sort | uniq

<a class="command-name" name="file-upload">file-upload</a>
--------

Upload selected file to user's private area. Must specify full path to filename.

* -f|--filename - change name of file saved to moodle.
* -m|--mintype - set type of displayed miniature.
* -l|--license - set license of upload file.
* -c|--contextid - set context id.
* -r|--replace - replace existing file

Example 1: Upload file /home/user/file to private area of user with id 2.
    
    moosh file-upload /home/user/file 2 

Example 2: Upload file /home/user/file and change its name for moodle.

    moosh file-upload --filename="new file name" /home/user/file 2 

<a class="command-name" name="form-add">form-add</a>
--------

Adds an element to the form. If there is a form in your current working directory, that you have recently worked on with
moosh (e.g. you have generated it with moosh generate-form), moosh will inject the code into that file. If moosh is not
 able to figure out where you would like the code added, it will display it on the standard output.

    moosh form-add type name

Example 1: Display list of all available element templates.

    moosh form-add

Example 2: Add (or display) the code for advanced checkbox element for Moodle form.

    moosh form-add advcheckbox checkboxid

<a class="command-name" name="forum-newdiscussion">forum-newdiscussion</a>
-------------------

Adds a new discussion to an existing forum. You should provide a course id, a forum id
and an user id in this order. If no name or message is specified it defaults to the data
generator one.

Example:

    moosh forum-newdiscussion 3 7 2
    moosh forum-newdiscussion --subject "Forum Name" --message "I am a long text" 3 7 2

<a class="command-name" name="generate-messageoutput">generate-messageoutput</a>
---------------

Creates new availability condition under availability/condition based on the template from https://github.com/danielneis/moodle-availability_newavailability.

    moosh generate-messageoutput processor_name

Example: Creates new availability condition under availability/condition/flashcard

    moosh generate-availability flashcard

<a class="command-name" name="generate-filepicker">generate-filepicker</a>
-------------------

Shows how to code filepicker, based on https://github.com/AndyNormore/filemanager. Takes no arguments.

    moosh generate-filepicker

<a class="command-name" name="generate-form">generate-form</a>
-------------

Creates a new file with the form class code. Will display on the screen a boilerplate code to use the form. If the form
file already exists, both form & form usage code will only be displayed on the standard output.
moosh will try to figure out what plugin are you currently working on, based on your current working directory, and prefix
the form accordingly.

    moosh generate-form form_name

Example: Assuming you are in mod/flashcard directory, the command will create edit_form.php containing mod_flashcard_edit_form
class. It will also display a boilerplate code on how can you use the form.

    moosh generate-form edit

<a class="command-name" name="generate-gradereport">generate-gradereport</a>
--------------

Creates new grade report under grade/report based on the template from https://github.com/danielneis/moodle-gradereport_newgradereport.

    moosh generate-gradereport report_name

Example: Create new report under grade/report/beststudents

    moosh generate-gradereport beststudents

<a class="command-name" name="generate-gradeexport">generate-gradeexport</a>
--------------

Creates new grade export under grade/export based on the template from https://github.com/danielneis/moodle-gradeexport_newgradeexport.

    moosh generate-gradeexport export_name

Example: Create new export under grade/export/mycustomsystem

    moosh generate-gradeexport mycustomsystem

<a class="command-name" name="generate-lang">generate-lang</a>
-------------

Scan files given as arguments or currently remembered file, extract language strings and add them to the lang file if
necessary.

    moosh generate-lang [file1] [file2] [file3]...

Example 1: Extract lang strings from edit_form.php.

    moosh generate-lang edit_form.php

<a class="command-name" name="generate-messageoutput">generate-messageoutput</a>
---------------

Creates new message output processor under message/output based on the template from https://github.com/danielneis/moodle-message_newprocessor.

    moosh generate-messageoutput processor_name

Example: Create new message output processor under message/output/flashcard

    moosh generate-messageoutput flashcard

<a class="command-name" name="generate-module">generate-module</a>
---------------

Creates new module based on the NEWMODULE template from Moodle HQ.

    moosh generate-module module_name

Example: Create new module under mod/flashcard

    moosh generate-module flashcard

<a class="command-name" name="generate-qtype">generate-qtype</a>
--------------

Creates new question type based on the NEWMODULE template from https://github.com/jamiepratt/moodle-qtype_TEMPLATE.

    moosh generate-qtype qtype_name

Example: Create new module under question/type/myqtype

    moosh generate-qtype myqtype

<a class="command-name" name="gradebook-import">gradebook-import</a>
---------------

Imports gradebook grades from csv file into a course given by id. With --course-idnumber use take mdl_course.idnumber instead of course.id.
--map-users-by will change what to use for mapping users from CSV (email or idnumber).

Use --test for testing the import first.

Example:

    moosh gradebook-import --test gradebook.csv course_id

Possible column headers to us:

* "ID number" user's ID number (idnumber)
* "email" user's email
* one or more columns matching grade item name

<a class="command-name" name="info">info</a>
---------------

Show information about plugin in current directory.

Example 1:

    moosh info

<a class="command-name" name="info-plugins">info-plugins</a>
---------------

List all possible plugins in this version of Moodle and directory for each.

Example 1: Show all plugin types.

    moosh info-plugins

<a class="command-name" name="languages-update">languages-update</a>
---------------

Update all installed language packs, in the current Moodle folder.

Example 1: Update all language packs.

    moosh languages-update
    
<a class="command-name" name="maintenance-off">maintenance-off</a>
---------------

Disable maintenance mode.

    moosh maintenance-off

<a class="command-name" name="maintenance-on">maintenance-on</a>
--------------

Enable maintenance mode.

    moosh maintenance-on
    
A maintenance message can also be set:

    moosh maintenace-on -m "Example message"

<a class="command-name" name="module-config">module-config</a>
----------------

Set or Get any plugin's settings values

Example:

    moosh module-config set dropbox dropbox_secret 123
    moosh module-config get dropbox dropbox_secret ?

<a class="command-name" name="module-manage">module-manage</a>
----------------

Show or Hide moudles, system wide (Will also delete, in the future)

Example:

    moosh module-manage hide scorm
    moosh module-manage show scorm

<a class="command-name" name="php-eval">php-eval</a>
----------------

Evaluate arbitrary php code after bootstrapping Moodle.

Example:

    moosh php-eval 'var_dump(get_object_vars($CFG))'


<a class="command-name" name="plugin-install">plugin-install</a>
----------------

Download and install plugin. Requires plugin short name, and moodle version. You can obtain those data by using `plugin-list' command. 

Example:

    moosh plugin-install mod_quickmail 10 


<a class="command-name" name="plugin-list">plugin-list</a>
----------------

List Moodle plugins filtered on given query. Returns plugin full name, short name, available Moodle versions and short description.

Example 1: list all plugins available on https://moodle.org/plugins

    moosh plugin-list

Example 2: download all modules available for version 2.8 or later

    moosh plugin-list  | grep '^mod_' | grep 2.8 | grep -o '[^,]*$' | wget -i -

<a class="command-name" name="plugin-uninstall">plugin-uninstall</a>
----------------

Removes given plugin from the DB and disk. It can remove plugins that have no folder on the disk and have some redundant data inside DB tables.
If you do not have write permissions on the plugins' folder it will advice you with the command that will give the right permissions and then you are asked to run the command again.

Example:

    moosh plugin-uninstall theme_elegance

<a class="command-name" name="question-import">question-import</a>
----------------

Import quiz question from xml file to selected course.

Example: import question from file path/to/question.xml to course with id 2

    moosh question-import path/to/question.xml 2

<a class="command-name" name="user-create">user-create</a>
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

<a class="command-name" name="user-delete">user-delete</a>
-----------

Delete user(s) from Moodle. Provide one ore more usernames as arguments.

Example 1: delete user testuser

    moosh user-delete testuser

Example 2: delete user testuser1 and user testuser2
    
    moosh user delete testuser1 testuser2

<a class="command-name" name="user-export">user-export</a>
-----------

Exports user with given username to csv file.

Example 1:

    moosh user-export testuser
    
<a class="command-name" name="user-getidbyname">user-getidbyname</a>
----------------

This command has been deprecated. Use user-list instead.

<a class="command-name" name="user-list">user-list</a>
--------

List user accounts. It accepts sql WHERE clause. If run without sql argument it will list first 100 users from database.

Example 1: list user accounts with id number higher than 10 and sort them by email

    moosh user-list --sort email "id > 10"

Example 2: list users with first name bruce and username batman

    moosh user-list "name = 'bruce' AND username = 'batman'"

Example 3: list users enrolled in course id 2

    moosh user-list --course 2

Example 4: list teachers enrolled in course id 2 that never accessed that course

    moosh user-list --course 2 --course-role editingteacher --course-inactive


<a class="command-name" name="user-mod">user-mod</a>
--------

Modify user(s) account.

Example 1: change admin's user password and email

    moosh user-mod --email my@email.com --password newpwd admin

Example 2: change authentication method for users with ids 17,20,22

    moosh user-mod -i --auth manual 17 20 22

Example 3: use bash/zsh expansion to change password for users with ID between 100 and 200

    moosh user-mod -i --password newpwd {100..200}

Example 4: update all users

    moosh user-mod --email my@email.com --password newpwd --auth manual --all

Example 4: set user as global super user

    moosh user-mod -g 
<a class="command-name" name="random-label">random-label</a>
------------

Add a label with random text to random section of course id provided.

Example 1: Add 5 labels to course id 17.

    for i in {1..5}; do moosh random-label 17; done

Example 2: Add label that will contain string " uniquetext " inside.

    moosh random-label -i ' uniquetext ' 17

<a class="command-name" name="report-concurrency">report-concurrency</a>
------------------

Get information about concurrent users online.

Use: -f and -t with date in either YYYYMMDD or YYYY-MM-DD date. Add -p te specify period.

Example 1: Get concurrent users between 20-01-2014 and 27-01-2014 with 30 minut periods.
    moosh report-concurrency -f 20140120 -t 20140127 -p 30

<a class="command-name" name="role-create">role-create</a>
-----------

Create new role, optionally provide description, archetype and name. Role id is returned.

Example 1: Create role with short name "newstudentrole" a description, name an archetype

    moosh role-create -d "Role description" -a student -n "Role name" newstudentrole

<a class="command-name" name="role-delete">role-delete</a>
-----------

Delete role by ID or shortname.

Example 1: Delete role "newstudentrole"

    moosh role-delete newstudentrole

Example 2: Delete role id 10.

    moosh role-delete -i 10

<a class="command-name" name="role-update-capability">role-update-capability</a>
----------------------

Update role capabilities on any context.

Use: -i "roleid" or "role_short_name" with "role capability" and "capability setting" (inherit|allow|prevent|prohibit)
and finally, "contextid" (where 1 is system wide)

Example 1: update "student" role (roleid=5) "mod/forumng:grade" capability, system wide (contextid=1)
    moosh student mod/forumng:grade allow 1

Example 2: update "editingteacher" role (roleid=3) "mod/forumng:grade" capability, system wide (contextid=1)
    moosh -i 3 mod/forumng:grade prevent 1

<a class="command-name" name="role-update-contextlevel">role-update-contextlevel</a>
------------------------

Update the context level upon a role can be updated.

Use: "short role name" or -i "roleid" with relevant context level (system|user|category|course|activity|block)
and add "-on" or "-off" to the caontext level name to turn it on or off.

Example 1: Allow "student" role to be set on block level
    moosh student -block-on

Example 2: Prevent "manager" role to be set on course level
    moosh manager -course-off

<a class="command-name" name="sql-run">sql-run</a>
-------

Run any custom SQL against bootstrapped Moodle instance DB. If query start with SELECT then matched rows will be displayed.

Example 1: Set the country of all the users to Poland

    moosh sql-run "update {user} set country='PL'"

Example 2: Count the number of rows is log table

    moosh sql-run "select count(*) from {log}"

<a class="command-name" name="sql-cli">sql-cli</a>
-------

Open a connection to the Moodle DB using credentials in config.php. Currently supports PostgreSQL and MySQL.

Example:

    moosh sql-cli

<a class="command-name" name="theme-info">theme-info</a>
----------

Show what themes are really used on Moodle site.

Example:

    moosh theme-info

<a class="command-name" name="webservice-call">webservice-call</a>
---------------

Calls 

Example: Get list of all courses enroled for a user

    moosh webservice-call --token 4ac42118db3ee8d4b1ae78f2c1232afd --params userid=3 core_enrol_get_users_courses
