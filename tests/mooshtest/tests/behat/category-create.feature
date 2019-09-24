@moosh
Feature: category-create


  Scenario: category-create run with test_category create a category with short name "test_category"
    When I run moosh "category-create test_category"
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    Then I should see "test_category"
    And a record in table "course_categories" with "name" = "test_category" and "visible" = "yes" exist

  Scenario: category-create run with -v 0 -d "My test category" test_category1 create a category with short
            name "test_category1", full name "My test category" and no visable
    When I run moosh "category-create -v 0 -d "My test category" test_category1"
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    Then I should see "test_category1"
    And a record in table "course_categories" with "name" = "test_category1" and "visible" = "no" exist


  Scenario: category-create run with -p 1 -v 0 -d "My test category" test_category3
    When I run moosh "category-create -p 1 -v 0 -d "Description" test_category3"
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    Then I should see "test_category3"
    And a record in table "course_categories" with "name" = "test_category3" and "visible" = "no" exist

  Scenario: category-create run with -p 1 test_category4
    When I run moosh "category-create -p 1 test_category4"
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    Then I should see "test_category4"
    And a record in table "course_categories" with "name" = "test_category4" and "parent" = "1" exist

  Scenario: category-create run with -i "first" test_category5
    When I run moosh "category-create -i "first" test_category5"
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    Then I should see "test_category5"
    And a record in table "course_categories" with "name" = "test_category5" and "idnumber" = "first" exist

  Scenario: category-create run with -r
    Given the following "categories" exist:
      | name | category | idnumber |
      | test_category6 | 0 | CAT6 |
    When I run moosh "category-create -r test_category6"
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Course categories and courses" management page
    Then I should see "test_category6"
