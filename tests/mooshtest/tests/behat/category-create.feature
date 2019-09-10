@moosh
Feature: category-create


  Scenario: category-create run with test_category
    When I run moosh "category-create test_category"
    Then a record in table "course_categories" with "name" = "test_category" and "visible" = "yes" exist

  Scenario: category-create run with -v 0 -d "My test category" test_category1
    When I run moosh "category-create -v 0 -d "My test category" test_category1"
    Then a record in table "course_categories" with "name" = "test_category1" and "visible" = "no" exist

  Scenario: category-create run with -p 1 -v 0 -d "My test category" test_category3
    When I run moosh "category-create -p 1 -v 0 -d "Description" test_category3"
    Then a record in table "course_categories" with "name" = "test_category3" and "visible" = "no" exist

  Scenario: category-create run with -p 1 test_category4
    When I run moosh "category-create -p 1 test_category4"
    Then a record in table "course_categories" with "name" = "test_category4" and "parent" = "1" exist
