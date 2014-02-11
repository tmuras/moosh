---
title: tutorials
layout: default
---

Tutorials
========
Moosh is a tool that exposes some common Moodle functionality to the commandline.

You install moosh outside of the Moodle directory, it can be anywhere really. Then simply make moosh.php (or symlink named like moosh) available somewhere in $PATH.

To run moosh, change the directory into your installed Moodle's instance and run one of the commands. To get the list of all available commands only run moosh.php (or just moosh if you've symlinked it like this) or read the documentation and examples and watch

<iframe width="560" height="315" src="//www.youtube.com/embed/pIaH3MDIZhU" frameborder="0" allowfullscreen></iframe>

or

<iframe width="560" height="315" src="//www.youtube.com/embed/dXAFQOgoHfA" frameborder="0" allowfullscreen></iframe>

Moosh is meant to work nicely with Linux features like bash/zsh expansion. For example, to create 10 users, you just type:

% moosh user-create username{1..10}