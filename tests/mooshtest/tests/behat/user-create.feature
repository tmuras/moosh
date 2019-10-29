@moosh
Feature: The command moosh user-create create a new Moodle user provide one or more
          arguments to create one or more users.
          Test the command with given options:
          OPTIONS:
          -h, --help           -   help information
          -a, --auth=          -   authentication plugin, e.g. ldap
          -p, --password=      -   password (NONE for a blank password)
          -e, --email=         -   email address
          -c, --city=          -   city
          -C, --country=       -   country
          -f, --firstname=     -   first name
          -l, --lastname=      -   last name
          -i, --idnumber=      -   idnumber
          -d, --digest=        -   mail digest type as int (0=No digest, 1=Complete, 2=Subjects)
          ARGUMENTS:
          username ...



  Scenario: user-create run with -e some@gmail.com creates a user with the e-mail address provided.
    When I run moosh "user-create -e some@gmail.com someuser"
    Then a record in table "user" with "username" = "someuser" and "email" = "some@gmail.com" exist

  Scenario: user-create run with -c London creates a user with the city provided.
    When I run moosh "user-create -c London someuser1"
    Then a record in table "user" with "username" = "someuser1" and "city" = "London" exist

  Scenario: user-create run with -C DE creates a user with the country provided.
    When I run moosh "user-create -C DE someuser2"
    Then a record in table "user" with "username" = "someuser2" and "country" = "DE" exist

  Scenario: user-create run with -f Katarzyna creates a user with the first name provided.
    When I run moosh "user-create -f Katarzyna someuser3"
    Then a record in table "user" with "username" = "someuser3" and "firstname" = "Katarzyna" exist

  Scenario: user-create run with -l Loop creates a user with the last name provided.
    When I run moosh "user-create -l Loop someuser4"
    Then a record in table "user" with "username" = "someuser4" and "lastname" = "Loop" exist

  Scenario: user-create run with -i 123 creates a user with the idnumber provided.
    When I run moosh "user-create -i 123 someuser5"
    Then a record in table "user" with "username" = "someuser5" and "idnumber" = "123" exist

  Scenario: user-create run with -d 2 creates a user with the mail digest provided.
    When I run moosh "user-create -d 2 someuser6"
    Then a record in table "user" with "username" = "someuser6" and "maildigest" = "2" exist

  Scenario: user-create run with -p pass -e me8@example.com -d 2 -c Szczecin -c PL
            -f "first name" -l name creates a user with the name, e-mail address,
            city, country, mail digest and password provided.
    When I run moosh "user-create -p pass -e me8@example.com -d 2 -c Szczecin -c PL -f "first name" -l name someuser8"
    Then a record in table "user" with "username" = "someuser8" and "country" = "PL" exist
    And a record in table "user" with "email" = "me8@example.com" and "username" = "someuser8" exist
    And a record in table "user" with "firstname" = "first name" and "lastname" = "name" exist

  Scenario: user-create run with -p pass -e me9@example.com -d 2 -c Szczecin -c PL -f First
            -l Last creates a user with the name, e-mail address, city, country, mail digest
            and password provided.
    Given I am on site homepage
    When I run moosh "user-create -p pass -e me9@example.com -d 2 -c Szczecin -c PL -f First -l Last someuser9"
    And I follow "Log in"
    And I set the field "Username" to "someuser9"
    And I set the field "Password" to "pass"
    And I press "Log in"
    Then I should see "You are logged in as First Last"

  Scenario: user-create run with someuser10 someuser11 someuser12 creates a multiple users.
    Given I log in as "admin"
    When I run moosh "user-create someuser10 someuser11 someuser12"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "someuser10"
    And I should see "someuser11"
    And I should see "someuser12"
