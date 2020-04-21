---
title: faq
layout: default
---

FAQ
========

<span class="anchor" id="bulf-restore"></span>
Course bulk restore with moosh.
------------------------

Moosh does not have a command to restore more than one course at the time.

Instead, you can create a simple bash script with each execution done separately, e.g.:

    #!/bin/bash
    moosh course-restore -e backup1.mbz 3
    moosh course-restore -e backup2.mbz 3
    ....
    moosh course-restore -e backup800.mbz 3

This is much easier approach: 

 * You don't need to worry too much about any memory leaks - after each restore a new PHP process is started.
 * If one restore dies, it does not affect the start of the following ones.

I also put "date" command between each restore to have an idea about time each one took, e.g.:

    #!/bin/bash
    date
    echo restoring backup1.mbz
    moosh course-restore -e backup1.mbz 3
    ....
    date
    echo restoring backup800.mbz
    moosh course-restore -e backup800.mbz 3

<span class="anchor" id="windows"></span>
Does it run on Windows?
------------------------
To quote <a href="https://moodle.org/mod/forum/discuss.php?d=257341#p1131033">Marcus</a>:

...installed Moosh under Win32 and it seems to work OK. I have cleared cache, added a user and generated a form. It will fall over on unixy things like your example of

    moosh user-create user_{1..5}

but

    moosh user-create_user_1

works fine.

At the same time <a href="https://github.com/tmuras/moosh/issues/40">several commands will not work</a>.

At the moment I do not have enough time to support moosh on Windows - but patches that fix Windows-specific issues are welcome.

Also see <a href="https://moodle.org/mod/forum/discuss.php?d=257341#p1131493">installation instructions</a> from Michael
and if you really need to run it on Windows, I suggest Cygwin.


<span class="anchor" id="permissions"></span>
As which user should moosh be run? What's the story with permission's check?
------------------------
The permissions are very important and incorrect use of them may break your Moodle installation.

Imagine that you have Moodle running by apache server, as user www-data. Now, you run moosh from commandline
as user root. Since most of the moosh commands use Moodle API and include Moodle libraries, there is
 a good chance that executing them will create new files in your data directory (e.g. in moodledata/cache).

If that happens, you end up with moodledata files that can not be deleted by your Moodle installation (www-data user). 
This may lead to very unexpected behaviour - trust me, I learned it the hard way.
 
 To help you not to break your Moodle, there is a check at the beginning that may give you a message:
  
    One of your Moodle data directories ({$moodledata_owner['dir']}) is owned by
    different user ({$moodledata_owner['user']['name']}) than the one that runs the script ({$shell_user['name']}).
    If you're sure you know what you're doing, run moosh with -n flag to skip that test.
    
If you want to ignore that warning, just run moosh with -n flag:
      
      moosh -n ...
      
But in most of the cases, you should run moosh as the same user as the owner of your web server.
For example:

    sudo -u www-data moosh ...
     