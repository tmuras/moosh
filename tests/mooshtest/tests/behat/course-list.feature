@moosh
Feature: moosh course-list

  Scenario: course-list run with no parameters returns a list of courses.
    Given the following "courses" exist:
    | fullname | shortname | category | format |
    | Course 1 | C1 | 0 | social |
    Then moosh command "course-list" contains "C1"

  Scenario: course-list run with -n returns a list of courses.
    Given the following "courses" exist:
    | fullname | shortname | category | format |
    | Course 1 | C1 | 0 | social |
    Then moosh command "course-list -n" contains "C1"

  Scenario: course-list run with -i shows only id column.
    Given the following "courses" exist:
    | fullname | shortname | category | format | visible |
    | Course 1 | C1 | 0 | social | 1 |
    | Course 2 | C2 | 0 | social | 1 |
    | Course 3 | C3 | 0 | social | 0 |
    | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -i" contains "%course:C1%"

  Scenario: course-list run with -c 0 show courses id from given category.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
      Then moosh command "course-list -c 0" contains "%course:C2%"

  Scenario: course-list run with -c 0 and -v yes shows visable courses from given category.
    Given the following "courses" exist:
    | fullname | shortname | category | format | visable |
    | Course 1 | C1 | 0 | social | 1 |
    | Course 2 | C2 | 0 | social | 1 |
    | Course 3 | C3 | 0 | social | 0 |
    | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -c 0 -v yes" contains "C1"

  Scenario: course-list run with -v yes shows visable courses.
    Given the following "courses" exist:
    | fullname | shortname | category | format | visible |
    | Course 1 | C1 | 0 | social | 1 |
    | Course 2 | C2 | 0 | social | 1 |
    | Course 3 | C3 | 0 | social | 0 |
    | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -v yes" contains "C1"

  Scenario: course-list run with -v yes returns a list of courses from given category.
    Given the following "courses" exist:
    | fullname | shortname | category | format |
    | Course 1 | C1 | 0 | social |
    Then moosh command "course-list -f category" contains "0"

  Scenario: course-list run with -e yes returns a list of empty courses.
    Given the following "courses" exist:
    | fullname | shortname | category | format |
    | Course 1 | C1 | 0 | social |
    Then moosh command "course-list -e yes" contains "0"

  Scenario: course-list run with -o tab returns a list of courses formatted to table.
    Given the following "courses" exist:
    | fullname | shortname | category | format |
    | Course 1 | C1 | 0 | social |
    Then moosh command "course-list -o tab" contains "C1"

  Scenario: course-list run with -f id shows courses fullname and shotname.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -f fullname,shortname" contains "%course:C3%"






