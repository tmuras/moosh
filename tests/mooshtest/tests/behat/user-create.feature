@moosh
Feature: Command moosh user-create create a new Moodle user provide one or more
          arguments to create one or more users.

  Scenario: Creating user with a given email address.
    When I run moosh "user-create -e some@gmail.com someuser"
    Then a record in table "user" with "username" = "someuser" and "email" = "some@gmail.com" exist

  Scenario: Creating user with a given city.
    When I run moosh "user-create -c London someuser1"
    Then a record in table "user" with "username" = "someuser1" and "city" = "London" exist

  Scenario: Creating user with a given country.
    When I run moosh "user-create -C DE someuser2"
    Then a record in table "user" with "username" = "someuser2" and "country" = "DE" exist

  Scenario: Creating user with a given first name.
    When I run moosh "user-create -f Katarzyna someuser3"
    Then a record in table "user" with "username" = "someuser3" and "firstname" = "Katarzyna" exist

  Scenario: Creating user with a given last name.
    When I run moosh "user-create -l Loop someuser4"
    Then a record in table "user" with "username" = "someuser4" and "lastname" = "Loop" exist

  Scenario: Creating user with a given idnumber.
    When I run moosh "user-create -i 123 someuser5"
    Then a record in table "user" with "username" = "someuser5" and "idnumber" = "123" exist

  Scenario: Creating user with a given mail digest.
    When I run moosh "user-create -d 2 someuser6"
    Then a record in table "user" with "username" = "someuser6" and "maildigest" = "2" exist

  Scenario: Creating user with a given authentication plugin.
    When I run moosh "user-create -a ldap someuser7"
    Then a record in table "user" with "username" = "someuser7" and "auth" = "ldap" exist

  Scenario: Creating user with a given first and last name, email, city, country, mail digest and pasword.
    When I run moosh "user-create -p pass -e me8@example.com -d 2 -c Szczecin -c PL -f "first name" -l name someuser8"
    Then a record in table "user" with "username" = "someuser8" and "country" = "PL" exist
    And a record in table "user" with "email" = "me8@example.com" and "username" = "someuser8" exist
    And a record in table "user" with "firstname" = "first name" and "lastname" = "name" exist

  Scenario: Creating user with a given password.
    Given I am on site homepage
    When I run moosh "user-create -p pass -e me9@example.com -d 2 -c Szczecin -c PL -f First -l Last someuser9"
    And I follow "Log in"
    And I set the field "Username" to "someuser9"
    And I set the field "Password" to "pass"
    And I press "Log in"
    Then I should see "You are logged in as First Last"

  Scenario: Creating multiple user.
    When I run moosh "user-create someuser10 someuser11 someuser12"
    Then moosh command "user-list" contains "someuser10"
    And moosh command "user-list" contains "someuser11"
    And moosh command "user-list" contains "someuser12"