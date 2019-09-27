@moosh
Feature: moosh category-list

  Scenario: category-list run with no parameters returns a list of categories.
    Given the following "categories" exist:
      | name          | idnumber   | category  |
      | Year          | year       |     0     |
      | Faculty       | faculty    |     0     |
    When I run moosh "category-delete %name:Year%"
    Then moosh command "category-list" does not contain "Year"
    And moosh command "category-list" contains "Faculty"