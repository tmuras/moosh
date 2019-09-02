@moosh
Feature:course-delate

  Scenario: course-delete run with the course id - which you want to delete
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Course 1 | C1 | 0 | social | 1 |
    When I run moosh "course-delete %shortname:C1%"
    Then moosh command "course-list" does not contain "C1"
    