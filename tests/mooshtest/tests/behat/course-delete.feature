@moosh
Feature:The command course-delete
  Test the command with given options:
  OPTIONS:
  -h, --help  - help information
  ARGUMENTS:
  id ...

  Scenario: course-delete run with the course id - which you want to delete
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Test course | TC1 | 0 | social | 1 |
      | Test course 2 | TC2 | 0 | social | 1 |
    When I run moosh "course-delete %course.shortname:TC1%"
    Then moosh command "course-list" does not contain "TC1"
    And moosh command "course-list" contains "TC2"

  Scenario: course-delete run with the course id - which you want to delete
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Test course 3 | TC3 | 0 | social | 1 |
      | Test course 4 | TC4 | 0 | social | 1 |
      | Test course 5 | TC5 | 0 | social | 1 |
      | Test course 6 | TC6 | 0 | social | 1 |
    When I run moosh "course-delete %course.shortname:TC4% %course.shortname:TC5%"
    Then moosh command "course-list" does not contain "TC4"
    And moosh command "course-list" contains "TC6"
    