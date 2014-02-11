---
title: Hello World!
layout: default
---
# Hello World

Moosh stands for MOOdle SHell. It is a commandline tool that will allow you to perform most common Moodle tasks. It's inspired by Drush - a similar tool for Drupal.
moosh is licenced under GNU GPL v3 or any later.

I've created it when I realized how much time I waste each time I debug/test some Moodle issue and need to setup my environment.
Here is for example how you can create 5 Moodle user accounts with moosh:

    cd /moodle/root/installation
    moosh user-create user_{1..5}

If you don't know the exact name of the command you want to run but know the part of it, run moosh with the substring:

    moosh user

As a result you will get a list of all commands that contain string "user":

    course-enrolleduser
    user-create
    user-delete
    user-getidbyname
    user-list
    user-mod

Contributing to moosh
=====================

1. Fork the project on github.
2. Follow "installation from Moodle git" section.
3. Look at existing plugins to see how they are done.
4. Create new plugin/update existing one. You can use moosh itself to generate a new command from a template for you:

    moosh generate-moosh category-command

5. Update this README.md file with the example on how to use your plugin.
6. Send me pull request.