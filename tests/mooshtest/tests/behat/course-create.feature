@moosh
Feature: The command course-create create a new course.
  You can create a second course with -r option and the same parameters
  the second course should not be created and a create command should return the ID of the first course created
  Test the command with given options:
  OPTIONS:
  -h, --help            - help information
  -c, --category=       -  category id
  -f, --fullname=       - full name
  -d, --description=    - description
  -F, --format=         - format (e.g. one of site, weeks, topics, etc.)
  -n, --numsections=    - number of sections (i.e. of weeks, topics, etc.)
  -i, --idnumber=       - id number
  -v, --visible=        - visible (y or n, by default created visible)
  -r, --reuse           - do not create new course if it a matching one already exists
  ARGUMENTS:
  shortname ...

  Scenario: two course-create run with -r and with the same value,
  checks if only one course has been added to the database
    When I run moosh "course-create -r -c 1 -i 1001 -F weeks -f full testr1"
    And I run moosh "course-create -r -c 1 -i 1001 -F weeks -f full testr1"
    When I log in as "admin"
    And I am on site homepage
    Then I should see "full"

  Scenario: two course-create run with -r and with the same value,
  second course was not created return command returns course
  id of the first created course
    When I run moosh "course-create -r tests1"
    And I run moosh "course-create -r tests1"
    Then moosh command "course-create -r tests1" print out id "%course.shortname:tests1%"
    And there are "1" "tests" record added to database

  Scenario: two course-create run with -r and with the different value,
  checks if two courses have been added to the database
    When I run moosh "course-create -r course1"
    And I run moosh "course-create -r course2"
    Then there are "2" "course" record added to database

  Scenario: two course-create run with -r and with the different value,
  first and second returns a different id
    When I run moosh "course-create -r -c 1 -i 101 -F weeks -f full test2"
    And I run moosh "course-create -r -c 1 -i 102 -F weeks -f full test3"
    Then moosh command "course-create -r -c 1 -i 101 -F weeks -f full test2" print out id "%course.shortname:test2%"
    And moosh command "course-create -r -c 1 -i 102 -F weeks -f full test3" print out id "%course.shortname:test3%"

  Scenario: course-create run with -f fulltest, created a course with fullname - fulltest
    When I run moosh "course-create -f fulltest4 test4"
    Then a record in table "course" with "shortname" = "test4" and "fullname" = "fulltest4" exist

  Scenario: course-create run with -F site, created a course with format - site
    When I run moosh "course-create -F site test5"
    Then a record in table "course" with "shortname" = "test5" and "format" = "site" exist

  Scenario: course-create run with -v no, created a course with visible - no
    When I run moosh "course-create -v no test6"
    Then a record in table "course" with "shortname" = "test6" and "visible" = "no" exist

  Scenario: course-create run with -i 123, created a course with idnumber - 123
    When I run moosh "course-create -i 123 test7"
    Then a record in table "course" with "shortname" = "test7" and "idnumber" = "123" exist

  Scenario: course-create run with -c 1, created a course with idnumber - "test 8 id
    When I run moosh "course-create -i 'test 8 id' test8"
    Then a record in table "course" with "shortname" = "test8" and "idnumber" = "test 8 id" exist

  Scenario: course-create run with -c 1, created a course with category id - 1
    When I run moosh "course-create -c 1 test9"
    Then a record in table "course" with "shortname" = "test9" and "category" = "1" exist
