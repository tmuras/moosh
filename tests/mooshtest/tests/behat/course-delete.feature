@moosh
Feature:course-delate

  Scenario: course-delete run with the course id - which you want to delete
    Given the following "courses" exist:
      | fullname | shortname | category | format | visible |
      | Test course | TC1 | 0 | social | 1 |
    When I run moosh "course-delete %shortname:TC1%"
    Then moosh command "course-list" does not contain "TC1"
    