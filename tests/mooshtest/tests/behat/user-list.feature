@moosh
Feature: The command user-list list existing users
  Test the command with given options:
  -h, --help        - help information
  -n, --limit=      - display max n users
  -i, --id          - display id only column
  -s, --sort=       - sort by (username, email or idnumber)
  -d, --descending  - sort in descending order
  --course-inactive - limit to users who never accessed course provided with --course.
  --course-role=    - limit to users with given role in a --course.
  --course=         - select all enrolled in given course id


  Scenario: user-list run with no parameters returns a list of users.
    Given the following "users" exist:
    | username | firstname | lastname | email |
    | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
    | fullname | shortname | category | format |
    | Course 1 | C1 | 0 | social |
    Then moosh command "user-list" contains "teacher1"


  Scenario: user-list run with --limit=3 shows 3 users only.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | teacher3 | Teacher | 3 | teacher3@example.com |
      | teacher4 | Teacher | 4 | teacher4@example.com |
    Then moosh command "user-list --limit=3" does not contain "teacher2"


  Scenario: user-list run with -i shows only id column.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | teacher3 | Teacher | 3 | teacher3@example.com |
      | teacher4 | Teacher | 4 | teacher4@example.com |
    Then moosh command "user-list -i" does not contain "admin"

  Scenario: user-list run with -s email and -n 3 shows users sorted by email and limited to 3.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | z@example.com |
      | teacher2 | Teacher | 2 | x@example.com |
      | teacher3 | Teacher | 3 | b@example.com |
      | teacher4 | Teacher | 4 | a@example.com |
    Then moosh command "user-list -s email -n 3" contains "a@example.com"
    And moosh command "user-list -s email -n 3" does not contain "z@example.com"

  Scenario: user-list run with -s email and -n 3 shows users sorted by email sort in descending order and limited to 3.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | z@example.com |
      | teacher2 | Teacher | 2 | x@example.com |
      | teacher3 | Teacher | 3 | b@example.com |
      | teacher4 | Teacher | 4 | a@example.com |
    Then moosh command "user-list -d -s email -n 3" does not contain "a@example.com"
    And moosh command "user-list -d -s email -n 3" contains "z@example.com"

  Scenario: user-list run with --course %course:C1% parameter returns user enroled to course C1.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | teacher |
      | student1 | C1 | student |
    Then moosh command "user-list --course %course.shortname:C1%" contains "teacher1"

  Scenario: user-list run with --course=%course.shortname:C1% parameter returns select all enrolled to course C1.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
      | student2 | Student | Second | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | teacher |
      | student1 | C1 | student |
    Then moosh command "user-list --course=%course.shortname:C1%" contains "teacher1"
    And moosh command "user-list --course=%course.shortname:C1%" does not contain "student2"

  Scenario: user-list run with --course-role=teacher parameter returns all teacher enroled to course C1.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
      | student2 | Student | Second | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | teacher |
      | student1 | C1 | student |
    Then moosh command "user-list --course=%course.shortname:C1% --course-role=teacher" contains "teacher1"
    And moosh command "user-list --course=%course.shortname:C1% --course-role=teacher" does not contain "student1"

  Scenario: user-list run with --course-inactive parameter returns user enroled who never accessed to course C1.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
      | student2 | Student | Second | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | teacher |
      | student1 | C1 | student |
    And I log in as "student1"
    And I am on "Test" course homepage
    And I log out
    Then moosh command "user-list --course=%course.shortname:C1% --course-inactive" contains "teacher1"
    And moosh command "user-list --course=%course.shortname:C1% --course-inactive" does not contain "student1"