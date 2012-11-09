Introduction
============

Moosh stands for MOOdle SHell. It is a commandline tool that will allow you to perform most common Moodle tasks. It's inspired by Drush - a similar tool for Drupal.

I've created it when I realized how much time I waste each time I debug/test some Moodle issue and need to setup my environment.
Here is for example how you can create 5 Moodle users.

    cd /moodle/root/installation
    moosh user-create user_{1..5}


Installation
============

Clone moosh github repository:
    git clone git@github.com:tmuras/moosh.git

Link to a location that is set in your $PATH, eg:
    ln -s $PWD/moosh/moosh.php ~/bin/moosh

Or system-wide:
    sudo ln -s $PWD/moosh/moosh.php /usr/local/bin/moosh


Commands
========

user-create
-----------

Create a new Moodle user.

Example 1: create user "testuser" with the all default profile fields.
    moosh user-create testuser

Example 2: create user "testuser" with the all the optional values
    moosh user-create --password=pass --email=me@example.com --city Szczecin --country=Poland --firstname "first name" --lastname=name testuser

Example 3: use bash/zsh expansion to create 10 users
    moosh user-create testuser{1..10}
The users will have unique email addresses based on the user name.