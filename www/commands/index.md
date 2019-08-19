---
title: commands
layout: command
---

Commands
========
<span class="anchor" id="activity-add"></span>
<a class="command-name">activity-add</a>
--------

Adds an activity instance to the specified course. The activity is specified by it's component name
without the plugin type prefix, so "forum", "assign" or "data" for example, and the course is specified
by it's id.
See [Moodle forum post](https://moodle.org/mod/forum/discuss.php?d=368091) about using the options. 

Example 1. Add new assignment activity to course with id 2.

    moosh activity-add assign 2

Example 2. Add forum to section 3 of course 4.

    moosh activity-add --section 3 forum 4
    
Example 3. Add lesson named "The first lesson" to course 2.

    moosh activity-add --name "The first lesson" lesson 2
    
Example 4. Add assignment with name "Easy assignment" and idnumber "ASD123"

    moosh activity-add --name "Easy assignment" --section 2 --idnumber "ASD123" assign 2
    
Example 5. Add quiz "more emails" with intro set to "polite orders", network address restriction set to  192.168.2.2

    moosh activity-add -n 'more emails' -o="--intro=\"polite orders.\" --subnet=192.168.2.2" quiz 33
    
Example 6. Add scorm "scorm1" with description "my intro ABC" and forcenewattempt set to yes.

    moosh activity-add -n scorm1 -o '--intro=my intro ABC --forcenewattempt=1' scorm 2    


<span class="anchor" id="activity-delete"></span>
<a class="command-name">activity-delete</a>
---------------

Deletes activity with given module id.

    moosh activity-delete 2


<span class="anchor" id="admin-login"></span>
<a class="command-name">admin-login</a>
---------------

Create a session (login) for admin user. Command returns session cookie name & value.

    moosh admin-login
    

<span class="anchor" id="apache-parse-extendedlog"></span>
<a class="command-name">apache-parse-extendedlog</a>
---------------

Parse Apache log that was configured to capture extra Moodle & timings information. To configure it for your Apache server:
 
1. Add new LogFormat to apache2.conf:
  
    LogFormat "H: %v U: %{MOODLEUSER}n T: %Ts / %Dµs | %{X-Forwarded-For}i %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"" moodle_log

2. Add new log in your virtual host configuration:

    CustomLog ${APACHE_LOG_DIR}/moodle.log moodle_log
    
You can then parse resulting moodle.log file with moosh:    

    moosh apache-parse-extendedlog /var/log/apache2/moodle.log


<span class="anchor" id="apache-parse-missing-files"></span>
<a class="command-name">apache-parse-missing-files</a>
---------------

Looks for missing files in apache log when moodle files are accessed and reports them

Example 1. Parse file `apache.log` and search for missing files

    moosh apache-parse-missing-files apache.log


<span class="anchor" id="apache-parse-perflog"></span>
<a class="command-name">apache-parse-perflog</a>
---------------

Parse log file, and construct query with performance log.

Example 1.

    moosh apache-parse-perflog apache.log

Sample line generated (INSERT):

    INSERT IGNORE INTO perflog (time,timestamp,url,memory_peak,includecount,langcountgetstring,db_reads,db_writes,db_queries_time,ticks,user,sys,cuser,csys,serverload,cache_mondodb_hits,cache_mondodb_misses,cache_mondodb_sets,cache_static_hits,cache_static_misses,cache_static_sets,cache_staticpersist_hits,cache_staticpersist_misses,cache_staticpersist_sets,cache_file_hits,cache_file_misses,cache_file_sets,cache_memcached_hits,cache_memcached_misses,cache_memcached_sets,cache_memcache_hits,cache_memcache_misses,cache_memcache_sets,cache_redis_hits,cache_redis_misses,cache_redis_sets,script,query,type) VALUES ('2478271','2016-07-26 10:21:30','/course/view.php?id=139&section=4','16427592','1465','1586','1314','1','1041750','248','58','12','0','0','14','0','0','0','1819','17','27','0','0','0','0','2','0','73','161','163','0','0','0','0','0','0','/course/view.php','id=139&section=4','script')

The DB table structure for the INSERTs generated:

<pre><code>
CREATE TABLE perflog (
 id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 timestamp datetime NOT NULL,
 time int(10) unsigned NOT NULL,
 url varchar(255) NOT NULL,
 memory_peak int(10) unsigned NOT NULL,
 includecount int(10) unsigned NOT NULL,
 contextswithfilters int(10) unsigned NOT NULL,
 filterscreated int(10) unsigned NOT NULL,
 textsfiltered int(10) unsigned NOT NULL,
 stringsfiltered int(10) unsigned NOT NULL,
 langcountgetstring int(10) unsigned NOT NULL,
 db_reads int(10) unsigned NOT NULL,
 db_writes int(10) unsigned NOT NULL,
 db_queries_time int(10) unsigned NOT NULL,
 ticks int(10) unsigned NOT NULL,
 user int(10) unsigned NOT NULL,
 sys int(10) unsigned NOT NULL,
 cuser int(10) unsigned NOT NULL,
 csys int(10) unsigned NOT NULL,
 serverload int(10) unsigned NOT NULL,
 cache_mondodb_sets int(10) unsigned NOT NULL,
 cache_mondodb_misses int(10) unsigned NOT NULL,
 cache_mondodb_hits int(10) unsigned NOT NULL,
 cache_static_sets int(10) unsigned NOT NULL,
 cache_static_misses int(10) unsigned NOT NULL,
 cache_static_hits int(10) unsigned NOT NULL,
 cache_staticpersist_sets int(10) unsigned NOT NULL,
 cache_staticpersist_misses int(10) unsigned NOT NULL,
 cache_staticpersist_hits int(10) unsigned NOT NULL,
 cache_file_sets int(10) unsigned NOT NULL,
 cache_file_misses int(10) unsigned NOT NULL,
 cache_file_hits int(10) unsigned NOT NULL,
 cache_memcache_sets int(10) unsigned NOT NULL,
 cache_memcache_misses int(10) unsigned NOT NULL,
 cache_memcache_hits int(10) unsigned NOT NULL,
 cache_memcached_sets int(10) unsigned NOT NULL,
 cache_memcached_misses int(10) unsigned NOT NULL,
 cache_memcached_hits int(10) unsigned NOT NULL,
 cache_redis_sets int(10) unsigned NOT NULL,
 cache_redis_misses int(10) unsigned NOT NULL,
 cache_redis_hits int(10) unsigned NOT NULL,
 query varchar(255) NULL,
 script varchar(255) NULL,
 path varchar(255) NULL,
 type varchar(255) NULL,
 PRIMARY KEY (id),
 UNIQUE KEY uniquerow (timestamp,time,url)
);
</code></pre>

<span class="anchor" id="audit-passwords"></span>
<a class="command-name">audit-passwords</a>
---------------

Audit hashed passwords - check if any one matches top 10 000 known passwords. With -r also show password matched.
 -u <userid> will check only user with given id.

Example 1. Check all users for easily crackable passwords and show them.

    moosh audit-passwords -r

Example 2. Check if user with id 17 has a weak password.

    moosh audit-passwords -u 17


<span class="anchor" id="auth-list"></span>
<a class="command-name">auth-list</a>
---------------

List authentication plugins.

Example 1. List enabled authentication plugins.

    moosh auth-list


<span class="anchor" id="auth-manage"></span>
<a class="command-name">auth-manage</a>
---------------

Allows to manage auth plugins. Disable, enable, moving up and down in order.

Example 1. Disable External database (db) auth plugin.

    moosh auth-manage disable db

Example 2. Move up Email-based self-registration (email).

    moosh auth-manage up email 

<span class="anchor" id="block-add"></span>
<a class="command-name">block-add</a>
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

<span class="anchor" id="block-manage"></span>
<a class="command-name">block-manage</a>
----------------

Show or Hide blocks, system wide (Will also delete, in the future)

Example:

    moosh block-manage hide calendar
    moosh block-manage show calendar


<span class="anchor" id="category-config-set"></span>
<a class="command-name">category-config-set</a>
---------------

Set category configuration. Arguments are: categoryid setting value. The setting should match one of the columns from mdl_course_categories table.

Example 1. Set ID for category 1 to "id17".

    moosh category-config-set 1 idnumber id17

Example 2. Set description of category id 1 to "My Description".

    moosh category-config-set 1 description "My Description" 


<span class="anchor" id="category-create"></span>
<a class="command-name">category-create</a>
---------------

Create new category.

Example 1: Add new top level category "mycat", invisible with no description.

    moosh category-create mycat

Example 2: Add category "mycat" under category id 6, set to visible and description to "My category".

    moosh category-create -p 6 -v 1 -d "My category" mycat


<span class="anchor" id="category-delete"></span>
<a class="command-name">category-delete</a>
---------------

Delete category, all sub-categories and all courses inside.

Example 1: Delete recursively category with id=2

    moosh category-delete 2


<span class="anchor" id="category-export"></span>
<a class="command-name">category-export</a>
-------------

Export category structure to XML starting from given category ID. Give 0 to export all categories.

Example 1: Export all categories to XML.

    moosh category-export 0

Example 2: Export category with id 3 and all its sub categiries.

    moosh category-export 3

<span class="anchor" id="category-import"></span>
<a class="command-name">category-import</a>
-------------

Imports category structure from XML.

Example 1: Import all categories from XML.

    moosh category-import categor-to-import.xml


<span class="anchor" id="category-list"></span>
<a class="command-name">category-list</a>
-------------

List all categories or those that match search string(s).

Example 1: List all categories

    moosh category-list

Example 2: List all categories with name "test" OR "foobar"

    moosh category-list test foobar

<span class="anchor" id="category-move"></span>
<a class="command-name">category-move</a>
---------------

Move one category to another category

Example 1: Move the category with id 5 to be in the category with id 7

    moosh category-move 5 7

Example 2: Make the category with id 3 a top-level category

    moosh category-move 3 0

<span class="anchor" id="chkdatadir"></span>
<a class="command-name">chkdatadir</a>
----------

Check if every file and directory in Moodle data is writeable for the user that runs the command.
You usually want to run the check as the same user that runs web server.

Example:

    sudo -u www-data moosh chkdatadir

<span class="anchor" id="cache-clear"></span>
<a class="command-name">cache-clear</a>
-----------

The same as "purge all caches" page.

    moosh cache-clear


<span class="anchor" id="cache-course-rebuild"></span>
<a class="command-name">cache-course-rebuild</a>
-----------

Rebuild course cache.

Example 1: Rebuild cache for course with id 2

    moosh cache-course-rebuild 2
    
Example 2: Rebuild cache for all the courses.

    moosh cache-course-rebuild -a


<span class="anchor" id="code-check"></span>
<a class="command-name">code-check</a>
-----------

Checks files if they are compatible with moodle code standards

Example 1:

    moosh code-check -p some/path/to/file.php

Example 2:

    moosh code-check -p some/path/to/dir

<span class="anchor" id="cohort-create"></span>
<a class="command-name">cohort-create</a>
-------------

Create new cohort.

Example 1: Create two system cohorts "mycohort1" and "mycohort2".

    moosh cohort-create mycohort1 mycohort2

Example 2: Create cohort "my cohort18" with id "cohort18" under category id 2, with description "Long description".

    moosh cohort-create -d "Long description" -i cohort18 -c 2 "my cohort18"

<span class="anchor" id="cohort-enrol"></span>
<a class="command-name">cohort-enrol</a>
------------

Add user to cohort or enroll cohort to a course.

Example 1: Add user id 17 to cohort named "my cohort18"

    moosh cohort-enrol -u 17 "my cohort18"

Example 2: Enroll cohort "my cohort18" to course id 4.

    moosh cohort-enrol -c 4 "my cohort18"

<span class="anchor" id="cohort-unenrol"></span>
<a class="command-name">cohort-unenrol</a>
--------------

Remove user(s) from a cohort (by cohort id)

Example 1: Remove users 20,30,40 from cohort id=7.

    moosh cohort-unenrol 7 20 30 40

<span class="anchor" id="config-get"></span>
<a class="command-name">config-get</a>
----------

Get config variable from config or config_plugins table. The syntax is based on get_config($plugin,$name) API function. Both arguments are optional.

Example 1: Show all core config variables.

    moosh config-get

Example 2: Show all config variables for "user"

    moosh config-get user

Example 3: Show core setting "dirroot"

    moosh config-get core dirroot

<span class="anchor" id="config-plugins"></span>
<a class="command-name">config-plugins</a>
--------------

Shows all plugins that have at least one entry in the config_plugins table. Optionally provide an argument to match plugin name.

Example 1: Show all plugins from config_plugins table.

    moosh config-plugins

Example 2: Show all themes that have some settings.

    moosh config-plugins theme_

<span class="anchor" id="config-set"></span>
<a class="command-name">config-set</a>
----------

Set config variable. The syntax of the command is based on the set_config() Moodle API:

    moosh config-set name value <plugin>

If third argument (plugin) is not provided then the variable is set in the core Moodle configuration table.

Example 1: Enable debug.

    moosh config-set debug 32767

Example 2: Set URL to logo for Sky High theme.

    moosh config-set logo http://example.com/logo.png theme_sky_high

<span class="anchor" id="course-backup"></span>
<a class="command-name">course-backup</a>
-------------

Backup course with provided id.

Example 1: Backup course id=3 into default .mbz file in current directory:

    moosh course-backup 3

Example 2: Backup course id=3 and save it as /tmp/mybackup.mbz:

    moosh course-backup -f /tmp/mybackup.mbz 3


<span class="anchor" id="course-cleanup"></span>
<a class="command-name">course-cleanup</a>
-------------

The command will to though various pieces of HTML texts contained in the given course and run purify_html() function on them. The command does not actually do any changes - 
it will only show which content could possibly be cleaned up.

Example 1: Check if there is any HTML to be cleaned-up in course 3:

    moosh course-cleanup 3


<span class="anchor" id="course-create"></span>
<a class="command-name">course-create</a>
-------------

Create a new course(s).

Example 1: Create 10 new courses using bash/zim expansion

    moosh course-create newcourse{1..10}

Example 2: Create new course

    moosh course-create --category 1 --fullname "full course name" --description "course description" --idnumber "course idnumber" shortname

Example 3: Create new course with section format, number options

    moosh course-create --category 4 --format topics --numsections 2 test


<span class="anchor" id="course-config-set"></span>
<a class="command-name">course-config-set</a>
-----------------

Update a field in the Moodle {course} table for a single course, or for all courses in a category.

Example 1: set the shortname of a single course with id=42

    moosh course-config-set course 42 shortname new_shortname
    
Example 2: set the format to topics for all courses in a category with id=7

    moosh course-config-set category 7 format topics


<span class="anchor" id="course-delete"></span>
<a class="command-name">course-delete</a>
-----------------

Delete course(s) with ID(s) given as argument(s).

Example 1: delete courses id 2,3 and 4.

    moosh course-delete 2 3 4


<span class="anchor" id="course-enrol"></span>
<a class="command-name">course-enrol</a>
------------

Enrol user(s) into a course id provided. First argument is a course ID, then put one or more user names.
Use -i for providing username IDs.  Optionally add -S and -E to define start and end dates for the enrollment.

Example 1: Enroll username1 and username2 into course ID 21 as students.

    moosh course-enrol 21 username1 username2

Example 2: Enroll user with id 21 into the course with id 31 as a non-editing teacher.

    moosh course-enrol -r teacher -i 31 21

Example 3: Enroll username3 into course ID 21 with start date of May 1st, 2018 10AM and end date May 31st, 2018 10AM

    moosh course-enrol 21 username3 -S 2018-05-01T10:00:00 -E 2018-05-31T10:00:00
	
Example 4: Enroll username4 into course ID 21 with start date of May 1st, 2018 10AM and duration of 30 days.

    moosh course-enrol 21 username3 -S 2018-05-01T10:00:00 -E 30
	
<span class="anchor" id="course-enrolbyname"></span>
<a class="command-name">course-enrolbyname</a>
------------------

Is similar to course-enrol function. But it can also be used the first- and lastname of the user and the course shortname.

Example 1: Enroll user with firstname test42 and lastname user42 into the course with shortname T12345 as an editing teacher.

    moosh course-enrolbyname -r editingteacher -f test42 -l user42 -c T12345

<span class="anchor" id="course-enrolleduser"></span>
<a class="command-name">course-enrolleduser</a>
-------------------

Returns all enrolled user in a course, which have a specific role. First argument is the shortname of a role, second argument is a course ID.

Example 1:

    moosh course-enrolleduser student 4

<span class="anchor" id="course-enableselfenrol"></span>
<a class="command-name">course-enableselfenrol</a>
----------------------

Enable self enrolment on one or more courses given a list of course IDs. By default self enrolment is enabled without an enrolment key, but one can be passed as an option.

Example 1: Enable self enrolment on a course without an enrolment key

    moosh course-enableselfenrol 3
    
Example 2: Enable self enrolment on a course with an enrolment key

    moosh course-enableselfenrol --key "an example enrolment key" 3


<span class="anchor" id="course-list"></span>
<a class="command-name">course-list</a>
----------------------

Lists courses that match your search criteria. As an argument you can provide an SQL fragment, 
that is simply injected after WHERE clause when searching for courses.
All cli argument(s) will be concateneted together, so there is no need to 
quote SQL into single argument. But on the other hand, you need to escape quotes, so they are not eaten by your shell.
Run command with global "-v" option to see the actual SQL used for search (`moosh -v course-list`).
There are also quite a few options that modify the behaviour - see `moosh course-list --help` for complete list.  

Example 1: List all courses with full name containing phrase 'student'

    moosh course-list "fullname like '%student%'"

Example 2: List above but as separate arguments - quotes are escaped

    moosh course-list fullname like \'%student%\'
    
Example 3: List only empty courses from category 1

    moosh course-list -c 1 -e yes

<span class="anchor" id="course-reset"></span>
<a class="command-name">course-reset</a>
--------------

Reset course by ID. With -s or --settings option you can provide any supported setting for the restore. The value for
 -s option is a string with each setting in format key=value separated by space. This means you'll need to quote this
  string with double or single quotes when running the command. unenrol_users setting requires an array as a value -
  put at least one comma character (,) as a value to make moosh convert that into an array. Example:

    moosh course-reset -s "reset_forum_all=1 reset_data=0 unenrol_users=6," 17

Add -n or --no-action to display all reset setting that would be used on course restore. When -n is used, no action is
  actually performed.

All posible backup settings I've found in Moodle 2.9 are listed below. Most of them is set to eiher 0 or 1.

* delete_blog_associations
* reset_assign_submissions
* reset_chat
* reset_choice
* reset_comments
* reset_completion
* reset_data
* reset_data_comments
* reset_data_notenrolled
* reset_data_ratings
* reset_events
* reset_forum_all
* reset_forum_digests
* reset_forum_ratings
* reset_forum_subscriptions
* reset_forum_track_prefs
* reset_forum_types
* reset_glossary_all
* reset_glossary_comments
* reset_glossary_notenrolled
* reset_glossary_ratings
* reset_glossary_types
* reset_gradebook_grades
* reset_gradebook_items
* reset_groupings_members
* reset_groupings_remove
* reset_groups_members
* reset_groups_remove
* reset_lesson
* reset_lesson_group_overrides
* reset_lesson_user_overrides
* reset_notes
* reset_quiz_attempts
* reset_quiz_group_overrides
* reset_quiz_user_overrides
* reset_roles_local
* reset_roles_overrides
* reset_scorm
* reset_start_date (timestamp as a value)
* reset_survey_analysis
* reset_survey_answers
* reset_wiki_comments
* reset_wiki_tags
* reset_workshop_assessments
* reset_workshop_phase
* reset_workshop_submissions
* unenrol_users (array of user roles to unenrol)


Example 1: Reset course with id=17 using default settings.

    moosh course-reset 17

Example 2: Show default settings  when resetting course id=17

    moosh course-reset -n 17
    
Example 3: Set unenrolment of participants with role id 5 and 6, and reset course with id=17

    moosh course-reset -s "unenrol_users=5,6" 17
        
<span class="anchor" id="course-restore"></span>
<a class="command-name">course-restore</a>
--------------

Restore course from path/to/backup.mbz to category or existig course.

Example 1: Restore backup.mbz into category with id=1

    moosh course-restore backup.mbz 1

Example 2: Restore backup.mbz into existing course with id=3

    moosh course-restore -e backup.mbz 3

<span class="anchor" id="course-unenrol"></span>
<a class="command-name">course-unenrol</a>
------------

Unerol user(s) from a course id provided. First argument is a course ID then list of users.


Example 1: Unenrol users with id 7, 9, 12 and 16 from course with id 2.

    moosh course-unenrol 2 7 9 12 16


<span class="anchor" id="data-stats"></span>
<a class="command-name">data-stats</a>
----------

Provides information on size of dataroot directory, dataroot/filedir subdirectory and total size of non-external files in moodle. Outpus data in json format when run using --json option.

    moosh data-stats

<span class="anchor" id="debug-off"></span>
<a class="command-name">debug-off</a>
---------

Turns off full debug and disables theme designer mode.

    moosh debug-off

<span class="anchor" id="debug-on"></span>
<a class="command-name">debug-on</a>
--------

Turns on full debug - all the options in debugging section of the settings plus enables theme designer mode.

    moosh debug-on

<span class="anchor" id="dev-versionbump"></span>
<a class="command-name">dev-versionbump</a>
---------------

Increase the version in module's version.php.

Example:

    cd <moodle_root>/mod/<your_module>
    moosh dev-versionbump

<span class="anchor" id="download-moodle"></span>
<a class="command-name">download-moodle</a>
---------------

Download latest Moodle version from the latest branch (default) or previous one if -v given.

Example 1: Download latest Moodle (as set up in default_options.php).

    moosh download-moodle

Example 2: Download latest Moodle 2.3.

        moosh download-moodle -v 2.3


<span class="anchor" id="event-fire"></span>
<a class="command-name">event-fire</a>
-----------

Fire an event. Provide event name and JSON encoded data.

    moosh event-fire report_log\\event\\report_viewed '{"contextid":1,"relateduserid":1,"other":{"groupid":1,"date":100,"modid":1,"modaction":"view","logformat":0}}' 


<span class="anchor" id="event-list"></span>
<a class="command-name">event-list</a>
-----------

List all events available in current Moodle installation.

    moosh event-list


<span class="anchor" id="file-datacheck"></span>
<a class="command-name">file-datacheck</a>
-----------

Go through all files in Moodle data and check them for corruption. The check is to compare file's SHA to their file names.

    moosh file-datacheck


<span class="anchor" id="file-dbcheck"></span>
<a class="command-name">file-dbcheck</a>
-----------

Check that all files recorder in the DB do exist in Moodle data directory.

    moosh file-dbcheck


<span class="anchor" id="file-delete"></span>
<a class="command-name">file-delete</a>
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

<span class="anchor" id="file-list"></span>
<a class="command-name">file-list</a>
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

Use the -m option to list files that exsist on the {files} DB table but are missing from the file system,
and add -r option to remove them from the {file} DB table.

Example 1: Show all legacy files for a course, which context id is 15

    moosh file-list "contextid=15 AND component='course' AND filearea='legacy'"

Example 2: Display full information on file with ID 17

    moosh file-list -a id=162

Example 3: Show all files from course 6

    moosh file-list course=6

Example 4: Super-combo. Get all course files and tar/bzip2 them up.

    moosh file-list -i course=2 | moosh file-path -s -r | tar -C $(moosh config-get core dataroot) -T - -cjf files.tar.bz2


<span class="anchor" id="file-path"></span>
<a class="command-name">file-path</a>
---------

Show full or relative path in the filesystem to Moodle file(s). Files can be identified by ID or hash (auto-detected) as arguments or on stdin (-s option).

Example 1: Show path to a file with contenthash da39a3ee5e6b4b0d3255bfef95601890afd80709

    moosh file-path da39a3ee5e6b4b0d3255bfef95601890afd80709

Example 2: Show paths to files with ID bewteen 100 and 200

    moosh file-list -i 'id>100 AND id<200' | moosh file-path -s

Example 3: Like above but with no duplicates and show path relative to data root (-r)

    moosh file-list -r -i 'id>100 AND id<200' | moosh file-path -s | sort | uniq

<span class="anchor" id="file-upload"></span>
<a class="command-name">file-upload</a>
--------

Upload selected file to Moodle data. Must specify full path to filename.

* -c|--contextid - set context id. Defaults to 5, which is a context ID of admin user in standard installation.
* -f|--filearea - set filearea, defaults to 'private'
* -m|--component - component field, defaults to 'user'
* -i|--itemid itemid column, defaults to '0'
* -s|--sortorder sort order, '0' by default
* -n|--filename change name of file saved to moodle, defaults to full name of the file given in argument
* -p|--filepath change path of file saved to moodle, defaults to file full path


Example 1: Upload file file.txt to private area of a user with context id 5 - usually "admin" user.
    
    moosh file-upload file.txt

Example 2: Upload to admin's private files a file file.txt, name in Moodle "myfile.txt" and 
place in directory "drop".

    moosh file-upload --filepath=drop --filename=myfile.txt file.txt 

<span class="anchor" id="filter-set"></span>
<a class="command-name">filter-set</a>
--------

Enable/disable global filter, equivalent to admin/filters.php settings page. First argument is a filter name without filter_ prefix.
Second argument is a state, use On = 1 , Off/but available per course = -1 , Off = -9999 .


Example 1: Disable multimedia filter completely.
    
    moosh filter-set mediaplugin -9999 
    
<span class="anchor" id="form-add"></span>
<a class="command-name">form-add</a>
--------

Adds an element to the form. If there is a form in your current working directory, that you have recently worked on with
moosh (e.g. you have generated it with moosh generate-form), moosh will inject the code into that file. If moosh is not
 able to figure out where you would like the code added, it will display it on the standard output.

    moosh form-add type name

Example 1: Display list of all available element templates.

    moosh form-add

Example 2: Add (or display) the code for advanced checkbox element for Moodle form.

    moosh form-add advcheckbox checkboxid

<span class="anchor" id="forum-newdiscussion"></span>
<a class="command-name">forum-newdiscussion</a>
-------------------

Adds a new discussion to an existing forum. You should provide a course id, a forum id
and an user id in this order. If no name or message is specified it defaults to the data
generator one.

Example:

    moosh forum-newdiscussion 3 7 2
    moosh forum-newdiscussion --subject "Forum Name" --message "I am a long text" 3 7 2


<span class="anchor" id="generate-availability"></span>
<a class="command-name">generate-availability</a>
-------------

Generate a code for new availability condition based on danielneis/moodle-availability_newavailability.

    moosh generate-availability newcondition


<span class="anchor" id="generate-block"></span>
<a class="command-name">generate-block</a>
-------------

Generate a code for new block based on the template.

Example: generate new block_abc

    moosh generate-block abc


<span class="anchor" id="generate-cfg"></span>
<a class="command-name">generate-cfg</a>
-------------

Generate fake class to get auto-completion for $CFG object. Properties genertated extracted from the current source code.
 See [setup instructions](http://moosh-online.com/#cfg-auto-completion).

    moosh generate-cfg > config.class.php
    
<span class="anchor" id="generate-enrol"></span>
<a class="command-name">generate-enrol</a>
-------------

Creates new local plugin under enrol/ based on template from https://github.com/danielneis/moodle-enrol_newenrol

    moosh generate-enrol name

Example 1: Generate new plugin under enrol/mynewenrol

    moosh generate-local mynewenrol

<span class="anchor" id="generate-filemanager"></span>
<a class="command-name">generate-filemanager</a>
-------------------

Shows how to code filepicker, based on https://github.com/AndyNormore/filemanager. Takes no arguments.

    moosh generate-filemanager

<span class="anchor" id="generate-form"></span>
<a class="command-name">generate-form</a>
-------------

Creates a new file with the form class code. Will display on the screen a boilerplate code to use the form. If the form
file already exists, both form & form usage code will only be displayed on the standard output.
moosh will try to figure out what plugin are you currently working on, based on your current working directory, and prefix
the form accordingly.

    moosh generate-form form_name

Example: Assuming you are in mod/flashcard directory, the command will create edit_form.php containing mod_flashcard_edit_form
class. It will also display a boilerplate code on how can you use the form.

    moosh generate-form edit

<span class="anchor" id="generate-gradereport"></span>
<a class="command-name">generate-gradereport</a>
--------------

Creates new grade report under grade/report based on the template from https://github.com/danielneis/moodle-gradereport_newgradereport.

    moosh generate-gradereport report_name

Example: Create new report under grade/report/beststudents

    moosh generate-gradereport beststudents

<span class="anchor" id="generate-gradeexport"></span>
<a class="command-name">generate-gradeexport</a>
--------------

Creates new grade export under grade/export based on the template from https://github.com/danielneis/moodle-gradeexport_newgradeexport.

    moosh generate-gradeexport export_name

Example: Create new export under grade/export/mycustomsystem

    moosh generate-gradeexport mycustomsystem

<span class="anchor" id="generate-lang"></span>
<a class="command-name">generate-lang</a>
-------------

Scan files given as arguments or currently remembered file, extract language strings and add them to the lang file if
necessary.

    moosh generate-lang [file1] [file2] [file3]...

Example 1: Extract lang strings from edit_form.php.

    moosh generate-lang edit_form.php

<span class="anchor" id="generate-local"></span>
<a class="command-name">generate-local</a>
-------------

Creates new local plugin under local/ based on template from https://github.com/danielneis/moodle-local_newlocal

    moosh generate-local name

Example 1: Generate new plugin under local/mynewlocal

    moosh generate-local mynewlocal


<span class="anchor" id="generate-messageoutput"></span>
<a class="command-name">generate-messageoutput</a>
---------------

Creates new message output processor under message/output based on the template from https://github.com/danielneis/moodle-message_newprocessor.

    moosh generate-messageoutput processor_name

Example: Create new message output processor under message/output/flashcard

    moosh generate-messageoutput flashcard

<span class="anchor" id="generate-module"></span>
<a class="command-name">generate-module</a>
---------------

Creates new module based on the NEWMODULE template from Moodle HQ.

    moosh generate-module module_name

Example: Create new module under mod/flashcard

    moosh generate-module flashcard


<span class="anchor" id="generate-moosh"></span>
<a class="command-name">generate-moosh</a>
---------------

Use moosh to create new moosh command.

    moosh generate-moosh category-command


<span class="anchor" id="generate-qtype"></span>
<a class="command-name">generate-qtype</a>
--------------

Creates new question type based on the NEWMODULE template from https://github.com/jamiepratt/moodle-qtype_TEMPLATE.

    moosh generate-qtype qtype_name

Example: Create new module under question/type/myqtype

    moosh generate-qtype myqtype


<span class="anchor" id="generate-userprofilefield"></span>
<a class="command-name">generate-userprofilefield</a>
--------------

Creates new profile field based on a template.

    moosh generate-userprofilefield newfield


<span class="anchor" id="generate-ws"></span>
<a class="command-name">generate-ws</a>
--------------

Creates new local plugin for WS development based on moodlehq/moodle-local_wstemplate.

    moosh generate-ws newws


<span class="anchor" id="gradecategory-create"></span>
<a class="command-name">gradecategory-create</a>
---------------

Creates grade category.

Example:

    moosh gradecategory-create -n category-name -a aggregation parent_id course_id

<span class="anchor" id="gradecategory-list"></span>
<a class="command-name">gradecategory-list</a>
---------------

Lists grade categories, with command-line options, arguments modeled on course-list's.

Example:

    moosh gradecategory-list --hidden=yes --empty=yes --fields=id,parent,fullname courseid=26

<span class="anchor" id="gradeitem-list"></span>
<a class="command-name">gradeitem-list</a>
---------------

Lists grade items, with command-line options, arguments modeled on course-list's.

Example:

    moosh gradeitem-list --hidden=yes --locked=no --empty=yes --fields=id,categoryid,itemname courseid=26

<span class="anchor" id="gradebook-import"></span>
<a class="command-name">gradebook-import</a>
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


<span class="anchor" id="group-create"></span>
<a class="command-name">group-create</a>
-------------

Create a new group.

Example 1:

    moosh group-create --description "group description" --key sesame --id "group idnumber" groupname courseid

<span class="anchor" id="group-list"></span>
<a class="command-name">group-list</a>
-------------

Lists groups in course, or grouping.

Example 1:

    moosh group-list courseid ...

Example 2:

    moosh group-list --id -G groupingid courseid

<span class="anchor" id="group-memberadd"></span>
<a class="command-name">group-memberadd</a>
-------------

Add a member to a group.

Example 1:

    moosh group-memberadd -c courseid -g groupid membername1 [membername2] ...

Example 2:

    moosh group-memberadd -g groupid memberid1 [memberid2] ...


<span class="anchor" id="grouping-create"></span>
<a class="command-name">grouping-create</a>
-------------

Create a new grouping.

Example:

    moosh grouping-create --description "grouping description" --id "grouping idnumber" groupingname courseid


<span class="anchor" id="group-assigngrouping"></span>
<a class="command-name">group-assigngrouping</a>
-------------

Add a group to a grouping.

Example:

    moosh group-assigngrouping -G groupingid groupid1 [groupid2] ...


<span class="anchor" id="info"></span>
<a class="command-name">info</a>
---------------

Show information about plugin in current directory.

Example 1:

    moosh info

<span class="anchor" id="info-plugins"></span>
<a class="command-name">info-plugins</a>
---------------

List all possible plugins in this version of Moodle and directory for each.

Example 1: Show all plugin types.

    moosh info-plugins

<span class="anchor" id="languages-update"></span>
<a class="command-name">languages-update</a>
---------------

Update all installed language packs, in the current Moodle folder.

Example 1: Update all language packs.

    moosh languages-update
    
<span class="anchor" id="maintenance-off"></span>
<a class="command-name">maintenance-off</a>
---------------

Disable maintenance mode.

    moosh maintenance-off

<span class="anchor" id="maintenance-on"></span>
<a class="command-name">maintenance-on</a>
--------------

Enable maintenance mode.

    moosh maintenance-on
    
A maintenance message can also be set:

    moosh maintenace-on -m "Example message"

<span class="anchor" id="module-config"></span>
<a class="command-name">module-config</a>
----------------

Set or Get any plugin's settings values

Example:

    moosh module-config set dropbox dropbox_secret 123
    moosh module-config get dropbox dropbox_secret ?

<span class="anchor" id="module-manage"></span>
<a class="command-name">module-manage</a>
----------------

Show or Hide moudles, system wide (Will also delete, in the future)

Example:

    moosh module-manage hide scorm
    moosh module-manage show scorm


<span class="anchor" id="module-reinstall"></span>
<a class="command-name">module-reinstall</a>

Re-install any Moodle plugin. It will remove all the data related to the module and install it from clean.

Example:

    moosh module-reinstall block_html
    moosh module-reinstall mod_book


<span class="anchor" id="nagios-check"></span>
<a class="command-name">nagios-check</a>

Create session login and login to a site using curl. Return error in Nagios format if login was not successful. 

    moosh nagios-check


<span class="anchor" id="php-eval"></span>
<a class="command-name">php-eval</a>
----------------

Evaluate arbitrary php code after bootstrapping Moodle.

Example:

    moosh php-eval 'var_dump(get_object_vars($CFG))'


<span class="anchor" id="plugin-install"></span>
<a class="command-name">plugin-install</a>
----------------

Download and install plugin. Requires plugin short name, and plugin version. You can obtain those data by using `plugin-list -v' command.

Example:

    moosh plugin-install mod_quickmail 20160101


<span class="anchor" id="plugin-list"></span>
<a class="command-name">plugin-list</a>
----------------

List Moodle plugins filtered on given query. Returns plugin full name, short name, available Moodle versions and short description.

Example 1: list all plugins available on https://moodle.org/plugins

    moosh plugin-list

Example 2: download all modules available for version 2.8 or later

    moosh plugin-list  | grep '^mod_' | grep 2.8 | grep -o '[^,]*$' | wget -i -

<span class="anchor" id="plugins-usage"></span>
<a class="command-name">plugins-usage</a>
----------------

Shows the usage of the subset of the plugins used in Moodle installation. 
Plugin will show the usages, wherever it can figure out if the plugin is used. 
Currently supported plugins are: filters, question types, course formats, enrolments,
blocks, authentication types, activities.

Example 1: show all plugins and their usage

    moosh plugins-usage 


Example 2: show only contrubuted (3-rd party) plugins

    moosh plugins-usage -c 1
    
<span class="anchor" id="plugin-uninstall"></span>
<a class="command-name">plugin-uninstall</a>
----------------

Removes given plugin from the DB and disk. It can remove plugins that have no folder on the disk and have some redundant data inside DB tables.
If you do not have write permissions on the plugins' folder it will advice you with the command that will give the right permissions and then you are asked to run the command again.

Example:

    moosh plugin-uninstall theme_elegance

<span class="anchor" id="question-import"></span>
<a class="command-name">question-import</a>
----------------

Import quiz question from xml file into selected quiz.

Example: import question from file path/to/question.xml to quiz with id 2

    moosh question-import path/to/question.xml 2

<span class="anchor" id="user-create"></span>
<a class="command-name">user-create</a>
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

<span class="anchor" id="user-delete"></span>
<a class="command-name">user-delete</a>
-----------

Delete user(s) from Moodle. Provide one ore more usernames as arguments.

Example 1: delete user testuser

    moosh user-delete testuser

Example 2: delete user testuser1 and user testuser2
    
    moosh user-delete testuser1 testuser2

<span class="anchor" id="user-export"></span>
<a class="command-name">user-export</a>
-----------

Exports user with given username to csv file.

Example 1:

    moosh user-export testuser
    
<span class="anchor" id="user-getidbyname"></span>
<a class="command-name">user-getidbyname</a>
----------------

This command has been deprecated. Use user-list instead.

<span class="anchor" id="user-import-pictures"></span>
<a class="command-name">user-import-pictures</a>

--------

Provides capability of importing user pictures from a specific directory (recursively including subdirectories). 
Image filename can be mapped to user ID, idnumber or username database field. Supported image types are jpg, gif and png.
--overwrite option flag can be used to force overwriting of existing user pictures.

Example 1: import user pictures from directory and map file names to user's ID value. 

    moosh user-import-pictures -i /path/to/import/dir

Example 2: import user pictures from directory and map file names to user's idnumber value. 

    moosh user-import-pictures -n /path/to/import/dir

Example 3: import user pictures from directory and map file names to user's username value. 

    moosh user-import-pictures -u /path/to/import/dir

<span class="anchor" id="user-list"></span>
<a class="command-name">user-list</a>
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


<span class="anchor" id="user-mod"></span>
<a class="command-name">user-mod</a>
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

Example 5: set user as global super user

    moosh user-mod -g
    
Example 6: change admin's password while ignoring password's policy

    moosh user-mod --ignorepolicy --password weakpassword admin
     
<span class="anchor" id="random-label"></span>
<a class="command-name">random-label</a>
------------

Add a label with random text to random section of course id provided.

Example 1: Add 5 labels to course id 17.

    for i in {1..5}; do moosh random-label 17; done

Example 2: Add label that will contain string " uniquetext " inside.

    moosh random-label -i ' uniquetext ' 17

<span class="anchor" id="report-concurrency"></span>
<a class="command-name">report-concurrency</a>
------------------

Get information about concurrent users online.

Use: -f and -t with date in either YYYYMMDD or YYYY-MM-DD date. Add -p te specify period.

Example 1: Get concurrent users between 20-01-2014 and 27-01-2014 with 30 minut periods.

    moosh report-concurrency -f 20140120 -t 20140127 -p 30

<span class="anchor" id="restore-settings"></span>
<a class="command-name">restore-settings</a>
------------------

Returns all possible restore settings for the current Moodle. To figure them out,
the command creates and then backes up an empty course with short name "moosh001" - unless it already exists.

Example 1: Dump all possible restore settings

    moosh restore-settings
    
<span class="anchor" id="role-create"></span>
<a class="command-name">role-create</a>
-----------

Create new role, optionally provide description, archetype and name. Role id is returned.

Example 1: Create role with short name "newstudentrole" a description, name an archetype

    moosh role-create -d "Role description" -a student -n "Role name" newstudentrole

<span class="anchor" id="role-delete"></span>
<a class="command-name">role-delete</a>
-----------

Delete role by ID or shortname.

Example 1: Delete role "newstudentrole"

    moosh role-delete newstudentrole

Example 2: Delete role id 10.

    moosh role-delete -i 10


<span class="anchor" id="role-reset"></span>
<a class="command-name">role-reset</a>
-----------

Reset give role's permissions from the file.

    moosh role-reset 1 definition_file.txt


<span class="anchor" id="role-update-capability"></span>
<a class="command-name">role-update-capability</a>
----------------------

Update role capabilities on any context.

Use: -i "roleid" or "role_short_name" with "role capability" and "capability setting" (inherit|allow|prevent|prohibit)
and finally, "contextid" (where 1 is system wide)

Example 1: update "student" role (roleid=5) "mod/forumng:grade" capability, system wide (contextid=1)

    moosh student mod/forumng:grade allow 1

Example 2: update "editingteacher" role (roleid=3) "mod/forumng:grade" capability, system wide (contextid=1)

    moosh -i 3 mod/forumng:grade prevent 1

<span class="anchor" id="role-update-contextlevel"></span>
<a class="command-name">role-update-contextlevel</a>
------------------------

Update the context level upon a role can be updated.

Use: "short role name" or -i "roleid" with relevant context level (system|user|category|course|activity|block)
and add "-on" or "-off" to the caontext level name to turn it on or off.

Example 1: Allow "student" role to be set on block level

    moosh student -block-on

Example 2: Prevent "manager" role to be set on course level

    moosh manager -course-off

<span class="anchor" id="sql-cli"></span>
<a class="command-name">sql-cli</a>
-------

Open a connection to the Moodle DB using credentials in config.php. Currently supports PostgreSQL and MySQL.

Example:

    moosh sql-cli


<span class="anchor" id="sql-dump"></span>
<a class="command-name">sql-dump</a>
-------

Dump Moodle DB to sql file. Works for PostgreSQL and MySQL.

Example 1: dump database to backup.sql file

    moosh sql-dump > backup.sql


<span class="anchor" id="sql-run"></span>
<a class="command-name">sql-run</a>
-------

Run any custom SQL against bootstrapped Moodle instance DB. If query start with SELECT then matched rows will be displayed.

Example 1: Set the country of all the users to Poland

    moosh sql-run "update {user} set country='PL'"

Example 2: Count the number of rows is log table

    moosh sql-run "select count(*) from {log}"


<span class="anchor" id="theme-info"></span>
<a class="command-name">theme-info</a>
----------

Show what themes are really used on Moodle site.

Example:

    moosh theme-info

<span class="anchor" id="webservice-call"></span>
<a class="command-name">webservice-call</a>
---------------

Calls 

Example: Get list of all courses enroled for a user

    moosh webservice-call --token 4ac42118db3ee8d4b1ae78f2c1232afd --params userid=3 core_enrol_get_users_courses
