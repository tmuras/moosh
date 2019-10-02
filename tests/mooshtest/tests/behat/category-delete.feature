@moosh
Feature: The moosh command category-delete deletes a given category.

  Scenario: category-delete run with id of category - deletes the category.
    Given the following "categories" exist:
      | name          | idnumber   | category  |
      | Year          | year       |     0     |
      | Faculty       | faculty    |     0     |
    When I run moosh "category-delete %course_categories.name:Year%"
    Then moosh command "category-list" does not contain "Year"
    And moosh command "category-list" contains "Faculty"