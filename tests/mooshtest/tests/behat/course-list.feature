 @moosh
  Feature: moosh course-list

  Scenario: course-list run with no parameters returns a list of courses.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list" contains "C1"
    And moosh command "course-list" contains "C3"

  Scenario: course-list run with -n returns a list of courses wit idnumber column.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -n" contains "C1"
    And moosh command "course-list" contains "C3"

  Scenario: course-list run with -i shows only id column.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -i" contains "%shortname:C1%"

  Scenario: course-list run with -c 0 show curse list from given category.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -c %course.category:0%" contains "C4"

  Scenario: course-list run with -c 0 and -v yes shows visible courses from given category.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -c 0 -v yes" contains "C1"
    Then moosh command "course-list -c 0 -v yes" does not contain "C3"

  Scenario: course-list run with -v yes shows visible courses.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -v yes" contains "C1"
    Then moosh command "course-list -c 0 -v yes" does not contain "C3"

  Scenario: course-list run with -e yes returns a list of empty courses.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -e yes" contains "C1"

  Scenario: course-list run with -o tab returns a list of courses formatted to table.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -o tab" contains "C1"

  Scenario: course-list run with -f id,fullname shows courses id and fullname.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -f id,fullname" contains "Course 3"

    Scenario: course-list run with -f show only given fields in the output
    category returns a list of courses from given category.
      Given the following "courses" exist:
        | fullname | shortname | category | format | visible |
        | Course 1 | C1 | 0 | social | 1 |
        | Course 2 | C2 | 0 | social | 1 |
        | Course 3 | C3 | 0 | social | 0 |
        | Course 4 | C4 | 0 | social | 1 |
      Then moosh command "course-list -f category" contains "0"
