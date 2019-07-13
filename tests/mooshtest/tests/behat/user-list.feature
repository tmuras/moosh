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