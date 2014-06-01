---
title: faq
layout: default
---

FAQ
========


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
