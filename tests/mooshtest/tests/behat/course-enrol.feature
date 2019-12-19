@moosh
Feature: The command course-enrol enrol existing user to existing course.
  Test the command with given options:
  OPTIONS:
  -h, --help           -   help information
  -i, --id             -   use numeric IDs instead of user name(s)
  -s, --shortname      -   use course short name instead of course ID as first argument
  -r, --role=          -   role short name
  -S, --startdate=     -   any date php strtotime can parse
  -E, --enddate=        -  any date php strtotime can parse, or duration in # of days
  ARGUMENTS:
  courseid username ...


  Scenario: course-enrol run with the course id and user name,
              erol the user to the course
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    When I run moosh "course-enrol %course.shortname:C1% teacher1"
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    Then I should see "Teacher 1" in the "participants" "table"


  Scenario: course-enrol run with the course id and two use rnames,
              erol two user to the course
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    When I run moosh "course-enrol %course.shortname:C1% teacher1 student1"
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    Then I should see "Teacher 1" in the "participants" "table"
    And I should see "Student 1" in the "participants" "table"


  Scenario: course-enrol run with the course name and user name,
              erol the user to the course
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    When I run moosh "course-enrol -s C1 teacher1"
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    Then I should see "Teacher 1" in the "participants" "table"


  Scenario: course-enrol run with the course id, username and role as a teacher,
              erol the user to the course as a teacher.
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    When I run moosh "course-enrol -r teacher %course.shortname:C1% teacher1"
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Participants"
    And I follow "Teacher 1"
    Then I should see "Teacher"


  Scenario: course-enrol run with the course id, username and role as a teacher,
  erol the user to the course as a teacher.
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    When I run moosh "course-enrol -r teacher %course.shortname:C1% teacher1"
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    Then I should see "Teacher 1" in the "participants" "table"
    And I follow "Teacher 1"
    And  I should see "Role: Teacher"