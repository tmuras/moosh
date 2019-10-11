 @moosh
  Feature: With moosh course-list command we list existing courses
    Test the command with given options:
    -h, --help            - help information
    -n, --idnumber        - show idnumber
    -i, --id              - display id only column
    -c, --categorysearch= - courses from given category id only
    -v, --visible=        - show all/yes/no visible
    -e, --empty=          - show only empty courses: all/yes/no
    -f, --fields=         - show only those fields in the output (comma separated)
    -o, --output=         - output format: tab, csv


  Scenario: course-list run with no parameters returns a list of courses.
    Given the following "courses" exist:
      | fullname  | shortname | idnumber  | format  | category | visible |
      | Course 1  | C1        | C1i       | topics  |    0     |    1    |
      | Course 2  | C2        | C2i       | topics  |    0     |    1    |
      | Course 3  | C3        | C3i       | topics  |    0     |    1    |
      | Course 4  | C4        | C4i       | topics  |    0     |    1    |
    Then moosh command "course-list" contains "C1"


  Scenario: course-list run with -n option returns a list of courses wit idnumber column.
    Given the following "courses" exist:
      | fullname  | shortname | idnumber  | format  | category | visible |
      | Course 1  | C1        | C1i       | topics  |    0     |    1    |
      | Course 2  | C2        | C2i       | topics  |    0     |    1    |
      | Course 3  | C3        | C3i       | topics  |    0     |    1    |
      | Course 4  | C4        | C4i       | topics  |    0     |    1    |
    Then moosh command "course-list -n" contains "C1i"

  Scenario: course-list run with -i option shows only id column.
    Given the following "courses" exist:
      | fullname  | shortname | idnumber  | format  | category | visible |
      | Course 1  | C1        | C1i       | topics  |    0     |    1    |
      | Course 2  | C2        | C2i       | topics  |    0     |    1    |
      | Course 3  | C3        | C3i       | topics  |    0     |    1    |
      | Course 4  | C4        | C4i       | topics  |    0     |    1    |
    Then moosh command "course-list -i" contains "%course.shortname:C1%"
    And moosh command "course-list -i" does not contain "C1"

  Scenario: course-list run with -c option show curse list from given category.
    Given the following "categories" exist:
      | name | idnumber | category |
      | A1   | a1       | 0        |
      | A2   | a2       | 0        |
    And the following "courses" exist:
      | fullname  | shortname | idnumber  | format  | category | visible |
      | Course 1  | C1        | C1i       | topics  |    a1    |    1    |
      | Course 2  | C2        | C2i       | topics  |    a2    |    1    |
      | Course 3  | C3        | C3i       | topics  |    a1    |    1    |
      | Course 4  | C4        | C4i       | topics  |    a2    |    1    |
    Then moosh command "course-list -c %course_categories.idnumber:a2%" contains "C4"
    And moosh command "course-list -c %course_categories.idnumber:a2%" does not contain "C3"

  Scenario: course-list run with -c and -v options shows visible courses from given category.
    Given the following "categories" exist:
      | name | idnumber | category |
      | A1   | a1       | 0        |
      | A2   | a2       | 0        |
    And the following "courses" exist:
      | fullname  | shortname | idnumber  | format  | category | visible |
      | Course 1  | C1        | C1i       | topics  |    a1    |    1    |
      | Course 2  | C2        | C2i       | topics  |    a2    |    1    |
      | Course 3  | C3        | C3i       | topics  |    a1    |    0    |
      | Course 4  | C4        | C4i       | topics  |    a2    |    1    |
    Then moosh command "course-list -c %course_categories.idnumber:a1% -v yes" contains "C1"
    And moosh command "course-list -c %course_categories.idnumber:a1% -v yes" does not contain "C3"

  Scenario: course-list run with -v optiom shows invisible courses list.
    Given the following "courses" exist:
      | fullname  | shortname | idnumber  | format  | category | visible |
      | Course 1  | C1        | C1i       | topics  |    0     |    1    |
      | Course 2  | C2        | C2i       | topics  |    0     |    1    |
      | Course 3  | C3        | C3i       | topics  |    0     |    0    |
      | Course 4  | C4        | C4i       | topics  |    0     |    0    |
    Then moosh command "course-list -v no" contains "C3"
    And moosh command "course-list -v no" does not contain "C1"

  Scenario: course-list run with -e optiom returns a list of empty courses.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -e yes" contains "C1"

  Scenario: course-list run with -o option returns a list of courses formatted to table.
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
      | Course 2 | C2 | 0 | social | 1 |
      | Course 3 | C3 | 0 | social | 0 |
      | Course 4 | C4 | 0 | social | 1 |
    Then moosh command "course-list -o tab" contains "C1"

  Scenario: course-list run with -f option shows courses id and fullname.
    Given the following "courses" exist:
      | fullname  | shortname | idnumber  | format  | category | visible |
      | Course 1  | C1        | C1i       | topics  |    0     |    1    |
      | Course 2  | C2        | C2i       | topics  |    0     |    1    |
      | Course 3  | C3        | C3i       | topics  |    0     |    1    |
      | Course 4  | C4        | C4i       | topics  |    0     |    1    |
    Then moosh command "course-list -f id,fullname" contains "Course 3"

    Scenario: course-list run with -f option show courses category.
      Given the following "categories" exist:
        | name | idnumber | category |
        | A1   | a1       | 0        |
        | A2   | a2       | 0        |
      And the following "courses" exist:
        | fullname  | shortname | idnumber  | format  | category | visible |
        | Course 1  | C1        | C1i       | topics  |    a1    |    1    |
        | Course 2  | C2        | C2i       | topics  |    a2    |    1    |
        | Course 3  | C3        | C3i       | topics  |    a1    |    1    |
        | Course 4  | C4        | C4i       | topics  |    a2    |    1    |
      Then moosh command "course-list -f category" contains "a1"
