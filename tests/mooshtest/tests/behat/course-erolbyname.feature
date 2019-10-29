@moosh
Feature: The command course-enrolbyname enrol existing user to existing course.
  Test the command with given options:
  OPTIONS:
  -h, --help              help information
  -i, --id=               use this user id instead of user name
  -r, --role=             role short name
  -f, --firstname=        users firstname
  -l, --lastname=         users lastname
  -c, --cshortname=       course shortname
  -S, --startdate=        any date php strtotime can parse
  -E, --enddate=          any date php strtotime can parse, or duration in # of days
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
    When I run moosh "course-enrolbyname -f "Student" -l 1 -c C1"
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    Then I should see "Student 1" in the "participants" "table"
    And I log out

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
    When I run moosh "course-enrolbyname -f "Student" -l 1 %course.shortname:C1%"
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    Then I should see "Student 1" in the "participants" "table"
    And I log out

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
    When I run moosh "course-enrolbyname -l 1 -f "Student" %course.shortname:C1%"
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    Then I should see "Student 1" in the "participants" "table"
    And I log out

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
    When I run moosh "course-enrolbyname -r "student" -l 1 -f "Student" %course.shortname:C1%"
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    Then I should see "Student 1" in the "participants" "table"
    And  I should see "Role: Student"

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
    When I run moosh "course-enrolbyname -c C1 student1"
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    Then I should see "Student 1" in the "participants" "table"