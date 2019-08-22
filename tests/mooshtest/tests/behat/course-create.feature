@moosh
Feature: You can create a second course with -r option and the same parameters
  the second course should not be created
  and a create command should return the ID of the first course created

  Scenario: two course-create run with -r and with the same value,
            checks if only one course has been added to the database
    When I run moosh "course-create -r -c 1 -i 100 -F weeks -f full test1"
    And I run moosh "course-create -r -c 1 -i 100 -F weeks -f full test1"
    Then there are "1" courses added to database


  Scenario: two course-create run with -r and with the different value,
            checks if two courses have been added to the database
    When I run moosh "course-create -r -c 1 -i 100 -F weeks -f full test1"
    And I run moosh "course-create -r -c 1 -i 101 -F weeks -f full test2"
    Then there are "2" courses added to database

  Scenario: two course-create run with -r and with the same value,
  second course was not created return command returns course
  id of the first created course
    Then moosh command "course-create -r -c 1 -i 100 -F weeks -f full test1" print out id "%shortname:test1%"
    And moosh command "course-create -r -c 1 -i 100 -F weeks -f full test1" print out id "%shortname:test1%"



  Scenario: two course-create run with -r and with the different value,
  first and second returns a different id
    Then moosh command "course-create -r -c 1 -i 100 -F weeks -f full test1" print out id "%shortname:test1%"
    And moosh command "course-create -r -c 1 -i 101 -F weeks -f full test2" print out id "%shortname:test2%"



