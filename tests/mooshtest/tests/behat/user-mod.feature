@moosh
Feature: With moosh user-mod command we modifying existing users data
  Test the command with given options:
  -h, --help          - help information
  --all               - modify all users
  -i, --id            - use id to match a user
  -a, --auth=         - auth
  -p, --password=     - password
  -e, --email=        - email address
  -g, --global        - user(s) to be set as global admin.
  -n, --ignorepolicy  - ignore password policy.


  Scenario: user-mod run with .
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | teacher3 | Teacher | 3 | teacher3@example.com |
      | teacher4 | Teacher | 4 | teacher4@example.com |
    When I run moosh "user-mod -e teacherone@gmail.com teacher1"
    Then moosh command "user-list" contains "teacherone@gmail.com"

  Scenario: user-mod run with .
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | teacher3 | Teacher | 3 | teacher3@example.com |
      | teacher4 | Teacher | 4 | teacher4@example.com |
    When I run moosh "user-mod -e teacherone@gmail.com teacher1"
    Then moosh command "user-list" contains "teacherone@gmail.com"

