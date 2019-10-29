@moosh
Feature: The command course-backup
  Test the command with given options:
  OPTIONS:
  -h, --help        -   help information
  -f, --filename=   -   path to filename to save the course backup
  -p, --path=       -   path to save the course backup
  -F, --fullbackup  -   do full backup instead of general
  --template        -   do template backup instead of general
  ARGUMENTS:        - id


  Scenario: Command course-backup
  Given the following "users" exist:
  | username  | firstname | lastname | email                 |
  | teacher1  | Teacher   | 1        | teacher1@example.com  |
  | student1  | Student   | 1        | student1@example.com  |
  | student2  | Student   | 2        | student2@example.com  |
  And the following "courses" exist:
  | fullname | shortname | category | format |
  | Course 1 | C1x        | 0        | topics |
  And the following "course enrolments" exist:
  | user      | course | role           | status |
  | teacher1  | C1x    | editingteacher |    0   |
  | student1  | C1x     | student        |    0   |
  | student2  | C1x     | student        |    1   |
  And I run moosh "course-backup -f /mybackup.mbz %course.shortname:C1x%"
  When I run moosh "course-delete %course.shortname:C1x%"
  And moosh command "course-list" does not contain "C1x"

