<?php
$template = array (
  'CSV_DELIMITER' => 
  array (
    'count' => 8,
    'short' => 'CSV delimiter',
    'long' => '',
  ),
  'CSV_ENCODE' => 
  array (
    'count' => 4,
    'short' => 'CSV encode',
    'long' => '',
  ),
  'X' => 
  array (
    'count' => 1,
    'short' => 'X',
    'long' => '',
  ),
  'additionalhtmlfooter' => 
  array (
    'count' => 2,
    'short' => 'Before BODY is closed',
    'long' => 'Content here will be added in to every page right before the body tag is closed.',
  ),
  'additionalhtmlhead' => 
  array (
    'count' => 14,
    'short' => 'Within HEAD',
    'long' => 'Content here will be added to the bottom of the HEAD tag for every page.',
  ),
  'additionalhtmltopofbody' => 
  array (
    'count' => 2,
    'short' => 'When BODY is opened',
    'long' => 'Content here will be added in to every page immediately after the opening body tag.',
  ),
  'admin' => 
  array (
    'count' => 355,
    'short' => 'Directory location',
    'long' => 'A very few webhosts use /admin as a special URL for you to access a
 control panel or something.  Unfortunately this conflicts with the
 standard location for the Moodle admin pages.  You can work around this
 by renaming the admin directory in your installation, and putting that
 new name here.  eg "moodleadmin".  This should fix all admin links in Moodle.
 After any change you need to visit your new admin directory
 and purge all caches.',
  ),
  'admineditalways' => 
  array (
    'count' => 2,
    'short' => 'Admin edit always',
    'long' => 'Setting this to true will enable admins to edit any post at any time',
  ),
  'adminsassignrolesincourse' => 
  array (
    'count' => 1,
    'short' => 'Admins assign roles in course',
    'long' => '',
  ),
  'adminsetuppending' => 
  array (
    'count' => 4,
    'short' => 'Admin set up pending',
    'long' => '',
  ),
  'airnotifieraccesskey' => 
  array (
    'count' => 3,
    'short' => 'Airnotifier access key',
    'long' => 'The access key to use when connecting to the airnotifier server.',
  ),
  'airnotifierappname' => 
  array (
    'count' => 4,
    'short' => 'Airnotifier app name',
    'long' => 'The app name identifier in Airnotifier.',
  ),
  'airnotifiermobileappname' => 
  array (
    'count' => 3,
    'short' => 'Mobile app name',
    'long' => 'The Mobile app unique identifier (usually something like com.moodle.moodlemobile).',
  ),
  'airnotifierport' => 
  array (
    'count' => 4,
    'short' => 'Airnotifier port',
    'long' => 'The port to use when connecting to the airnotifier server.',
  ),
  'airnotifierurl' => 
  array (
    'count' => 5,
    'short' => 'Airnotifier URL',
    'long' => 'The server url to connect to to send push notifications.',
  ),
  'allcountrycodes' => 
  array (
    'count' => 2,
    'short' => 'All country codes',
    'long' => 'This is the list of countries that may be selected in various places, for example in a user\\\'s profile. If blank (the default) the list in countries.php in the standard English language pack is used. That is the list from ISO 3166-1. Otherwise, you can specify a comma-separated list of codes, for example \\\'GB,FR,ES\\\'. If you add new, non-standard codes here, you will need to add them to countries.php in \\\'en\\\' and your language pack.',
  ),
  'allowattachments' => 
  array (
    'count' => 4,
    'short' => 'Allow attachments',
    'long' => 'If enabled, emails sent from the site can have attachments, such as badges.',
  ),
  'allowbeforeblock' => 
  array (
    'count' => 1,
    'short' => 'Allowed list will be processed first',
    'long' => 'By default, entries in the blocked IPs list are matched first. If this option is enabled, entries in the allowed IPs list are processed before the blocked list.',
    'short_help' => 
    array (
      0 => 'allowbeforeblock',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'allowbeforeblockdesc',
      1 => 'admin',
    ),
  ),
  'allowblockstodock' => 
  array (
    'count' => 3,
    'short' => 'Allow blocks to use the dock',
    'long' => 'If enabled and supported by the selected theme users can choose to move blocks to a special dock.',
  ),
  'allowcategorythemes' => 
  array (
    'count' => 6,
    'short' => 'Allow category themes',
    'long' => 'If you enable this, then themes can be set at the category level. This will affect all child categories and courses unless they have specifically set their own theme. WARNING: Enabling category themes may affect performance.',
  ),
  'allowcoursethemes' => 
  array (
    'count' => 8,
    'short' => 'Allow course themes',
    'long' => 'If you enable this, then courses will be allowed to set their own themes.  Course themes override all other theme choices (site, user, or session themes)',
  ),
  'allowedip' => 
  array (
    'count' => 6,
    'short' => 'Allowed IP',
    'long' => 'Allowed IP list',
    'long_help' => 
    array (
      0 => 'allowediplist',
      1 => 'admin',
    ),
  ),
  'allowemailaddresses' => 
  array (
    'count' => 4,
    'short' => 'Allowed email domains',
    'long' => 'If you want to restrict all new email addresses to particular domains, then list them here separated by spaces.  All other domains will be rejected.  To allow subdomains add the domain with a preceding \\\'.\\\'. eg <strong>ourcollege.edu.au .gov.au</strong>',
  ),
  'allowframembedding' => 
  array (
    'count' => 1,
    'short' => 'Allow frame embedding',
    'long' => 'Allow embedding of this site in frames on external sites. Enabling of this feature is not recommended for security reasons.',
  ),
  'allowguestmymoodle' => 
  array (
    'count' => 1,
    'short' => 'Allow guest access to Dashboard',
    'long' => 'If enabled guests can access Dashboard, otherwise guests are redirected to the site front page.',
  ),
  'allowobjectembed' => 
  array (
    'count' => 6,
    'short' => 'Allow EMBED and OBJECT tags',
    'long' => 'As a default security measure, normal users are not allowed to embed multimedia (like Flash) within texts using explicit EMBED and OBJECT tags in their HTML (although it can still be done safely using the mediaplugins filter).  If you wish to allow these tags then enable this option.',
  ),
  'allowthemechangeonurl' => 
  array (
    'count' => 1,
    'short' => 'Allow theme changes in the URL',
    'long' => 'If enabled, the theme can be changed by adding either:<br />?theme=themename to any Moodle URL (eg: mymoodlesite.com/?theme=afterburner ) or <br />&theme=themename to any internal Moodle URL (eg: mymoodlesite.com/course/view.php?id=2&theme=afterburner ).',
  ),
  'allowuserblockhiding' => 
  array (
    'count' => 1,
    'short' => 'Allow users to hide blocks',
    'long' => 'Do you want to allow users to hide/show side blocks throughout this site?  This feature uses Javascript and cookies to remember the state of each collapsible block, and only affects the user\\\'s own view.',
  ),
  'allowusermailcharset' => 
  array (
    'count' => 4,
    'short' => 'Allow user to select character set',
    'long' => 'If enabled, users can choose an email charset in their messaging preferences.',
  ),
  'allowuserthemes' => 
  array (
    'count' => 3,
    'short' => 'Allow user themes',
    'long' => 'If you enable this, then users will be allowed to set their own themes.  User themes override site themes (but not course themes)',
  ),
  'allversionshash' => 
  array (
    'count' => 2,
    'short' => 'All versions hash',
    'long' => '',
  ),
  'altcacheconfigpath' => 
  array (
    'count' => 14,
    'short' => 'Alt cache config path',
    'long' => 'Moodle 2.4 introduced a new cache API.
 The cache API stores a configuration file within the Moodle data directory and
 uses that rather than the database in order to function in a stand-alone manner.
 Using altcacheconfigpath you can change the location where this config file is
 looked for.
 It can either be a directory in which to store the file, or the full path to the
 file if you want to take full control. Either way it must be writable by the
 webserver',
  ),
  'alternateloginurl' => 
  array (
    'count' => 4,
    'short' => 'Alternate login URL',
    'long' => 'Alternate login URL',
    'long_help' => 
    array (
      0 => 'alternateloginurl',
      1 => 'auth',
    ),
  ),
  'alternative_component_cache' => 
  array (
    'count' => 10,
    'short' => 'Alternative component cache',
    'long' => '',
  ),
  'alternativefullnameformat' => 
  array (
    'count' => 10,
    'short' => 'Alternative full name format',
    'long' => 'This defines how names are shown to users with the viewfullnames capability (by default users with the role of manager, teacher or non-editing teacher). Placeholders that can be used are as for the "Full name format" setting.',
  ),
  'alternativeupdateproviderurl' => 
  array (
    'count' => 1,
    'short' => 'Alternative update provide URL',
    'long' => ' During the development or testing, you can set $CFG->alternativeupdateproviderurl
',
  ),
  'amf_introspection' => 
  array (
    'count' => 2,
    'short' => 'AMF introspection',
    'long' => '',
  ),
  'apacheloguser' => 
  array (
    'count' => 10,
    'short' => 'Apache log user',
    'long' => ' The following setting will turn on username logging into Apache log. For full details regarding setting
 up of this function please refer to the install section of the document.
     $CFG->apacheloguser = 0; // Turn this feature off. Default value.
     $CFG->apacheloguser = 1; // Log user id.
     $CFG->apacheloguser = 2; // Log full name in cleaned format. ie, Darth Vader will be displayed as darth_vader.
     $CFG->apacheloguser = 3; // Log username.
 To get the values logged in Apache\'s log, add to your httpd.conf
 the following statements. In the General part put:
     LogFormat "%h %l %{MOODLEUSER}n %t \\"%r\\" %s %b \\"%{Referer}i\\" \\"%{User-Agent}i\\"" moodleformat
 And in the part specific to your Moodle install / virtualhost:
     CustomLog "/your/path/to/log" moodleformat
 CAUTION: Use of this option will expose usernames in the Apache log,
 If you are going to publish your log, or the output of your web stats analyzer
 this will weaken the security of your website.',
  ),
  'apachemaxmem' => 
  array (
    'count' => 2,
    'short' => 'Apache max memory',
    'long' => '',
  ),
  'aspellpath' => 
  array (
    'count' => 3,
    'short' => 'Path to aspell',
    'long' => 'To use spell-checking within the editor, you MUST have aspell 0.50 or later
 installed on your server, and you must specify the correct path to access the
 aspell binary. On Unix/Linux systems, this path is usually /usr/bin/aspell,
 but it might be something else.
      $CFG->aspellpath = \'\';',
  ),
  'auth' => 
  array (
    'count' => 13,
    'short' => 'Authentication',
    'long' => 'Authentication',
    'long_help' => 
    array (
      0 => 'authentication',
      1 => 'admin',
    ),
  ),
  'auth_instructions' => 
  array (
    'count' => 2,
    'short' => 'Auth instructions',
    'long' => 'Use the <a href="{$a}">Shibboleth login</a> to get access via Shibboleth, if your institution supports it.<br />Otherwise, use the normal login form shown here.',
    'long_help' => 
    array (
      0 => 'auth_shib_instructions',
      1 => 'auth_shibboleth',
    ),
  ),
  'authloginviaemail' => 
  array (
    'count' => 5,
    'short' => 'Allow log in via email',
    'long' => 'Allow users to use both username and email address (if unique) for site login.',
  ),
  'authpreventaccountcreation' => 
  array (
    'count' => 1,
    'short' => 'Prevent account creation when authenticating',
    'long' => 'When a user authenticates, an account on the site is automatically created if it doesn\\\'t yet exist. If an external database, such as LDAP, is used for authentication, but you wish to restrict access to the site to users with an existing account only, then this option should be enabled. New accounts will need to be created manually or via the upload users feature. Note that this setting doesn\\\'t apply to MNet authentication.',
  ),
  'autolang' => 
  array (
    'count' => 1,
    'short' => 'Language autodetect',
    'long' => 'Detect default language from browser setting, if disabled site default is used.',
  ),
  'autologinguests' => 
  array (
    'count' => 2,
    'short' => 'Auto-login guests',
    'long' => 'Should visitors be logged in as guests automatically when entering courses with guest access?',
  ),
  'backup_database_logger_level' => 
  array (
    'count' => 10,
    'short' => 'Backup database logger level',
    'long' => '',
  ),
  'backup_error_log_logger_level' => 
  array (
    'count' => 14,
    'short' => 'Backup error log logger level',
    'long' => '',
  ),
  'backup_file_logger_extra' => 
  array (
    'count' => 7,
    'short' => 'Backup file logger extra',
    'long' => '',
  ),
  'backup_file_logger_extra_level' => 
  array (
    'count' => 3,
    'short' => 'Backup file logger extra level',
    'long' => '',
  ),
  'backup_file_logger_level' => 
  array (
    'count' => 12,
    'short' => 'Backup file logger level',
    'long' => '',
  ),
  'backup_file_logger_level_extra' => 
  array (
    'count' => 8,
    'short' => 'Backup file logger level extra',
    'long' => '',
  ),
  'backup_output_indented_logger_level' => 
  array (
    'count' => 7,
    'short' => 'Backup output indented logger level',
    'long' => '',
  ),
  'backup_release' => 
  array (
    'count' => 2,
    'short' => 'Backup relase',
    'long' => '',
  ),
  'backup_version' => 
  array (
    'count' => 3,
    'short' => 'Backup version',
    'long' => '',
  ),
  'badges_allowcoursebadges' => 
  array (
    'count' => 13,
    'short' => 'Enable course badges',
    'long' => 'Allow badges to be created and awarded in the course context.',
  ),
  'badges_allowexternalbackpack' => 
  array (
    'count' => 10,
    'short' => 'Enable connection to external backpacks',
    'long' => 'Allow users to set up connections and display badges from their external backpack providers.

Note: It is recommended to leave this option disabled if the website cannot be accessed from the Internet (e.g. because of the firewall).',
  ),
  'badges_badgesalt' => 
  array (
    'count' => 2,
    'short' => 'Salt for hashing the recepient\\\'s email address',
    'long' => 'Using a hash allows backpack services to confirm the badge earner without having to expose their email address. This setting should only use numbers and letters.

Note: For recipient verification purposes, please avoid changing this setting once you start issuing badges.',
  ),
  'badges_defaultissuercontact' => 
  array (
    'count' => 4,
    'short' => 'Default badge issuer contact details',
    'long' => 'An email address associated with the badge issuer.',
  ),
  'badges_defaultissuername' => 
  array (
    'count' => 5,
    'short' => 'Default badge issuer name',
    'long' => 'Name of the issuing agent or authority.',
  ),
  'behat_' => 
  array (
    'count' => 5,
    'short' => 'Behat',
    'long' => 'Error running behat CLI command. Try running "{$a} --help" manually from CLI to find out more about the problem.',
    'long_help' => 
    array (
      0 => 'errorbehatcommand',
      1 => 'tool_behat',
    ),
  ),
  'behat_X' => 
  array (
    'count' => 1,
    'short' => 'Behat X',
    'long' => '',
  ),
  'behat_additionalfeatures' => 
  array (
    'count' => 4,
    'short' => 'Behat additional features',
    'long' => 'Including feature files from directories outside the dirroot is possible if required. The setting
 requires that the running user has executable permissions on all parent directories in the paths.
 Example:
   $CFG->behat_additionalfeatures = array(\'/home/developer/code/wipfeatures\');',
  ),
  'behat_config' => 
  array (
    'count' => 8,
    'short' => 'Behat config',
    'long' => ' You can override default Moodle configuration for Behat and add your own
 params; here you can add more profiles, use different Mink drivers than Selenium...
 These params would be merged with the default Moodle behat.yml, giving priority
 to the ones specified here. The array format is YAML, following the Behat
 params hierarchy. More info: http://docs.behat.org/guides/7.config.html
 Example:
   $CFG->behat_config = array(
       \'default\' => array(
           \'formatter\' => array(
               \'name\' => \'pretty\',
               \'parameters\' => array(
                   \'decorated\' => true,
                   \'verbose\' => false
               )
           )
       ),
      
           
   );',
  ),
  'behat_dataroot' => 
  array (
    'count' => 53,
    'short' => 'Behat dataroot',
    'long' => ' Behat test site needs a unique www root, data directory and database prefix:
            $CFG->behat_dataroot = \'/home/example/bht_moodledata\';
            ',
  ),
  'behat_extraallowedsettings' => 
  array (
    'count' => 4,
    'short' => 'Behat extra allowed settings',
    'long' => 'All this page\'s extra Moodle settings are compared against a white list of allowed settings
 (the basic and behat_* ones) to avoid problems with production environments. This setting can be
 used to expand the default white list with an array of extra settings.
 Example:
   $CFG->behat_extraallowedsettings = array(\'somecoresetting\', ...);',
  ),
  'behat_faildump_path' => 
  array (
    'count' => 10,
    'short' => 'Behat faildump path',
    'long' => 'You can make behat save several dumps when a scenario fails. The dumps currently saved are:
 * a dump of the DOM in it\'s state at the time of failure; and
 * a screenshot (JavaScript is required for the screenshot functionality, so not all browsers support this option)
 Example:
   $CFG->behat_faildump_path = \'/my/path/to/save/failure/dumps\';',
  ),
  'behat_parallel_run' => 
  array (
    'count' => 25,
    'short' => 'Behat parallel run',
    'long' => ' You can specify db, selenium wd_host etc. for behat parallel run by setting following variable.
 Example:
   $CFG->behat_parallel_run = array (
       array (
           \'dbtype\' => \'mysqli\',
           \'dblibrary\' => \'native\',
           \'dbhost\' => \'localhost\',
           \'dbname\' => \'moodletest\',
           \'dbuser\' => \'moodle\',
           \'dbpass\' => \'moodle\',
           \'behat_prefix\' => \'mdl_\',
           \'wd_host\' => \'http://127.0.0.1:4444/wd/hub\',
           \'behat_wwwroot\' => \'http://127.0.0.1/moodle\',
           \'behat_dataroot\' => \'/home/example/bht_moodledata\'
       ),
   );',
  ),
  'behat_prefix' => 
  array (
    'count' => 22,
    'short' => 'Key prefix',
    'long' => 'This prefix is used for all key names on the memcache server.
* If you only have one Moodle instance using this server, you can leave this value default.
* Due to key length restrictions, a maximum of 5 characters is permitted.',
  ),
  'behat_restart_browser_after' => 
  array (
    'count' => 5,
    'short' => 'Behat restart browser after',
    'long' => 'You can force the browser session (not user\'s sessions) to restart after N seconds. This could
 be useful if you are using a cloud-based service with time restrictions in the browser side.
 Setting this value the browser session that Behat is using will be restarted. Set the time in
 seconds. Is not recommended to use this setting if you don\'t explicitly need it.
 Example:
   $CFG->behat_restart_browser_after = 7200;     // Restarts the browser session after 2 hours',
  ),
  'behat_usedeprecated' => 
  array (
    'count' => 6,
    'short' => 'Behat use deprecated',
    'long' => 'You should explicitly allow the usage of the deprecated behat steps, otherwise an exception will
 be thrown when using them. The setting is disabled by default.
 Example:
   $CFG->behat_usedeprecated = true;',
  ),
  'behat_wwwroot' => 
  array (
    'count' => 36,
    'short' => 'Behat WWWROOT',
    'long' => '',
  ),
  'behatrunprocess' => 
  array (
    'count' => 13,
    'short' => 'Behat run process',
    'long' => '',
  ),
  'block_course_list_adminview' => 
  array (
    'count' => 2,
    'short' => 'Course list admin view',
    'long' => 'Admin view',
    'long_help' => 
    array (
      0 => 'adminview',
      1 => 'block_course_list',
    ),
  ),
  'block_course_list_hideallcourseslink' => 
  array (
    'count' => 3,
    'short' => 'Hide all courses link',
    'long' => 'Hide \'All courses\' link',
    'long_help' => 
    array (
      0 => 'hideallcourseslink',
      1 => 'block_course_list',
    ),
  ),
  'block_html_allowcssclasses' => 
  array (
    'count' => 2,
    'short' => 'Allow css classes in HTML',
    'long' => 'Allow additional CSS classes',
    'long_help' => 
    array (
      0 => 'allowadditionalcssclasses',
      1 => 'block_html',
    ),
  ),
  'block_online_users_timetosee' => 
  array (
    'count' => 6,
    'short' => 'Online users time to see',
    'long' => 'Remove after inactivity (minutes)',
    'long_help' => 
    array (
      0 => 'timetosee',
      1 => 'block_online_users',
    ),
  ),
  'block_rss_client_num_entries' => 
  array (
    'count' => 4,
    'short' => 'RSS client num entries',
    'long' => 'Entries per feed',
    'long_help' => 
    array (
      0 => 'numentries',
      1 => 'block_rss_client',
    ),
  ),
  'block_rss_client_timeout' => 
  array (
    'count' => 2,
    'short' => 'RSS client timeout',
    'long' => 'Timeout',
    'long_help' => 
    array (
      0 => 'timeout2',
      1 => 'block_rss_client',
    ),
  ),
  'block_tags_showcoursetags' => 
  array (
    'count' => 2,
    'short' => 'Show course tags',
    'long' => 'Show course tags',
    'long_help' => 
    array (
      0 => 'showcoursetags',
      1 => 'block_tags',
    ),
  ),
  'blockedip' => 
  array (
    'count' => 4,
    'short' => 'Blocked IP',
    'long' => 'Blocked IP List',
    'long_help' => 
    array (
      0 => 'blockediplist',
      1 => 'admin',
    ),
  ),
  'blockeditingmenu' => 
  array (
    'count' => 2,
    'short' => 'Block editing menus',
    'long' => 'If enabled many of the block editing icons shown when editing is on will be displayed within a drop-down menu. This reduces the content on screen by hiding the icons until they are needed.',
  ),
  'blockmanagerclass' => 
  array (
    'count' => 4,
    'short' => 'Blog manager class',
    'long' => 'You can specify a different class to be created for the $PAGE global, and to
 compute which blocks appear on each page. However, I cannot think of any good
 reason why you would need to change that. It just felt wrong to hard-code the
 the class name. You are strongly advised not to use these to settings unless
 you are absolutely sure you know what you are doing.
 $CFG->blockmanagerclass = \'block_manager\';',
  ),
  'blockmanagerclassfile' => 
  array (
    'count' => 4,
    'short' => 'Block manager class file',
    'long' => 'You can specify a different class to be created for the $PAGE global, and to
 compute which blocks appear on each page. However, I cannot think of any good
 reason why you would need to change that. It just felt wrong to hard-code the
 the class name. You are strongly advised not to use these to settings unless
 you are absolutely sure you know what you are doing.
 $CFG->blockmanagerclassfile = "$CFG->dirroot/local/myplugin/myblockamanagerclass.php";',
  ),
  'blocksdrag' => 
  array (
    'count' => 1,
    'short' => 'Blocks drag',
    'long' => '',
  ),
  'bloglevel' => 
  array (
    'count' => 39,
    'short' => 'Blog visibility',
    'long' => 'This setting allows you to restrict the level to which user blogs can be viewed on this site.  Note that they specify the maximum context of the VIEWER not the poster or the types of blog posts.  Blogs can also be disabled completely if you don\\\'t want them at all.',
  ),
  'blogshowcommentscount' => 
  array (
    'count' => 1,
    'short' => 'Show comments count',
    'long' => 'Show comments count, it will cost one more query when display comments link',
    'short_help' => 
    array (
      0 => 'showcommentscount',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'configshowcommentscount',
      1 => 'admin',
    ),
  ),
  'blogusecomments' => 
  array (
    'count' => 2,
    'short' => 'Enable comments',
    'long' => 'Enable comments',
    'short_help' => 
    array (
      0 => 'enablecomments',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'configenablecomments',
      1 => 'admin',
    ),
  ),
  'bounceratio' => 
  array (
    'count' => 5,
    'short' => 'Bounce ratio',
    'long' => 'The following line is for handling email bounces',
  ),
  'branch' => 
  array (
    'count' => 8,
    'short' => 'Branch',
    'long' => 'Content',
    'long_help' => 
    array (
      0 => 'branch',
      1 => 'lesson',
    ),
  ),
  'cachedir' => 
  array (
    'count' => 32,
    'short' => 'Cache directory',
    'long' => 'Path to moodles cache directory on servers filesystem (shared by cluster nodes)',
  ),
  'cachejs' => 
  array (
    'count' => 5,
    'short' => 'Cache Javascript',
    'long' => 'Javascript caching and compression greatly improves page loading performance. it is strongly recommended for production sites. Developers will probably want to disable this feature.',
  ),
  'calendar' => 
  array (
    'count' => 6,
    'short' => 'Calendar',
    'long' => 'Calendar types',
    'long_help' => 
    array (
      0 => 'calendartypes',
      1 => 'calendar',
    ),
  ),
  'calendar_adminseesall' => 
  array (
    'count' => 3,
    'short' => 'Calendar admin see sall',
    'long' => 'Admins see all',
    'long_help' => 
    array (
      0 => 'adminseesall',
      1 => 'admin',
    ),
  ),
  'calendar_customexport' => 
  array (
    'count' => 1,
    'short' => 'Enable custom date range export of calendar',
    'long' => 'Enable custom date range export option in calendar exports. Calendar exports must be enabled before this is effective.',
    'short_help' => 
    array (
      0 => 'configcalendarcustomexport',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'helpcalendarcustomexport',
      1 => 'admin',
    ),
  ),
  'calendar_exportlookahead' => 
  array (
    'count' => 2,
    'short' => 'Days to look ahead during export',
    'long' => 'How many days in the future does the calendar look for events during export for the custom export option?',
    'short_help' => 
    array (
      0 => 'configexportlookahead',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'helpexportlookahead',
      1 => 'admin',
    ),
  ),
  'calendar_exportlookback' => 
  array (
    'count' => 2,
    'short' => 'Days to look back during export',
    'long' => 'How many days in the past does the calendar look for events during export for the custom export option?',
    'short_help' => 
    array (
      0 => 'configexportlookback',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'helpexportlookback',
      1 => 'admin',
    ),
  ),
  'calendar_exportsalt' => 
  array (
    'count' => 5,
    'short' => 'Calendar export salt',
    'long' => 'This random text is used for improving of security of authentication tokens used for exporting of calendars. Please note that all current tokens are invalidated if you change this hash salt.',
    'short_help' => 
    array (
      0 => 'calendarexportsalt',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'configcalendarexportsalt',
      1 => 'admin',
    ),
  ),
  'calendar_lookahead' => 
  array (
    'count' => 6,
    'short' => 'Calendar look ahead',
    'long' => 'How many days in the future does the calendar look for upcoming events by default?',
    'short_help' => 
    array (
      0 => 'configlookahead',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'helpupcominglookahead',
      1 => 'admin',
    ),
  ),
  'calendar_maxevents' => 
  array (
    'count' => 6,
    'short' => 'Calendar max events',
    'long' => 'How many (maximum) upcoming events are shown to users by default?',
    'short_help' => 
    array (
      0 => 'configmaxevents',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'helpupcomingmaxevents',
      1 => 'admin',
    ),
  ),
  'calendar_showicalsource' => 
  array (
    'count' => 2,
    'short' => 'Show source information for iCal events',
    'long' => 'If enabled, the subscription name and link will be shown for iCal-imported events.',
    'short_help' => 
    array (
      0 => 'configshowicalsource',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'helpshowicalsource',
      1 => 'admin',
    ),
  ),
  'calendar_startwday' => 
  array (
    'count' => 3,
    'short' => 'Start of week',
    'long' => 'Which day starts the week in the calendar?',
    'short_help' => 
    array (
      0 => 'configstartwday',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'helpstartofweek',
      1 => 'admin',
    ),
  ),
  'calendar_weekend' => 
  array (
    'count' => 6,
    'short' => 'Calendar weekend',
    'long' => 'Weekend days',
    'long_help' => 
    array (
      0 => 'calendar_weekend',
      1 => 'admin',
    ),
  ),
  'calendartype' => 
  array (
    'count' => 8,
    'short' => 'Calendar type',
    'long' => 'Choose a default calendar type for the whole site. This setting can be overridden in the course settings or by users in their personal profile.',
  ),
  'chat_method' => 
  array (
    'count' => 2,
    'short' => 'Chat method',
    'long' => 'The ajax chat method provide an ajax based chat interface, it contacts server regularly for update. The normal chat method involves the clients regularly contacting the server for updates. It requires no configuration and works everywhere, but it can create a large load on the server with many chatters.  Using a server daemon requires shell access to Unix, but it results in a fast scalable chat environment.',
  ),
  'chat_normal_updatemode' => 
  array (
    'count' => 3,
    'short' => 'Chat normal update mode',
    'long' => 'Update method',
    'long_help' => 
    array (
      0 => 'updatemethod',
      1 => 'chat',
    ),
  ),
  'chat_old_ping' => 
  array (
    'count' => 12,
    'short' => 'Chat old ping',
    'long' => 'Disconnect timeout',
    'long_help' => 
    array (
      0 => 'oldping',
      1 => 'chat',
    ),
  ),
  'chat_refresh_room' => 
  array (
    'count' => 6,
    'short' => 'Chat refresh room',
    'long' => 'Refresh room',
    'long_help' => 
    array (
      0 => 'refreshroom',
      1 => 'chat',
    ),
  ),
  'chat_refresh_userlist' => 
  array (
    'count' => 3,
    'short' => 'Chat refresh userlist',
    'long' => 'Refresh user list',
    'long_help' => 
    array (
      0 => 'refreshuserlist',
      1 => 'chat',
    ),
  ),
  'chat_serverhost' => 
  array (
    'count' => 7,
    'short' => 'Server name',
    'long' => 'The hostname of the computer where the server daemon is',
  ),
  'chat_serverip' => 
  array (
    'count' => 1,
    'short' => 'Server ip',
    'long' => 'The numerical IP address that matches the above hostname',
  ),
  'chat_servermax' => 
  array (
    'count' => 1,
    'short' => 'Max users',
    'long' => 'Max number of clients allowed',
  ),
  'chat_serverport' => 
  array (
    'count' => 10,
    'short' => 'Server port',
    'long' => 'Port to use on the server for the daemon',
  ),
  'clamfailureonupload' => 
  array (
    'count' => 1,
    'short' => 'On clam AV failure',
    'long' => 'If you have configured clam to scan uploaded files, but it is configured incorrectly or fails to run for some unknown reason, how should it behave?  If you choose \\\'Treat files like viruses\\\', they\\\'ll be moved into the quarantine area, or deleted. If you choose \\\'Treat files as OK\\\', the files will be moved to the destination directory like normal. Either way, admins will be alerted that clam has failed.  If you choose \\\'Treat files like viruses\\\' and for some reason clam fails to run (usually because you have entered an invalid pathtoclam), ALL files that are uploaded will be moved to the given quarantine area, or deleted. Be careful with this setting.',
  ),
  'commentsenabled' => 
  array (
    'count' => 1,
    'short' => 'Comments enabled',
    'long' => 'Automatically enable or disable this plugin based on "$CFG->commentsenabled"
',
  ),
  'commentsperpage' => 
  array (
    'count' => 5,
    'short' => 'Comments per page',
    'long' => 'Comments displayed per page',
  ),
  'completiondefault' => 
  array (
    'count' => 2,
    'short' => 'Default completion tracking',
    'long' => 'The default setting for completion tracking when creating new activities.',
  ),
  'config_php_settings' => 
  array (
    'count' => 23,
    'short' => 'Config PHP settings',
    'long' => '',
  ),
  'cookiehttponly' => 
  array (
    'count' => 5,
    'short' => 'Only http cookies',
    'long' => 'Enables new PHP 5.2.0 feature - browsers are instructed to send cookie with real http requests only, cookies should not be accessible by scripting languages. This is not supported in all browsers and it may not be fully compatible with current code. It helps to prevent some types of XSS attacks.',
  ),
  'cookiesecure' => 
  array (
    'count' => 6,
    'short' => 'Secure cookies only',
    'long' => 'If server is accepting only https connections it is recommended to enable sending of secure cookies. If enabled please make sure that web server is not accepting http:// or set up permanent redirection to https:// address. When <em>wwwroot</em> address does not start with https:// this setting is turned off automatically.',
  ),
  'core_media_enable_flv' => 
  array (
    'count' => 5,
    'short' => 'Core media enable FLV',
    'long' => 'Set to \'true\' to enable FLV support',
  ),
  'core_media_enable_html5audio' => 
  array (
    'count' => 6,
    'short' => 'Core media enable HTML5 audio',
    'long' => 'Set to \'true\' to enable HTML5 audios',
  ),
  'core_media_enable_html5video' => 
  array (
    'count' => 10,
    'short' => 'Core media enable HTML5 video',
    'long' => 'Set to \'true\' to enable HTML5 videos',
  ),
  'core_media_enable_mp3' => 
  array (
    'count' => 6,
    'short' => 'Core media enable MP3',
    'long' => 'Set to \'true\' to enable MP3 support',
  ),
  'core_media_enable_qt' => 
  array (
    'count' => 6,
    'short' => 'Core media enable GT',
    'long' => 'Set to \'true\' to enable GT support',
  ),
  'core_media_enable_rm' => 
  array (
    'count' => 4,
    'short' => 'Core media enable RM',
    'long' => 'Set to \'true\' to enable RM support',
  ),
  'core_media_enable_swf' => 
  array (
    'count' => 4,
    'short' => 'Core media enable SWF',
    'long' => 'Set to \'true\' to enable SWF support',
  ),
  'core_media_enable_test' => 
  array (
    'count' => 2,
    'short' => 'Core media enable test',
    'long' => '',
  ),
  'core_media_enable_vimeo' => 
  array (
    'count' => 4,
    'short' => 'Core media enable vimeo',
    'long' => 'Set to \'true\' to enable vimeo support',
  ),
  'core_media_enable_wmp' => 
  array (
    'count' => 5,
    'short' => 'Core media enable WMP',
    'long' => 'Set to \'true\' to enable WMP support',
  ),
  'core_media_enable_youtube' => 
  array (
    'count' => 5,
    'short' => 'Core media enable youtube',
    'long' => 'Set to \'true\' to enable youtube support',
  ),
  'country' => 
  array (
    'count' => 7,
    'short' => 'Country',
    'long' => 'If you set a country here, then this country will be selected by default on new user accounts.  To force users to choose a country, just leave this unset.',
  ),
  'coursecontact' => 
  array (
    'count' => 22,
    'short' => 'Course contacts',
    'long' => 'This setting allows you to control who appears on the course description. Users need to have at least one of these roles in a course to be shown on the course description for that course.',
  ),
  'courselistshortnames' => 
  array (
    'count' => 3,
    'short' => 'Display extended course names',
    'long' => 'If enabled, course short names will be displayed in addition to full names in course lists. If required, extended course names may be customised by editing the \\\'courseextendednamedisplay\\\' language string using the language customisation feature.',
  ),
  'courseoverviewfilesext' => 
  array (
    'count' => 5,
    'short' => 'Course summary files extensions',
    'long' => 'A comma-separated list of allowed course summary files extensions.',
  ),
  'courseoverviewfileslimit' => 
  array (
    'count' => 11,
    'short' => 'Course summary files limit',
    'long' => 'The maximum number of files that can be attached to a course summary.',
  ),
  'courserequestnotify' => 
  array (
    'count' => 1,
    'short' => 'Course request notification',
    'long' => 'Type username of user to be notified when new course requested.',
  ),
  'coursesperpage' => 
  array (
    'count' => 25,
    'short' => 'Courses per page',
    'long' => 'Enter the number of courses to be displayed per page in a course listing.',
  ),
  'courseswithsummarieslimit' => 
  array (
    'count' => 4,
    'short' => 'Courses with summaries limit',
    'long' => 'The maximum number of courses to display in a course listing including summaries before falling back to a simpler listing.',
  ),
  'creatornewroleid' => 
  array (
    'count' => 8,
    'short' => 'Creators\\\' role in new courses',
    'long' => 'If the user does not already have the permission to manage the new course, the user is automatically enrolled using this role.',
  ),
  'cronclionly' => 
  array (
    'count' => 4,
    'short' => 'Cron execution via command line only',
    'long' => 'Running the cron from a web browser can expose privileged information to anonymous users. Thus it is recommended to only run the cron from the command line or set a cron password for remote access.',
  ),
  'cronremotepassword' => 
  array (
    'count' => 5,
    'short' => 'Cron password for remote access',
    'long' => 'This means that the cron.php script cannot be run from a web browser without supplying the password using the following form of URL:<pre>
    http://site.example.com/admin/cron.php?password=opensesame
</pre>If this is left empty, no password is required.',
  ),
  'cssoptimiserpretty' => 
  array (
    'count' => 9,
    'short' => 'CSS optimiser pretty',
    'long' => 'If set the CSS that is optimised will still retain a minimalistic formatting
 so that anyone wanting to can still clearly read it.',
  ),
  'cssoptimiserstats' => 
  array (
    'count' => 3,
    'short' => 'CSS optimizer stats',
    'long' => 'If set the CSS optimiser will add stats about the optimisation to the top of
 the optimised CSS file. You can then inspect the CSS to see the affect the CSS
 optimiser is having.',
  ),
  'curlcache' => 
  array (
    'count' => 4,
    'short' => 'cURL cache TTL',
    'long' => 'Time-to-live for cURL cache, in seconds.',
  ),
  'curltimeoutkbitrate' => 
  array (
    'count' => 3,
    'short' => 'Bitrate to use when calculating cURL timeouts (Kbps)',
    'long' => 'This setting is used to calculate an appropriate timeout during large cURL requests. As part of this calculation an HTTP HEAD request is made to determine the size of the content. Setting this to 0 disables this request from being made.',
  ),
  'custom_context_classes' => 
  array (
    'count' => 3,
    'short' => 'Custom context classes',
    'long' => '',
  ),
  'customfiletypes' => 
  array (
    'count' => 13,
    'short' => 'Custom file types',
    'long' => ' Moodle 2.9 allows administrators to customise the list of supported file types.
 To add a new filetype or override the definition of an existing one, set the
 customfiletypes variable like this:

 $CFG->customfiletypes = array(
     (object)array(
         \'extension\' => \'frog\',
         \'icon\' => \'archive\',
         \'type\' => \'application/frog\',
         \'customdescription\' => \'Amphibian-related file archive\'
     )
 );',
  ),
  'customfrontpageinclude' => 
  array (
    'count' => 2,
    'short' => 'Custom front page include',
    'long' => '',
  ),
  'custommenuitems' => 
  array (
    'count' => 6,
    'short' => 'Custom menu items',
    'long' => 'You can configure a custom menu here to be shown by themes. Each line consists of some menu text, a link URL (optional), a tooltip title (optional) and a language code or comma-separated list of codes (optional, for displaying the line to users of the specified language only), separated by pipe characters. You can specify a structure using hyphens, and dividers can be used by adding a line of one or more # characters where desired. For example:
<pre>
Moodle community|https://moodle.org
-Moodle free support|https://moodle.org/support
-###
-Moodle development|https://moodle.org/development
--Moodle Docs|http://docs.moodle.org|Moodle Docs
--German Moodle Docs|http://docs.moodle.org/de|Documentation in German|de
#####
Moodle.com|http://moodle.com/
</pre>',
  ),
  'customscripts' => 
  array (
    'count' => 6,
    'short' => 'Custom scripts',
    'long' => 'Enabling this will allow custom scripts to replace existing moodle scripts.
 For example: if $CFG->customscripts/course/view.php exists then
 it will be used instead of $CFG->wwwroot/course/view.php
 At present this will only work for files that include config.php and are called
 as part of the url (index.php is implied).
 Some examples are:
      http://my.moodle.site/course/view.php
      http://my.moodle.site/index.php
      http://my.moodle.site/admin            (index.php implied)
 Custom scripts should not include config.php
 Warning: Replacing standard moodle scripts may pose security risks and/or may not
 be compatible with upgrades. Use this option only if you are aware of the risks
 involved.
 Specify the full directory path to the custom scripts',
  ),
  'customusermenuitems' => 
  array (
    'count' => 1,
    'short' => 'User menu items',
    'long' => 'You can configure the contents of the user menu (with the exception of the log out link, which is automatically added). Each line is separated by | characters and consists of 1) a string in "langstringname, componentname" form or as plain text, 2) a URL, and 3) an icon either as a pix icon or as a URL. Dividers can be used by adding a line of one or more # characters where desired.',
  ),
  'data_enablerssfeeds' => 
  array (
    'count' => 7,
    'short' => 'Enable RSS feeds',
    'long' => 'This switch will enable the possibility of RSS feeds for all glossaries.  You will still need to turn feeds on manually in the settings for each glossary.',
  ),
  'dataroot' => 
  array (
    'count' => 254,
    'short' => 'Data files location',
    'long' => 'This
 directory should be readable AND WRITEABLE by the web server user
 (usually \'nobody\' or \'apache\'), but it should not be accessible
 directly via the web.

 - On hosting systems you might need to make sure that your "group" has
   no permissions at all, but that "others" have full permissions.

 - On Windows systems you might specify something like \'c:\\moodledata\'',
  ),
  'dbfamily' => 
  array (
    'count' => 1,
    'short' => 'Database family',
    'long' => '',
  ),
  'dbhost' => 
  array (
    'count' => 22,
    'short' => 'Host server',
    'long' => 'Type database server IP address or host name. Use a system DSN name if using ODBC.',
  ),
  'dblibrary' => 
  array (
    'count' => 9,
    'short' => 'Library database',
    'long' => '"native" only at the moment.',
  ),
  'dbname' => 
  array (
    'count' => 20,
    'short' => 'Database name',
    'long' => 'Leave empty if using a DSN name in database host.',
  ),
  'dboptions' => 
  array (
    'count' => 55,
    'short' => 'Database options',
    'long' => '    \'dbpersist\' => false,        should persistent database connections be
                                  used? set to \'false\' for the most stable
                                  setting, \'true\' can improve performance
                                  sometimes
    \'dbsocket\'  => false,        should connection via UNIX socket be used?
                                  if you set it to \'true\' or custom path
                                  here set dbhost to \'localhost\',
                                  (please note mysql is always using socket
                                  if dbhost is \'localhost\' - if you need
                                  local port connection use \'127.0.0.1\')
    \'dbport\'    => \'\',           the TCP port number to use when connecting
                                  to the server. keep empty string for the
                                  default port',
  ),
  'dbpass' => 
  array (
    'count' => 19,
    'short' => 'Database password',
    'long' => 'Your database password.',
  ),
  'dbpersist' => 
  array (
    'count' => 2,
    'short' => 'Database presist',
    'long' => '[[databasepersist]]',
    'long_help' => 
    array (
      0 => 'databasepersist',
      1 => 'NULL',
    ),
  ),
  'dbsessions' => 
  array (
    'count' => 5,
    'short' => 'Use database for session information',
    'long' => 'If enabled, this setting will use the database to store information about current sessions. Note that changing this setting now will log out all current users (including you). If you are using MySQL please make sure that \\\'max_allowed_packet\\\' in my.cnf (or my.ini) is at least 4M. Other session drivers can be configured directly in config.php, see config-dist.php for more information. This option disappears if you specify session driver in config.php file.',
  ),
  'dbtype' => 
  array (
    'count' => 20,
    'short' => 'Type',
    'long' => 'ADOdb database driver name, type of the external database engine.',
  ),
  'dbuser' => 
  array (
    'count' => 21,
    'short' => 'Database user',
    'long' => 'Your database username.',
  ),
  'debug' => 
  array (
    'count' => 100,
    'short' => 'Debug messages',
    'long' => 'If you turn this on, then PHP\\\'s error_reporting will be increased so that more warnings are printed.  This is only useful for developers.',
  ),
  'debugdeveloper' => 
  array (
    'count' => 71,
    'short' => 'Debug developer',
    'long' => 'DEVELOPER: extra Moodle debug messages for developers',
    'long_help' => 
    array (
      0 => 'debugdeveloper',
      1 => 'admin',
    ),
  ),
  'debugdisplay' => 
  array (
    'count' => 42,
    'short' => 'Display debug messages',
    'long' => 'Set to on, the error reporting will go to the HTML page. This is practical, but breaks XHTML, JS, cookies and HTTP headers in general. Set to off, it will send the output to your server logs, allowing better debugging. The PHP setting error_log controls which log this goes to.',
  ),
  'debugpageinfo' => 
  array (
    'count' => 1,
    'short' => 'Show page information',
    'long' => 'Enable if you want page information printed in page footer.',
  ),
  'debugsmtp' => 
  array (
    'count' => 1,
    'short' => 'Debug email sending',
    'long' => 'Enable verbose debug information during sending of email messages to SMTP server.',
  ),
  'debugstringids' => 
  array (
    'count' => 4,
    'short' => 'Show origin of languages strings',
    'long' => 'This option is designed to help translators. When this option is enabled, if you add the parameter strings=1 to a request URL, it will show the language file and string id beside each string that is output.',
  ),
  'debugusers' => 
  array (
    'count' => 4,
    'short' => 'Debug users',
    'long' => 'You can specify a comma separated list of user ids that that always see
debug messages, this overrides the debug flag in $CFG->debug and $CFG->debugdisplay
for these users only.',
  ),
  'debugvalidators' => 
  array (
    'count' => 2,
    'short' => 'Show validator links',
    'long' => 'Enable if you want to have links to external validator servers in page footer. You may need to create new user with username <em>w3cvalidator</em>, and enable guest access. These changes may allow unauthorized access to server, do not enable on production sites!',
  ),
  'defaultallowedmodules' => 
  array (
    'count' => 1,
    'short' => 'Default allowed modules',
    'long' => '',
  ),
  'defaultblocks' => 
  array (
    'count' => 6,
    'short' => 'Default blocks',
    'long' => 'These blocks are used when no other default setting is found.
            $CFG->defaultblocks = \'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity\';

            ',
  ),
  'defaultblocks_override' => 
  array (
    'count' => 4,
    'short' => 'Override',
    'long' => 'If this one is set it overrides all others and is the only one used.$CFG->defaultblocks_override = \'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity\';',
  ),
  'defaultblocks_site' => 
  array (
    'count' => 4,
    'short' => 'Default blocks, site',
    'long' => 'This var define the specific settings for defined course formats and
            override any settings defined in the formats own config file.
             $CFG->defaultblocks_site = \'site_main_menu,course_list:course_summary,calendar_month\';',
  ),
  'defaultblocks_social' => 
  array (
    'count' => 2,
    'short' => 'Default blocks, social',
    'long' => 'This var define the specific settings for defined course formats and
            override any settings defined in the formats own config file.
            $CFG->defaultblocks_social = \'participants,search_forums,calendar_month,calendar_upcoming,social_activities,recent_activity,course_list\';',
  ),
  'defaultblocks_topics' => 
  array (
    'count' => 2,
    'short' => 'Default blocks, topics',
    'long' => 'This var define the specific settings for defined course formats and
            override any settings defined in the formats own config file.
            $CFG->defaultblocks_topics = \'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity\';',
  ),
  'defaultblocks_weeks' => 
  array (
    'count' => 2,
    'short' => 'Default blocks, week',
    'long' => 'This var define the specific settings for defined course formats and
            override any settings defined in the formats own config file.
            $CFG->defaultblocks_weeks = \'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity\';',
  ),
  'defaultcity' => 
  array (
    'count' => 6,
    'short' => 'Default city',
    'long' => 'A city entered here will be the default city when creating new user accounts.',
  ),
  'defaultfrontpageroleid' => 
  array (
    'count' => 27,
    'short' => 'Default front page role ID',
    'long' => 'Default frontpage role',
    'long_help' => 
    array (
      0 => 'frontpagedefaultrole',
      1 => 'admin',
    ),
  ),
  'defaulthomepage' => 
  array (
    'count' => 14,
    'short' => 'Default home page for users',
    'long' => 'This determines the home page for logged in users',
  ),
  'defaultpreference_autosubscribe' => 
  array (
    'count' => 5,
    'short' => 'Default preference autosubscribe',
    'long' => '[[autosubscribe]]',
    'long_help' => 
    array (
      0 => 'autosubscribe',
      1 => 'NULL',
    ),
  ),
  'defaultpreference_maildigest' => 
  array (
    'count' => 5,
    'short' => 'Default preference mail digest',
    'long' => '[[emaildigest]]',
    'long_help' => 
    array (
      0 => 'emaildigest',
      1 => 'NULL',
    ),
  ),
  'defaultpreference_maildisplay' => 
  array (
    'count' => 5,
    'short' => 'Default preference mail display',
    'long' => '[[emaildisplay]]',
    'long_help' => 
    array (
      0 => 'emaildisplay',
      1 => 'NULL',
    ),
  ),
  'defaultpreference_mailformat' => 
  array (
    'count' => 4,
    'short' => 'Default preference mail format',
    'long' => '[[emailformat]]',
    'long_help' => 
    array (
      0 => 'emailformat',
      1 => 'NULL',
    ),
  ),
  'defaultpreference_trackforums' => 
  array (
    'count' => 4,
    'short' => 'Default preference track forums',
    'long' => '[[trackforums]]',
    'long_help' => 
    array (
      0 => 'trackforums',
      1 => 'NULL',
    ),
  ),
  'defaultrequestcategory' => 
  array (
    'count' => 12,
    'short' => 'Default category for course requests',
    'long' => 'Courses requested by users will be automatically placed in this category.',
  ),
  'defaultuserroleid' => 
  array (
    'count' => 29,
    'short' => 'Default role for all users',
    'long' => 'All logged in users will be given the capabilities of the role you specify here, at the site level, in ADDITION to any other roles they may have been given.  The default is the Authenticated user role.  Note that this will not conflict with other roles they have unless you prohibit capabilities, it just ensures that all users have capabilities that are not assignable at the course level (eg post blog entries, manage own calendar, etc).',
  ),
  'deleteincompleteusers' => 
  array (
    'count' => 2,
    'short' => 'Delete incomplete users after',
    'long' => 'After this period, old not fully setup accounts are deleted.',
  ),
  'deleteunconfirmed' => 
  array (
    'count' => 2,
    'short' => 'Delete not fully setup users after',
    'long' => 'If you are using email authentication, this is the period within which a response will be accepted from users.  After this period, old unconfirmed accounts are deleted.',
  ),
  'denyemailaddresses' => 
  array (
    'count' => 4,
    'short' => 'Denied email domains',
    'long' => 'To deny email addresses from particular domains list them here in the same way.  All other domains will be accepted. To deny subdomains add the domain with a preceding \\\'.\\\'. eg <strong>hotmail.com yahoo.co.uk .live.com</strong>',
  ),
  'devicedetectregex' => 
  array (
    'count' => 4,
    'short' => 'Device detection regular expressions',
    'long' => '<p>By default, Moodle can detect devices of the type default (desktop PCs, laptops, etc), mobile (phones and small hand held devices), tablet (iPads, Android tablets) and legacy (Internet Explorer 6 users).  The theme selector can be used to apply separate themes to all of these.  This setting allows regular expressions that allow the detection of extra device types (these take precedence over the default types).</p>
<p>For example, you could enter the regular expression \\\'/(MIDP-1.0|Maemo|Windows CE)/\\\' to detect some commonly used feature phones add the return value \\\'featurephone\\\'.  This adds \\\'featurephone\\\' on the theme selector that would allow you to add a theme that would be used on these devices.  Other phones would still use the theme selected for the mobile device type.</p>',
  ),
  'digestmailtime' => 
  array (
    'count' => 3,
    'short' => 'Hour to send digest emails',
    'long' => 'People who choose to have emails sent to them in digest form will be emailed the digest daily. This setting controls which time of day the daily mail will be sent (the next cron that runs after this hour will send it).',
  ),
  'digestmailtimelast' => 
  array (
    'count' => 4,
    'short' => 'Digest mail time last',
    'long' => '',
  ),
  'directorypermissions' => 
  array (
    'count' => 61,
    'short' => 'Data file permissions',
    'long' => 'The following parameter sets the permissions of new directories
 created by Moodle within the data directory.  The format is in
 octal format (as used by the Unix utility chmod, for example).
 The default is usually OK, but you may want to change it to 0750
 if you are concerned about world-access to the files (you will need
 to make sure the web server process (eg Apache) can access the files.
 NOTE: the prefixed 0 is important, and don\'t use quotes.',
  ),
  'dirroot' => 
  array (
    'count' => 3408,
    'short' => 'Root directory',
    'long' => 'Path to moodles library folder on servers filesystem',
  ),
  'disablebyteserving' => 
  array (
    'count' => 2,
    'short' => 'Disable byte serving',
    'long' => '',
  ),
  'disablegradehistory' => 
  array (
    'count' => 5,
    'short' => 'Disable grade history',
    'long' => 'Disable history tracking of changes in grades related tables. This may speed up the server a little and conserve space in database.',
  ),
  'disablemycourses' => 
  array (
    'count' => 2,
    'short' => 'Disable my courses',
    'long' => '',
  ),
  'disableonclickaddoninstall' => 
  array (
    'count' => 8,
    'short' => 'Disable on click addon install',
    'long' => 'Use the following flag to completely disable the On-click add-on installation
 feature and hide it from the server administration UI.',
  ),
  'disablestatsprocessing' => 
  array (
    'count' => 3,
    'short' => 'Disable stats processing',
    'long' => 'Prevent stats processing and hide the GUI',
  ),
  'disableupdateautodeploy' => 
  array (
    'count' => 5,
    'short' => 'Disable update auto deploy',
    'long' => 'Use the following flag to completely disable the Automatic updates deployment
 feature and hide it from the server administration UI.',
  ),
  'disableupdatenotifications' => 
  array (
    'count' => 12,
    'short' => 'Disable update notifications',
    'long' => 'Use the following flag to completely disable the Available update notifications
 feature and hide it from the server administration UI.',
  ),
  'disableusercreationonrestore' => 
  array (
    'count' => 4,
    'short' => 'Disable user creation on restore',
    'long' => 'Completely disable user creation when restoring a course, bypassing any
 permissions granted via roles and capabilities. Enabling this setting
 results in the restore process stopping when a user attempts to restore a
 course requiring users to be created.',
  ),
  'disableuserimages' => 
  array (
    'count' => 4,
    'short' => 'Disable user profile images',
    'long' => 'Disable the ability for users to change user profile images.',
  ),
  'displayloginfailures' => 
  array (
    'count' => 3,
    'short' => 'Display login failures',
    'long' => 'This will display information to users about previous failed logins.',
  ),
  'divertallemailsto' => 
  array (
    'count' => 4,
    'short' => 'Divert all email',
    'long' => ' Divert all outgoing emails to this address to test and debug emailing features
 $CFG->divertallemailsto = \'root@localhost.local\'; // NOT FOR PRODUCTION SERVERS!',
  ),
  'dndallowtextandlinks' => 
  array (
    'count' => 1,
    'short' => 'Drag and drop upload of text/links',
    'long' => 'Enable or disable the dragging and dropping of text and links onto a course page, alongside the dragging and dropping of files. Note that the dragging of text into Firefox or between different browsers is unreliable and may result in no data being uploaded, or corrupted text being uploaded.',
  ),
  'doclang' => 
  array (
    'count' => 2,
    'short' => 'Language for docs',
    'long' => 'This language will be used in links for the documentation pages.',
  ),
  'docroot' => 
  array (
    'count' => 14,
    'short' => 'Moodle Docs document root',
    'long' => 'Defines the path to the Moodle Docs for providing context-specific documentation via \\\'Moodle Docs for this page\\\' links in the footer of each page. If the field is left blank, links will not be displayed.',
  ),
  'doctonewwindow' => 
  array (
    'count' => 3,
    'short' => 'Open in new window',
    'long' => 'If you enable this, then links to Moodle Docs will be shown in a new window.',
  ),
  'early_install_lang' => 
  array (
    'count' => 11,
    'short' => 'Early install language',
    'long' => '',
  ),
  'earlyprofilingenabled' => 
  array (
    'count' => 6,
    'short' => 'Early profiling',
    'long' => 'Enable earlier profiling that causes more code to be covered
   on every request (db connections, config load, other inits...).
   Requires extra configuration to be defined in config.php like:
   profilingincluded, profilingexcluded, profilingautofrec,
   profilingallowme, profilingallowall, profilinglifetime',
  ),
  'emailchangeconfirmation' => 
  array (
    'count' => 5,
    'short' => 'Email change confirmation',
    'long' => 'Require an email confirmation step when users change their email address in their profile.',
  ),
  'emailconnectionerrorsto' => 
  array (
    'count' => 5,
    'short' => 'Email connection errors',
    'long' => 'Email database connection errors to someone.  If Moodle cannot connect to the
 database, then email this address with a notice.',
  ),
  'emailonlyfromnoreplyaddress' => 
  array (
    'count' => 3,
    'short' => 'Always send email from the no-reply address?',
    'long' => 'If enabled, all email will be sent using the no-reply address as the "from" address. This can be used to stop anti-spoofing controls in external mail systems blocking emails.',
  ),
  'embeddedsoforcelinktarget' => 
  array (
    'count' => 2,
    'short' => 'Embedded so force link target',
    'long' => '',
  ),
  'emoticons' => 
  array (
    'count' => 2,
    'short' => 'Emoticons',
    'long' => 'This form defines the emoticons (or smileys) used at your site. To remove a row from the table, save the form with an empty value in any of the required fields. To register a new emoticon, fill the fields in the last blank row. To reset all the fields into default values, follow the link above.

* Text (required) - This text will be replaced with the emoticon image. It must be at least two characters long.
* Image name (required) - The emoticon image file name without the extension, relative to the component pix folder.
* Image component (required) - The component providing the icon.
* Alternative text (optional) - String identifier and component of the alternative text of the emoticon.',
  ),
  'enableavailability' => 
  array (
    'count' => 59,
    'short' => 'Enable conditional access',
    'long' => 'When enabled, this lets you set conditions (based on date, grade, or completion) that control whether an activity or resource can be accessed.',
  ),
  'enablebadges' => 
  array (
    'count' => 28,
    'short' => 'Enable badges',
    'long' => 'When enabled, this feature lets you create badges and award them to site users.',
  ),
  'enableblogs' => 
  array (
    'count' => 22,
    'short' => 'Enable blogs',
    'long' => 'This switch provides all site users with their own blog.',
  ),
  'enablecalendarexport' => 
  array (
    'count' => 3,
    'short' => 'Enable calendar export',
    'long' => 'Enable exporting or subscribing to calendars.',
  ),
  'enablecompletion' => 
  array (
    'count' => 40,
    'short' => 'Enable completion tracking',
    'long' => 'If enabled, activity completion conditions may be set in the activity settings and/or course completion conditions may be set.',
  ),
  'enablecourserequests' => 
  array (
    'count' => 5,
    'short' => 'Enable course requests',
    'long' => 'This will allow any user to request a course be created.',
  ),
  'enablecssoptimiser' => 
  array (
    'count' => 4,
    'short' => 'Enable CSS optimiser',
    'long' => 'When enabled CSS will be run through an optimisation process before being cached. The optimiser processes the CSS removing duplicate rules and styles, as well as white space removable and reformatting. Please note turning this on at the same time as theme designer mode is awful for performance but will help theme designers create optimised CSS.',
  ),
  'enabledevicedetection' => 
  array (
    'count' => 4,
    'short' => 'Enable device detection',
    'long' => 'Enables detection of mobiles, smartphones, tablets or default devices (desktop PCs, laptops, etc) for the application of themes and other features.',
  ),
  'enablegravatar' => 
  array (
    'count' => 3,
    'short' => 'Enable Gravatar',
    'long' => 'When enabled Moodle will attempt to fetch a user profile picture from Gravatar if the user has not uploaded an image.',
  ),
  'enablegroupmembersonly' => 
  array (
    'count' => 4,
    'short' => 'Enable group members only',
    'long' => '$CFG->enablegroupmembersonly no longer exists.',
  ),
  'enablenotes' => 
  array (
    'count' => 21,
    'short' => 'Enable notes',
    'long' => 'Enable storing of notes about individual users.',
  ),
  'enableoutcomes' => 
  array (
    'count' => 31,
    'short' => 'Enable outcomes',
    'long' => 'Support for Outcomes (also known as Competencies, Goals, Standards or Criteria) means that we can grade things using one or more scales that are tied to outcome statements. Enabling outcomes makes such special grading possible throughout the site.',
  ),
  'enableplagiarism' => 
  array (
    'count' => 21,
    'short' => 'Enable plagiarism plugins',
    'long' => 'This will allow administrators to configure plagiarism plugins (if installed)',
  ),
  'enableportfolios' => 
  array (
    'count' => 25,
    'short' => 'Enable portfolios',
    'long' => 'If enabled, users can export content, such as forum posts and assignment submissions, to external portfolios or HTML pages.',
    'short_help' => 
    array (
      0 => 'enabled',
      1 => 'portfolio',
    ),
    'long_help' => 
    array (
      0 => 'enableddesc',
      1 => 'portfolio',
    ),
  ),
  'enablerssfeeds' => 
  array (
    'count' => 34,
    'short' => 'Enable RSS feeds',
    'long' => 'This switch will enable the possibility of RSS feeds for all glossaries.  You will still need to turn feeds on manually in the settings for each glossary.',
  ),
  'enablesafebrowserintegration' => 
  array (
    'count' => 1,
    'short' => 'Enable Safe Exam Browser integration',
    'long' => 'This adds the choice \\\'Require Safe Exam Browser\\\' to the \\\'Browser security\\\' field on the quiz settings form. See http://www.safeexambrowser.org/ for more information.',
  ),
  'enablestats' => 
  array (
    'count' => 11,
    'short' => 'Enable statistics',
    'long' => 'If you choose \\\'yes\\\' here, Moodle\\\'s cronjob will process the logs and gather some statistics.  Depending on the amount of traffic on your site, this can take awhile. If you enable this, you will be able to see some interesting graphs and statistics about each of your courses, or on a sitewide basis.',
  ),
  'enabletrusttext' => 
  array (
    'count' => 2,
    'short' => 'Enable trusted content',
    'long' => 'By default Moodle will always thoroughly clean text that comes from users to remove any possible bad scripts, media etc that could be a security risk.  The Trusted Content system is a way of giving particular users that you trust the ability to include these advanced features in their content without interference.  To enable this system, you need to first enable this setting, and then grant the Trusted Content permission to a specific Moodle role.  Texts created or uploaded by such users will be marked as trusted and will not be cleaned before display.',
  ),
  'enablewebservices' => 
  array (
    'count' => 13,
    'short' => 'Enable web services',
    'long' => 'Web services enable other systems to log in to this Moodle and perform operations.  For extra security this feature should be disabled unless you are really using it.',
  ),
  'enablewsdocumentation' => 
  array (
    'count' => 3,
    'short' => 'Web services documentation',
    'long' => 'Enable auto-generation of web services documentation. A user can access to his own documentation on his security keys page {$a}. It displays the documentation for the enabled protocols only.',
  ),
  'enrol_plugins_enabled' => 
  array (
    'count' => 13,
    'short' => 'Enrol plugins enabled',
    'long' => '',
  ),
  'errordocroot' => 
  array (
    'count' => 2,
    'short' => 'Error doc root',
    'long' => '',
  ),
  'extendedusernamechars' => 
  array (
    'count' => 7,
    'short' => 'Allow extended characters in usernames',
    'long' => 'Enable this setting to allow students to use any characters in their usernames (note this does not affect their actual names).  The default is "false" which restricts usernames to be alphanumeric lowercase characters, underscore (_), hyphen (-), period (.) or at symbol (@).',
  ),
  'externalblogcrontime' => 
  array (
    'count' => 1,
    'short' => 'External blog cron schedule',
    'long' => 'How often Moodle checks the external blogs for new entries.',
  ),
  'extramemorylimit' => 
  array (
    'count' => 8,
    'short' => 'Extra PHP memory limit',
    'long' => 'Some scripts like search, backup/restore or cron require more memory. Set higher values for large sites.',
  ),
  'feedback_allowfullanonymous' => 
  array (
    'count' => 8,
    'short' => 'Allow full anonymous',
    'long' => 'If set to \\\'yes\\\', users can complete a feedback activity on the front page without being required to log in.',
  ),
  'file_lock_root' => 
  array (
    'count' => 4,
    'short' => 'File lock root ',
    'long' => '',
  ),
  'filedir' => 
  array (
    'count' => 4,
    'short' => 'File dir',
    'long' => 'for custom $CFG->filedir locations',
  ),
  'filelifetime' => 
  array (
    'count' => 15,
    'short' => 'File lifetime',
    'long' => 'Seconds for files to remain in caches. Decrease this if you are worried
 about students being served outdated versions of uploaded files.',
  ),
  'filepermissions' => 
  array (
    'count' => 33,
    'short' => 'File permissions',
    'long' => '',
  ),
  'fileslastcleanup' => 
  array (
    'count' => 3,
    'short' => 'files last cleanup',
    'long' => 'if you want to disable purging of trash put $CFG->fileslastcleanup=time(); into config.php
',
  ),
  'filesrootrecordsfixed' => 
  array (
    'count' => 1,
    'short' => 'Files root records fixed',
    'long' => '',
  ),
  'filter_censor_badwords' => 
  array (
    'count' => 3,
    'short' => 'Filter censor bad words',
    'long' => 'Custom bad words list',
    'long_help' => 
    array (
      0 => 'badwordslist',
      1 => 'admin',
    ),
  ),
  'filter_mediaplugin_enable_flv' => 
  array (
    'count' => 1,
    'short' => 'Filter media plugin enable FLV',
    'long' => '',
  ),
  'filter_mediaplugin_enable_html5audio' => 
  array (
    'count' => 1,
    'short' => 'Filter media plugin enable HTML5 audio',
    'long' => '',
  ),
  'filter_mediaplugin_enable_html5video' => 
  array (
    'count' => 1,
    'short' => 'Filter media plugin enable HTML5 video',
    'long' => '',
  ),
  'filter_mediaplugin_enable_mp3' => 
  array (
    'count' => 1,
    'short' => 'Filter media plugin enable MP3',
    'long' => '',
  ),
  'filter_mediaplugin_enable_qt' => 
  array (
    'count' => 1,
    'short' => 'Filter media plugin enable GT',
    'long' => '',
  ),
  'filter_mediaplugin_enable_rm' => 
  array (
    'count' => 1,
    'short' => 'Filter media plugin enable RM',
    'long' => '',
  ),
  'filter_mediaplugin_enable_swf' => 
  array (
    'count' => 2,
    'short' => 'Filter media plugin enable SWF',
    'long' => '',
  ),
  'filter_mediaplugin_enable_vimeo' => 
  array (
    'count' => 1,
    'short' => 'Filter media plugin enable vimeo',
    'long' => '',
  ),
  'filter_mediaplugin_enable_wmp' => 
  array (
    'count' => 1,
    'short' => 'Filter media plugin enable windows media player',
    'long' => '',
  ),
  'filter_mediaplugin_enable_youtube' => 
  array (
    'count' => 1,
    'short' => 'Filter media plugin enable youtube',
    'long' => '',
  ),
  'filter_multilang_converted' => 
  array (
    'count' => 2,
    'short' => 'Filter multilang converted',
    'long' => 'Multilang upgrade',
    'long_help' => 
    array (
      0 => 'pluginname',
      1 => 'tool_multilangupgrade',
    ),
  ),
  'filter_multilang_force_old' => 
  array (
    'count' => 1,
    'short' => 'Filter multilang force old',
    'long' => '',
  ),
  'filterall' => 
  array (
    'count' => 11,
    'short' => 'Filter all strings',
    'long' => 'Filter all strings, including headings, titles, navigation bar and so on.  This is mostly useful when using the multilang filter, otherwise it will just create extra load on your site for little gain.',
  ),
  'filtermatchoneperpage' => 
  array (
    'count' => 5,
    'short' => 'Filter match once per page',
    'long' => 'Automatic linking filters will only generate a single link for the first matching text instance found on the complete page. All others are ignored.',
  ),
  'filtermatchonepertext' => 
  array (
    'count' => 1,
    'short' => 'Filter match once per text',
    'long' => 'Automatic linking filters will only generate a single link for the first matching text instance found in each item of text (e.g., resource, block) on the page. All others are ignored. This setting is ignored if the one per page setting is <i>yes</i>.',
  ),
  'filteruploadedfiles' => 
  array (
    'count' => 1,
    'short' => 'Filter uploaded files',
    'long' => 'Process all uploaded HTML and text files with the filters before displaying them, only uploaded HTML files or none at all.',
  ),
  'forced_plugin_settings' => 
  array (
    'count' => 16,
    'short' => 'Forced plugin settings',
    'long' => ' Plugin settings have to be put into a special array.
 Example:
   $CFG->forced_plugin_settings = array(\'pluginname\'  => array(\'settingname\' => \'value\', \'secondsetting\' => \'othervalue\'),
                                        \'otherplugin\' => array(\'mysetting\' => \'myvalue\', \'thesetting\' => \'thevalue\'));
 Module default settings with advanced/locked checkboxes can be set too. To do this, add
 an extra config with \'_adv\' or \'_locked\' as a suffix and set the value to true or false.
 Example:
   $CFG->forced_plugin_settings = array(\'pluginname\'  => array(\'settingname\' => \'value\', \'settingname_locked\' => true, \'settingname_adv\' => true));
',
  ),
  'forcedefaultmymoodle' => 
  array (
    'count' => 2,
    'short' => 'Force default my moodle',
    'long' => '',
  ),
  'forcedifferentsitecheckingusersonrestore' => 
  array (
    'count' => 3,
    'short' => 'Force different site checking users on restore',
    'long' => 'Modify the restore process in order to force the "user checks" to assume
 that the backup originated from a different site, so detection of matching
 users is performed with different (more "relaxed") rules. Note that this is
 only useful if the backup file has been created using Moodle < 1.9.4 and the
 site has been rebuilt from scratch using backup files (not the best way btw).
 If you obtain user conflicts on restore, rather than enabling this setting
 permanently, try restoring the backup on a different site, back it up again
 and then restore on the target server.',
  ),
  'forcefirstname' => 
  array (
    'count' => 4,
    'short' => 'Force displayed firstnames',
    'long' => 'A little hack to anonymise user names for all students.  If you set these
   then all non-teachers will always see these for every person.',
  ),
  'forcelastname' => 
  array (
    'count' => 4,
    'short' => 'Force displayed lastnames',
    'long' => 'A little hack to anonymise user names for all students.  If you set these
   then all non-teachers will always see these for every person.',
  ),
  'forcelogin' => 
  array (
    'count' => 34,
    'short' => 'Force users to log in',
    'long' => 'Normally, the front page of the site and the course listings (but not courses) can be read by people without logging in to the site.  If you want to force people to log in before they do ANYTHING on the site, then you should enable this setting.',
  ),
  'forceloginforprofileimage' => 
  array (
    'count' => 4,
    'short' => 'Force users to log in to view user pictures',
    'long' => 'If enabled, users must log in in order to view user profile pictures and the default user picture will be used in all notification emails.',
  ),
  'forceloginforprofiles' => 
  array (
    'count' => 10,
    'short' => 'Force users to log in for profiles',
    'long' => 'This setting forces people to log in as a real (non-guest) account before viewing any user\\\'s profile. If you disabled this setting, you may find that some users post advertising (spam) or other inappropriate content in their profiles, which is then visible to the whole world.',
  ),
  'forcetimezone' => 
  array (
    'count' => 30,
    'short' => 'Force time zone',
    'long' => 'You can allow users to individually select their timezone, or force a timezone for everyone.',
  ),
  'forgottenpasswordurl' => 
  array (
    'count' => 2,
    'short' => '',
    'long' => 'Forgotten password URL',
    'long_help' => 
    array (
      0 => 'forgottenpasswordurl',
      1 => 'auth',
    ),
  ),
  'format_plugins_sortorder' => 
  array (
    'count' => 2,
    'short' => 'Format plugins sort order',
    'long' => '',
  ),
  'formatstringstriptags' => 
  array (
    'count' => 23,
    'short' => 'Remove HTML tags from all activity names',
    'long' => 'Uncheck this setting to allow HTML tags in activity and resource names.',
    'short_help' => 
    array (
      0 => 'stripalltitletags',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'configstripalltitletags',
      1 => 'admin',
    ),
  ),
  'forum_allowforcedreadtracking' => 
  array (
    'count' => 26,
    'short' => 'Forum allow forced read tracking',
    'long' => 'Allow forced read tracking',
    'long_help' => 
    array (
      0 => 'forcedreadtracking',
      1 => 'forum',
    ),
  ),
  'forum_displaymode' => 
  array (
    'count' => 4,
    'short' => 'Display mode for the options',
    'long' => 'The default display mode for discussions if one isn\\\'t set.',
  ),
  'forum_enablerssfeeds' => 
  array (
    'count' => 12,
    'short' => 'Enable RSS feeds',
    'long' => 'This switch will enable the possibility of RSS feeds for all glossaries.  You will still need to turn feeds on manually in the settings for each glossary.',
  ),
  'forum_enabletimedposts' => 
  array (
    'count' => 22,
    'short' => 'Enable timed posts on forum',
    'long' => 'Timed posts',
    'long_help' => 
    array (
      0 => 'timedposts',
      1 => 'forum',
    ),
  ),
  'forum_lastreadclean' => 
  array (
    'count' => 2,
    'short' => 'Forum last read clean',
    'long' => '',
  ),
  'forum_longpost' => 
  array (
    'count' => 2,
    'short' => 'Long post',
    'long' => 'Any post over this length (in characters not including HTML) is considered long. Posts displayed on the site front page, social format course pages, or user profiles are shortened to a natural break somewhere between the forum_shortpost and forum_longpost values.',
  ),
  'forum_manydiscussions' => 
  array (
    'count' => 4,
    'short' => 'Discussions per page',
    'long' => 'Maximum number of discussions shown in a forum per page',
  ),
  'forum_maxattachments' => 
  array (
    'count' => 1,
    'short' => 'Maximum number of attachments',
    'long' => 'Default maximum number of attachments allowed per post.',
  ),
  'forum_maxbytes' => 
  array (
    'count' => 4,
    'short' => 'Maximum embedded file size (bytes)',
    'long' => 'Default maximum submission file size for all workshops on the site (subject to course limits and other local settings)',
  ),
  'forum_oldpostdays' => 
  array (
    'count' => 19,
    'short' => 'Read after days',
    'long' => 'Number of days old any post is considered read.',
  ),
  'forum_replytouser' => 
  array (
    'count' => 1,
    'short' => 'Use email address in reply',
    'long' => 'When a forum post is mailed out, should it contain the user\\\'s email address so that recipients can reply personally rather than via the forum? Even if set to \\\'Yes\\\' users can choose in their profile to keep their email address secret.',
  ),
  'forum_rssarticles' => 
  array (
    'count' => 2,
    'short' => 'Number of RSS recent articles',
    'long' => 'This setting specifies the number of glossary entry concepts to include in the RSS feed. Between 5 and 20 generally acceptable.',
  ),
  'forum_rsstype' => 
  array (
    'count' => 2,
    'short' => 'RSS feed for this activity',
    'long' => 'To enable the RSS feed for this activity, select either concepts with author or concepts without author to be included in the feed.',
  ),
  'forum_shortpost' => 
  array (
    'count' => 5,
    'short' => 'Short post',
    'long' => 'Any post under this length (in characters not including HTML) is considered short (see below).',
  ),
  'forum_trackingtype' => 
  array (
    'count' => 1,
    'short' => 'Read tracking',
    'long' => 'Default setting for read tracking.',
  ),
  'forum_trackreadposts' => 
  array (
    'count' => 6,
    'short' => 'Track read posts on forum',
    'long' => 'Track unread posts',
    'long_help' => 
    array (
      0 => 'trackforum',
      1 => 'forum',
    ),
  ),
  'forum_usermarksread' => 
  array (
    'count' => 5,
    'short' => 'Manual message read marking',
    'long' => 'If \\\'yes\\\', the user must manually mark a post as read. If \\\'no\\\', when the post is viewed it is marked as read.',
  ),
  'frontpage' => 
  array (
    'count' => 2,
    'short' => 'Front page',
    'long' => 'The items selected above will be displayed on the site\\\'s front page.',
  ),
  'frontpagecourselimit' => 
  array (
    'count' => 4,
    'short' => 'Maximum number of courses',
    'long' => 'Maximum number of courses to be displayed on the site\'s front page in course listings.',
    'short_help' => 
    array (
      0 => 'configfrontpagecourselimit',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'configfrontpagecourselimithelp',
      1 => 'admin',
    ),
  ),
  'frontpageloggedin' => 
  array (
    'count' => 3,
    'short' => 'Front page items when logged in',
    'long' => 'The items selected above will be displayed on the site\\\'s front page when a user is logged in.',
  ),
  'fullnamedisplay' => 
  array (
    'count' => 42,
    'short' => '{$a->firstname} {$a->lastname}',
    'long' => 'This defines how names are shown when they are displayed in full. The default value, "language", leaves it to the string "fullnamedisplay" in the current language pack to decide. Some languages have different name display conventions.

For most mono-lingual sites the most efficient setting is "firstname lastname", but you may choose to hide surnames altogether. Placeholders that can be used are: firstname, lastname, firstnamephonetic, lastnamephonetic, middlename, and alternatename.',
  ),
  'gdversion' => 
  array (
    'count' => 1,
    'short' => 'GD version',
    'long' => '',
  ),
  'geoipfile' => 
  array (
    'count' => 5,
    'short' => 'GeoIP city data file',
    'long' => 'Location of GeoIP City binary data file. This file is not part of Moodle distribution and must be obtained separately from <a href="http://www.maxmind.com/">MaxMind</a>. You can either buy a commercial version or use the free version. Simply download <a href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz" >http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz</a> and extract it into "{$a}" directory on your server.',
  ),
  'getremoteaddrconf' => 
  array (
    'count' => 2,
    'short' => 'Logged IP address source',
    'long' => 'If your server is behind a reverse proxy, you can use this setting to specify which HTTP headers can be trusted to contain the remote IP address. The headers are read in order, using the first one that is available.',
  ),
  'glossary_allowcomments' => 
  array (
    'count' => 3,
    'short' => 'Allow comments on entries',
    'long' => 'If enabled, all participants with permission to create comments will be able to add comments to glossary entries.',
  ),
  'glossary_casesensitive' => 
  array (
    'count' => 4,
    'short' => 'This entry is case sensitive',
    'long' => 'This setting specifies whether matching exact upper and lower case is necessary when auto-linking to an entry.',
  ),
  'glossary_defaultapproval' => 
  array (
    'count' => 3,
    'short' => 'Approved by default',
    'long' => 'If set to no, entries require approving by a teacher before they are viewable by everyone.',
  ),
  'glossary_dupentries' => 
  array (
    'count' => 3,
    'short' => 'Glossary duplicate entries',
    'long' => 'Duplicate entries allowed',
    'long_help' => 
    array (
      0 => 'allowduplicatedentries',
      1 => 'glossary',
    ),
  ),
  'glossary_enablerssfeeds' => 
  array (
    'count' => 7,
    'short' => 'Enable RSS feeds',
    'long' => 'This switch will enable the possibility of RSS feeds for all glossaries.  You will still need to turn feeds on manually in the settings for each glossary.',
  ),
  'glossary_entbypage' => 
  array (
    'count' => 7,
    'short' => 'Glossary enteries by page',
    'long' => 'Entries shown per page',
    'long_help' => 
    array (
      0 => 'entbypage',
      1 => 'glossary',
    ),
  ),
  'glossary_fullmatch' => 
  array (
    'count' => 4,
    'short' => 'Match whole words only',
    'long' => 'This setting specifies whether only whole words will be linked, for example, a glossary entry named "construct" will not create a link inside the word "constructivism".',
  ),
  'glossary_linkbydefault' => 
  array (
    'count' => 4,
    'short' => 'Glossary link by default',
    'long' => 'Automatically link glossary entries',
    'long_help' => 
    array (
      0 => 'usedynalink',
      1 => 'glossary',
    ),
  ),
  'glossary_linkentries' => 
  array (
    'count' => 7,
    'short' => 'Glossary link entries',
    'long' => 'Automatically link glossary entries',
    'long_help' => 
    array (
      0 => 'usedynalink',
      1 => 'glossary',
    ),
  ),
  'googlemapkey3' => 
  array (
    'count' => 3,
    'short' => 'Google Maps API V3 key',
    'long' => 'You need to enter a special key to use Google Maps for IP address lookup visualization. You can obtain the key free of charge at <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank">https://developers.google.com/maps/documentation/javascript/tutorial#api_key</a>',
  ),
  'grade_aggregateonlygraded' => 
  array (
    'count' => 1,
    'short' => 'Exclude empty grades',
    'long' => 'An empty grade is a grade which is missing from the gradebook. It may be from an assignment submission which has not yet been graded or from a quiz which has not yet been attempted etc.

This setting determines whether empty grades are not included in the aggregation or are counted as minimal grades, for example 0 for an assignment graded between 0 and 100.',
  ),
  'grade_aggregateonlygraded_flag' => 
  array (
    'count' => 1,
    'short' => 'Grade aggregate only graded flag',
    'long' => '',
  ),
  'grade_aggregateoutcomes' => 
  array (
    'count' => 1,
    'short' => 'Include outcomes in aggregation',
    'long' => 'If enabled, outcomes are included in the aggregation. This may result in an unexpected category total.',
  ),
  'grade_aggregateoutcomes_flag' => 
  array (
    'count' => 1,
    'short' => 'Grade aggregate out comes flag',
    'long' => '',
  ),
  'grade_aggregation' => 
  array (
    'count' => 1,
    'short' => 'Grades aggregation',
    'long' => 'The aggregation determines how grades in a category are combined, such as

* Mean of grades - The sum of all grades divided by the total number of grades
* Median of grades - The middle grade when grades are arranged in order of size
* Lowest grade
* Highest grade
* Mode of grades - The grade that occurs the most frequently
* Natural - The sum of all grade values scaled by weight',
  ),
  'grade_aggregation_flag' => 
  array (
    'count' => 1,
    'short' => 'Grade aggregation flag',
    'long' => '',
  ),
  'grade_aggregationposition' => 
  array (
    'count' => 7,
    'short' => 'Aggregation position',
    'long' => 'This setting determines whether the category and course total columns are displayed first or last in the gradebook reports.',
  ),
  'grade_aggregations_visible' => 
  array (
    'count' => 2,
    'short' => 'Grade aggregations visible',
    'long' => 'Available aggregation types',
    'long_help' => 
    array (
      0 => 'aggregationsvisible',
      1 => 'grades',
    ),
  ),
  'grade_decimalpoints' => 
  array (
    'count' => 6,
    'short' => 'Decimal points',
    'long' => 'This setting determines the number of decimal points to display for each grade. It has no effect on grade calculations, which are made with an accuracy of 5 decimal places.',
  ),
  'grade_displaytype' => 
  array (
    'count' => 4,
    'short' => 'Grade display type',
    'long' => 'Grade display type',
    'long_help' => 
    array (
      0 => 'gradedisplaytype',
      1 => 'grades',
    ),
  ),
  'grade_droplow' => 
  array (
    'count' => 2,
    'short' => 'Drop the lowest',
    'long' => 'This setting enables a specified number of the lowest grades to be excluded from the aggregation.',
  ),
  'grade_droplow_flag' => 
  array (
    'count' => 1,
    'short' => 'Grade drop low flag',
    'long' => '',
  ),
  'grade_export_customprofilefields' => 
  array (
    'count' => 2,
    'short' => 'Grade export custom profile fields',
    'long' => 'Include these custom profile fields in the grade export, separated by commas.',
    'short_help' => 
    array (
      0 => 'gradeexportcustomprofilefields',
      1 => 'grades',
    ),
    'long_help' => 
    array (
      0 => 'gradeexportcustomprofilefields_desc',
      1 => 'grades',
    ),
  ),
  'grade_export_decimalpoints' => 
  array (
    'count' => 6,
    'short' => 'Grade export decimal points',
    'long' => 'Grade export decimal points',
    'long_help' => 
    array (
      0 => 'gradeexportdecimalpoints',
      1 => 'grades',
    ),
  ),
  'grade_export_displaytype' => 
  array (
    'count' => 12,
    'short' => 'Grade export display type',
    'long' => 'Grade export display type',
    'long_help' => 
    array (
      0 => 'gradeexportdisplaytype',
      1 => 'grades',
    ),
  ),
  'grade_export_userprofilefields' => 
  array (
    'count' => 1,
    'short' => 'Grade export user profile fields',
    'long' => 'Include these user profile fields in the grade export, separated by commas.',
    'short_help' => 
    array (
      0 => 'gradeexportuserprofilefields',
      1 => 'grades',
    ),
    'long_help' => 
    array (
      0 => 'gradeexportuserprofilefields_desc',
      1 => 'grades',
    ),
  ),
  'grade_hiddenasdate' => 
  array (
    'count' => 2,
    'short' => 'Show submitted date for hidden grades',
    'long' => 'If user can not see hidden grades show date of submission instead of \\\'-\\\'.',
  ),
  'grade_hideforcedsettings' => 
  array (
    'count' => 1,
    'short' => 'Hide forced settings',
    'long' => 'Do not show forced settings in grading UI.',
  ),
  'grade_includescalesinaggregation' => 
  array (
    'count' => 14,
    'short' => 'Include scales in aggregation',
    'long' => 'You can change whether scales are to be included as numbers in all aggregated grades across all gradebooks in all courses. CAUTION: changing this setting will force all aggregated grades to be recalculated.',
  ),
  'grade_item_advanced' => 
  array (
    'count' => 6,
    'short' => 'Advanced grade item options',
    'long' => 'Select all elements that should be displayed as advanced when editing grade items.',
    'short_help' => 
    array (
      0 => 'gradeitemadvanced',
      1 => 'grades',
    ),
    'long_help' => 
    array (
      0 => 'gradeitemadvanced_help',
      1 => 'grades',
    ),
  ),
  'grade_keephigh' => 
  array (
    'count' => 2,
    'short' => 'Keep the highest',
    'long' => 'If set, this option will only keep the X highest grades, X being the selected value for this option.',
  ),
  'grade_keephigh_flag' => 
  array (
    'count' => 1,
    'short' => 'Grade keep high flag',
    'long' => '',
  ),
  'grade_minmaxtouse' => 
  array (
    'count' => 38,
    'short' => 'Min and max grades used in calculation',
    'long' => 'This setting determines whether to use the initial minimum and maximum grades from when the grade was given, or the minimum and maximum grades as specified in the settings for the grade item, when calculating the grade displayed in the gradebook.',
  ),
  'grade_mygrades_report' => 
  array (
    'count' => 6,
    'short' => 'Grade my grades report',
    'long' => '[[mygrades]]',
    'long_help' => 
    array (
      0 => 'mygrades',
      1 => 'grade',
    ),
  ),
  'grade_navmethod' => 
  array (
    'count' => 5,
    'short' => 'Navigation method',
    'long' => 'In Free navigation, questions may be answered in any order using navigation. In Sequential, questions must be answered in strict sequence.',
  ),
  'grade_overridecat' => 
  array (
    'count' => 1,
    'short' => 'Allow category grades to be manually overridden',
    'long' => 'Disabling this setting makes it impossible for users to override category grades.',
  ),
  'grade_profilereport' => 
  array (
    'count' => 9,
    'short' => 'User profile report',
    'long' => 'Grade report used on user profile page.',
  ),
  'grade_report_overview_showrank' => 
  array (
    'count' => 2,
    'short' => 'Grade report overview show rank',
    'long' => 'Show rank',
    'long_help' => 
    array (
      0 => 'showrank',
      1 => 'grades',
    ),
  ),
  'grade_report_overview_showtotalsifcontainhidden' => 
  array (
    'count' => 2,
    'short' => 'Grade report overview show totals if contain hidden',
    'long' => '',
  ),
  'grade_report_showonlyactiveenrol' => 
  array (
    'count' => 10,
    'short' => 'Show only active enrol',
    'long' => 'Shows only active enrols on grade report',
    'long_help' => 
    array (
      0 => 'showonlyactiveenrol',
      1 => 'grade',
    ),
  ),
  'grade_report_showuserimage' => 
  array (
    'count' => 4,
    'short' => 'Show user image',
    'long' => 'Shows user image on grade report',
    'long_help' => 
    array (
      0 => 'showuserimage',
      1 => 'grade',
    ),
  ),
  'grade_report_user_rangedecimals' => 
  array (
    'count' => 3,
    'short' => 'Grade report user range decimals',
    'long' => 'Range decimal points',
    'long_help' => 
    array (
      0 => 'rangedecimals',
      1 => 'grades',
    ),
  ),
  'grade_report_user_showaverage' => 
  array (
    'count' => 2,
    'short' => 'Show average',
    'long' => 'Show the average column? Students may be able to estimate other student\'s grades if the average is calculated from a small number of grades. For performance reasons the average is approximate if it is dependent on any hidden items.',
    'short_help' => 
    array (
      0 => 'showaverage',
      1 => 'grades',
    ),
    'long_help' => 
    array (
      0 => 'showaverage_help',
      1 => 'grades',
    ),
  ),
  'grade_report_user_showcontributiontocoursetotal' => 
  array (
    'count' => 3,
    'short' => 'Grade report user show contribution to course total',
    'long' => 'Default ({$a})',
    'long_help' => 
    array (
      0 => 'defaultprev',
      1 => 'grades',
    ),
  ),
  'grade_report_user_showfeedback' => 
  array (
    'count' => 2,
    'short' => 'Show feedback',
    'long' => 'Show the feedback column?',
    'short_help' => 
    array (
      0 => 'showfeedback',
      1 => 'grades',
    ),
    'long_help' => 
    array (
      0 => 'showfeedback_help',
      1 => 'grades',
    ),
  ),
  'grade_report_user_showgrade' => 
  array (
    'count' => 2,
    'short' => 'Show grades',
    'long' => 'Show the grade column?',
    'short_help' => 
    array (
      0 => 'showgrade',
      1 => 'grades',
    ),
    'long_help' => 
    array (
      0 => 'showgrade_help',
      1 => 'grades',
    ),
  ),
  'grade_report_user_showhiddenitems' => 
  array (
    'count' => 3,
    'short' => 'Show hidden items',
    'long' => 'Whether hidden grade items are hidden entirely or if the names of hidden grade items are visible to students.

* Show hidden - Hidden grade item names are shown but student grades are hidden
* Only hidden until - Grade items with a "hide until" date set are hidden completely until the set date, after which the whole item is shown
* Do not show - Hidden grade items are completely hidden',
    'short_help' => 
    array (
      0 => 'showhiddenitems',
      1 => 'grades',
    ),
    'long_help' => 
    array (
      0 => 'showhiddenitems_help',
      1 => 'grades',
    ),
  ),
  'grade_report_user_showlettergrade' => 
  array (
    'count' => 2,
    'short' => 'Show letter grades',
    'long' => 'Show the letter grade column?',
    'short_help' => 
    array (
      0 => 'showlettergrade',
      1 => 'grades',
    ),
    'long_help' => 
    array (
      0 => 'showlettergrade_help',
      1 => 'grades',
    ),
  ),
  'grade_report_user_showpercentage' => 
  array (
    'count' => 2,
    'short' => 'Show percentage',
    'long' => 'Show the percentage value of each grade item?',
    'short_help' => 
    array (
      0 => 'showpercentage',
      1 => 'grades',
    ),
    'long_help' => 
    array (
      0 => 'showpercentage_help',
      1 => 'grades',
    ),
  ),
  'grade_report_user_showrange' => 
  array (
    'count' => 2,
    'short' => 'Show ranges',
    'long' => 'Show the range column?',
    'short_help' => 
    array (
      0 => 'showrange',
      1 => 'grades',
    ),
    'long_help' => 
    array (
      0 => 'showrange_help',
      1 => 'grades',
    ),
  ),
  'grade_report_user_showrank' => 
  array (
    'count' => 2,
    'short' => 'Show rank',
    'long' => 'Show the position of the student in relation to the rest of the class for each grade item?',
    'short_help' => 
    array (
      0 => 'showrank',
      1 => 'grades',
    ),
    'long_help' => 
    array (
      0 => 'showrank_help',
      1 => 'grades',
    ),
  ),
  'grade_report_user_showtotalsifcontainhidden' => 
  array (
    'count' => 3,
    'short' => 'Grade report user show totals if contain hidden',
    'long' => 'Hide totals if they contain hidden items',
    'long_help' => 
    array (
      0 => 'hidetotalifhiddenitems',
      1 => 'grades',
    ),
  ),
  'grade_report_user_showweight' => 
  array (
    'count' => 2,
    'short' => 'Grade report user show weight',
    'long' => '',
  ),
  'gradebookroles' => 
  array (
    'count' => 7,
    'short' => 'Graded roles',
    'long' => 'This setting allows you to control who appears on the gradebook.  Users need to have at least one of these roles in a course to be shown in the gradebook for that course.',
  ),
  'gradeexport' => 
  array (
    'count' => 1,
    'short' => 'Primary grade export methods',
    'long' => 'Choose which gradebook export formats are your primary methods for exporting grades.  Chosen plugins will then set and use a "last exported" field for every grade.  For example, this might result in exported records being identified as being "new" or "updated".  If you are not sure about this then leave everything unchecked.',
  ),
  'gradehistorylifetime' => 
  array (
    'count' => 2,
    'short' => 'Grade history lifetime',
    'long' => 'This specifies the length of time you want to keep history of changes in grade related tables. It is recommended to keep it as long as possible. If you experience performance problems or have limited database space, try to set lower value.',
  ),
  'gradeoverhundredprocentmax' => 
  array (
    'count' => 2,
    'short' => 'Grade over hundred procent max',
    'long' => '',
  ),
  'gradepointdefault' => 
  array (
    'count' => 3,
    'short' => 'Grade point default',
    'long' => 'This setting determines the default value for the grade point value available in an activity.',
  ),
  'gradepointmax' => 
  array (
    'count' => 3,
    'short' => 'Grade point maximum',
    'long' => 'This setting determines the maximum grade point value available in an activity.',
  ),
  'gradepublishing' => 
  array (
    'count' => 28,
    'short' => 'Enable publishing',
    'long' => 'Enable publishing in exports and imports: Exported grades can be accessed by accessing a URL, without having to log on to a Moodle site. Grades can be imported by accessing such a URL (which means that a Moodle site can import grades published by another site). By default only administrators may use this feature, please educate users before adding required capabilities to other roles (dangers of bookmark sharing and download accelerators, IP restrictions, etc.).',
  ),
  'gradereport_mygradeurl' => 
  array (
    'count' => 2,
    'short' => 'Grade my grade URL',
    'long' => '[[externalurl]]',
    'long_help' => 
    array (
      0 => 'externalurl',
      1 => 'grade',
    ),
  ),
  'gravatardefaulturl' => 
  array (
    'count' => 3,
    'short' => 'Gravatar default image URL',
    'long' => 'Gravatar needs a default image to display if it is unable to find a picture for a given user. Provide a full URL for an image. If you leave this setting empty, Moodle will attempt to use the most appropriate default image for the page you are viewing. Note also that Gravatar has a number of codes which can be used to <a href="https://en.gravatar.com/site/implement/images/#default-image">generate default images</a>.',
  ),
  'groupenrolmentkeypolicy' => 
  array (
    'count' => 2,
    'short' => 'Group enrolment key policy',
    'long' => 'Turning this on will make Moodle check group enrolment keys against a valid password policy.',
  ),
  'guestloginbutton' => 
  array (
    'count' => 5,
    'short' => 'Guest login button',
    'long' => 'Guest login button',
    'long_help' => 
    array (
      0 => 'guestloginbutton',
      1 => 'auth',
    ),
  ),
  'guestroleid' => 
  array (
    'count' => 8,
    'short' => 'Role for guest',
    'long' => 'This role is automatically assigned to the guest user. It is also temporarily assigned to not enrolled users that enter the course via guest enrolment plugin.',
  ),
  'handlebounces' => 
  array (
    'count' => 4,
    'short' => 'Handle bounces',
    'long' => 'The following line is for handling email bounces',
  ),
  'hiddenuserfields' => 
  array (
    'count' => 9,
    'short' => 'Hide user fields',
    'long' => 'Select which user information fields you wish to hide from other users other than course teachers/admins. This will increase student privacy. Hold CTRL key to select multiple fields.',
  ),
  'httpswwwroot' => 
  array (
    'count' => 106,
    'short' => 'HTPPS www root',
    'long' => 'Set httpswwwroot default value (this variable will replace $CFG->wwwroot inside some URLs used in HTTPSPAGEREQUIRED pages.)',
  ),
  'includeuserpasswordsinbackup' => 
  array (
    'count' => 3,
    'short' => 'Include user passwords in backup',
    'long' => 'Allow user passwords to be included in backup files. Very dangerous
 setting as far as it publishes password hashes that can be unencrypted
 if the backup file is publicy available. Use it only if you can guarantee
 that all your backup files remain only privacy available and are never
 shared out from your site/institution!',
  ),
  'iplookup' => 
  array (
    'count' => 1,
    'short' => 'IP address lookup',
    'long' => 'When you click on an IP address (such as 34.12.222.93), such as in the logs, you are shown a map with a best guess of where that IP is located.  There are different plugins for this that you can choose from, each has benefits and disadvantages.',
  ),
  'jabberhost' => 
  array (
    'count' => 2,
    'short' => 'Jabber host',
    'long' => 'The server to connect to to send jabber message notifications',
  ),
  'jabberpassword' => 
  array (
    'count' => 2,
    'short' => 'Jabber password',
    'long' => 'The password to use when connecting to the Jabber server',
  ),
  'jabberport' => 
  array (
    'count' => 2,
    'short' => 'Jabber port',
    'long' => 'The port to use when connecting to the Jabber server',
  ),
  'jabberserver' => 
  array (
    'count' => 1,
    'short' => 'Jabber server',
    'long' => 'XMPP host ID (can be left empty if the same as Jabber host)',
  ),
  'jabberusername' => 
  array (
    'count' => 2,
    'short' => 'Jabber user name',
    'long' => 'The user name to use when connecting to the Jabber server',
  ),
  'jsrev' => 
  array (
    'count' => 11,
    'short' => 'JS rev',
    'long' => 'When jsrev is positive, the function is minified and stored in a MUC cache for subsequent uses',
  ),
  'keeptagnamecase' => 
  array (
    'count' => 2,
    'short' => 'Keep tag name casing',
    'long' => 'Check this if you want tag names to keep the original casing as entered by users who created them',
  ),
  'keeptempdirectoriesonbackup' => 
  array (
    'count' => 25,
    'short' => 'Keep temp directories on backup',
    'long' => ' Keep the temporary directories used by backup and restore without being
 deleted at the end of the process. Use it if you want to debug / view
 all the information stored there after the process has ended. Note that
 those directories may be deleted (after some ttl) both by cron and / or
 by new backup / restore invocations.',
  ),
  'lang' => 
  array (
    'count' => 55,
    'short' => 'Lang',
    'long' => 'Choose a default language for the whole site. Users can override this setting using the language menu or the setting in their personal profile.',
  ),
  'langlist' => 
  array (
    'count' => 2,
    'short' => 'Languages on language menu',
    'long' => 'Leave this blank to allow users to choose from any language you have in this installation of Moodle.  However, you can shorten the language menu by entering a comma-separated list of language codes that you want.  For example:  en,es_es,fr,it',
  ),
  'langlocalroot' => 
  array (
    'count' => 9,
    'short' => 'Language local root',
    'long' => '',
  ),
  'langmenu' => 
  array (
    'count' => 2,
    'short' => 'Display language menu',
    'long' => 'Choose whether or not you want to display the general-purpose language menu on the home page, login page etc.  This does not affect the user\\\'s ability to set the preferred language in their own profile.',
  ),
  'langotherroot' => 
  array (
    'count' => 13,
    'short' => 'Language other root',
    'long' => '',
  ),
  'langrev' => 
  array (
    'count' => 2,
    'short' => 'Language rev',
    'long' => '',
  ),
  'langstringcache' => 
  array (
    'count' => 5,
    'short' => 'Cache all language strings',
    'long' => 'Caches all the language strings into compiled files in the data directory.  If you are translating Moodle or changing strings in the Moodle source code then you may want to switch this off.  Otherwise leave it on to see performance benefits.',
  ),
  'lastnotifyfailure' => 
  array (
    'count' => 10,
    'short' => 'Last notify failure',
    'long' => '',
  ),
  'legacyfilesaddallowed' => 
  array (
    'count' => 1,
    'short' => 'Allow adding to legacy course files',
    'long' => 'If a course has legacy course files, allow new files and folders to be added to it.',
  ),
  'legacyfilesinnewcourses' => 
  array (
    'count' => 5,
    'short' => 'Legacy course files in new courses',
    'long' => 'By default, legacy course files areas are available in upgraded courses only. Please note that some features such as activity backup and restore are not compatible with this setting.',
  ),
  'lesson_defaultnextpage' => 
  array (
    'count' => 2,
    'short' => 'Lesson default next page',
    'long' => 'Action after correct answer',
    'long_help' => 
    array (
      0 => 'actionaftercorrectanswer',
      1 => 'lesson',
    ),
  ),
  'lesson_maxanswers' => 
  array (
    'count' => 2,
    'short' => 'Lesson max answers',
    'long' => 'Maximum number of answers',
    'long_help' => 
    array (
      0 => 'maximumnumberofanswersbranches',
      1 => 'lesson',
    ),
  ),
  'lesson_maxhighscores' => 
  array (
    'count' => 1,
    'short' => 'Number of high scores displayed',
    'long' => 'Number of high scores displayed',
  ),
  'lesson_mediaclose' => 
  array (
    'count' => 1,
    'short' => 'Show close button:',
    'long' => 'Displays a close button as part of the popup generated for a linked media file',
  ),
  'lesson_mediaheight' => 
  array (
    'count' => 1,
    'short' => 'Popup window height:',
    'long' => 'Sets the height of the popup displayed for a linked media file',
  ),
  'lesson_mediawidth' => 
  array (
    'count' => 1,
    'short' => 'Popup window width:',
    'long' => 'Sets the width of the popup displayed for a linked media file',
  ),
  'lesson_slideshowbgcolor' => 
  array (
    'count' => 1,
    'short' => 'Slideshow background colour',
    'long' => 'Background colour to for the slideshow if it is enabled',
  ),
  'lesson_slideshowheight' => 
  array (
    'count' => 1,
    'short' => 'Slideshow height',
    'long' => 'Sets the height of the slideshow if it is enabled',
  ),
  'lesson_slideshowwidth' => 
  array (
    'count' => 1,
    'short' => 'Slideshow width',
    'long' => 'Sets the width of the slideshow if it is enabled',
  ),
  'libdir' => 
  array (
    'count' => 1648,
    'short' => 'Lib directory',
    'long' => 'Path to moodles library folder on servers filesystem.',
  ),
  'licenses' => 
  array (
    'count' => 6,
    'short' => 'Licence settings',
    'long' => 'Available licences',
    'short_help' => 
    array (
      0 => 'licensesettings',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'availablelicenses',
      1 => 'admin',
    ),
  ),
  'limitconcurrentlogins' => 
  array (
    'count' => 5,
    'short' => 'Limit concurrent logins',
    'long' => 'If enabled the number of concurrent browser logins for each user is restricted. The oldest session is terminated after reaching the limit, please note that users may lose all unsaved work. This setting is not compatible with single sign-on (SSO) authentication plugins.',
  ),
  'linkadmincategories' => 
  array (
    'count' => 1,
    'short' => 'Link admin categories',
    'long' => 'If enabled admin setting categories will be displayed as links in the navigation and will lead to the admin category pages.',
  ),
  'linkcoursesections' => 
  array (
    'count' => 3,
    'short' => 'Always link course sections',
    'long' => 'Always try to provide a link for course sections. Course sections are usually only shown as links if the course format displays a single section per page. If this setting is enabled a link will always be provided.',
  ),
  'localcachedir' => 
  array (
    'count' => 48,
    'short' => 'Local cache dir',
    'long' => ' for custom $CFG->localcachedir locations',
  ),
  'localcachedirpurged' => 
  array (
    'count' => 5,
    'short' => 'Local cached dir purged',
    'long' => ' The $CFG->localcachedirpurged flag forces local directories to be purged on cluster nodes.
',
  ),
  'locale' => 
  array (
    'count' => 2,
    'short' => 'en_AU.UTF-8',
    'long' => 'Choose a sitewide locale - this will override the format and language of dates for all language packs (though names of days in calendar are not affected). You need to have this locale data installed on your operating system (eg for linux en_US.UTF-8 or es_ES.UTF-8). In most cases this field should be left blank.',
  ),
  'lock_factory' => 
  array (
    'count' => 14,
    'short' => 'Lock factory',
    'long' => ' Moodle 2.7 introduces a locking api for critical tasks (e.g. cron).
 The default locking system to use is DB locking for MySQL and Postgres, and File
 locking for Oracle and SQLServer. If $CFG->preventfilelocking is set, then the default
 will always be DB locking. It can be manually set to one of the lock
 factory classes listed below, or one of your own custom classes implementing the
 \\core\\lock\\lock_factory interface.
  The list of available lock factories is:

 "\\core\\lock\\file_lock_factory" - File locking
      Uses lock files stored by default in the dataroot. Whether this
      works on clusters depends on the file system used for the dataroot.

 "\\core\\lock\\db_record_lock_factory" - DB locking based on table rows.

 "\\core\\lock\\postgres_lock_factory" - DB locking based on postgres advisory locks.',
  ),
  'lock_file_root' => 
  array (
    'count' => 2,
    'short' => 'Lock file root',
    'long' => 'Location for lock files used by the File locking factory. This must exist
 on a shared file system that supports locking.',
  ),
  'lockoutduration' => 
  array (
    'count' => 2,
    'short' => 'Account lockout duration',
    'long' => 'Locked out account is automatically unlocked after this duration.',
  ),
  'lockoutthreshold' => 
  array (
    'count' => 3,
    'short' => 'Account lockout threshold',
    'long' => 'Select number of failed login attempts that result in account lockout. This feature may be abused in denial of service attacks.',
  ),
  'lockoutwindow' => 
  array (
    'count' => 2,
    'short' => 'Account lockout observation window',
    'long' => 'Observation time for lockout threshold, if there are no failed attempts the threshold counter is reset after this time.',
  ),
  'logguests' => 
  array (
    'count' => 2,
    'short' => 'Log guest access',
    'long' => 'This setting enables logging of actions by guest account and not logged in users. High profile sites may want to disable this logging for performance reasons. It is recommended to keep this setting enabled on production sites.',
  ),
  'loginhttps' => 
  array (
    'count' => 34,
    'short' => 'Use HTTPS for logins',
    'long' => 'Turning this on will make Moodle use a secure https connection just for the login page (providing a secure login), and then afterwards revert back to the normal http URL for general speed.  CAUTION: this setting REQUIRES https to be specifically enabled on the web server - if it is not then YOU COULD LOCK YOURSELF OUT OF YOUR SITE.',
  ),
  'loginpageautofocus' => 
  array (
    'count' => 1,
    'short' => 'Autofocus login page form',
    'long' => 'Enabling this option improves usability of the login page, but automatically focusing fields may be considered an accessibility issue.',
  ),
  'loginpasswordautocomplete' => 
  array (
    'count' => 2,
    'short' => 'Prevent password autocompletion on login form',
    'long' => 'If enabled, users are not allowed to save their account password in their browser.',
  ),
  'loglifetime' => 
  array (
    'count' => 10,
    'short' => 'Keep logs for',
    'long' => 'This specifies the length of time you want to keep backup logs information. Logs that are older than this age are automatically deleted. It is recommended to keep this value small, because backup logged information can be huge.',
  ),
  'maildomain' => 
  array (
    'count' => 3,
    'short' => 'Mail domain',
    'long' => 'The next line is needed for bounce handling and any other email to module processing.
            $CFG->maildomain = \'youremaildomain.com\';',
  ),
  'mailnewline' => 
  array (
    'count' => 2,
    'short' => 'Newline characters in mail',
    'long' => 'Newline characters used in mail messages. CRLF is required according to RFC 822bis, some mail servers do automatic conversion from LF to CRLF, other mail servers do incorrect conversion from CRLF to CRCRLF, yet others reject mails with bare LF (qmail for example). Try changing this setting if you are having problems with undelivered emails or double newlines.',
  ),
  'mailprefix' => 
  array (
    'count' => 5,
    'short' => 'Mail prefix',
    'long' => 'The next line is needed for bounce handling and any other email to module processing.
  mailprefix must be EXACTLY four characters.
  $CFG->mailprefix = \'mdl+\'; // + is the separator for Exim and Postfix.
  $CFG->mailprefix = \'mdl-\'; // - is the separator for qmail',
  ),
  'maintenance_enabled' => 
  array (
    'count' => 8,
    'short' => 'Enabled',
    'long' => 'If enabled, the teacher will be able to upload files with feedback when marking the assignments. These files may be, but are not limited to marked up student submissions, documents with comments or spoken audio feedback. ',
  ),
  'maintenance_later' => 
  array (
    'count' => 7,
    'short' => 'Maintenance later',
    'long' => 'status: CLI maintenance mode will be enabled on {$a}',
    'long_help' => 
    array (
      0 => 'clistatusenabledlater',
      1 => 'admin',
    ),
  ),
  'maintenance_message' => 
  array (
    'count' => 7,
    'short' => 'Maintenance message',
    'long' => 'Optional maintenance message',
    'long_help' => 
    array (
      0 => 'optionalmaintenancemessage',
      1 => 'admin',
    ),
  ),
  'maxbytes' => 
  array (
    'count' => 69,
    'short' => 'Maximum embedded file size (bytes)',
    'long' => 'Default maximum submission file size for all workshops on the site (subject to course limits and other local settings)',
  ),
  'maxcategorydepth' => 
  array (
    'count' => 6,
    'short' => 'Maximum category depth',
    'long' => 'This specifies the maximum depth of child categories expanded when displaying categories or combo list. Deeper level categories will appear as links and user can expand them with AJAX request.',
    'short_help' => 
    array (
      0 => 'configsitemaxcategorydepth',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'configsitemaxcategorydepthhelp',
      1 => 'admin',
    ),
  ),
  'maxconsecutiveidentchars' => 
  array (
    'count' => 2,
    'short' => 'Consecutive identical characters',
    'long' => 'Passwords must not have more than this number of consecutive identical characters. Use 0 to disable this check.',
  ),
  'maxeditingtime' => 
  array (
    'count' => 15,
    'short' => 'Maximum time to edit posts',
    'long' => 'This specifies the amount of time people have to re-edit forum postings, glossary comments etc.  Usually 30 minutes is a good value.',
  ),
  'maxexternalblogsperuser' => 
  array (
    'count' => 2,
    'short' => 'Maximum number of external blogs per user',
    'long' => 'The number of external blogs each user is allowed to link to their Moodle blog.',
  ),
  'maxtimelimit' => 
  array (
    'count' => 2,
    'short' => 'Maximum time limit',
    'long' => 'To restrict the maximum PHP execution time that Moodle will allow without any output being displayed, enter a value in seconds here. 0 means that Moodle default restrictions are used. If you have a front-end server with its own time limit, set this value lower to receive PHP errors in logs. Does not apply to CLI scripts.',
  ),
  'maxusersperpage' => 
  array (
    'count' => 2,
    'short' => ' Maximum users per page',
    'long' => 'Maximum number of users displayed within user selector in course, group, cohort, webservice etc.',
  ),
  'messageinbound_domain' => 
  array (
    'count' => 12,
    'short' => 'Message in bound domain',
    'long' => '',
  ),
  'messageinbound_enabled' => 
  array (
    'count' => 8,
    'short' => 'Enabled',
    'long' => 'If enabled, the teacher will be able to upload files with feedback when marking the assignments. These files may be, but are not limited to marked up student submissions, documents with comments or spoken audio feedback. ',
  ),
  'messageinbound_host' => 
  array (
    'count' => 2,
    'short' => 'Message in bound host',
    'long' => '',
  ),
  'messageinbound_hostpass' => 
  array (
    'count' => 1,
    'short' => 'Message in bound host pass',
    'long' => '',
  ),
  'messageinbound_hostssl' => 
  array (
    'count' => 1,
    'short' => 'Message in bound host ssl',
    'long' => '',
  ),
  'messageinbound_hostuser' => 
  array (
    'count' => 2,
    'short' => 'Message in bound host user',
    'long' => '',
  ),
  'messageinbound_mailbox' => 
  array (
    'count' => 12,
    'short' => 'Message in bound mailbox',
    'long' => '',
  ),
  'messaging' => 
  array (
    'count' => 28,
    'short' => 'Messaging',
    'long' => 'Should the messaging system between site users be enabled?',
  ),
  'messagingallowemailoverride' => 
  array (
    'count' => 2,
    'short' => 'Notification email override',
    'long' => 'Allow users to have email message notifications sent to an email address other than the email address in their profile',
  ),
  'messagingdeletereadnotificationsdelay' => 
  array (
    'count' => 2,
    'short' => 'Delete read notifications',
    'long' => 'Read notifications can be deleted to save space. How long after a notification is read can it be deleted?',
  ),
  'messaginghidereadnotifications' => 
  array (
    'count' => 1,
    'short' => 'Hide read notifications',
    'long' => 'Hide read notifications of events like forum posts when viewing messaging history',
  ),
  'minbounces' => 
  array (
    'count' => 5,
    'short' => 'Min bounces',
    'long' => 'The following line is for handling email bounces',
  ),
  'minpassworddigits' => 
  array (
    'count' => 5,
    'short' => 'Digits',
    'long' => 'Passwords must have at least these many digits.',
  ),
  'minpasswordlength' => 
  array (
    'count' => 5,
    'short' => 'Password length',
    'long' => 'Passwords must be at least these many characters long.',
  ),
  'minpasswordlower' => 
  array (
    'count' => 5,
    'short' => 'Lowercase letters',
    'long' => 'Passwords must have at least these many lower case letters.',
  ),
  'minpasswordnonalphanum' => 
  array (
    'count' => 5,
    'short' => 'Non-alphanumeric characters',
    'long' => 'Passwords must have at least these many non-alphanumeric characters.',
  ),
  'minpasswordupper' => 
  array (
    'count' => 5,
    'short' => 'Uppercase letters',
    'long' => 'Passwords must have at least these many upper case letters.',
  ),
  'mnet_all_hosts_id' => 
  array (
    'count' => 19,
    'short' => '',
    'long' => '',
  ),
  'mnet_dispatcher_mode' => 
  array (
    'count' => 28,
    'short' => 'Networking',
    'long' => 'MNet allows communication of this server with other servers or services.',
    'short_help' => 
    array (
      0 => 'net',
      1 => 'mnet',
    ),
    'long_help' => 
    array (
      0 => 'configmnet',
      1 => 'mnet',
    ),
  ),
  'mnet_localhost_id' => 
  array (
    'count' => 103,
    'short' => '',
    'long' => '',
  ),
  'mnet_register_allhosts' => 
  array (
    'count' => 2,
    'short' => '',
    'long' => '',
  ),
  'mnet_rpcdebug' => 
  array (
    'count' => 1,
    'short' => '',
    'long' => '',
  ),
  'mnetkeylifetime' => 
  array (
    'count' => 4,
    'short' => 'Key pair lifetime for Moodle Networking',
    'long' => 'Change the key pair lifetime for Moodle Networking
The default is 28 days. You would only want to change this if the key
was not getting regenerated for any reason. You would probably want
make it much longer. Note that youll need to delete and manually update
any existing key.',
  ),
  'mnetprofileexportfields' => 
  array (
    'count' => 2,
    'short' => 'Fields to send',
    'long' => 'Here you can configure the list of profile fields that are sent and received over MNet when user accounts are created, or updated.  You can also override this for each MNet peer individually. Note that the following fields are always sent and are not optional: {$a}',
    'short_help' => 
    array (
      0 => 'profileexportfields',
      1 => 'mnet',
    ),
    'long_help' => 
    array (
      0 => 'profilefielddesc',
      1 => 'mnet',
    ),
  ),
  'mnetprofileimportfields' => 
  array (
    'count' => 2,
    'short' => 'Fields to import',
    'long' => 'Here you can configure the list of profile fields that are sent and received over MNet when user accounts are created, or updated.  You can also override this for each MNet peer individually. Note that the following fields are always sent and are not optional: {$a}',
    'short_help' => 
    array (
      0 => 'profileimportfields',
      1 => 'mnet',
    ),
    'long_help' => 
    array (
      0 => 'profilefielddesc',
      1 => 'mnet',
    ),
  ),
  'mobilecssurl' => 
  array (
    'count' => 2,
    'short' => 'CSS',
    'long' => 'A CSS file to customise your mobile app interface.',
  ),
  'mod_lti_forcessl' => 
  array (
    'count' => 2,
    'short' => 'Mod lti forcessl',
    'long' => '',
  ),
  'mod_lti_institution_name' => 
  array (
    'count' => 4,
    'short' => 'Mod lti institution name',
    'long' => '',
  ),
  'mod_lti_log_users' => 
  array (
    'count' => 2,
    'short' => 'Mod lti log users',
    'long' => '',
  ),
  'modchooserdefault' => 
  array (
    'count' => 2,
    'short' => 'Activity chooser default',
    'long' => 'Should the activity chooser be presented to users by default?',
  ),
  'moddata' => 
  array (
    'count' => 7,
    'short' => 'Mod data',
    'long' => '',
  ),
  'modeditingmenu' => 
  array (
    'count' => 2,
    'short' => 'Activity editing menus',
    'long' => 'If enabled many of the activity editing icons shown when viewing a course with editing on will be displayed within a drop-down menu. This reduces the content on screen when editing a course by hiding the icons until they are needed.',
  ),
  'moodlepageclass' => 
  array (
    'count' => 4,
    'short' => 'Moodle page class',
    'long' => 'You can specify a different class to be created for the $PAGE global, and to
 compute which blocks appear on each page. However, I cannot think of any good
 reason why you would need to change that. It just felt wrong to hard-code the
 the class name. You are strongly advised not to use these to settings unless
 you are absolutely sure you know what you are doing.
 $CFG->moodlepageclass = \'moodle_page\';',
  ),
  'moodlepageclassfile' => 
  array (
    'count' => 4,
    'short' => 'Moodle page class file',
    'long' => 'You can specify a different class to be created for the $PAGE global, and to
 compute which blocks appear on each page. However, I cannot think of any good
 reason why you would need to change that. It just felt wrong to hard-code the
 the class name. You are strongly advised not to use these to settings unless
 you are absolutely sure you know what you are doing.
  $CFG->moodlepageclassfile = "$CFG->dirroot/local/myplugin/mypageclass.php";',
  ),
  'moodlewstextformatlinkstoimagesfile' => 
  array (
    'count' => 1,
    'short' => 'Moodle text format links to images file',
    'long' => '',
  ),
  'movingmoduleupgradescriptwasrun' => 
  array (
    'count' => 1,
    'short' => 'Moving module upgrade descript was run',
    'long' => '',
  ),
  'mysetting' => 
  array (
    'count' => 1,
    'short' => 'My setting',
    'long' => '',
  ),
  'navadduserpostslinks' => 
  array (
    'count' => 2,
    'short' => 'Add links to view user posts',
    'long' => 'If enabled two links will be added to each user in the navigation to view discussions the user has started and posts the user has made in forums throughout the site or in specific courses.',
  ),
  'navcourselimit' => 
  array (
    'count' => 12,
    'short' => 'Course limit',
    'long' => 'Limits the number of courses shown to the user when they are either not logged in or are not enrolled in any courses.',
  ),
  'navexpandmycourses' => 
  array (
    'count' => 1,
    'short' => 'Show My courses expanded on Dashboard',
    'long' => 'If enabled, My courses is initially shown expanded in the navigation block on Dashboard.',
  ),
  'navshowallcourses' => 
  array (
    'count' => 2,
    'short' => 'Show all courses',
    'long' => 'This setting determines whether users who are enrolled in courses can see Courses (listing all courses) in the navigation, in addition to My Courses (listing courses in which they are enrolled).',
  ),
  'navshowcategories' => 
  array (
    'count' => 3,
    'short' => 'Show course categories',
    'long' => 'Show course categories in the navigation bar and navigation blocks. This does not occur with courses the user is currently enrolled in, they will still be listed under mycourses without categories.',
  ),
  'navshowfrontpagemods' => 
  array (
    'count' => 1,
    'short' => 'Show front page activities in the navigation',
    'long' => 'If enabled, front page activities will be shown on the navigation under site pages.',
  ),
  'navshowfullcoursenames' => 
  array (
    'count' => 1,
    'short' => 'Show course full names',
    'long' => 'If enabled, course full names will be used in the navigation rather than short names.',
  ),
  'navshowmycoursecategories' => 
  array (
    'count' => 3,
    'short' => 'Show my course categories',
    'long' => 'If enabled courses in the users my courses branch will be shown in categories.',
  ),
  'navsortmycoursessort' => 
  array (
    'count' => 9,
    'short' => 'Sort my courses',
    'long' => 'This determines whether courses are listed under My courses according to the sort order (i.e. the order set in Site administration > Courses > Manage courses and categories) or alphabetically by course setting.',
  ),
  'noemailever' => 
  array (
    'count' => 15,
    'short' => 'No email ever',
    'long' => 'When working with production data on test servers, no emails or other messages
 should ever be send to real users
 $CFG->noemailever = true;    // NOT FOR PRODUCTION SERVERS!',
  ),
  'nofixday' => 
  array (
    'count' => 3,
    'short' => 'No fix day',
    'long' => 'This setting will cause the userdate() function not to fix %d in
 date strings, and just let them show with a zero prefix.',
  ),
  'nofixhour' => 
  array (
    'count' => 1,
    'short' => 'No fix hour',
    'long' => '',
  ),
  'nolastloggedin' => 
  array (
    'count' => 3,
    'short' => 'No last logged in',
    'long' => 'do not save $CFG->nolastloggedin in database!
',
  ),
  'noreplyaddress' => 
  array (
    'count' => 11,
    'short' => 'No-reply address',
    'long' => 'Emails are sometimes sent out on behalf of a user (eg forum posts). The email address you specify here will be used as the "From" address in those cases when the recipients should not be able to reply directly to the user (eg when a user chooses to keep their address private).',
  ),
  'noreplyuserid' => 
  array (
    'count' => 9,
    'short' => 'No reply user id',
    'long' => 'Use the following flag to set userid for noreply user. If not set then moodle will
 create dummy user and use -ve value as user id.',
  ),
  'notifyloginfailures' => 
  array (
    'count' => 2,
    'short' => 'Email login failures to',
    'long' => 'Send login failure notification messages to these selected users. This requires an internal logstore (eg Standard Logstore) to be enabled.',
  ),
  'notifyloginthreshold' => 
  array (
    'count' => 4,
    'short' => 'Threshold for email notifications',
    'long' => 'If notifications about failed logins are active, how many failed login attempts by one user or one IP address is it worth notifying about?',
  ),
  'notloggedinroleid' => 
  array (
    'count' => 6,
    'short' => 'Role for visitors',
    'long' => 'Users who are not logged in to the site will be treated as if they have this role granted to them at the site context.  Guest is almost always what you want here, but you might want to create roles that are less or more restrictive.  Things like creating posts still require the user to log in properly.',
  ),
  'numcoursesincombo' => 
  array (
    'count' => 4,
    'short' => 'Num courses in combo',
    'long' => 'In 2.4 combo list was not displayed if there are more than $CFG->numcoursesincombo courses in the system.
         $CFG->numcoursesincombo no longer affects whether the combo list is displayed. Setting is deprecated.
',
  ),
  'opensslcnf' => 
  array (
    'count' => 7,
    'short' => 'Open SSL config',
    'long' => 'Allow specification of openssl.cnf especially for Windows installs.',
  ),
  'opentogoogle' => 
  array (
    'count' => 2,
    'short' => 'Open to Google',
    'long' => 'If you enable this setting, then Google will be allowed to enter your site as a Guest.  In addition, people coming in to your site via a Google search will automatically be logged in as a Guest.  Note that this only provides transparent access to courses that already allow guest access.',
  ),
  'os' => 
  array (
    'count' => 2,
    'short' => 'OS',
    'long' => '',
  ),
  'ostype' => 
  array (
    'count' => 22,
    'short' => 'OS type',
    'long' => ' Calculate and set $CFG->ostype to be used everywhere. Possible values are:
   $CFG->ostype = \'WINDOWS\';
   $CFG->ostype = \'UNIX\';
',
  ),
  'pagepath' => 
  array (
    'count' => 6,
    'short' => 'Page patch',
    'long' => '[[pagepath]]',
    'long_help' => 
    array (
      0 => 'pagepath',
      1 => 'NULL',
    ),
  ),
  'passwordchangelogout' => 
  array (
    'count' => 3,
    'short' => 'Log out after password change',
    'long' => 'If enabled, when a password is changed, all browser sessions are terminated, apart from the one in which the new password is specified. (This setting does not affect password changes via bulk user upload.)',
  ),
  'passwordpolicy' => 
  array (
    'count' => 9,
    'short' => 'Password policy',
    'long' => 'Turning this on will make Moodle check user passwords against a valid password policy. Use the settings below to specify your policy (they will be ignored if you set this to \\\'No\\\').',
  ),
  'passwordreuselimit' => 
  array (
    'count' => 13,
    'short' => 'Password rotation limit',
    'long' => 'Number of times a user must change their password before they are allowed to reuse a password. Hashes of previously used passwords are stored in local database table. This feature might not be compatible with some external authentication plugins.',
  ),
  'passwordsaltalt1' => 
  array (
    'count' => 2,
    'short' => 'Secret password salt',
    'long' => 'A site-wide password salt is no longer used in new installations.
 If upgrading from 2.6 or older, keep all existing salts in config.php file.

 $CFG->passwordsaltmain = \'a_very_long_random_string_of_characters#@6&*1\';

 You may also have some alternative salts to allow migration from previously
 used salts.',
  ),
  'passwordsaltalt19' => 
  array (
    'count' => 2,
    'short' => 'Secret password salt',
    'long' => 'A site-wide password salt is no longer used in new installations.
 If upgrading from 2.6 or older, keep all existing salts in config.php file.

 $CFG->passwordsaltmain = \'a_very_long_random_string_of_characters#@6&*1\';

 You may also have some alternative salts to allow migration from previously
 used salts.',
  ),
  'passwordsaltalt2' => 
  array (
    'count' => 2,
    'short' => 'Secret password salt',
    'long' => 'A site-wide password salt is no longer used in new installations.
 If upgrading from 2.6 or older, keep all existing salts in config.php file.

 $CFG->passwordsaltmain = \'a_very_long_random_string_of_characters#@6&*1\';

 You may also have some alternative salts to allow migration from previously
 used salts.',
  ),
  'passwordsaltalt20' => 
  array (
    'count' => 2,
    'short' => 'Secret password salt',
    'long' => 'A site-wide password salt is no longer used in new installations.
 If upgrading from 2.6 or older, keep all existing salts in config.php file.

 $CFG->passwordsaltmain = \'a_very_long_random_string_of_characters#@6&*1\';

 You may also have some alternative salts to allow migration from previously
 used salts.',
  ),
  'passwordsaltalt3' => 
  array (
    'count' => 2,
    'short' => 'Secret password salt',
    'long' => 'A site-wide password salt is no longer used in new installations.
 If upgrading from 2.6 or older, keep all existing salts in config.php file.

 $CFG->passwordsaltmain = \'a_very_long_random_string_of_characters#@6&*1\';

 You may also have some alternative salts to allow migration from previously
 used salts.',
  ),
  'passwordsaltmain' => 
  array (
    'count' => 4,
    'short' => 'Secret password salt',
    'long' => 'A site-wide password salt is no longer used in new installations.
 If upgrading from 2.6 or older, keep all existing salts in config.php file.

 $CFG->passwordsaltmain = \'a_very_long_random_string_of_characters#@6&*1\';

 You may also have some alternative salts to allow migration from previously
 used salts.',
  ),
  'pathtoclam' => 
  array (
    'count' => 11,
    'short' => 'clam AV path',
    'long' => 'Path to clam AV.  Probably something like /usr/bin/clamscan or /usr/bin/clamdscan. You need this in order for clam AV to run.',
  ),
  'pathtodot' => 
  array (
    'count' => 5,
    'short' => 'Path to dot',
    'long' => 'Path to dot. Probably something like /usr/bin/dot. To be able to generate graphics from DOT files, you must have installed the dot executable and point to it here. Note that, for now, this only used by the profiling features (Development->Profiling) built into Moodle.',
  ),
  'pathtodu' => 
  array (
    'count' => 5,
    'short' => 'Path to du',
    'long' => 'Path to du. Probably something like /usr/bin/du. If you enter this, pages that display directory contents will run much faster for directories with a lot of files.',
  ),
  'pathtogs' => 
  array (
    'count' => 5,
    'short' => 'Path to ghostscript',
    'long' => 'On most Linux installs, this can be left as \\\'/usr/bin/gs\\\'. On Windows it will be something like \\\'c:\\\\gs\\\\bin\\\\gswin32c.exe\\\' (make sure there are no spaces in the path - if necessary copy the files \\\'gswin32c.exe\\\' and \\\'gsdll32.dll\\\' to a new folder without a space in the path)',
  ),
  'perfdebug' => 
  array (
    'count' => 8,
    'short' => 'Performance info',
    'long' => 'If you turn this on, performance info will be printed in the footer of the standard theme',
  ),
  'phpunit_dataroot' => 
  array (
    'count' => 38,
    'short' => 'PHP unit dataroot',
    'long' => ' $CFG->phpunit_dataroot = \'/home/example/phpu_moodledata\';
',
  ),
  'phpunit_dbhost' => 
  array (
    'count' => 2,
    'short' => 'Host server',
    'long' => 'Type database server IP address or host name. Use a system DSN name if using ODBC.',
  ),
  'phpunit_dblibrary' => 
  array (
    'count' => 2,
    'short' => 'PHP unit database library',
    'long' => '',
  ),
  'phpunit_dbname' => 
  array (
    'count' => 2,
    'short' => 'Database name',
    'long' => 'Leave empty if using a DSN name in database host.',
  ),
  'phpunit_dboptions' => 
  array (
    'count' => 2,
    'short' => 'PHP unit database options',
    'long' => '',
  ),
  'phpunit_dbpass' => 
  array (
    'count' => 2,
    'short' => 'PHP unit database password',
    'long' => '',
  ),
  'phpunit_dbtype' => 
  array (
    'count' => 2,
    'short' => 'Type',
    'long' => 'ADOdb database driver name, type of the external database engine.',
  ),
  'phpunit_dbuser' => 
  array (
    'count' => 2,
    'short' => 'PHP unit database user',
    'long' => '',
  ),
  'phpunit_directorypermissions' => 
  array (
    'count' => 4,
    'short' => 'PHP unit directory permissions',
    'long' => '$CFG->phpunit_directorypermissions = 02777; // optional',
  ),
  'phpunit_extra_drivers' => 
  array (
    'count' => 12,
    'short' => 'PHP unit extra drivers',
    'long' => '',
  ),
  'phpunit_prefix' => 
  array (
    'count' => 20,
    'short' => 'Key prefix',
    'long' => 'This prefix is used for all key names on the memcache server.
* If you only have one Moodle instance using this server, you can leave this value default.
* Due to key length restrictions, a maximum of 5 characters is permitted.',
  ),
  'phpunit_test_get_config_1' => 
  array (
    'count' => 1,
    'short' => 'PHP unit test get config 1',
    'long' => '',
  ),
  'phpunit_test_get_config_5' => 
  array (
    'count' => 1,
    'short' => 'PHP unit test get config 5',
    'long' => '',
  ),
  'portfolio_high_dbsize_threshold' => 
  array (
    'count' => 2,
    'short' => 'Portfolio high databaze size threshold',
    'long' => '',
  ),
  'portfolio_moderate_dbsize_threshold' => 
  array (
    'count' => 2,
    'short' => 'Portfolio moderate database size threshold',
    'long' => '',
  ),
  'preferlinegraphs' => 
  array (
    'count' => 4,
    'short' => 'Prefer line graphs',
    'long' => 'This setting will make some graphs (eg user logs) use lines instead of bars',
  ),
  'prefix' => 
  array (
    'count' => 47,
    'short' => 'Key prefix',
    'long' => 'This prefix is used for all key names on the memcache server.
* If you only have one Moodle instance using this server, you can leave this value default.
* Due to key length restrictions, a maximum of 5 characters is permitted.',
  ),
  'prefix_dataroot' => 
  array (
    'count' => 2,
    'short' => 'Prefix data root',
    'long' => '',
  ),
  'preventexecpath' => 
  array (
    'count' => 6,
    'short' => 'Prevent exec path',
    'long' => 'Some administration options allow setting the path to executable files. This can
 potentially cause a security risk. Set this option to true to disable editing
 those config settings via the web. They will need to be set explicitly in the
 config.php file',
  ),
  'preventfilelocking' => 
  array (
    'count' => 7,
    'short' => 'Prevent file locking',
    'long' => 'Some filesystems such as NFS may not support file locking operations.
 Locking resolves race conditions and is strongly recommended for production servers.',
  ),
  'preventscheduledtaskchanges' => 
  array (
    'count' => 5,
    'short' => 'Prevent scheduled task changes',
    'long' => 'Use the following flag to disable modifications to scheduled tasks
 whilst still showing the state of tasks.',
  ),
  'profileroles' => 
  array (
    'count' => 5,
    'short' => 'Profile visible roles',
    'long' => 'List of roles that are visible on user profiles and participation page.',
  ),
  'profilesforenrolledusersonly' => 
  array (
    'count' => 4,
    'short' => 'Profiles for enrolled users only',
    'long' => 'To prevent misuse by spammers, profile descriptions of users who are not yet enrolled in any course are hidden. New users must enrol in at least one course before they can add a profile description.',
  ),
  'profilingallowall' => 
  array (
    'count' => 2,
    'short' => 'Continuous profiling',
    'long' => 'If you enable this setting, then, at any moment, you can use the PROFILEALL parameter anywhere (PGC) to enable profiling for all the executed scripts along the Moodle session life. Analogously, you can use the PROFILEALLSTOP parameter to stop it.',
  ),
  'profilingallowme' => 
  array (
    'count' => 2,
    'short' => 'Selective profiling',
    'long' => 'If you enable this setting, then, selectively, you can use the PROFILEME parameter anywhere (PGC) and profiling for that script will happen. Analogously, you can use the DONTPROFILEME parameter to prevent profiling to happen',
  ),
  'profilingautofrec' => 
  array (
    'count' => 3,
    'short' => 'Automatic profiling',
    'long' => 'By configuring this setting, some request (randomly, based on the frequency specified - 1 of N) will be picked and automatically profiled, storing results for further analysis. Note that this way of profiling observes the include/exclude settings. Set it to 0 to disable automatic profiling.',
  ),
  'profilingenabled' => 
  array (
    'count' => 4,
    'short' => 'Enable profiling',
    'long' => 'If you enable this setting, then profiling will be available in this site and you will be able to define its behavior by configuring the next options.',
  ),
  'profilingexcluded' => 
  array (
    'count' => 2,
    'short' => 'Exclude profiling',
    'long' => 'List of (comma separated, absolute skipping wwwroot, callable) URLs that will be excluded from being profiled from the ones defined by \\\'Profile these\\\' setting.',
  ),
  'profilingimportprefix' => 
  array (
    'count' => 1,
    'short' => 'Profiling import prefix',
    'long' => 'For easier detection, all the imported profiling runs will be prefixed with the value specified here.',
  ),
  'profilingincluded' => 
  array (
    'count' => 2,
    'short' => 'Profile these',
    'long' => 'List of (comma separated, absolute skipping wwwroot, callable) URLs that will be automatically profiled. Examples: /index.php, /course/view.php. Also accepts the * wildchar at any position. Examples: /mod/forum/*, /mod/*/view.php.',
  ),
  'profilinglifetime' => 
  array (
    'count' => 2,
    'short' => 'Keep profiling runs',
    'long' => 'Specify the time you want to keep information about old profiling runs. Older ones will be pruned periodically. Note that this excludes any profiling run marked as \\\'reference run\\\'.',
  ),
  'protectusernames' => 
  array (
    'count' => 3,
    'short' => 'Protect usernames',
    'long' => 'By default forget_password.php does not display any hints that would allow guessing of usernames or email addresses.',
  ),
  'proxybypass' => 
  array (
    'count' => 7,
    'short' => 'Proxy bypass hosts',
    'long' => 'Comma separated list of (partial) hostnames or IPs that should bypass proxy (e.g., 192.168., .mydomain.com)',
  ),
  'proxyhost' => 
  array (
    'count' => 20,
    'short' => 'Proxy host',
    'long' => 'If this <b>server</b> needs to use a proxy computer (eg a firewall) to access the Internet, then provide the proxy hostname here.  Otherwise leave it blank.',
  ),
  'proxypassword' => 
  array (
    'count' => 10,
    'short' => 'Proxy password',
    'long' => 'Password needed to access internet through proxy if required, empty if none (PHP cURL extension required).',
  ),
  'proxyport' => 
  array (
    'count' => 10,
    'short' => 'Proxy port',
    'long' => 'If this server needs to use a proxy computer, then provide the proxy port here.',
  ),
  'proxytype' => 
  array (
    'count' => 8,
    'short' => 'Proxy type',
    'long' => 'Type of web proxy (PHP5 and cURL extension required for SOCKS5 support).',
  ),
  'proxyuser' => 
  array (
    'count' => 10,
    'short' => 'Proxy username',
    'long' => 'Username needed to access internet through proxy if required, empty if none (PHP cURL extension required).',
  ),
  'pwresettime' => 
  array (
    'count' => 7,
    'short' => 'Password reset time',
    'long' => '',
  ),
  'questionbankcolumns' => 
  array (
    'count' => 2,
    'short' => 'Qestion bank columns',
    'long' => '',
  ),
  'quizquestionbankcolumns' => 
  array (
    'count' => 2,
    'short' => 'Quiz question bank columns',
    'long' => '',
  ),
  'recaptchaprivatekey' => 
  array (
    'count' => 7,
    'short' => 'ReCAPTCHA private key',
    'long' => 'String of characters used to communicate between your Moodle server and the recaptcha server. Obtain one for this site by visiting http://www.google.com/recaptcha',
  ),
  'recaptchapublickey' => 
  array (
    'count' => 7,
    'short' => 'ReCAPTCHA public key',
    'long' => 'String of characters used to display the reCAPTCHA element in the signup form. Generated by http://www.google.com/recaptcha',
  ),
  'recovergradesdefault' => 
  array (
    'count' => 2,
    'short' => 'Recover grades default',
    'long' => 'By default recover old grades when re-enrolling a user in a course.',
  ),
  'registerauth' => 
  array (
    'count' => 16,
    'short' => 'Register authorization',
    'long' => 'If an authentication plugin, such as email-based self-registration, is selected, then it enables potential users to register themselves and create accounts. This results in the possibility of spammers creating accounts in order to use forum posts, blog entries etc. for spam. To avoid this risk, self-registration should be disabled or limited by <em>Allowed email domains</em> setting.',
    'short_help' => 
    array (
      0 => 'selfregistration',
      1 => 'auth',
    ),
    'long_help' => 
    array (
      0 => 'selfregistration_help',
      1 => 'auth',
    ),
  ),
  'release' => 
  array (
    'count' => 32,
    'short' => 'Relase',
    'long' => 'Moodle release',
    'long_help' => 
    array (
      0 => 'siterelease',
      1 => 'hub',
    ),
  ),
  'rememberusername' => 
  array (
    'count' => 6,
    'short' => 'Remember username',
    'long' => 'Enable if you want to store permanent cookies with usernames during user login. Permanent cookies may be considered a privacy issue if used without consent.',
  ),
  'repository' => 
  array (
    'count' => 3,
    'short' => 'Repository',
    'long' => 'Repositories',
    'long_help' => 
    array (
      0 => 'repositories',
      1 => 'repository',
    ),
  ),
  'repository_no_delete' => 
  array (
    'count' => 4,
    'short' => 'Repository no delete',
    'long' => '',
  ),
  'repositorycacheexpire' => 
  array (
    'count' => 4,
    'short' => 'Cache expire',
    'long' => 'The amount of time that file listings are cached locally (in seconds) when browsing external repositories.',
    'short_help' => 
    array (
      0 => 'cacheexpire',
      1 => 'repository',
    ),
    'long_help' => 
    array (
      0 => 'configcacheexpire',
      1 => 'repository',
    ),
  ),
  'repositorygetfiletimeout' => 
  array (
    'count' => 9,
    'short' => 'Repository get file timeout',
    'long' => 'Timeout in seconds for downloading the external file into moodle',
  ),
  'repositorysyncfiletimeout' => 
  array (
    'count' => 2,
    'short' => 'Repository sync file timeout',
    'long' => 'Timeout in seconds for syncronising the external file size',
  ),
  'repositorysyncimagetimeout' => 
  array (
    'count' => 7,
    'short' => 'Repositoy sync image timeout',
    'long' => 'Timeout in seconds for downloading an image file from external repository during syncronisation',
  ),
  'requestcategoryselection' => 
  array (
    'count' => 2,
    'short' => 'Enable category selection',
    'long' => 'Allow the selection of a category when requesting a course.',
  ),
  'requiremodintro' => 
  array (
    'count' => 1,
    'short' => 'Require activity description',
    'long' => 'If enabled, users will be forced to enter a description for each activity.',
  ),
  'restorernewroleid' => 
  array (
    'count' => 3,
    'short' => 'Restorers\\\' role in courses',
    'long' => 'If the user does not already have the permission to manage the newly restored course, the user is automatically assigned this role and enrolled if necessary. Select "None" if you do not want restorers to be able to manage every restored course.',
  ),
  'restrictmodulesfor' => 
  array (
    'count' => 2,
    'short' => 'Restric modules for',
    'long' => '',
  ),
  'reverseproxy' => 
  array (
    'count' => 5,
    'short' => 'Reverse proxy',
    'long' => 'Enable when setting up advanced reverse proxy load balancing configurations,
 it may be also necessary to enable this when using port forwarding.',
  ),
  'rolesactive' => 
  array (
    'count' => 7,
    'short' => 'Roles active',
    'long' => '',
  ),
  'runclamonupload' => 
  array (
    'count' => 3,
    'short' => 'Run clam on upload',
    'long' => 'If $CFG->runclamonupload is set, we scan a given file. (called from {@link preprocess_files()})
  @deprecated since 2.7',
  ),
  'running_installer' => 
  array (
    'count' => 2,
    'short' => 'Running installer',
    'long' => '',
  ),
  'scorm_updatetimelast' => 
  array (
    'count' => 2,
    'short' => 'Scorm update time last',
    'long' => '',
  ),
  'session_database_acquire_lock_timeout' => 
  array (
    'count' => 4,
    'short' => 'Session database acquire lock timeout',
    'long' => '
            $CFG->session_memcached_acquire_lock_timeout = 120;
',
  ),
  'session_file_save_path' => 
  array (
    'count' => 4,
    'short' => 'Session file save patch',
    'long' => '$CFG->session_file_save_path = $CFG->dataroot.\'/sessions\';
',
  ),
  'session_handler_class' => 
  array (
    'count' => 11,
    'short' => 'Session handler class',
    'long' => 'Following settings may be used to select session driver:
             Database session handler (not compatible with MyISAM):
      $CFG->session_handler_class = \'\\core\\session\\database\';
     

   File session handler (file system locking required):
      $CFG->session_handler_class = \'\\core\\session\\file\';
    

   Memcached session handler (requires memcached server and extension):
      $CFG->session_handler_class = \'\\core\\session\\memcached\';
     
   Memcache session handler (requires memcached server and memcache extension):
      $CFG->session_handler_class = \'\\core\\session\\memcache\';
     
      ** NOTE: Memcache extension has less features than memcached and may be
         less reliable. Use memcached where possible or if you encounter
         session problems. **
            ',
  ),
  'session_memcache_acquire_lock_timeout' => 
  array (
    'count' => 4,
    'short' => 'Session memcache qcquire lock timeout',
    'long' => '$CFG->session_memcache_acquire_lock_timeout = 120;',
  ),
  'session_memcache_save_path' => 
  array (
    'count' => 6,
    'short' => 'Session memcache save path',
    'long' => '$CFG->session_memcache_save_path = \'127.0.0.1:11211\';',
  ),
  'session_memcached_acquire_lock_timeout' => 
  array (
    'count' => 4,
    'short' => 'Session memcached acquire lock timeout',
    'long' => '$CFG->session_memcached_acquire_lock_timeout = 120;
',
  ),
  'session_memcached_lock_expire' => 
  array (
    'count' => 4,
    'short' => 'Session memcached lock expire',
    'long' => '$CFG->session_memcached_lock_expire = 7200;       // Ignored if PECL memcached is below version 2.2.0',
  ),
  'session_memcached_prefix' => 
  array (
    'count' => 4,
    'short' => 'Session memcached prefix',
    'long' => '$CFG->session_memcached_prefix = \'memc.sess.key.\';
',
  ),
  'session_memcached_save_path' => 
  array (
    'count' => 9,
    'short' => 'Session memchaed save path',
    'long' => '$CFG->session_memcached_save_path = \'127.0.0.1:11211\';
',
  ),
  'session_update_timemodified_frequency' => 
  array (
    'count' => 5,
    'short' => 'Session update time modified frequency',
    'long' => 'Following setting allows you to alter how frequently is timemodified updated in sessions table.',
  ),
  'sessioncookie' => 
  array (
    'count' => 5,
    'short' => 'Cookie prefix',
    'long' => 'This setting customises the name of the cookie used for Moodle sessions.  This is optional, and only useful to avoid cookies being confused when there is more than one copy of Moodle running within the same web site.',
  ),
  'sessioncookiedomain' => 
  array (
    'count' => 12,
    'short' => 'Cookie domain',
    'long' => 'This allows you to change the domain that the Moodle cookies are available from. This is useful for Moodle customisations (e.g. authentication or enrolment plugins) that need to share Moodle session information with a web application on another subdomain. <strong>WARNING: it is strongly recommended to leave this setting at the default (empty) - an incorrect value will prevent all logins to the site.</strong>',
  ),
  'sessioncookiepath' => 
  array (
    'count' => 11,
    'short' => 'Cookie path',
    'long' => 'If you need to change where browsers send the Moodle cookies, you can change this setting to specify a subdirectory of your web site.  Otherwise the default \\\'/\\\' should be fine.',
  ),
  'sessiontimeout' => 
  array (
    'count' => 17,
    'short' => 'Timeout',
    'long' => 'If people logged in to this site are idle for a long time (without loading pages) then they are automatically logged out (their session is ended).  This variable specifies how long this time should be.',
  ),
  'showcrondebugging' => 
  array (
    'count' => 4,
    'short' => 'Show cron debugging',
    'long' => 'Force developer level debug and add debug info to the output of cron
 $CFG->showcrondebugging = true;',
  ),
  'showcronsql' => 
  array (
    'count' => 4,
    'short' => 'Show cron SQL',
    'long' => 'Add SQL queries to the output of cron, just before their execution
$CFG->showcronsql = true;',
  ),
  'showuseridentity' => 
  array (
    'count' => 19,
    'short' => 'Show user identity',
    'long' => 'When selecting or searching for users, and when displaying lists of users, these fields may be shown in addition to their full name. The fields are only shown to users who have the moodle/site:viewuseridentity capability; by default, teachers and managers. (This option makes most sense if you choose one or two fields that are mandatory at your institution.)',
  ),
  'siteadmins' => 
  array (
    'count' => 24,
    'short' => 'Site admins',
    'long' => '[[administrationsite]]',
    'long_help' => 
    array (
      0 => 'administrationsite',
      1 => 'NULL',
    ),
  ),
  'sitedefaultlicense' => 
  array (
    'count' => 14,
    'short' => 'Default site license',
    'long' => 'The default licence for publishing content on this site',
    'short_help' => 
    array (
      0 => 'configsitedefaultlicense',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'configsitedefaultlicensehelp',
      1 => 'admin',
    ),
  ),
  'siteguest' => 
  array (
    'count' => 47,
    'short' => '',
    'long' => '',
  ),
  'siteidentifier' => 
  array (
    'count' => 8,
    'short' => 'Site iidentifier',
    'long' => '',
  ),
  'sitemailcharset' => 
  array (
    'count' => 6,
    'short' => 'Character set',
    'long' => 'This setting specifies the default charset for all emails sent from the site.',
  ),
  'sitepolicy' => 
  array (
    'count' => 9,
    'short' => 'Site policy URL',
    'long' => 'If you have a site policy that all registered users must see and agree to before using this site, then specify the URL to it here, otherwise leave this field blank. This setting can contain any public URL.',
  ),
  'sitepolicyguest' => 
  array (
    'count' => 2,
    'short' => 'Site policy URL for guests',
    'long' => 'If you have a site policy that all guests must see and agree to before using this site, then specify the URL to it here, otherwise leave this field blank. This setting can contain any public URL. Note: access of not-logged-in users may be prevented with forcelogin setting.',
  ),
  'skiplangupgrade' => 
  array (
    'count' => 5,
    'short' => 'Skip language upgrade',
    'long' => '',
  ),
  'slasharguments' => 
  array (
    'count' => 24,
    'short' => 'Use slash arguments',
    'long' => 'Files (images, uploads etc) are provided via a script using \\\'slash arguments\\\'. This method allows files to be more easily cached in web browsers, proxy servers etc.  Unfortunately, some PHP servers don\\\'t allow this method, so if you have trouble viewing uploaded files or images (eg user pictures), disable this setting.',
  ),
  'smtphosts' => 
  array (
    'count' => 3,
    'short' => 'SMTP hosts',
    'long' => 'Give the full name of one or more local SMTP servers that Moodle should use to send mail (eg \\\'mail.a.com\\\' or \\\'mail.a.com;mail.b.com\\\'). To specify a non-default port (i.e other than port 25), you can use the [server]:[port] syntax (eg \\\'mail.a.com:587\\\'). For secure connections, port 465 is usually used with SSL, port 587 is usually used with TLS, specify security protocol below if required. If you leave this field blank, Moodle will use the PHP default method of sending mail.',
  ),
  'smtpmaxbulk' => 
  array (
    'count' => 4,
    'short' => 'SMTP session limit',
    'long' => 'Maximum number of messages sent per SMTP session. Grouping messages may speed up the sending of emails. Values lower than 2 force creation of new SMTP session for each email.',
  ),
  'smtppass' => 
  array (
    'count' => 1,
    'short' => 'SMTP password',
    'long' => 'If you have specified an SMTP server above, and the server requires authentication, then enter the username and password here.',
    'short_help' => 
    array (
      0 => 'smtppass',
      1 => 'message_email',
    ),
    'long_help' => 
    array (
      0 => 'configsmtpuser',
      1 => 'message_email',
    ),
  ),
  'smtpsecure' => 
  array (
    'count' => 1,
    'short' => 'SMTP security',
    'long' => 'If SMTP server requires secure connection, specify the correct protocol type.',
  ),
  'smtpuser' => 
  array (
    'count' => 2,
    'short' => 'SMTP username',
    'long' => 'If you have specified an SMTP server above, and the server requires authentication, then enter the username and password here.',
  ),
  'somecoresetting' => 
  array (
    'count' => 2,
    'short' => 'Some core setting',
    'long' => ' It is possible to specify normal admin settings here, the point is that
 they can not be changed through the standard admin settings pages any more.

Core settings are specified directly via assignment to $CFG variable.
Example:
$CFG->somecoresetting = \'value\';',
  ),
  'sslproxy' => 
  array (
    'count' => 8,
    'short' => 'SSL proxy',
    'long' => 'Enable when using external SSL appliance for performance reasons.
  Please note that site may be accessible via http: or https:, but not both!',
  ),
  'statsfirstrun' => 
  array (
    'count' => 8,
    'short' => 'Maximum processing interval',
    'long' => 'This specifies how far back the logs should be processed <b>the first time</b> the cronjob wants to process statistics. If you have a lot of traffic and are on shared hosting, it\\\'s probably not a good idea to go too far back, as it could take a long time to run and be quite resource intensive. (Note that for this setting, 1 month = 28 days. In the graphs and reports generated, 1 month = 1 calendar month.)',
  ),
  'statslastdaily' => 
  array (
    'count' => 1,
    'short' => 'Stats last daily',
    'long' => '',
  ),
  'statslastexecution' => 
  array (
    'count' => 3,
    'short' => 'Stats last execution',
    'long' => '',
  ),
  'statsmaxruntime' => 
  array (
    'count' => 6,
    'short' => 'Maximum runtime',
    'long' => 'Stats processing can be quite intensive, so use a combination of this field and the next one to specify when it will run and how long for.',
  ),
  'statsruntimedays' => 
  array (
    'count' => 2,
    'short' => 'Days to process',
    'long' => 'This specifies the maximum number of days processed in each statistics execution. Once the statistics are up-to-date, only one day will be processed, so adjust this value depending of your server load, reducing it if shorter cron executions are needed.',
  ),
  'statsruntimestarthour' => 
  array (
    'count' => 3,
    'short' => 'Run at',
    'long' => 'What time should the cronjob that does the statistics processing start? Specifying different times is recommended if there are multiple Moodle sites on one server.',
    'short_help' => 
    array (
      0 => 'statsruntimestart',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'configstatsruntimestart',
      1 => 'admin',
    ),
  ),
  'statsruntimestartminute' => 
  array (
    'count' => 3,
    'short' => 'Run at',
    'long' => 'What time should the cronjob that does the statistics processing start? Specifying different times is recommended if there are multiple Moodle sites on one server.',
    'short_help' => 
    array (
      0 => 'statsruntimestart',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'configstatsruntimestart',
      1 => 'admin',
    ),
  ),
  'statsuserthreshold' => 
  array (
    'count' => 6,
    'short' => 'User threshold',
    'long' => 'This setting specifies the minimum number of enrolled users for a course to be included in statistics calculations.',
  ),
  'strictformsrequired' => 
  array (
    'count' => 12,
    'short' => 'Strict validation of required fields',
    'long' => 'If enabled, users are prevented from entering a space or line break only in required fields in forms.',
  ),
  'stringfilters' => 
  array (
    'count' => 12,
    'short' => 'String filters',
    'long' => ' These one is managed in a strange way by the filters setting page, so have to be initialised in install.php.',
  ),
  'stylesheets' => 
  array (
    'count' => 5,
    'short' => 'Stylesheets',
    'long' => '',
  ),
  'supportemail' => 
  array (
    'count' => 11,
    'short' => 'Support email',
    'long' => 'This email address will be published to users of this site as the one to email when they need general help (for example, when new users create their own accounts).  If this email is left blank then no such helpful email address is supplied.',
  ),
  'supportname' => 
  array (
    'count' => 4,
    'short' => 'Support name',
    'long' => 'This is the name of a person or other entity offering general help via the support email or web address.',
  ),
  'supportpage' => 
  array (
    'count' => 2,
    'short' => 'Support page',
    'long' => 'This web address will be published to users of this site as the one to go to when they need general help (for example, when new users create their own accounts).  If this address is left blank then no link will be supplied.',
  ),
  'supportuserid' => 
  array (
    'count' => 6,
    'short' => 'Support user id',
    'long' => 'As of version 2.6 Moodle supports admin to set support user. If not set, all mails
 will be sent to supportemail.',
  ),
  'svgicons' => 
  array (
    'count' => 11,
    'short' => 'SVG icons',
    'long' => 'As of version 2.4 Moodle serves icons as SVG images if the users browser appears
 to support SVG.
 For those wanting to control the serving of SVG images the following setting can
 be defined in your config.php.
 If it is not defined then the default (browser detection) will occur.',
  ),
  'tagsort' => 
  array (
    'count' => 11,
    'short' => 'Tag sort',
    'long' => 'Sort the tag display by',
    'long_help' => 
    array (
      0 => 'tagsort',
      1 => 'blog',
    ),
  ),
  'target_release' => 
  array (
    'count' => 14,
    'short' => 'Moodle {$a} command line installation program',
    'long' => 'Installation',
    'short_help' => 
    array (
      0 => 'cliinstallheader',
      1 => 'install',
    ),
    'long_help' => 
    array (
      0 => 'installation',
      1 => 'install',
    ),
  ),
  'tempdatafoldercleanup' => 
  array (
    'count' => 2,
    'short' => 'Clean up temporary data files older than',
    'long' => 'Remove temporary data files from the data folder that are older than the selected time.',
  ),
  'tempdir' => 
  array (
    'count' => 181,
    'short' => 'Temp dir',
    'long' => 'for custom $CFG->tempdir locations',
  ),
  'texteditors' => 
  array (
    'count' => 33,
    'short' => 'Text ediotors',
    'long' => '',
  ),
  'theme' => 
  array (
    'count' => 13,
    'short' => 'Theme',
    'long' => '',
  ),
  'themedesignermode' => 
  array (
    'count' => 7,
    'short' => 'Theme designer mode',
    'long' => 'Normally all theme images and style sheets are cached in browsers and on the server for a very long time, for performance. If you are designing themes or developing code then you probably want to turn this mode on so that you are not served cached versions.  Warning: this will make your site slower for all users!  Alternatively, you can also reset the theme caches manually from the Theme selection page.',
  ),
  'themedir' => 
  array (
    'count' => 24,
    'short' => 'Theme directory',
    'long' => 'It is possible to add extra themes directory stored outside of $CFG->dirroot.
 This local directory does not have to be accessible from internet.',
  ),
  'themelist' => 
  array (
    'count' => 2,
    'short' => 'Theme list',
    'long' => 'Leave this blank to allow any valid theme to be used.  If you want to shorten the theme menu, you can specify a comma-separated list of names here (Don\\\'t use spaces!).
For example:  standard,orangewhite.',
  ),
  'themeorder' => 
  array (
    'count' => 4,
    'short' => 'Theme order',
    'long' => 'Set the priority of themes from highest to lowest. This is useful (for
 example) in sites where the user theme should override all other theme
 settings for accessibility reasons. You can also disable types of themes
 (other than site)  by removing them from the array. The default setting is:
      $CFG->themeorder = array(\'course\', \'category\', \'session\', \'user\', \'site\');
 NOTE: course, category, session, user themes still require the
 respective settings to be enabled',
  ),
  'themerev' => 
  array (
    'count' => 8,
    'short' => 'Theme rev',
    'long' => '',
  ),
  'timezone' => 
  array (
    'count' => 46,
    'short' => 'Timezone',
    'long' => 'This is the default timezone for displaying dates - each user can override this setting in their profile. Cron tasks and other server settings are specified in this timezone. You should change the setting if it shows as "Invalid timezone"',
  ),
  'tool_dbransfer_migration_running' => 
  array (
    'count' => 4,
    'short' => 'Tool dbransfer migration running',
    'long' => '',
  ),
  'tool_generator_users_password' => 
  array (
    'count' => 20,
    'short' => 'Tool generator users password',
    'long' => 'The developer data generator tool is intended to be used only in development or testing sites and
 it\'s usage in production environments is not recommended; if it is used to create JMeter test plans
 is even less recommended as JMeter needs to log in as site course users. JMeter needs to know the
 users passwords but would be dangerous to have a default password as everybody would know it, which would
 be specially dangerouse if somebody uses this tool in a production site, so in order to prevent unintended
 uses of the tool and undesired accesses as well, is compulsory to set a password for the users
 generated by this tool, but only in case you want to generate a JMeter test. The value should be a string.
 Example:
   $CFG->tool_generator_users_password = \'examplepassword\';',
  ),
  'tracksessionip' => 
  array (
    'count' => 3,
    'short' => 'Track session IP',
    'long' => 'If this setting is set to true, then Moodle will track the IP of the
 current user to make sure it hasn\'t changed during a session.  This
 will prevent the possibility of sessions being hijacked via XSS, but it
 may break things for users coming using proxies that change all the time,
 like AOL.',
  ),
  'trashdir' => 
  array (
    'count' => 2,
    'short' => 'Trash directory',
    'long' => '',
  ),
  'umaskpermissions' => 
  array (
    'count' => 9,
    'short' => 'Umask permissions',
    'long' => '',
  ),
  'undeletableblocktypes' => 
  array (
    'count' => 12,
    'short' => 'Undeletable block types',
    'long' => 'List of undeletable block types',
  ),
  'unittestprefix' => 
  array (
    'count' => 4,
    'short' => 'Unit test prefix',
    'long' => '',
  ),
  'unlimitedgrades' => 
  array (
    'count' => 4,
    'short' => 'Unlimited grades',
    'long' => 'By default grades are limited by the maximum and minimum values of the grade item. Enabling this setting removes this limit, and allows grades of over 100% to be entered directly in the gradebook. It is recommended that this setting is enabled at an off-peak time, as all grades will be recalculated, which may result in a high server load.',
  ),
  'updateautocheck' => 
  array (
    'count' => 1,
    'short' => 'Automatically check for available updates',
    'long' => 'If enabled, your site will automatically check for available updates for both Moodle code and all additional plugins. If there is a new update available, a notification will be sent to site admins.',
  ),
  'updatecronoffset' => 
  array (
    'count' => 2,
    'short' => 'Update cron offset',
    'long' => '',
  ),
  'updateminmaturity' => 
  array (
    'count' => 4,
    'short' => 'Required code maturity',
    'long' => 'Notify about available updates only if the available code has the selected maturity level at least. Updates for plugins that do not declare their code maturity level are always reported regardless this setting.',
  ),
  'updatenotifybuilds' => 
  array (
    'count' => 2,
    'short' => 'Notify about new builds',
    'long' => 'If enabled, the available update for Moodle code is also reported when a new build for the current version is available. Builds are continuous improvements of a given Moodle version. They are generally released every week. If disabled, the available update will be reported only when there is a higher version of Moodle released. Checks for plugins are not affected by this setting.',
  ),
  'upgrade_calculatedgradeitemsignored' => 
  array (
    'count' => 1,
    'short' => 'Upgrade calculated grade items ignored',
    'long' => 'New installs should not run this upgrade step.',
  ),
  'upgrade_calculatedgradeitemsonlyregrade' => 
  array (
    'count' => 1,
    'short' => 'Upgrade calculate grade items only regrade',
    'long' => '',
  ),
  'upgrade_extracreditweightsstepignored' => 
  array (
    'count' => 1,
    'short' => 'Upgrade extrac r edit weights step ignored',
    'long' => 'New installs should not run this upgrade step.
',
  ),
  'upgrade_minmaxgradestepignored' => 
  array (
    'count' => 1,
    'short' => 'Upgrade min/max grade step ignored',
    'long' => 'New installs should not run this upgrade step.',
  ),
  'upgraderunning' => 
  array (
    'count' => 8,
    'short' => 'Upgrade running',
    'long' => '',
  ),
  'upgradeshowsql' => 
  array (
    'count' => 3,
    'short' => 'Upgrade show sql',
    'long' => 'Since 2.0 sql queries are not shown during upgrade by default.
 Please note that this setting may produce very long upgrade page on large sites.
 $CFG->upgradeshowsql = true; // NOT FOR PRODUCTION SERVERS!',
  ),
  'useblogassociations' => 
  array (
    'count' => 20,
    'short' => 'Enable blog associations',
    'long' => 'Enables the association of blog entries with courses and course modules.',
  ),
  'usecomments' => 
  array (
    'count' => 31,
    'short' => 'Enable comments',
    'long' => 'Enable comments',
    'short_help' => 
    array (
      0 => 'enablecomments',
      1 => 'admin',
    ),
    'long_help' => 
    array (
      0 => 'configenablecomments',
      1 => 'admin',
    ),
  ),
  'useexternalblogs' => 
  array (
    'count' => 3,
    'short' => 'Enable external blogs',
    'long' => 'Enables users to specify external blog feeds. Moodle regularly checks these blog feeds and copies new entries to the local blog of that user.',
  ),
  'useexternalyui' => 
  array (
    'count' => 3,
    'short' => 'Use online YUI libraries',
    'long' => 'Instead of using local files, use online files available on Yahoo&#145;s servers. WARNING: This requires an internet connection, or no AJAX will work on your site. This setting is not compatible with sites using https.',
  ),
  'usepaypalsandbox' => 
  array (
    'count' => 1,
    'short' => 'Use paypal sandbox',
    'long' => '',
  ),
  'userquota' => 
  array (
    'count' => 10,
    'short' => 'User quota',
    'long' => 'The maximum number of bytes that a user can store in their own private file area. {$a->bytes} bytes == {$a->displaysize}',
  ),
  'usesitenameforsitepages' => 
  array (
    'count' => 1,
    'short' => 'Use site name for site pages',
    'long' => 'If enabled the site\\\'s shortname will be used for the site pages node in the navigation rather than the string \\\'Site pages\\\'',
  ),
  'usetags' => 
  array (
    'count' => 52,
    'short' => 'Enable tags functionality',
    'long' => 'Should tags functionality across the site be enabled?',
  ),
  'usezipbackups' => 
  array (
    'count' => 5,
    'short' => 'Use zip backups',
    'long' => 'Force the backup system to continue to create backups in the legacy zip
 format instead of the new tgz format. Does not affect restore, which
 auto-detects the underlying file format.',
  ),
  'verifychangedemail' => 
  array (
    'count' => 1,
    'short' => 'Restrict domains when changing email',
    'long' => 'Enables verification of changed email addresses using allowed and denied email domains settings. If this setting is disabled the domains are enforced only when creating new users.',
  ),
  'version' => 
  array (
    'count' => 73,
    'short' => 'Version',
    'long' => '[[mdl_version]]',
    'long_help' => 
    array (
      0 => 'mdl_version',
      1 => 'cache',
    ),
  ),
  'webserviceprotocols' => 
  array (
    'count' => 17,
    'short' => 'Web service protocols',
    'long' => 'Manage protocols',
    'long_help' => 
    array (
      0 => 'manageprotocols',
      1 => 'webservice',
    ),
  ),
  'wordlist' => 
  array (
    'count' => 2,
    'short' => 'Word list',
    'long' => 'Results of searching user profiles containing:',
    'long_help' => 
    array (
      0 => 'spamresult',
      1 => 'tool_spamcleaner',
    ),
  ),
  'wwwdir' => 
  array (
    'count' => 1,
    'short' => 'WWW directory',
    'long' => '',
  ),
  'wwwroot' => 
  array (
    'count' => 1400,
    'short' => 'Web site location',
    'long' => 'Path to moodle index directory in url format',
  ),
  'xmldbdisablecommentchecking' => 
  array (
    'count' => 4,
    'short' => 'XML db disable comment checking',
    'long' => 'Uncomment if you want to allow empty comments when modifying install.xml files.
 $CFG->xmldbdisablecommentchecking = true;    // NOT FOR PRODUCTION SERVERS!',
  ),
  'xsendfile' => 
  array (
    'count' => 12,
    'short' => 'X send file',
    'long' => 'Some web servers can offload the file serving from PHP process',
  ),
  'xsendfilealiases' => 
  array (
    'count' => 5,
    'short' => 'X send file aliases',
    'long' => 'If your X-Sendfile implementation (usually Nginx) uses directory aliases specify them
 in the following array setting:
     $CFG->xsendfilealiases = array(
         \'/dataroot/\' => $CFG->dataroot,
         \'/cachedir/\' => \'/var/www/moodle/cache\',    // for custom $CFG->cachedir locations
         \'/localcachedir/\' => \'/var/local/cache\',    // for custom $CFG->localcachedir locations
         \'/tempdir/\'  => \'/var/www/moodle/temp\',     // for custom $CFG->tempdir locations
         \'/filedir\'   => \'/var/www/moodle/filedir\',  // for custom $CFG->filedir locations
     );',
  ),
  'xx' => 
  array (
    'count' => 4,
    'short' => 'XX',
    'long' => '',
  ),
  'yui2version' => 
  array (
    'count' => 6,
    'short' => 'YUI 2 version',
    'long' => '',
  ),
  'yui3version' => 
  array (
    'count' => 12,
    'short' => 'YUI 3 version',
    'long' => '',
  ),
  'yuicomboloading' => 
  array (
    'count' => 1,
    'short' => 'YUI combo loading',
    'long' => 'This options enables combined file loading optimisation for YUI libraries. This setting should be enabled on production sites for performance reasons.',
  ),
  'yuilogexclude' => 
  array (
    'count' => 5,
    'short' => '',
    'long' => 'Restrict which YUI logging statements are shown in the browser console.
 For details see the upstream documentation:
   http://yuilibrary.com/yui/docs/api/classes/config.html#property_logExclude
   $CFG->yuilogexclude = array(
     \'moodle-core-dock\' => true,
     \'moodle-core-notification\' => true,
 );',
  ),
  'yuiloginclude' => 
  array (
    'count' => 5,
    'short' => 'YUI log include',
    'long' => 'Restrict which YUI logging statements are shown in the browser console.
 For details see the upstream documentation:
   http://yuilibrary.com/yui/docs/api/classes/config.html#property_logInclude
    $CFG->yuiloginclude = array(
    \'moodle-core-dock-loader\' => true,
    \'moodle-course-categoryexpander\' => true,',
  ),
  'yuiloglevel' => 
  array (
    'count' => 4,
    'short' => 'YUI log level',
    'long' => 'Set the minimum log level for YUI logging statements.
 For details see the upstream documentation:
   http://yuilibrary.com/yui/docs/api/classes/config.html#property_logLevel
 $CFG->yuiloglevel = \'debug\';',
  ),
  'yuipatchedmodules' => 
  array (
    'count' => 3,
    'short' => 'YUI patched modules',
    'long' => 'List of YUI patched modules',
  ),
  'yuipatchlevel' => 
  array (
    'count' => 5,
    'short' => 'YUI patch level',
    'long' => 'If we need to patch a YUI modules between official YUI releases, the yuipatchlevel will need to be manually
',
  ),
  'yuislasharguments' => 
  array (
    'count' => 3,
    'short' => 'YUI slash arguments',
    'long' => 'YUI caching may be sometimes improved by slasharguments:
     $CFG->yuislasharguments = 1;',
  ),
);