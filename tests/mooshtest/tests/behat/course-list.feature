@moosh
Feature: moosh course-list

  Scenario: course-list run with no parameters returns a list of courses.
    Given the following "courses" exist:
    | category | shortname | fullname | visible |
    | 0 | dog1 | dog training | 1 |
    Then moosh command "course-list" contains "dog1"
    

  Scenario: course-list run with --i shows only id column.
    Given the following "courses" exist:
    | category | shortname | fullname | visible |
    | 0 | dog1 | dog training | 1 |
    | 0 | dog2 | advanced dog training | 0 |
    | 1 | dog3 | training of the Maltese| 1 |
    | 1 | dog4 | training of Bernese | 0 |
    Then moosh command "course-list -i" contain "dog1"


  Scenario: course-list run with --c 0 show courses id from given category.
    Given the following "courses" exist:
    | category | shortname | fullname | visible |
    | 0 | dog1 | dog training | 1 |
    | 0 | dog2 | advanced dog training | 0 |
    | 1 | dog3 | training of the Maltese| 1 |
    | 1 | dog4 | training of Bernese | 0 |
    Then moosh command "course-list -c 1" does not contain "dog3"


  Scenario: course-list run with --c 0 and -v yes shows courses visable courses from given category.
    Given the following "courses" exist:
    | category | shortname | fullname | visible |
    | 0 | dog1 | dog training | 1 |
    | 0 | dog2 | advanced dog training | 0 |
    | 1 | dog3 | training of the Maltese| 1 |
    | 1 | dog4 | training of Bernese | 0 |
    Then moosh command "course-list -c 0 -v yes" contain "dog1"


