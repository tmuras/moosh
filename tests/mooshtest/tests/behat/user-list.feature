@moosh
Feature: moosh user-list

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

  Scenario: user-list run with --course %course:C1% parameter returns user enroled to course C12.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher12 | Teacher | 12 | teacher12@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 12 | C12        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher12  | C12     | teacher |
    Then moosh command "user-list --course %shortname:C12%" contains "teacher12"

