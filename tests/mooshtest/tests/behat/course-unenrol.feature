@moosh
Feature: Command course-unenrol unenroll given user from a given course.


  Scenario: Command course-unenrol run with user and course id,
              unenroll user from given course
    Given the following "users" exist:
      | username  | firstname | lastname | email                 |
      | teacher1  | Teacher   | 1        | teacher1@example.com  |
      | student1  | Student   | 1        | student1@example.com  |
      | student2  | Student   | 2        | student2@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course | role           | status |
      | teacher1  | C1     | editingteacher |    0   |
      | student1  | C1     | student        |    0   |
      | student2  | C1     | student        |    1   |
    When I run moosh "course-unenrol %shortname:C1% %username:student1%"
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    Then I should not see "Student 1" in the "participants" "table"