---
title: commands
layout: command
---

Commands
========

activity-add
------------

Adds an activity instance to the specified course. The activity is specified by it's component name
without the plugin type prefix, so "forum", "assign" or "data" for example, and the course is specified
by it's id.
Usually you will want to use "-n, --name=" option to set the name of the activity.

See [Moodle forum post](https://moodle.org/mod/forum/discuss.php?d=368091) about using the options. 

Example 1. Add new assignment activity to course with id 2.

    moosh activity-add assign 2

Example 2. Add forum to section 3 of course 4.

    moosh activity-add --section 3 forum 4
    
Example 3. Add lesson named "The first lesson" to course 2.

    moosh activity-add --name "The first lesson" lesson 2
    
Example 4. Add assignment with name "Easy assignment" and idnumber "ASD123" to course 2.

    moosh activity-add --name "Easy assignment" --section 2 --idnumber "ASD123" assign 2
    
Example 5. Add quiz "more emails" with intro set to "polite orders", network address restriction set to 192.168.2.2 to course 33.

    moosh activity-add -n 'more emails' -o="--intro=\"polite orders.\" --subnet=192.168.2.2" quiz 33
    
Example 6. Add scorm "scorm1" with description "my intro ABC" and forcenewattempt set to yes to course 2.

    moosh activity-add -n scorm1 -o '--intro=my intro ABC --forcenewattempt=1' scorm 2    

Example 7. Add quiz named "moosh test quiz" with intro set to "Here is your quiz", and activity completion options enabled to course 33

    moosh activity-add -n 'moosh test quiz' -o="--intro=Here is your quiz.  --completion=2 --completionview=1 --completionusegrade=1 --completionpass=1" quiz 33

Example 8. Add page "my page" to course id 8, with page content that contains newline.

    moosh activity-add -n 'my page' -o '--intro=my introduction --content=fist line
    second line' page 8


activity-delete
---------------

Deletes activity with given module id.

    moosh activity-delete 2


activity-config-set
-------------------

Follows the course-config-set pattern, updating a field in the Moodle {$module} table, (NOT {course_modules} table!), for a single activity of a given module type, or for all activities of that type (or only those in one section (optional)) in a course.

Example 1: set the name of a single URL resource with instance(!) id=151

    moosh activity-config-set activity 151 url name "Examinee handbook"

Example 2: set introformat to markdown in all forums in a course with id=41

    moosh activity-config-set course 41 forum introformat 4

Example 3: set reviewrightanswer to "After quiz closes" for quizzes in section number 2 in a course with id=45

    moosh activity-config-set -s 2 course 45 quiz reviewrightanswer 65552


activity-move
-------------

Moves activity with module id in the first argument to the end of its present section (if alone), to the end of the section in the \-\-section number (not id) option (if given), and before the activity with the module id in the second, optional argument (which is not respected if it conflicts with the section number option).

    moosh activity-move -s 2 4576 4578


admin-login
-----------

Create a session (login) for admin user. Command returns session cookie name & value.

    moosh admin-login


apache-parse-extendedlog
------------------------

Parse Apache log that was configured to capture extra Moodle & timings information. To configure it for your Apache server:
 
1. Add new LogFormat to apache2.conf:
  
    LogFormat "H: %v U: %{MOODLEUSER}n T: %Ts / %Dµs | %{X-Forwarded-For}i %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"" moodle_log

2. Add new log in your virtual host configuration:

    CustomLog ${APACHE_LOG_DIR}/moodle.log moodle_log

You can then parse resulting moodle.log file with moosh:    

    moosh apache-parse-extendedlog /var/log/apache2/moodle.log


apache-parse-missing-files
--------------------------

Looks through apache access log for potentially missing files.

Example 1. Parse file `apache.log` and search for missing files.

    moosh apache-parse-missing-files apache.log


apache-parse-perflog
--------------------

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

audit-passwords
---------------

Audit hashed passwords - check if any one matches top 10 000 known passwords. With -r also show password matched.
 -u <userid> will check only user with given id.

Example 1. Check all users for easily crackable passwords and show them.

    moosh audit-passwords -r

Example 2. Check if user with id 17 has a weak password.

    moosh audit-passwords -u 17


auth-list
---------

List authentication plugins.

Example 1. List enabled authentication plugins.

    moosh auth-list


auth-manage
-----------

Allows to manage auth plugins. Disable, enable, moving up and down in order.

Example 1. Disable External database (db) auth plugin.

    moosh auth-manage disable db

Example 2. Move up Email-based self-registration (email).

    moosh auth-manage up email 

backup-info
-----------

Provides some basic information about Moodle backup file.
The command works by extracting files like users.xml, gradebook.xml, course/logs.xml and getting the information from them 

Example 1. Display basic stats about backup-moodle2-course-2-course1-20200405-1947.mbz file.

    moosh backup-info backup-moodle2-course-2-course1-20200405-1947.mbz

badge-delete
------------

Deletes badges by **criteria**

**criteria** - a string with SQL fragment that selects the records from mdl_bagdes table. The same idea as with `moosh user-list` command.

Example 1: Show all badges without deleting.

    moosh badge-delete --no-action "1 = 1"

Example 2: Delete badge WHERE id = 1.

    moosh badge-delete "id = 4"

Example 3: Delete all badges WHERE courseid = 433 and status = 0.

    moosh badge-delete "courseid=433 AND status=0"

Example 4: Delete all badges with timecreated = 1617009565 without the badge with the lowest ID.

    moosh badge-delete -n --keepfirst 'timecreated=1617009565'

badge-delete-duplicates
------------

This command detects badge duplicates (with the same timecreated and courseid) and removes them.

It will keep only one (with the lowest ID) duplicate which has the same timecreated and courseid.

    moosh badge-delete-duplicates [-n, --no-action]

Example 1: Just show all duplicates.

    moosh badge-delete-duplicates --no-action

Example 2: Delete all duplicates. 

    moosh badge-delete-duplicates

base-path
---------

For a given path/file inside Moodle plugin, the command returns initial directories that are a base directory for that plugin.
The path can be provided as a single argument or read from standard input. 

Example 1. Displays mod/choice.

    moosh base-path mod/choice/index.php

Example 2. Displays theme/boost.

    moosh base-path theme/boost/pix/mod/quiz/whitecircle.png

Example 3. Like above but using stdin.

    echo theme/boost/pix/mod/quiz/whitecircle.png | moosh base-path

block-add
---------

Add a new block instance to any system context (front page, category, course, module ...)
Can add a block instance to a single course or to all courses in a category
Can add a block to the category itself which will appear in all it's sub categories and courses
(use "moosh block-add -h" for more help)

Example:

    moosh block-add category 2 calendar_month admin-course-category side-pre -1
    moosh block-add -s category 2 calendar_month admin-course-category side-pre -1
    moosh block-add categorycourses 2 calendar_month course-view-* side-post 0
    moosh block-add course 32 calendar_month course-view-* side-post 0

block-manage
------------

Show or Hide blocks, system wide (Will also delete, in the future)

Example:

    moosh block-manage hide calendar
    moosh block-manage show calendar

cache-add-redis-store
---------------------

Adds a new redis store instance to cache like in
`Dashboard > Site administration > Plugins > Caching > Configuration > Redis > Add instance`

Example 1: Add new instance "Test" with server set to "localhost"
    
    moosh cache-add-redis-store "Test" "localhost"

Example 2: Add new instance "Test2" with server set to "localhost", password set to "123456" and key prefix set to "key_"

    moosh cache-add-redis-store --password "123456" -k "key_" "Test2" "localhost"

cache-add-mem-store
--------------------

Adds a new mem  store instance to cache.

Example 1: Add new instance "Test" with server set to "localhost".

    moosh cache-add-mem-store "Test" "localhost"

Example 2: Add new instance "Test2" with multiple servers

    moosh cache-add-mem-store "Test2" "192.168.0.1,192.168.0.2"

Example 3: Add new instance "Test3" with server set to "localhost", password set to "123456" and key prefix set to "key_"

    moosh cache-add-mem-store --password "123456" -k "key_" "Test3" "localhost"

Example 4: Add new instance "Test4" with server set to "localhost", with serialiser and compression enabled and password set to "12345"

    moosh cache-add-mem-store --compression "1" --serialiser "1" --password "12345" "Test4" "localhost"

Example 5: Add new instance "Test5" with server set to "localhost", with hash set to "md5" and password set to "12345"

    moosh cache-add-mem-store --hash "md5" --password "12345" "Test5" "localhost"

cache-clear
-----------

The same as "purge all caches" page.

    moosh cache-clear

cache-config-get
----------------

Gets cache config and print_r() it. You can find cache configuration in
`Dashboard > Site administration > Plugins > Caching > Configuration`

Example 1: Show every cache config

    moosh cache-config-get --all

Example 2: Show all of the configured stores

    moosh cache-config-get --stores

Example 3: Show all the known definitions and definition mappings

    moosh cache-config-get -dD

cache-course-rebuild
--------------------

Rebuild course cache.

Example 1: Rebuild cache for course with id 2

    moosh cache-course-rebuild 2

Example 2: Rebuild cache for all the courses.

    moosh cache-course-rebuild -a

cache-edit-mappings
-------------------

Edits default mode mappings like in `Dashboard > Site administration > Plugins > Caching > Configuration > Edit mappings`


Example 1: Show default mode mappings without changing
    
    moosh cache-edit-mappings

Example 2: Set MODE_APPLICATION to "new"

    moosh cache-edit-mappings --application new

Example 3: Set Application to "store name", Session to "Tests" and Request to "default_request"

    moosh cache-edit-mappings -a "store name" -s Tests -r default_request

cache-set
-------------------

Enable to attribute cache to specific cache definition

Requires cache definition and cache store

Example 1: Set "redisstore" to "core/calendar"_subscriptions definition 

    moosh cache-set core/calendar_subscriptions redisstore

cache-store-clear
-----------------

Clear a specific cache store or only a definition for the given cache store

Requires cache store name

Option:
* --definition, -d : cache definition name

Example 1 : Clear redisstore cache store 

    moosh cache-store-clear --defintion=calendar_subscriptions redisstore

Example 2 : Clear cache definition core/calendar_subscriptions for store named redisstore

    moosh cache-store-clear --definition=core/calendar_subscriptions redisstore

category-config-set
-------------------

Set category configuration. Arguments are: categoryid setting value. The setting should match one of the columns from mdl_course_categories table.

Example 1. Set ID for category 1 to "id17".

    moosh category-config-set 1 idnumber id17

Example 2. Set description of category id 1 to "My Description".

    moosh category-config-set 1 description "My Description" 


category-create
---------------

Create new category.

Example 1: Add new top level category "mycat", invisible with no description.

    moosh category-create mycat

Example 2: Add category "mycat" under category id 6, set to visible and description to "My category".

    moosh category-create -p 6 -v 1 -d "My category" mycat

Example 3: Create category only once. The second run of the command with "-r" will return the ID of the existing, matching category. The same category is defined as one having the same name, idnumber, parent and description. Also there must be exactly 1 match.

    moosh category-create -r CategoryABC
    moosh category-create -r CategoryABC


category-delete
---------------

Delete category, all sub-categories and all courses inside.

Example 1: Delete recursively category with id=2

    moosh category-delete 2


category-export
---------------

Export category structure to XML starting from given category ID. Give 0 to export all categories.

Example 1: Export all categories to XML.

    moosh category-export 0

Example 2: Export category with id 3 and all its sub categories.

    moosh category-export 3

category-import
---------------

Imports category structure from XML.

Example 1: Import all categories from XML.

    moosh category-import categor-to-import.xml


category-list
-------------

List all categories or those that match search string(s).

Example 1: List all categories

    moosh category-list

Example 2: List all categories with name "test" OR "foobar"

    moosh category-list test foobar

category-move
-------------

Move one category to another category

Example 1: Move the category with id 5 to be in the category with id 7

    moosh category-move 5 7

Example 2: Make the category with id 3 a top-level category

    moosh category-move 3 0

category-move-courses-from-category-to-another
----------------------------------------------

Move all courses included into a source category into a destination category

Requires Source course category id and Destination course category id

Example 1: Move all courses included into category with id 1 to category with id 2

    moosh category-move-courses-from-category-to-another 1 2

category-resortcourses
----------------------

Sort courses and/or categories by fullname, shortname or idnumber.


Example 1: Sort courses in category id 1 by idnumber. 

    moosh category-resortcourses 1 idnumber

Example 2: Sort courses and categories by shortname in category id 1 and all sub-categories below. 

    moosh category-resortcourses -r 1 shortname
    
Example 3: Sort courses by fullname in category id 1 and all sub-categories below. Do not change the ordering of categories. 

    moosh category-resortcourses -n -r 1 fullname


chkdatadir
----------

Check if every file and directory in Moodle data is writeable for the user that runs the command.
You usually want to run the check as the same user that runs web server.

Example:

    sudo -u www-data moosh chkdatadir


code-check
----------

Run Moodle code checker against the files.

Example 1:

    moosh code-check -p some/path/to/file.php

Example 2:

    moosh code-check -p some/path/to/dir

cohort-create
-------------

Create new cohort.

Example 1: Create two system cohorts "mycohort1" and "mycohort2".

    moosh cohort-create mycohort1 mycohort2

Example 2: Create cohort "my cohort18" with id "cohort18" under category id 2, with description "Long description".

    moosh cohort-create -d "Long description" -i cohort18 -c 2 "my cohort18"

cohort-delete
------------

Delete one or more cohorts

Requires cohort ids to delete separated by ,

Example 1: Delete cohort of id 42

    moosh cohort-cohort-delete 42

Example 2: Delete cohorts of ids 42 and 2012

    moosh cohort-cohort-delete 42,2012


cohort-enrol
------------

Add user to cohort or enroll cohort to a course.

Example 1: Add user id 17 to cohort named "my cohort18"

    moosh cohort-enrol -u 17 "my cohort18"

Example 2: Enroll cohort "my cohort18" to course id 4.

    moosh cohort-enrol -c 4 "my cohort18"

cohort-enrolfile
--------------

Add users to cohorts from a CSV file. The vaild fields for the CSV file include: username,
email, cohortid, cohortname

The CSV must include at least one of username/email and one of cohortid/cohortname. If
more than one of either category is given, username and cohortid take precedence over the
other values

Example 1: Add users to specified cohorts from /home/me/testing.csv. If the contents of
testing.csv are

	username,cohortid
	johndoe,1
	janedoe,2

then user johndoe is enrolled in cohort id 1, and user janedoe is enrolled in cohort with
id 2 by:

	moosh cohort-enrolfile /home/me/testing.csv

cohort-unenrol
--------------

Remove user(s) from a cohort (by cohort id)

Example 1: Remove users 20,30,40 from cohort id=7.

    moosh cohort-unenrol 7 20 30 40

config-get
----------

Get config variable from config or config_plugins table. The syntax is based on get_config($plugin,$name) API function. Both arguments are optional.

Example 1: Show all core config variables.

    moosh config-get

Example 2: Show all config variables for "user"

    moosh config-get user

Example 3: Show core setting "dirroot"

    moosh config-get core dirroot


config-plugin-export
--------------

Exports whole configuration of selected plugin to .xml

Example 1: Export mod_book plugin configuration to Book_config_{timestamp}.xml in current directory. 

    moosh config-plugin-export book

Example 2: Export mod_book plugin configuration to /tmp/plugin/Book_config_{timestamp}.xml

    moosh config-plugin-export -o /tmp/plugin/ mod_book

config-plugin-import
--------------

Imports configuration of plugin from .xml created by **config-plugin-export**

Example 1: Import configuration of plugin mod_book into moodle.

    moosh config-plugin-import /tmp/Book_config_1608106580.xml

To see changes in Moodle you need to execute `moosh cache-clear`

config-plugins
--------------

Shows all plugins that have at least one entry in the config_plugins table. Optionally provide an argument to match plugin name.

Example 1: Show all plugins from config_plugins table.

    moosh config-plugins

Example 2: Show all themes that have some settings.

    moosh config-plugins theme_

config-set
----------

Set config variable. The syntax of the command is based on the set_config() Moodle API:

    moosh config-set name value <plugin>

If third argument (plugin) is not provided then the variable is set in the core Moodle configuration table.

Example 1: Enable debug.

    moosh config-set debug 32767

Example 2: Set URL to logo for Sky High theme.

    moosh config-set logo http://example.com/logo.png theme_sky_high

context-freeze
--------------
Freeze or unfreeze a given context

Requires instance id, context level and lock

Lock is 1 if locked, 0 if unlock

Example 1 : Lock course context of id 20

    moosh context-freeze 20 50 1

Example 1 : Unlock course context of id 20

    moosh context-freeze 20 50 0

context-rebuild
---------------

Rebuild context paths - it does the same thing as command bellow with \context_helper::build_all_paths(true)

    php admin/tool/task/cli/schedule_task.php --execute='\core\task\context_cleanup_task' --showdebugging

(see https://docs.moodle.org/311/en/How_to_rebuild_context_paths)

Example:

    moosh context-rebuild

course-backup
-------------

Backup course with provided id.  By default, logs and grade histories are excluded.

Example 1: Backup course id=3 into default .mbz file in current directory:

    moosh course-backup 3

Example 2: Backup course id=3 and save it as /tmp/mybackup.mbz:

    moosh course-backup -f /tmp/mybackup.mbz 3

Example 3: Backup course id=3, including logs and grade histories:

    moosh course-backup --fullbackup 3

Example 3: Backup course id=3 without any user data (excludes users, logs, grade historyies, role assignments, comments, and filters):

    moosh course-backup --template 3


course-cleanup
--------------

The command will to though various pieces of HTML texts contained in the given course and run purify_html() function on them. The command does not actually do any changes - 
it will only show which content could possibly be cleaned up.

Example 1: Check if there is any HTML to be cleaned-up in course 3:

    moosh course-cleanup 3


course-config-set
-----------------

Update a field in the Moodle {course} table for a single course, or for all courses in a category.

Example 1: set the shortname of a single course with id=42

    moosh course-config-set course 42 shortname new_shortname

Example 2: set the format to topics for all courses in a category with id=7

    moosh course-config-set category 7 format topics


course-create
-------------

Create a new course(s).

Example 1: Create 10 new courses using bash/zim expansion

    moosh course-create newcourse{1..10}

Example 2: Create new course

    moosh course-create --category 1 --fullname "full course name" --description "course description" --idnumber "course idnumber" shortname

Example 3: Create new course with section format, number options

    moosh course-create --category 4 --format topics --numsections 2 test


course-delete
-------------

Delete course(s) with ID(s) given as argument(s).

Example 1: delete courses id 2,3 and 4.

    moosh course-delete 2 3 4


course-enrol
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

course-enrolbyname
------------------

Is similar to course-enrol function. But it can also be used the first- and lastname of the user and the course shortname.

Example 1: Enroll user with firstname test42 and lastname user42 into the course with shortname T12345 as an editing teacher.

    moosh course-enrolbyname -r editingteacher -f test42 -l user42 -c T12345

course-enrol-change-status
------------------

Requires course id

Options :
* --instanceid=?, -i ? : enrolment instance id, if not command enter in interactive mode and show all available enrolment instance for the given course, you'll have to choose status from prompt
* --status=0 or 1, -s 0 or 1 : status of enrolment instance 0 (default value) -> enabled, 1 -> disabled

Example1 : change course enrolment instance status in interactive mode 

    moosh course-enrol-change-status 2

Example1 : change course enrolment instance status to disable for instance 42

    moosh course-enrol-change-status -i=42 -s=1 2

course-enableselfenrol
----------------------

Enable self enrolment on one or more courses given a list of course IDs. By default self enrolment is enabled without an enrolment key, but one can be passed as an option.

Example 1: Enable self enrolment on a course without an enrolment key

    moosh course-enableselfenrol 3

Example 2: Enable self enrolment on a course with an enrolment key

    moosh course-enableselfenrol --key "an example enrolment key" 3

course-info
-----------

Shows basic information about given course ID.
This command will also clear course cache, to measure how much time the cache rebuild takes.

Example 1: Show statistics for course ID 2.

    moosh course-info 2  

Example 2: Show statistics for course ID 2 - display in CSV format.

    moosh course-info -c 2  


course-last-visited
-------------------

Shows how many hours ago any user has last visited the course.

Example 1: How many hours ago course ID 2 was visited.

    moosh course-last-visited 2

course-list
-----------

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

course-move
-----------

Moves one or more courses into a given category.

Example 1: Move the course with ID 5 into the category with ID 2.

    moosh course-move 5 2

Example 2: Move courses with IDs 5, 6, 7, and 10 into the category with ID 2.

    moosh course-move 5,6,7,10 2

course-reset
------------

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
        
course-restore
--------------

Restore course from path/to/backup.mbz to category or existig course.

Example 1: Restore backup.mbz into category with id=1

    moosh course-restore backup.mbz 1

Example 2: Restore backup.mbz into existing course with id=3

    moosh course-restore -e backup.mbz 3

Example 3: Ignore pre-check warnings, like restoring backup from higher Moodle version.

    moosh course-restore --ignore-warnings backup.mbz 1

Example 4: Restore backup.mbz into existing course with id=3, overwrite course content.

    moosh course-restore --overwrite backup.mbz 3

course-unenrol
--------------

Unerol user(s) from a course id provided. First argument is a course ID then list of users.


Example 1: Unenrol users with id 7, 9, 12 and 16 from course with id 2.

    moosh course-unenrol 2 7 9 12 16

dashboard-reset-all
----------
Reset all users dashboard

Example 1: Reset all users dashboard

    moosh dashboard-reset-all


data-stats
----------

Provides information on size of dataroot directory, dataroot/filedir subdirectory and total size of non-external files in Moodle.
Outputs data in json format when run using --json option.

    moosh data-stats

db-stats
--------

Shows the total size of the Moodle database and the biggest tables.
With -H or -j options the sizes will be shown in bytes (ie "286720" instead of "280KB").

Example 1: Show me basic database statistics, formatted for human consumption. 

    moosh db-stats

Example 2: Database statistics in JSON format. 

    moosh db-stats -j
    
debug-off
---------

Turns off full debug and disables theme designer mode.

    moosh debug-off

debug-on
--------

Turns on full debug - all the options in debugging section of the settings plus enables theme designer mode.

    moosh debug-on

delete-missingplugins
---------------------

Uninstalls all plugins that are "missing" (PLUGIN_STATUS_MISSING) - meaning there is no source code for them.
Running this command will delete database tables - destroying any data that may have been stored in plugin. 

    moosh delete-missingplugins

dev-langusage
-------------

The command parses PHP file given as an argument. If the argument points to directory, 
then all the .php files inside it ae parsed.
A parser will look for the use of language strings, that is calls to get_string(), print_string(), string_for_js().
It then checks if the string is known by the string_manager - that is, if the sting is listed in the lang file.
With -l / --lang flag you may include and check against any lang file.

Example 1: Check if strings used in mod/book exist in the lang file. 
Show only the ones missing (if any).  

    moosh dev-langusage mod/book | grep missing

Example 2: Check if "mod_book" component strings used in mod/book are defined in the lang file. 

    moosh dev-langusage -c mod_book mod/book

dev-versionbump
---------------

Increase the version in module's version.php.

Example:

    cd <moodle_root>/mod/<your_module>
    moosh dev-versionbump

download-moodle
---------------

Download latest stable Moodle version (default) or another version -v is provided.

Example 1: Download latest Moodle

    moosh download-moodle

Example 2: Download latest Moodle 3.10.

    moosh download-moodle -v 3.10

Example 2: Download Moodle 3.5.15.

    moosh download-moodle -v 3.5.15

event-fire
----------

Fire an event. Provide event name and JSON encoded data.

    moosh event-fire report_log\\event\\report_viewed '{"contextid":1,"relateduserid":1,"other":{"groupid":1,"date":100,"modid":1,"modaction":"view","logformat":0}}' 


event-list
----------

List all events available in current Moodle installation.

    moosh event-list

file-check
------------

For each file entry in the database, check that the file exists in moodledata (in filedir).
With --stop <number> stop the search after number of missing files found. 

Example:

    moosh file-check -s 1
    
Result:

    Missing /opt/data/filedir/5f/8e/5f8e911d0da441e36f47c5c46f4393269211ca56
    assignfeedback_editpdf / stamps "smile.png" 2019-08-25 15:43 / 2019-08-25 15:43
    Found 1 missing files, not searching anymore. Set -s 0 option to disable the limit.


file-datacheck
--------------

Go through all files in Moodle data and check them for corruption. The check is to compare file's SHA to their file names.

    moosh file-datacheck


file-dbcheck
------------

For each file in moodledata, check that there is an entry in the Moodle DB.
This command will find any extra files in "filedir".

    moosh file-dbcheck


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

Example 4: Remove all automated backups and reclaim the space

    moosh file-list -i 'component="backup" AND filearea="automated"' | moosh file-delete -s
    moosh file-delete --flush

file-hash-delete
----------------

Delete files that match given hash from the dababase (mdl_files).

Example:

    moosh file-hash-delete 5f8e911d0da441e36f47c5c46f4393269211ca56
    
Resuls:

    There is: 1 results of this hash. 
    Successfully deleted files.
    File ID: 1, contenthash: 5f8e911d0da441e36f47c5c46f4393269211ca56, itemid: 0, component: assignfeedback_editpdf, filearea: stamps, filename: smile.png


file-list
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


file-path
---------

Show full or relative path in the filesystem to Moodle file(s). Files can be identified by ID or hash (auto-detected) as arguments or on stdin (-s option).

Example 1: Show path to a file with contenthash da39a3ee5e6b4b0d3255bfef95601890afd80709

    moosh file-path da39a3ee5e6b4b0d3255bfef95601890afd80709

Example 2: Show paths to files with ID bewteen 100 and 200

    moosh file-list -i 'id>100 AND id<200' | moosh file-path -s

Example 3: Like above but with no duplicates and show path relative to data root (-r)

    moosh file-list -r -i 'id>100 AND id<200' | moosh file-path -s | sort | uniq

file-upload
-----------

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

filter-set
---------

Enable/disable global filter, equivalent to admin/filters.php settings page. First argument is a filter name without filter_ prefix.
Second argument is a state, use On = 1 , Off/but available per course = -1 , Off = -9999 .


Example 1: Disable multimedia filter completely.

    moosh filter-set mediaplugin -9999 

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

forum-newdiscussion
-------------------

Adds a new discussion to an existing forum. You should provide a course id, a forum id
and an user id in this order. If no name or message is specified it defaults to the data
generator one.

Example:

    moosh forum-newdiscussion 3 7 2
    moosh forum-newdiscussion --subject "Forum Name" --message "I am a long text" 3 7 2


generate-availability
---------------------

Generate a code for new availability condition based on danielneis/moodle-availability_newavailability.

    moosh generate-availability newcondition


generate-block
--------------

Generate a code for new block based on the template.

Example: generate new block_abc

    moosh generate-block abc


generate-cfg
------------

Generate fake class to get auto-completion for $CFG object. Properties genertated extracted from the current source code.
 See [setup instructions](http://moosh-online.com/#cfg-auto-completion).

    moosh generate-cfg > config.class.php

generate-enrol
--------------

Creates new local plugin under enrol/ based on template from https://github.com/danielneis/moodle-enrol_newenrol

    moosh generate-enrol name

Example 1: Generate new plugin under enrol/mynewenrol

    moosh generate-local mynewenrol

generate-filemanager
--------------------

Shows how to code filepicker, based on https://github.com/AndyNormore/filemanager. Takes no arguments.

    moosh generate-filemanager

generate-files
--------------

This command creates new mod_resource in choosen course with a selected number of files and their size.

moosh generate-files [-n, --name] [-s, --section] (courseid) (filescount) (filesize)

Example of total sizes:
- 1KB (1 file x 1024 Bytes),
- 1MB (64 files x 16384 Bytes),
- 10MB (128 files x 81920 Bytes),
- 100MB (1024 files x 102400 Bytes),
- 1GB (16384 files x 65536 Bytes)
- 2GB (32768 files x 65536 Bytes)

Example 1: Add 1 file that weighs 1MB into course with id = 4

    moosh generate-files 4 1 1048576

Example 2: Add 1000 files (1KB each) into course with id=4, the file should be named 'Test' and placed in section number 3

    moosh generate-files -n 'Test' -s 3 4 1000 1024

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

generate-gradereport
--------------------

Creates new grade report under grade/report based on the template from https://github.com/danielneis/moodle-gradereport_newgradereport.

    moosh generate-gradereport report_name

Example: Create new report under grade/report/beststudents

    moosh generate-gradereport beststudents

generate-gradeexport
--------------------

Creates new grade export under grade/export based on the template from https://github.com/danielneis/moodle-gradeexport_newgradeexport.

    moosh generate-gradeexport export_name

Example: Create new export under grade/export/mycustomsystem

    moosh generate-gradeexport mycustomsystem

generate-lang
-------------

Scan files given as arguments or currently remembered file, extract language strings and add them to the lang file if
necessary.

    moosh generate-lang [file1] [file2] [file3]...

Example 1: Extract lang strings from edit_form.php.

    moosh generate-lang edit_form.php

generate-local
--------------

Creates new local plugin under local/ based on template from https://github.com/danielneis/moodle-local_newlocal

    moosh generate-local name

Example 1: Generate new plugin under local/mynewlocal

    moosh generate-local mynewlocal


generate-messageoutput
----------------------

Creates new message output processor under message/output based on the template from https://github.com/danielneis/moodle-message_newprocessor.

    moosh generate-messageoutput processor_name

Example: Create new message output processor under message/output/flashcard

    moosh generate-messageoutput flashcard

generate-module
---------------

Creates new module based on the NEWMODULE template from Moodle HQ.

    moosh generate-module module_name

Example: Create new module under mod/flashcard

    moosh generate-module flashcard


generate-moosh
--------------

Use moosh to create new moosh command.

    moosh generate-moosh category-command


generate-qtype
--------------

Creates new question type based on the NEWMODULE template from https://github.com/jamiepratt/moodle-qtype_TEMPLATE.

    moosh generate-qtype qtype_name

Example: Create new module under question/type/myqtype

    moosh generate-qtype myqtype


generate-userprofilefield
-------------------------

Creates new profile field based on a template.

    moosh generate-userprofilefield newfield


generate-ws
-----------

Creates new local plugin for WS development based on moodlehq/moodle-local_wstemplate.

    moosh generate-ws newws


gradebook-import
----------------

Imports gradebook grades from csv file into a course given by id. With --course-idnumber use take mdl_course.idnumber instead of course.id.
--map-users-by will change what to use for mapping users from CSV (email or idnumber).

Use --test for testing the import first.

Example:

    moosh gradebook-import --test gradebook.csv course_id

Possible column headers to us:

* "ID number" user's ID number (idnumber)
* "email" user's email
* one or more columns matching grade item name


gradecategory-create
--------------------

Creates grade category.

Example:

    moosh gradecategory-create -n category-name -a aggregation parent_id course_id

gradecategory-list
------------------

Lists grade categories, with command-line options, arguments modeled on course-list's.

Example:

    moosh gradecategory-list --hidden=yes --empty=yes --fields=id,parent,fullname courseid=26

gradeitem-create
----------------

Creates grade items, with command-line options and courseid, gradecategoryid arguments.

Example:

    moosh gradeitem-create --itemname=Boost --grademax=3 --calculation='=max(3, ##gi5075##)' -o '--aggregationcoef=1' 37 527

gradeitem-list
--------------

Lists grade items, with command-line options, arguments modeled on course-list's.

Example:

    moosh gradeitem-list --hidden=yes --locked=no --empty=yes --fields=id,categoryid,itemname courseid=26

gradebook-export
----------------

Exports gradebook grades for grade item(s) (comma-separated if more than 1) in specified course.

Example:

    moosh gradebook-export -g 0 -x 1 -a 1 -d 2 -p 0 -s comma -f txt 4755,4756 40 > grades.csv

Options and defaults:

* 'group id': 0
* 'exportfeedback': 0
* 'onlyactive': 1
* 'displaytype (real=1, percentage=2, letter=3)': 1
* 'decimalpoints': 2
* 'separator (tab, comma)': comma
* 'export format: (ods, txt, xls, xml)': txt

group-create
------------

Create a new group.

Example 1:

    moosh group-create --description "group description" --key sesame --id "group idnumber" groupname courseid

group-list
----------

Lists groups in course, or grouping.

Example 1:

    moosh group-list courseid ...

Example 2:

    moosh group-list --id -G groupingid courseid

group-memberadd
---------------

Add a member to a group.

Example 1:

    moosh group-memberadd -c courseid -g groupid membername1 [membername2] ...

Example 2:

    moosh group-memberadd -g groupid memberid1 [memberid2] ...


grouping-create
---------------

Create a new grouping.

Example:

    moosh grouping-create --description "grouping description" --id "grouping idnumber" groupingname courseid

group-assigngrouping
--------------------

Add a group to a grouping.

Example:

    moosh group-assigngrouping -G groupingid groupid1 [groupid2] ...

info
----

Show information about plugin in current directory.

Example 1:

    moosh info

info-context
------------

Show information about given context ID - as in mdl_context.id.

Example 1: Show information about Moodle context 123.

    moosh info-context 123


info-plugins
------------

List all possible plugins in this version of Moodle and directory for each.

Example 1: Show all plugin types.

    moosh info-plugins

lang-compare
------------

Compare 2 Moodle language files and lists the difference in strings (keys) between them.
It does not compare the translations (values).

Example: 

    moosh lang-compare book.php book2.php
    
Result:

    Comparing book.php and book2.php. Summary:
    Number of strings in book.php: 72
    Number of strings in book2.php: 72
    Number of strings missing in book2.php: 1
    Number of strings missing in book.php: 1

    List of language strings that exist in book.php but are not present in book2.php
    pluginname

    List of language strings that exist in book2.php but are not present in book.php
    pluginname2

language-install
----------------

Install language pack.

Example: Install French language pack.

    moosh language-install fr

languages-update
----------------

Update all installed language packs, in the current Moodle folder.

Example 1: Update all language packs.

    moosh languages-update

maintenance-off
---------------

Disable maintenance mode.

    moosh maintenance-off

maintenance-on
--------------

Enable maintenance mode.

    moosh maintenance-on

A maintenance message can also be set:

    moosh maintenace-on -m "Example message"

module-config
-------------

Set or Get any plugin's settings values

Example:

    moosh module-config set dropbox dropbox_secret 123
    moosh module-config get dropbox dropbox_secret ?

module-config
-------------
Copy a module from one course to another.

Example 1: Copy module id 27 to course id 34.

    moosh module-copy 27 34

Example 2: Copy module id 27 to course id 34 and name the new module "Assignment 1".

    moosh module-copy --name "Assignment 1" 27 34

Example 3: Copy module id 27 to course id 34 and name the new module "Assignment 1",
placing it in section 2.

    moosh module-copy --name "Assignment 1" --section 2 27 34

module-manage
-------------

Show or Hide moudles, system wide (Will also delete, in the future)

Example:

    moosh module-manage hide scorm
    moosh module-manage show scorm


module-reinstall
----------------

Re-install any Moodle plugin. It will remove all the data related to the module and install it from clean.

Example:

    moosh module-reinstall block_html
    moosh module-reinstall mod_book


nagios-check
------------

Create session login and login to a site using curl. Return error in Nagios format if login was not successful. 

    moosh nagios-check


php-eval
--------

Evaluate arbitrary php code after bootstrapping Moodle.

Example:

    moosh php-eval 'var_dump(get_object_vars($CFG))'

plugin-hideshow
---------------
Hide or show a plugin in all site context

Requires plugin type, plugin name and show option.

Show option is 0 if hide and 1 if show.

This will work for the following plugin types:

block, mod, assignfeedback, assignsubmission, qtype, qbehaviour, enrol, filter, editor, auth, license, repository, courseformat or avaibility

Example 1 : Hide chat module plugin for entire site

    moosh plugin-hideshow mod chat 0


plugin-download
---------------

Download plugin for a given Moodle version to current directory.
Requires plugin short name, and optional Moodle version.
You can obtain avalible plugins names by using `plugin-list -n' command.
You may specify proxy server with command line option or define ENV variable http_proxy.

Example 1: Download block_fastnav for moodle 3.9 into ./block_fastnav.zip

    moosh plugin-download -v 3.9 block_fastnav

Example 2: Only show link for block_fastnav moodle current version

    moosh plugin-download -u block_fastnav

Output:

    https://moodle.org/plugins/download.php/23108/block_fastnav_moodle310_2020120800.zip


plugin-install
--------------

Download and install plugin. Requires plugin short name, and optional version. You can obtain those data by using `plugin-list -v' command.
You may specify proxy server with command line option or define ENV variable http_proxy.

Example 1: install a specific version

    moosh plugin-install --release 20160101 mod_quickmail

Example 2: install the latest release supported by current Moodle version.

    moosh plugin-install block_checklist


plugin-list
-----------

List Moodle plugins filtered on given query. Returns plugin full name, short name, available Moodle versions and short description.
You may specify proxy server with command line option or define ENV variable http_proxy.

Example 1: list all plugins available on https://moodle.org/plugins

    moosh plugin-list

Example 2: download all modules available for version 2.8 or later

    moosh plugin-list  | grep '^mod_' | grep 2.8 | grep -o '[^,]*$' | wget -i -

plugins-usage
-------------

Shows the usage of the subset of the plugins used in Moodle installation. 
Plugin will show the usages, wherever it can figure out if the plugin is used. 
Currently supported plugins are: filters, question types, course formats, enrolments,
blocks, authentication types, activities.

Example 1: show all plugins and their usage

    moosh plugins-usage 


Example 2: show only contrubuted (3-rd party) plugins

    moosh plugins-usage -c 1

plugin-uninstall
----------------

Removes given plugin from the DB and disk. It can remove plugins that have no folder on the disk and have some redundant data inside DB tables.
If you do not have write permissions on the plugins' folder it will advice you with the command that will give the right permissions and then you are asked to run the command again.

Example:

    moosh plugin-uninstall theme_elegance

question-import
---------------

Import quiz question from xml file into selected quiz.

Example: import question from file path/to/question.xml to quiz with id 2

    moosh question-import path/to/question.xml 2

questionbank-import
-----------------------

Import questions in XML or GIFT format into question bank.

Example: import questions from file path/to/question.xml to question bank category id 10

    moosh questionbank-import path/to/question.xml 10


questioncategory-create
-----------------------

Creates a 'name' question category in a parent category with
context. The --reuse option creates the category
if and only if there is not exactly one already in existence with the
same parent, context and name, returning the existing category id, otherwise.

Other options are idnumber and infoformat, which defaults to
FORMAT_MARKDOWN.

Example: create a question category, 'noclass' in parent category 6044, with
context 754 and info 'New Year'. 

     moosh questioncategory-create --reuse -p 6044 -c 754 -d 'New Year' noclass

quiz-delete-attempts
--------------------

Deletes all quiz-attempts with given quiz id.

Example 1: delete all attempts from quiz id 2.

    moosh quiz-delete-attempts 2

Resuls:

    Deleted attempt: 15
    Deleted attempt: 16
    Deleted attempt: 17
    Deleted attempt: 18
    Deleted 4 questions

random-label
------------

Add a label with random text to random section of course id provided.

Example 1: Add 5 labels to course id 17.

    for i in {1..5}; do moosh random-label 17; done

Example 2: Add label that will contain string " uniquetext " inside.

    moosh random-label -i ' uniquetext ' 17

report-concurrency
------------------

Get information about concurrent users online.

Use: -f and -t with date in either YYYYMMDD or YYYY-MM-DD date. Add -p to specify period.

Example 1: Get concurrent users between 20-01-2014 and 27-01-2014 with 30 minut periods.

    moosh report-concurrency -f 20140120 -t 20140127 -p 30

Example 2: Create the report for the last week. Could be used in a cronjob.

    start=$(date --date="7 days ago" +"%Y-%m-%d");finish=$(date +"%Y-%m-%d");moosh report-concurrency --from $start --to $finish

request-select
-------

Run any custom SQL Select against bootstrapped Moodle instance DB and return resultset with csv format with ; separator.

Requires a select query as argument.

Usefull to create a csv file to import datas in moodle

Usefull also to retrieve an value to reuse it later in a script composed of moosh commands

Example 1: Select username, firstname, lastname and email 

    moosh request-select "select username,firstname,lastname, email from {user}"

Output:

    egrieg;Edward,Grieg,egrieg@example.com

    hpurcell;Henry;Purcell,hpurcell@example.com


restore-settings
----------------

Returns all possible restore settings for the current Moodle. To figure them out,
the command creates and then backes up an empty course with short name "moosh001" - unless it already exists.

Example 1: Dump all possible restore settings

    moosh restore-settings

role-create
-----------

Create new role, optionally provide description, archetype, context and name. Role id is returned.

Example 1: Create role with short name "newstudentrole" a description, name an archetype

    moosh role-create -d "Role description" -a student -n "Role name" newstudentrole

Example 2: Create role with short name "newrole" a description, context level

    moosh role-create -d "Description" -c system,user,block newrole
 
This command will create a role named "newrole" with system,user and block contextlevels checked.
Note: If neither an archetype nor the context level is defined, system context would be checked by default.

role-delete
-----------

Delete role by ID or shortname.

Example 1: Delete role "newstudentrole"

    moosh role-delete newstudentrole

Example 2: Delete role id 10.

    moosh role-delete -i 10


role-export
-----------

Export role data, including permissions, role overrides, allow view settings and other related data.

Example 1: Export specific role data to a an output XML file.

    moosh role-export -f target_file.xml ROLENAME

Example 2: Export specific role data, generate output XML export and print to stdout.

    moosh role-export ROLENAME
Example 3: Format XML with whitespaces (pretty format) and output XML contents to stdout.

    moosh role-export --pretty ROLENAME

role-import
-----------

Import role data from an XML file that was produced either by role-export command or Moodle permissions UI. 

Example 1: Import role from a specific XML file.

    moosh role-import -f source_file.xml

Example 2: Import role by reading XML data from STDIN.

    moosh role-import --stdin < source_file.xml

You can either load XML data from a file or from STDIN.
If XML defines an existing rolename then this command will sync changes to an existing role.
If role does not exist and archetype is defined this command will create a new role with archetype defaults and xml permissions.
If neither role nor archetype exist the new role will be created with INHERITED permissions defaults.

role-list
---------

Get list of roles

Example 1: Get roles with id, shortname and name.

    moosh role-list

Result:

    |   ID |Shortname                      |Name                           |
    |    1 |manager                        | Manager                       |
    |    2 |coursecreator                  | Course creator                |
    |    3 |editingteacher                 | Teacher                       |
    |    4 |teacher                        | Non-editing teacher           |
    |    5 |student                        | Student                       |
    |    6 |guest                          | Guest                         |
    |    7 |user                           | Authenticated user            |
    |    8 |frontpage                      |                               |


role-reset
----------

Reset give role's permissions from the file.

    moosh role-reset 1 definition_file.txt


role-update-capability
----------------------

Update role capabilities on any context.

Use: -i "roleid" or "role_short_name" with "role capability" and "capability setting" (inherit|allow|prevent|prohibit)
and finally, "contextid" (where 1 is system wide)

Example 1: update "student" role (roleid=5) "mod/forumng:grade" capability, system wide (contextid=1)

    moosh role-update-capability student mod/forumng:grade allow 1

Example 2: update "editingteacher" role (roleid=3) "mod/forumng:grade" capability, system wide (contextid=1)

    moosh role-update-capability -i 3 mod/forumng:grade prevent 1

role-update-contextlevel
------------------------

Update the context level upon a role can be updated.

Use: "short role name" or -i "roleid" with relevant context level (system|user|category|course|activity|block)
and add "-on" or "-off" to the caontext level name to turn it on or off.

Example 1: Allow "student" role to be set on block level

    moosh role-update-contextlevel student -block-on

Example 2: Prevent "manager" role to be set on course level

    moosh role-update-contextlevel manager -course-off

section-config-set
-------------------

Follows the course-config-set pattern, updating a field in the Moodle {course_sections} table, for all the course sections (or optionally a single section), in all the courses in a course category, or alternatively in one course.

Example 1: set the name of the second section in course with id 45 to "Describe a picture"

    moosh section-config-set -s 2 course 45 name "Describe a picture"

Example 2: set summaryformat to markdown in all sections in courses in the Miscellaneous category

    moosh section-config-set category 1 summaryformat 4

Example 3: Hide all sections in course with id 45

    moosh section-config-set course 45 visible 0

sql-cli
-------

Open a connection to the Moodle DB using credentials in config.php. Currently supports PostgreSQL, Cockroachdb, and MySQL.

Example:

    moosh sql-cli


sql-dump
--------

Dump Moodle DB to sql file. Works for PostgreSQL, Cockroachdb, and MySQL.

Example 1: dump database to backup.sql file

    moosh sql-dump > backup.sql


sql-run
-------

Run any custom SQL against bootstrapped Moodle instance DB. If query start with SELECT then matched rows will be displayed.

Example 1: Set the country of all the users to Poland

    moosh sql-run "update {user} set country='PL'"

Example 2: Count the number of rows is log table

    moosh sql-run "select count(*) from {log}"

task-lock-check
---------------

Show locked tasks. The command only works with MySQL. 

Example 1: show list of locked tasks (if any)

    moosh task-lock-check

Example 2: show all tasks and their status (locked/unlocked)

    moosh task-lock-check -a

task-schedule
-------------

Schedule Moodle task

Requires task name with namespace

Options:
* -M, --minute :minute
* -H, --hour : hour
* -d, --day : day
* -m, --month : month
* -w, --dayofweek : Day of week
* -x, --disabled : Disabled
* -r, --resettodefaults : Reset to defaults


Example 1 : Schedule cleanup_task to launch every day at 01:01

    moosh -d=* -H=1 -m=1 "\tool_messageinbound\task\cleanup_task"

theme-info
----------

Show what themes are really used on Moodle site.

Example:

    moosh theme-info

theme-settings-export
---------------------

Export theme settings (including uploaded files) as a tar.gz for use with `theme-settings-import`.

Example 1: run within a theme directory and it will know which theme you want

    moosh theme-settings-export

Example 2: run it anywhere within the Moodle dir if you specify the theme name

    moosh theme-settings-export --themename boost

theme-settings-import
---------------------

Import settings from file created with `theme-settings-export`.

Example 1: give the name of the exported file

    moosh theme-settings-import boost_settings_1558197087.tar.gz

Example 2: specify a target theme name if you want to transfer settings to a different (but compatible) theme

    moosh theme-settings-import --targettheme boostfork boost_settings_1558197087.tar.gz

top
---

Display the latest entries from the log table mdl_logstore_standard_log.
If combined with "watch" command, it will imitate the poor man's "top" utility.

Example:

    watch moosh top

user-online
---

Display currently online users in a simple table. In a contrast to TOP command this command uses Fetcher API and queries user table instead of standard log store.
Available options:

| Option           | Description                                                   |
|------------------|---------------------------------------------------------------|
| -t, --time       | Show users online in last N seconds. Default 15 sec.          |
| -l, --limit      | Show maximum number of users. If empty all users are fetched. |
| -e, --hideheader | Print header with table column names.                         |

Use linux "watch" command to refresh screen periodically.

Example: Show online users in last 5 minutes and refresh list every 5 seconds.

    watch moosh user-online -t 300

user-assign-system-role
-----------------------

Assign system role to user.

Example 1: assign "manager" role for "testuser"
    
    moosh user-assign-system-role testuser manager
    
Example 1: assign "coursecreator" role for "testuser2"
    
    moosh user-assign-system-role testuser2 coursecreator

user-create
-----------

Create a new Moodle user. Provide one or more arguments to create one or more users.

Example 1: create user "testuser" with the all default profile fields.

    moosh user-create testuser

Example 2: create user "testuser" with the all the optional values

    moosh user-create --password pass --email me@example.com --digest 2 --city Szczecin --country PL --institution "State University" --department "Technology" --firstname "first name" --lastname name testuser

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

    moosh user-delete testuser1 testuser2

user-export
-----------

Exports user with given username to csv file.

Example 1:

    moosh user-export testuser

user-getidbyname
----------------

This command has been deprecated. Use user-list instead.

user-import-pictures
--------------------

Provides capability of importing user pictures from a specific directory (recursively including subdirectories). 
Image filename can be mapped to user ID, idnumber or username database field. Supported image types are jpg, gif and png.
--overwrite option flag can be used to force overwriting of existing user pictures.
To make this command compliant with RGPD, --policy option can be use to check policy acceptance by user before importing picture.

Example 1: import user pictures from directory and map file names to user's ID value. 

    moosh user-import-pictures -i /path/to/import/dir

Example 2: import user pictures from directory and map file names to user's idnumber value. 

    moosh user-import-pictures -n /path/to/import/dir

Example 3: import user pictures from directory and map file names to user's username value. 

    moosh user-import-pictures -u /path/to/import/dir

Example 4: import user pictures from directory and map file names to user's username value. Before importing, check if user has accepted the policy with id 4.

    moosh user-import-pictures -u --policy 4 /path/to/import/dir

user-list
---------

List user accounts. It accepts sql WHERE clause. If run without sql argument it will list first 100 users from database.

Example 1: list user accounts with id number higher than 10 and sort them by email

    moosh user-list --sort email "id > 10"

Example 2: list users with first name bruce and username batman

    moosh user-list "name = 'bruce' AND username = 'batman'"

Example 3: list users enrolled in course id 2

    moosh user-list --course 2

Example 4: list teachers enrolled in course id 2 that never accessed that course

    moosh user-list --course 2 --course-role editingteacher --course-inactive

user-login
----------

Logs given user in and return the session cookie.
This can then be used in another HTTP call to impersonate that user. 
It is important that you run moosh as correct www user.
If you run it as another user than your web server, then the server may not be able 
to read session file and you will see the error: 

    error/An error occurred whilst communicating with the server 

Example: login as student1 and fetch his dashboard page.

    $ moosh user-login student1
    MoodleSession:h6v2l11946ne16tejogs55vhcn
    $ curl -b 'MoodleSession=h6v2l11946ne16tejogs55vhcn' http://localhost/vanilla37/my/index.php
    
    
user-mod
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

user-unassign-system-role
-------------------------

Unassign system role to user.

Example 1: unassign "manager" role for "testuser"
    
    moosh user-unassign-system-role testuser manager
    
Example 2: unassign "coursecreator" role for "testuser2"
    
    moosh user-unassign-system-role testuser2 coursecreator

userprofilefields-export
------------------------

Export the definition of the defined user profile fields to CSV file
named by default "user_profile_fields.csv".

Example 1: export to "user_profile_fields.csv" in current directory

    moosh userprofilefields-export

Example 2: save CSV file as /tmp/fields.csv

    moosh userprofilefields-export -p /tmp/fields.csv

userprofilefields-import
------------------------

Import user profile fields defined in given CSV file.

Example: import user profie fields from user_profile_fields.csv
 
    moosh userprofilefields-import user_profile_fields.csv

webservice-call
---------------

Calls 

Example: Get list of all courses enroled for a user

    moosh webservice-call --token 4ac42118db3ee8d4b1ae78f2c1232afd --params userid=3 core_enrol_get_users_courses

webservice-install
------------------

Install a Moodle Webservice

Requires service name and capabilities
* service name is the services["shortname'] defined in db/services.php file of a plugin
* Capabilities are separated by commas

Options:
* -m, --mail : mail, by default noreply user mail
* -u, --username : username
  * by default 'user_<servicename> will be used
  * if not exists user is created
* -r, --rolename : role shortname
  * by default role_<servicename> will used
  * if not exists role will be created
* i, --iprestriction : ip restriction
  * empty by default so all ip authorized
-v, --validuntil : valid until
  * empty so valid forever

Output:
* user user_servicename is created
* role role_servicename are created with definied capabilities
* user has role role_servicename on system
* webservice is instanciate with its function for user user_servicename
* token is genrated and output

Example 1 : Install a service named wsservicename with capabilities 

   moosh webservice-install wsservicename 'plugintype/pluginname:capability1,plugintype/pluginname:capability2'

