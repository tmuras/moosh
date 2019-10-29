@moosh
Feature: The command course-config-set, with this command you can chane for example a shornam for a given course
  Test the command with given options:
  OPTIONS:
  -h, --help  - help information
  ARGUMENTS:
  course courseid setting value
  Or...
  category categoryid[all] setting value


  Scenario: course-config-set run with course %shortname:CR1% shortname course_rename,
            the shorname will change from CR1 to course_rename
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course raname | CR1 | 0 | social |
    When I run moosh "course-config-set course %course.shortname:CR1% shortname course_rename"
    Then a record in table "course" with "shortname" = "course_rename" and "format" = "social" exist



  Scenario: course-config-set run with course %shortname:CR2% fullname 'course will be renamed',
  the shorname will change from CR1 to course_rename
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course raname | CR2 | 0 | social |
    When I run moosh "course-config-set course %course.shortname:CR2% fullname 'renamed'"
    Then a record in table "course" with "shortname" = "CR2" and "fullname" = "renamed" exist


  Scenario: course-config-set run with course %shortname:CR3% format weeks,
  the shorname will change from CR1 to course_rename
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course raname | CR3 | 0 | social |
    When I run moosh "course-config-set course %course.shortname:CR3% format weeks"
    Then a record in table "course" with "shortname" = "CR3" and "format" = "weeks" exist

  Scenario: course-config-set run with course %shortname:CR4% category 1,
  the shorname will change from CR1 to course_rename
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course raname | CR4 | 0 | social |
    When I run moosh "course-config-set course %course.shortname:CR4% category 1"
    Then a record in table "course" with "shortname" = "CR4" and "category" = "1" exist

  Scenario: course-config-set run with course %shortname:CR5% ,
  the shorname will change from CR1 to course_rename
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course raname | CR4 | 0 | social |
    When I run moosh "course-config-set course %course.shortname:CR4% category 1"
    Then a record in table "course" with "shortname" = "CR4" and "category" = "1" exist