@moosh
Feature:  The command user-delete delete a given user.
  Test the command with given options:
  OPTIONS:
  -h, --help      - help information
  ARGUMENTS:
  username ...


  Scenario: user-delete run with the username teacher1 - which you want to delete
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | tea1 | Teacher | 1 | teacher1@example.com |
      | tea2 | Teacher | 2 | teacher2@example.com |
      | tea3 | Teacher | 3 | teacher3@example.com |
      | tea4 | Teacher | 4 | teacher4@example.com |
    When I run moosh "user-delete tea2"
    Then moosh command "user-list" does not contain "tea2"

  Scenario: user-delete run with the username teacher1 - which you want to delete
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | tea1 | Teacher | 1 | teacher1@example.com |
      | tea2 | Teacher | 2 | teacher2@example.com |
      | tea3 | Teacher | 3 | teacher3@example.com |
      | tea4 | Teacher | 4 | teacher4@example.com |
    When I run moosh "user-delete tea2 tea1"
    Then moosh command "user-list" does not contain "tea1"