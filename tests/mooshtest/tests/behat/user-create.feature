@moosh
Feature: moosh user-create

  Scenario:user is created with pass -e some@gmail.com and username student1
    When I run moosh "user-create -e some@gmail.com someuser"
    Then a record in table "user" with "username" = "someuser" and "email" = "some@gmail.com" exist

  Scenario:user is created with pass -c London and username student1
    When I run moosh "user-create -c London someuser1"
    Then a record in table "user" with "username" = "someuser1" and "city" = "London" exist

  Scenario:user is created with pass -C DE and username student2
    When I run moosh "user-create -C DE someuser2"
    Then a record in table "user" with "username" = "someuser2" and "country" = "DE" exist

  Scenario:user is created with pass -f Katarzyna and username student3
    When I run moosh "user-create -f Katarzyna someuser3"
    Then a record in table "user" with "username" = "someuser3" and "firstname" = "Katarzyna" exist

  Scenario:user is created with pass -l Loop and username student4
    When I run moosh "user-create -l Loop someuser4"
    Then a record in table "user" with "username" = "someuser4" and "lastname" = "Loop" exist

  Scenario:user is created with pass -i 123 and username student5
    When I run moosh "user-create -i 123 someuser5"
    Then a record in table "user" with "username" = "someuser5" and "idnumber" = "123" exist

  Scenario:user is created with pass -d 2 and username student6
    When I run moosh "user-create -d 2 someuser6"
    Then a record in table "user" with "username" = "someuser6" and "maildigest" = "2" exist

  Scenario:user is created with pass -a ldap and username student7
    When I run moosh "user-create -a ldap someuser7"
    Then a record in table "user" with "username" = "someuser7" and "auth" = "ldap" exist

  Scenario:user is created with pass -a ldap and username student8
    When I run moosh "user-create -p pass -e me@example.com -d 2 -c Szczecin -c PL -f "first name" -l name someuser8"
    Then a record in table "user" with "username" = "someuser8" and "country" = "PL" exist