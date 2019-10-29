@moosh
Feature: The command category-list
  Test the command with given options:
  OPTIONS:
  -h, --help        -   help information
  ARGUMENTS:
  search ...



  Scenario: category-list run with no parameters returns a list of categories.
    Given the following "categories" exist:
      | name            | idnumber      | category  |
      | Year            | year          |           |
      | Faculty A       | faculty-a     | year      |
      | Faculty B       | faculty-b     | year      |
      | Department A1   | department-a1 | faculty-a |
      | Department A2   | department-a2 | faculty-a |
      | Department B1   | department-b1 | faculty-b |
      | Department B2   | department-b2 | faculty-b |
    And the following "courses" exist:
      | fullname    | shortname | idnumber     | format        | category          |
      | Course A1i  | A1i       | A1i          | topics        | department-a1     |
      | Course A2i  | A2i       | A2i          | topics        | department-a2     |
      | Course B1i  | B1i       | B1i          | topics        | department-b1     |
      | Course B2i  | B2i       | B2i          | topics        | department-b2     |
    Then moosh command "category-list" contains "department-a1"

  Scenario: category-list run with Faculty returns a List all categories with name Faculty.
    Given the following "categories" exist:
      | name            | idnumber      | category  |
      | Year            | year          |           |
      | Faculty A       | faculty-a     | year      |
      | Faculty B       | faculty-b     | year      |
      | Department A1   | department-a1 | faculty-a |
      | Department A2   | department-a2 | faculty-a |
      | Department B1   | department-b1 | faculty-b |
      | Department B2   | department-b2 | faculty-b |
    And the following "courses" exist:
      | fullname    | shortname | idnumber     | format        | category          |
      | Course A1i  | A1i       | A1i          | topics        | department-a1     |
      | Course A2i  | A2i       | A2i          | topics        | department-a2     |
      | Course B1i  | B1i       | B1i          | topics        | department-b1     |
      | Course B2i  | B2i       | B2i          | topics        | department-b2     |
    Then moosh command "category-list Faculty" contains "Faculty A"

  Scenario: category-list run with Faculty Department returns a List all categories with name Faculty and Department.
    Given the following "categories" exist:
      | name            | idnumber      | category  |
      | Year            | year          |           |
      | Faculty A       | faculty-a     | year      |
      | Faculty B       | faculty-b     | year      |
      | Department A1   | department-a1 | faculty-a |
      | Department A2   | department-a2 | faculty-a |
      | Department B1   | department-b1 | faculty-b |
      | Department B2   | department-b2 | faculty-b |
    And the following "courses" exist:
      | fullname    | shortname | idnumber     | format        | category          |
      | Course A1i  | A1i       | A1i          | topics        | department-a1     |
      | Course A2i  | A2i       | A2i          | topics        | department-a2     |
      | Course B1i  | B1i       | B1i          | topics        | department-b1     |
      | Course B2i  | B2i       | B2i          | topics        | department-b2     |
    Then moosh command "category-list Faculty Department" contains "Department"