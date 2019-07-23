<?php
class moodle_config {
    /**
     * 
     *
     * @var string CSV_DELIMITER CSV delimiter
     */
    public $CSV_DELIMITER;

    /**
     * 
     *
     * @var string CSV_ENCODE CSV encode
     */
    public $CSV_ENCODE;

    /**
Content here will be added in to every page right before the body tag is closed.
     *
     * @var string additionalhtmlfooter Before BODY is closed
     */
    public $additionalhtmlfooter;

    /**
Content here will be added to the bottom of the HEAD tag for every page.
     *
     * @var string additionalhtmlhead Within HEAD
     */
    public $additionalhtmlhead;

    /**
Content here will be added in to every page immediately after the opening body tag.
     *
     * @var string additionalhtmltopofbody When BODY is opened
     */
    public $additionalhtmltopofbody;

    /**

     *
     * @var string admin Admin
     */
    public $admin;

    /**
     * 
     *
     * @var string adminsassignrolesincourse Admins assign roles in course
     */
    public $adminsassignrolesincourse;

    /**
     * 
     *
     * @var string adminsetuppending Admin set up pending
     */
    public $adminsetuppending;

    /**
The access key to use when connecting to the airnotifier server.
     *
     * @var string airnotifieraccesskey Airnotifier access key
     */
    public $airnotifieraccesskey;

    /**
The app name identifier in Airnotifier.
     *
     * @var string airnotifierappname Airnotifier app name
     */
    public $airnotifierappname;

    /**
The Mobile app unique identifier (usually something like com.moodle.moodlemobile).
     *
     * @var string airnotifiermobileappname Mobile app name
     */
    public $airnotifiermobileappname;

    /**
The port to use when connecting to the airnotifier server.
     *
     * @var string airnotifierport Airnotifier port
     */
    public $airnotifierport;

    /**
The server url to connect to to send push notifications.
     *
     * @var string airnotifierurl Airnotifier URL
     */
    public $airnotifierurl;

    /**
This is the list of countries that may be selected in various places, for example in a user\'s profile. If blank (the default) the list in countries.php in the standard English language pack is used. That is the list from ISO 3166-1. Otherwise, you can specify a comma-separated list of codes, for example \'GB,FR,ES\'. If you add new, non-standard codes here, you will need to add them to countries.php in \'en\' and your language pack.
     *
     * @var string allcountrycodes All country codes
     */
    public $allcountrycodes;

    /**
If enabled, more than one user account can share the same email address. This may result in security or privacy issues, for example with the password change confirmation email.
     *
     * @var string allowaccountssameemail Allow accounts with same email
     */
    public $allowaccountssameemail;

    /**
If enabled, emails sent from the site can have attachments, such as badges.
     *
     * @var string allowattachments Allow attachments
     */
    public $allowattachments;

    /**

     *
     * @var string allowbeforeblock Allowed list will be processed first
     */
    public $allowbeforeblock;

    /**
If enabled and supported by the selected theme users can choose to move blocks to a special dock.
     *
     * @var string allowblockstodock Allow blocks to use the dock
     */
    public $allowblockstodock;

    /**
If you enable this, then themes can be set at the category level. This will affect all child categories and courses unless they have specifically set their own theme. WARNING: Enabling category themes may affect performance.
     *
     * @var string allowcategorythemes Allow category themes
     */
    public $allowcategorythemes;

    /**
If you enable this, then courses will be allowed to set their own themes.  Course themes override all other theme choices (site, user, or session themes)
     *
     * @var string allowcoursethemes Allow course themes
     */
    public $allowcoursethemes;

    /**
List email domains that are allowed to be disclosed in the "From" section of outgoing email. The default of "Empty" will use the No-reply address for all outgoing email. The use of wildcards is allowed e.g. *.example.com will allow emails sent from any subdomain of example.com, but not example.com itself. This will require separate entry.
     *
     * @var string allowedemaildomains Allowed email domains
     */
    public $allowedemaildomains;

    /**
     * Allowed IP list
     *
     * @var string allowedip Allowed IP
     */
    public $allowedip;

    /**
To restrict new email addresses to particular domains, list them here separated by spaces. All other domains will be rejected. To allow subdomains, add the domain with a preceding \'.\'. To allow a root domain together with its subdomains, add the domain twice - once with a preceding \'.\' and once without e.g. .ourcollege.edu.au ourcollege.edu.au.
     *
     * @var string allowemailaddresses Allowed email domains
     */
    public $allowemailaddresses;

    /**
If enabled, this site may be embedded in a frame in a remote system, as recommended when using the \'Publish as LTI tool\' enrolment plugin. Otherwise, it is recommended to leave frame embedding disabled for security reasons.
     *
     * @var string allowframembedding Allow frame embedding
     */
    public $allowframembedding;

    /**
If enabled guests can access Dashboard, otherwise guests are redirected to the site front page.
     *
     * @var string allowguestmymoodle Allow guest access to Dashboard
     */
    public $allowguestmymoodle;

    /**
This determines whether to allow search engines to index your site. "Everywhere" will allow the search engines to search everywhere including login and signup pages, which means sites with Force Login turned on are still indexed. To avoid the risk of spam involved with the signup page being searchable, use "Everywhere except login and signup pages". "Nowhere" will tell search engines not to index any page. Note this is only a tag in the header of the site. It is up to the search engine to respect the tag.
     *
     * @var string allowindexing Allow indexing by search engines
     */
    public $allowindexing;

    /**
As a default security measure, normal users are not allowed to embed multimedia (like Flash) within texts using explicit EMBED and OBJECT tags in their HTML (although it can still be done safely using the mediaplugins filter).  If you wish to allow these tags then enable this option.
     *
     * @var string allowobjectembed Allow EMBED and OBJECT tags
     */
    public $allowobjectembed;

    /**
     * If enabled, activities can be made available but not shown in visible sections of the course page. If so, links to stealth activities must be provided from elsewhere, such as from a page resource. Stealth activities are still listed in the gradebook and other reports.
     *
     * @var string allowstealth Allow stealth activities
     */
    public $allowstealth;

    /**
If enabled, the theme can be changed by adding either:<br />?theme=themename to any Moodle URL (eg: mymoodlesite.com/?theme=afterburner ) or <br />&theme=themename to any internal Moodle URL (eg: mymoodlesite.com/course/view.php?id=2&theme=afterburner ).
     *
     * @var string allowthemechangeonurl Allow theme changes in the URL
     */
    public $allowthemechangeonurl;

    /**
Do you want to allow users to hide/show side blocks throughout this site?  This feature uses Javascript and cookies to remember the state of each collapsible block, and only affects the user\'s own view.
     *
     * @var string allowuserblockhiding Allow users to hide blocks
     */
    public $allowuserblockhiding;

    /**
If enabled, users can choose an email charset in their messaging preferences.
     *
     * @var string allowusermailcharset Allow user to select character set
     */
    public $allowusermailcharset;

    /**
If you enable this, then users will be allowed to set their own themes.  User themes override site themes (but not course themes)
     *
     * @var string allowuserthemes Allow user themes
     */
    public $allowuserthemes;

    /**
     * 
     *
     * @var string allversionshash All versions hash
     */
    public $allversionshash;

    /**
     * Moodle 2.4 introduced a new cache API.
     *  The cache API stores a configuration file within the Moodle data directory and
     *  uses that rather than the database in order to function in a stand-alone manner.
     *  Using altcacheconfigpath you can change the location where this config file is
     *  looked for.
     *  It can either be a directory in which to store the file, or the full path to the
     *  file if you want to take full control. Either way it must be writable by the
     *  webserver
     *
     * @var string altcacheconfigpath Alt cache config path
     */
    public $altcacheconfigpath;

    /**

     *
     * @var string alternateloginurl Alternate login URL
     */
    public $alternateloginurl;

    /**
     * 
     *
     * @var string alternative_component_cache Alternative component cache
     */
    public $alternative_component_cache;

    /**
     * Since 3.3 it is possible to override file_storage and file_system API and use alternative storage systems (e.g. S3,
     * Rackspace Cloud Files, Google Cloud Storage, Azure Storage, etc.).
     * To set the alternative file storage system in config.php you can use the following setting, providing the
     * alternative system class name that will be auto-loaded by file_storage API.
     *
     * @var string alternative_file_system_class  Alternative file system
     */
    public $alternative_file_system_class;

    /**
This defines how names are shown to users with the viewfullnames capability (by default users with the role of manager, teacher or non-editing teacher). Placeholders that can be used are as for the "Full name format" setting.
     *
     * @var string alternativefullnameformat Alternative full name format
     */
    public $alternativefullnameformat;

    /**

     *
     * @var string antiviruses Antivirus plugins
     */
    public $antiviruses;

    /**
     *  The following setting will turn on username logging into Apache log. For full details regarding setting
     *  up of this function please refer to the install section of the document.
     *      $CFG->apacheloguser = 0; // Turn this feature off. Default value.
     *      $CFG->apacheloguser = 1; // Log user id.
     *      $CFG->apacheloguser = 2; // Log full name in cleaned format. ie, Darth Vader will be displayed as darth_vader.
     *      $CFG->apacheloguser = 3; // Log username.
     *  To get the values logged in Apache's log, add to your httpd.conf
     *  the following statements. In the General part put:
     *      LogFormat "%h %l %{MOODLEUSER}n %t \"%r\" %s %b \"%{Referer}i\" \"%{User-Agent}i\"" moodleformat
     *  And in the part specific to your Moodle install / virtualhost:
     *      CustomLog "/your/path/to/log" moodleformat
     *  CAUTION: Use of this option will expose usernames in the Apache log,
     *  If you are going to publish your log, or the output of your web stats analyzer
     *  this will weaken the security of your website.
     *
     * @var string apacheloguser Apache log user
     */
    public $apacheloguser;

    /**
     * 
     *
     * @var string apachemaxmem Apache max memory
     */
    public $apachemaxmem;

    /**

     *
     * @var string aspellpath Path to aspell
     */
    public $aspellpath;

    /**
     * Authentication
     *
     * @var string auth Authentication
     */
    public $auth;

    /**
     * Use the <a href="{$a}">Shibboleth login</a> to get access via Shibboleth, if your institution supports it.<br />Otherwise, use the normal login form shown here.
     *
     * @var string auth_instructions Auth instructions
     */
    public $auth_instructions;

    /**
Allow users to use both username and email address (if unique) for site login.
     *
     * @var string authloginviaemail Allow log in via email
     */
    public $authloginviaemail;

    /**
When a user authenticates, an account on the site is automatically created if it doesn\'t yet exist. If an external database, such as LDAP, is used for authentication, but you wish to restrict access to the site to users with an existing account only, then this option should be enabled. New accounts will need to be created manually or via the upload users feature. Note that this setting doesn\'t apply to MNet authentication.
     *
     * @var string authpreventaccountcreation Prevent account creation when authenticating
     */
    public $authpreventaccountcreation;

    /**
Detect default language from browser setting, if disabled site default is used.
     *
     * @var string autolang Language autodetect
     */
    public $autolang;

    /**
Should visitors be logged in as guests automatically when entering courses with guest access?
     *
     * @var string autologinguests Auto-login guests
     */
    public $autologinguests;

    /**
     * 
     *
     * @var string backup_database_logger_level Backup database logger level
     */
    public $backup_database_logger_level;

    /**
     * 
     *
     * @var string backup_error_log_logger_level Backup error log logger level
     */
    public $backup_error_log_logger_level;

    /**
     * 
     *
     * @var string backup_file_logger_extra Backup file logger extra
     */
    public $backup_file_logger_extra;

    /**
     * 
     *
     * @var string backup_file_logger_extra_level Backup file logger extra level
     */
    public $backup_file_logger_extra_level;

    /**
     * 
     *
     * @var string backup_file_logger_level Backup file logger level
     */
    public $backup_file_logger_level;

    /**
     * 
     *
     * @var string backup_file_logger_level_extra Backup file logger level extra
     */
    public $backup_file_logger_level_extra;

    /**
     * 
     *
     * @var string backup_output_indented_logger_level Backup output indented logger level
     */
    public $backup_output_indented_logger_level;

    /**
     * 
     *
     * @var string backup_release Backup relase
     */
    public $backup_release;

    /**
     * 
     *
     * @var string backup_version Backup version
     */
    public $backup_version;

    /**
Allow badges to be created and awarded in the course context.
     *
     * @var string badges_allowcoursebadges Enable course badges
     */
    public $badges_allowcoursebadges;

    /**
Allow users to set up connections and display badges from their external backpack providers.

Note: It is recommended to leave this option disabled if the website cannot be accessed from the Internet (e.g. because of the firewall).
     *
     * @var string badges_allowexternalbackpack Enable connection to external backpacks
     */
    public $badges_allowexternalbackpack;

    /**
Using a hash allows backpack services to confirm the badge earner without having to expose their email address. This setting should only use numbers and letters.

Note: For recipient verification purposes, please avoid changing this setting once you start issuing badges.
     *
     * @var string badges_badgesalt Salt for hashing the recipient\'s email address
     */
    public $badges_badgesalt;

    /**
An email address associated with the badge issuer.
     *
     * @var string badges_defaultissuercontact Default badge issuer contact details
     */
    public $badges_defaultissuercontact;

    /**
Name of the issuing agent or authority.
     *
     * @var string badges_defaultissuername Default badge issuer name
     */
    public $badges_defaultissuername;

    /**
     * Error running behat CLI command. Try running "{$a} --help" manually from CLI to find out more about the problem.
     *
     * @var string behat_ Behat
     */
    public $behat_;

    /**
     * Including feature files from directories outside the dirroot is possible if required. The setting
     *  requires that the running user has executable permissions on all parent directories in the paths.
     *  Example:
     *    $CFG->behat_additionalfeatures = array('/home/developer/code/wipfeatures');
     *
     * @var string behat_additionalfeatures Behat additional features
     */
    public $behat_additionalfeatures;

    /**
     *  You can override default Moodle configuration for Behat and add your own
     *  params; here you can add more profiles, use different Mink drivers than Selenium...
     *  These params would be merged with the default Moodle behat.yml, giving priority
     *  to the ones specified here. The array format is YAML, following the Behat
     *  params hierarchy. More info: http://docs.behat.org/guides/7.config.html
     *  Example:
     *    $CFG->behat_config = array(
     *        'default' => array(
     *            'formatter' => array(
     *                'name' => 'pretty',
     *                'parameters' => array(
     *                    'decorated' => true,
     *                    'verbose' => false
     *                )
     *            )
     *        ),
     *       
     *            
     *    );
     *
     * @var string behat_config Behat config
     */
    public $behat_config;

    /**
     *  Behat test site needs a unique www root, data directory and database prefix:
     *             $CFG->behat_dataroot = '/home/example/bht_moodledata';
     *             
     *
     * @var string behat_dataroot Behat dataroot
     */
    public $behat_dataroot;

    /**

     *
     * @var string behat_dataroot_parent 
     */
    public $behat_dataroot_parent;

    /**
     * All this page's extra Moodle settings are compared against a white list of allowed settings
     *  (the basic and behat_* ones) to avoid problems with production environments. This setting can be
     *  used to expand the default white list with an array of extra settings.
     *  Example:
     *    $CFG->behat_extraallowedsettings = array('somecoresetting', ...);
     *
     * @var string behat_extraallowedsettings Behat extra allowed settings
     */
    public $behat_extraallowedsettings;

    /**
     * You can make behat save several dumps when a scenario fails. The dumps currently saved are:
     *  * a dump of the DOM in it's state at the time of failure; and
     *  * a screenshot (JavaScript is required for the screenshot functionality, so not all browsers support this option)
     *  Example:
     *    $CFG->behat_faildump_path = '/my/path/to/save/failure/dumps';
     *
     * @var string behat_faildump_path Behat faildump path
     */
    public $behat_faildump_path;

    /**
     *  You can specify db, selenium wd_host etc. for behat parallel run by setting following variable.
     *  Example:
     *    $CFG->behat_parallel_run = array (
     *        array (
     *            'dbtype' => 'mysqli',
     *            'dblibrary' => 'native',
     *            'dbhost' => 'localhost',
     *            'dbname' => 'moodletest',
     *            'dbuser' => 'moodle',
     *            'dbpass' => 'moodle',
     *            'behat_prefix' => 'mdl_',
     *            'wd_host' => 'http://127.0.0.1:4444/wd/hub',
     *            'behat_wwwroot' => 'http://127.0.0.1/moodle',
     *            'behat_dataroot' => '/home/example/bht_moodledata'
     *        ),
     *    );
     *
     * @var string behat_parallel_run Behat parallel run
     */
    public $behat_parallel_run;

    /**
This prefix is used for all key names on the Redis server.
* If you only have one Moodle instance using this server, you can leave this value default.
* Due to key length restrictions, a maximum of 5 characters is permitted.
     *
     * @var string behat_prefix Key prefix
     */
    public $behat_prefix;

    /**
     * You can also use the following config to override default Moodle configuration for Behat.
     * // This config is limited to default suite and will be supported in later versions.
     * // It will have precedence over $CFG->behat_config.
     *
     * @var string behat_profiles behat_profiles
     */
    public $behat_profiles;

    /**
     * You can force the browser session (not user's sessions) to restart after N seconds. This could
     *  be useful if you are using a cloud-based service with time restrictions in the browser side.
     *  Setting this value the browser session that Behat is using will be restarted. Set the time in
     *  seconds. Is not recommended to use this setting if you don't explicitly need it.
     *  Example:
     *    $CFG->behat_restart_browser_after = 7200;     // Restarts the browser session after 2 hours
     *
     * @var string behat_restart_browser_after Behat restart browser after
     */
    public $behat_restart_browser_after;

    /**
     * You should explicitly allow the usage of the deprecated behat steps, otherwise an exception will
     *  be thrown when using them. The setting is disabled by default.
     *  Example:
     *    $CFG->behat_usedeprecated = true;
     *
     * @var string behat_usedeprecated Behat use deprecated
     */
    public $behat_usedeprecated;

    /**
     * 
     *
     * @var string behat_wwwroot Behat WWWROOT
     */
    public $behat_wwwroot;

    /**
     * 
     *
     * @var string behatrunprocess Behat run process
     */
    public $behatrunprocess;

    /**
     * Admin view
     *
     * @var string block_course_list_adminview Course list admin view
     */
    public $block_course_list_adminview;

    /**
     * Hide 'All courses' link
     *
     * @var string block_course_list_hideallcourseslink Hide all courses link
     */
    public $block_course_list_hideallcourseslink;

    /**
     * Allow additional CSS classes
     *
     * @var string block_html_allowcssclasses Allow css classes in HTML
     */
    public $block_html_allowcssclasses;

    /**
     * Remove after inactivity (minutes)
     *
     * @var string block_online_users_timetosee Online users time to see
     */
    public $block_online_users_timetosee;

    /**
     * Entries per feed
     *
     * @var string block_rss_client_num_entries RSS client num entries
     */
    public $block_rss_client_num_entries;

    /**
     * Timeout
     *
     * @var string block_rss_client_timeout RSS client timeout
     */
    public $block_rss_client_timeout;

    /**
     * Blocked IP List
     *
     * @var string blockedip Blocked IP
     */
    public $blockedip;

    /**
     * You can specify a different class to be created for the $PAGE global, and to
     *  compute which blocks appear on each page. However, I cannot think of any good
     *  reason why you would need to change that. It just felt wrong to hard-code the
     *  the class name. You are strongly advised not to use these to settings unless
     *  you are absolutely sure you know what you are doing.
     *  $CFG->blockmanagerclass = 'block_manager';
     *
     * @var string blockmanagerclass Blog manager class
     */
    public $blockmanagerclass;

    /**
     * You can specify a different class to be created for the $PAGE global, and to
     *  compute which blocks appear on each page. However, I cannot think of any good
     *  reason why you would need to change that. It just felt wrong to hard-code the
     *  the class name. You are strongly advised not to use these to settings unless
     *  you are absolutely sure you know what you are doing.
     *  $CFG->blockmanagerclassfile = "$CFG->dirroot/local/myplugin/myblockamanagerclass.php";
     *
     * @var string blockmanagerclassfile Block manager class file
     */
    public $blockmanagerclassfile;

    /**
     * 
     *
     * @var string blocksdrag Blocks drag
     */
    public $blocksdrag;

    /**
This setting allows you to restrict the level to which user blogs can be viewed on this site.  Note that they specify the maximum context of the VIEWER not the poster or the types of blog posts.  Blogs can also be disabled completely if you don\'t want them at all.
     *
     * @var string bloglevel Blog visibility
     */
    public $bloglevel;

    /**
     * Show comments count, it will cost one more query when display comments link
     *
     * @var string blogshowcommentscount Show comments count
     */
    public $blogshowcommentscount;

    /**
     * Enable comments
     *
     * @var string blogusecomments Enable comments
     */
    public $blogusecomments;

    /**
     * The following line is for handling email bounces
     *
     * @var string bounceratio Bounce ratio
     */
    public $bounceratio;

    /**

     *
     * @var string branch Content
     */
    public $branch;

    /**
     * Path to moodles cache directory on servers filesystem (shared by cluster nodes)
     *
     * @var string cachedir Cache directory
     */
    public $cachedir;

    /**
Javascript caching and compression greatly improves page loading performance. it is strongly recommended for production sites. Developers will probably want to disable this feature.
     *
     * @var string cachejs Cache Javascript
     */
    public $cachejs;

    /**
     * Admins see all
     *
     * @var string calendar_adminseesall Calendar admin see sall
     */
    public $calendar_adminseesall;

    /**
     * Enable custom date range export option in calendar exports. Calendar exports must be enabled before this is effective.
     *
     * @var string calendar_customexport Enable custom date range export of calendar
     */
    public $calendar_customexport;

    /**
     * How many days in the future does the calendar look for events during export for the custom export option?
     *
     * @var string calendar_exportlookahead Days to look ahead during export
     */
    public $calendar_exportlookahead;

    /**
     * How many days in the past does the calendar look for events during export for the custom export option?
     *
     * @var string calendar_exportlookback Days to look back during export
     */
    public $calendar_exportlookback;

    /**
     * This random text is used for improving of security of authentication tokens used for exporting of calendars. Please note that all current tokens are invalidated if you change this hash salt.
     *
     * @var string calendar_exportsalt Calendar export salt
     */
    public $calendar_exportsalt;

    /**
     * How many days in the future does the calendar look for upcoming events by default?
     *
     * @var string calendar_lookahead Calendar look ahead
     */
    public $calendar_lookahead;

    /**
     * How many (maximum) upcoming events are shown to users by default?
     *
     * @var string calendar_maxevents Calendar max events
     */
    public $calendar_maxevents;

    /**
     * If enabled, the subscription name and link will be shown for iCal-imported events.
     *
     * @var string calendar_showicalsource Show source information for iCal events
     */
    public $calendar_showicalsource;

    /**
     * Which day starts the week in the calendar?
     *
     * @var string calendar_startwday Start of week
     */
    public $calendar_startwday;

    /**

     *
     * @var string calendar_weekend Weekend days
     */
    public $calendar_weekend;

    /**
Choose a default calendar type for the whole site. This setting can be overridden in the course settings or by users in their personal profile.
     *
     * @var string calendartype Calendar type
     */
    public $calendartype;

    /**

     *
     * @var string chart_colorset 
     */
    public $chart_colorset;

    /**
The ajax chat method provide an ajax based chat interface, it contacts server regularly for update. The normal chat method involves the clients regularly contacting the server for updates. It requires no configuration and works everywhere, but it can create a large load on the server with many chatters.  Using a server daemon requires shell access to Unix, but it results in a fast scalable chat environment.
     *
     * @var string chat_method Method
     */
    public $chat_method;

    /**
     * Update method
     *
     * @var string chat_normal_updatemode Chat normal update mode
     */
    public $chat_normal_updatemode;

    /**
     * Disconnect timeout
     *
     * @var string chat_old_ping Chat old ping
     */
    public $chat_old_ping;

    /**
     * Refresh room
     *
     * @var string chat_refresh_room Chat refresh room
     */
    public $chat_refresh_room;

    /**
     * Refresh user list
     *
     * @var string chat_refresh_userlist Chat refresh userlist
     */
    public $chat_refresh_userlist;

    /**
The hostname of the computer where the server daemon is
     *
     * @var string chat_serverhost Server name
     */
    public $chat_serverhost;

    /**
The numerical IP address that matches the above hostname
     *
     * @var string chat_serverip Server ip
     */
    public $chat_serverip;

    /**
Max number of clients allowed
     *
     * @var string chat_servermax Max users
     */
    public $chat_servermax;

    /**
Port to use on the server for the daemon
     *
     * @var string chat_serverport Server port
     */
    public $chat_serverport;

    /**

     *
     * @var string commentsperpage Comments displayed per page
     */
    public $commentsperpage;

    /**
The default setting for completion tracking when creating new activities.
     *
     * @var string completiondefault Default completion tracking
     */
    public $completiondefault;

    /**
     * 
     *
     * @var string config_php_settings Config PHP settings
     */
    public $config_php_settings;

    /**

     *
     * @var string converter_plugins_sortorder 
     */
    public $converter_plugins_sortorder;

    /**
Enables new PHP 5.2.0 feature - browsers are instructed to send cookie with real http requests only, cookies should not be accessible by scripting languages. This is not supported in all browsers and it may not be fully compatible with current code. It helps to prevent some types of XSS attacks.
     *
     * @var string cookiehttponly Only http cookies
     */
    public $cookiehttponly;

    /**
If server is accepting only https connections it is recommended to enable sending of secure cookies. If enabled please make sure that web server is not accepting http:// or set up permanent redirection to https:// address and ideally send HSTS headers. When <em>wwwroot</em> address does not start with https:// this setting is ignored.
     *
     * @var string cookiesecure Secure cookies only
     */
    public $cookiesecure;

    /**

     *
     * @var string core_competency_url_resolver 
     */
    public $core_competency_url_resolver;

    /**
     * Set to 'true' to enable FLV support
     *
     * @var string core_media_enable_flv Core media enable FLV
     */
    public $core_media_enable_flv;

    /**
     * Set to 'true' to enable MP3 support
     *
     * @var string core_media_enable_mp3 Core media enable MP3
     */
    public $core_media_enable_mp3;

    /**
     * Set to 'true' to enable youtube support
     *
     * @var string core_media_enable_youtube Core media enable youtube
     */
    public $core_media_enable_youtube;

    /**
If you set a country here, then this country will be selected by default on new user accounts.  To force users to choose a country, just leave this unset.
     *
     * @var string country Default country
     */
    public $country;

    /**
This setting allows you to control who appears on the course description. Users need to have at least one of these roles in a course to be shown on the course description for that course.
     *
     * @var string coursecontact Course contacts
     */
    public $coursecontact;

    /**
If enabled, course short names will be displayed in addition to full names in course lists. If required, extended course names may be customised by editing the \'courseextendednamedisplay\' language string using the language customisation feature.
     *
     * @var string courselistshortnames Display extended course names
     */
    public $courselistshortnames;

    /**
A comma-separated list of allowed course summary files extensions.
     *
     * @var string courseoverviewfilesext Course summary files extensions
     */
    public $courseoverviewfilesext;

    /**
The maximum number of files that can be attached to a course summary.
     *
     * @var string courseoverviewfileslimit Course summary files limit
     */
    public $courseoverviewfileslimit;

    /**
Type username of user to be notified when new course requested.
     *
     * @var string courserequestnotify Course request notification
     */
    public $courserequestnotify;

    /**
Enter the number of courses to be displayed per page in a course listing.
     *
     * @var string coursesperpage Courses per page
     */
    public $coursesperpage;

    /**
The maximum number of courses to display in a course listing including summaries before falling back to a simpler listing.
     *
     * @var string courseswithsummarieslimit Courses with summaries limit
     */
    public $courseswithsummarieslimit;

    /**
If the user does not already have the permission to manage the new course, the user is automatically enrolled using this role.
     *
     * @var string creatornewroleid Creators\' role in new courses
     */
    public $creatornewroleid;

    /**
Running the cron from a web browser can expose privileged information to anonymous users. Thus it is recommended to only run the cron from the command line or set a cron password for remote access.
     *
     * @var string cronclionly Cron execution via command line only
     */
    public $cronclionly;

    /**
This means that the cron.php script cannot be run from a web browser without supplying the password using the following form of URL:<pre>
    http://site.example.com/admin/cron.php?password=opensesame
</pre>If this is left empty, no password is required.
     *
     * @var string cronremotepassword Cron password for remote access
     */
    public $cronremotepassword;

    /**
Time-to-live for cURL cache, in seconds.
     *
     * @var string curlcache cURL cache TTL
     */
    public $curlcache;

    /**

     *
     * @var string curlsecurityallowedport cURL allowed ports list
     */
    public $curlsecurityallowedport;

    /**

     *
     * @var string curlsecurityblockedhosts cURL blocked hosts list
     */
    public $curlsecurityblockedhosts;

    /**
This setting is used to calculate an appropriate timeout during large cURL requests. As part of this calculation an HTTP HEAD request is made to determine the size of the content. Setting this to 0 disables this request from being made.
     *
     * @var string curltimeoutkbitrate Bitrate to use when calculating cURL timeouts (Kbps)
     */
    public $curltimeoutkbitrate;

    /**
     * 
     *
     * @var string custom_context_classes Custom context classes
     */
    public $custom_context_classes;

    /**
     *  Moodle 2.9 allows administrators to customise the list of supported file types.
     *  To add a new filetype or override the definition of an existing one, set the
     *  customfiletypes variable like this:
     * 
     *  $CFG->customfiletypes = array(
     *      (object)array(
     *          'extension' => 'frog',
     *          'icon' => 'archive',
     *          'type' => 'application/frog',
     *          'customdescription' => 'Amphibian-related file archive'
     *      )
     *  );
     *
     * @var string customfiletypes Custom file types
     */
    public $customfiletypes;

    /**
     * 
     *
     * @var string customfrontpageinclude Custom front page include
     */
    public $customfrontpageinclude;

    /**
You can configure a custom menu here to be shown by themes. Each line consists of some menu text, a link URL (optional), a tooltip title (optional) and a language code or comma-separated list of codes (optional, for displaying the line to users of the specified language only), separated by pipe characters. Lines starting with a hyphen will appear as menu items in the previous top level menu, and dividers can be used by adding a line of one or more # characters where desired. For example:
<pre>
Moodle community|https://moodle.org
-Moodle free support|https://moodle.org/support
-Moodle Docs|http://docs.moodle.org|Moodle Docs
-German Moodle Docs|http://docs.moodle.org/de|Documentation in German|de
-###
-Moodle development|https://moodle.org/development
Moodle.com|http://moodle.com/
</pre>
     *
     * @var string custommenuitems Custom menu items
     */
    public $custommenuitems;

    /**
     * Enabling this will allow custom scripts to replace existing moodle scripts.
     *  For example: if $CFG->customscripts/course/view.php exists then
     *  it will be used instead of $CFG->wwwroot/course/view.php
     *  At present this will only work for files that include config.php and are called
     *  as part of the url (index.php is implied).
     *  Some examples are:
     *       http://my.moodle.site/course/view.php
     *       http://my.moodle.site/index.php
     *       http://my.moodle.site/admin            (index.php implied)
     *  Custom scripts should not include config.php
     *  Warning: Replacing standard moodle scripts may pose security risks and/or may not
     *  be compatible with upgrades. Use this option only if you are aware of the risks
     *  involved.
     *  Specify the full directory path to the custom scripts
     *
     * @var string customscripts Custom scripts
     */
    public $customscripts;

    /**
You can configure the contents of the user menu (with the exception of the log out link, which is automatically added). Each line is separated by pipe characters and consists of 1) a string in "langstringname, componentname" form or as plain text, 2) a URL, and 3) an icon either as a pix icon (in the folder pix/t, or prefix the icon name with ../ if icon is in another pix folder) or as a URL. Dividers can be used by adding a line of one or more # characters where desired.
     *
     * @var string customusermenuitems User menu items
     */
    public $customusermenuitems;

    /**
If enabled, RSS feeds are generated by various features across the site, such as blogs, forums, database activities and glossaries. Note that RSS feeds also need to be enabled for the particular activity modules.
     *
     * @var string data_enablerssfeeds Enable RSS feeds
     */
    public $data_enablerssfeeds;

    /**

     *
     * @var string dataformat_plugins_sortorder 
     */
    public $dataformat_plugins_sortorder;

    /**

     *
     * @var string dataroot Data directory
     */
    public $dataroot;

    /**
     * 
     *
     * @var string dbfamily Database family
     */
    public $dbfamily;

    /**
Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.
     *
     * @var string dbhost Host server
     */
    public $dbhost;

    /**
     * "native" only at the moment.
     *
     * @var string dblibrary Library database
     */
    public $dblibrary;

    /**
Leave empty if using a DSN name in database host.
     *
     * @var string dbname Database name
     */
    public $dbname;

    /**
     *     'dbpersist' => false,        should persistent database connections be
     *                                   used? set to 'false' for the most stable
     *                                   setting, 'true' can improve performance
     *                                   sometimes
     *     'dbsocket'  => false,        should connection via UNIX socket be used?
     *                                   if you set it to 'true' or custom path
     *                                   here set dbhost to 'localhost',
     *                                   (please note mysql is always using socket
     *                                   if dbhost is 'localhost' - if you need
     *                                   local port connection use '127.0.0.1')
     *     'dbport'    => '',           the TCP port number to use when connecting
     *                                   to the server. keep empty string for the
     *                                   default port
     *
     * @var string dboptions Database options
     */
    public $dboptions;

    /**

     *
     * @var string dbpass Password
     */
    public $dbpass;

    /**
     * [[databasepersist]]
     *
     * @var string dbpersist Database presist
     */
    public $dbpersist;

    /**
If enabled, this setting will use the database to store information about current sessions. Note that changing this setting now will log out all current users (including you). If you are using MySQL please make sure that \'max_allowed_packet\' in my.cnf (or my.ini) is at least 4M. Other session drivers can be configured directly in config.php, see config-dist.php for more information. This option disappears if you specify session driver in config.php file.
     *
     * @var string dbsessions Use database for session information
     */
    public $dbsessions;

    /**
ADOdb database driver name, type of the external database engine.
     *
     * @var string dbtype Type
     */
    public $dbtype;

    /**

     *
     * @var string dbuser Database user
     */
    public $dbuser;

    /**
If you turn this on, then PHP\'s error_reporting will be increased so that more warnings are printed.  This is only useful for developers.
     *
     * @var string debug Debug messages
     */
    public $debug;

    /**

     *
     * @var string debugdeveloper DEVELOPER: extra Moodle debug messages for developers
     */
    public $debugdeveloper;

    /**
Set to on, the error reporting will go to the HTML page. This is practical, but breaks XHTML, JS, cookies and HTTP headers in general. Set to off, it will send the output to your server logs, allowing better debugging. The PHP setting error_log controls which log this goes to.
     *
     * @var string debugdisplay Display debug messages
     */
    public $debugdisplay;

    /**
     * Enable verbose debug information during fetching of email messages from IMAP server.
     *
     * @var string debugimap debugimap
     */
    public $debugimap;

    /**
Enable if you want page information printed in page footer.
     *
     * @var string debugpageinfo Show page information
     */
    public $debugpageinfo;

    /**
Enable verbose debug information during sending of email messages to SMTP server.
     *
     * @var string debugsmtp Debug email sending
     */
    public $debugsmtp;

    /**
This option is designed to help translators. When this option is enabled, if you add the parameter strings=1 to a request URL, it will show the language file and string id beside each string that is output.
     *
     * @var string debugstringids Show origin of languages strings
     */
    public $debugstringids;

    /**
     * You can specify a comma separated list of user ids that that always see
     * debug messages, this overrides the debug flag in $CFG->debug and $CFG->debugdisplay
     * for these users only.
     *
     * @var string debugusers Debug users
     */
    public $debugusers;

    /**
Enable if you want to have links to external validator servers in page footer. You may need to create new user with username <em>w3cvalidator</em>, and enable guest access. These changes may allow unauthorized access to server, do not enable on production sites!
     *
     * @var string debugvalidators Show validator links
     */
    public $debugvalidators;

    /**
     * These blocks are used when no other default setting is found.
     *             $CFG->defaultblocks = 'participants,activity_modules,search_forums,course_list:news_items,calendar_upcoming,recent_activity';
     * 
     *             
     *
     * @var string defaultblocks Default blocks
     */
    public $defaultblocks;

    /**
Defined in config.php
     *
     * @var string defaultblocks_override Override
     */
    public $defaultblocks_override;

    /**
     * This var define the specific settings for defined course formats and
     *             override any settings defined in the formats own config file.
     *              $CFG->defaultblocks_site = 'site_main_menu,course_list:course_summary,calendar_month';
     *
     * @var string defaultblocks_site Default blocks, site
     */
    public $defaultblocks_site;

    /**
A city entered here will be the default city when creating new user accounts.
     *
     * @var string defaultcity Default city
     */
    public $defaultcity;

    /**
     * Default frontpage role
     *
     * @var string defaultfrontpageroleid Default front page role ID
     */
    public $defaultfrontpageroleid;

    /**
This determines the home page for logged in users
     *
     * @var string defaulthomepage Default home page for users
     */
    public $defaulthomepage;

    /**
     * [[autosubscribe]]
     *
     * @var string defaultpreference_autosubscribe Default preference autosubscribe
     */
    public $defaultpreference_autosubscribe;

    /**
     * [[emaildigest]]
     *
     * @var string defaultpreference_maildigest Default preference mail digest
     */
    public $defaultpreference_maildigest;

    /**
     * [[emaildisplay]]
     *
     * @var string defaultpreference_maildisplay Default preference mail display
     */
    public $defaultpreference_maildisplay;

    /**
     * [[emailformat]]
     *
     * @var string defaultpreference_mailformat Default preference mail format
     */
    public $defaultpreference_mailformat;

    /**
     * [[trackforums]]
     *
     * @var string defaultpreference_trackforums Default preference track forums
     */
    public $defaultpreference_trackforums;

    /**
Courses requested by users will be automatically placed in this category.
     *
     * @var string defaultrequestcategory Default category for course requests
     */
    public $defaultrequestcategory;

    /**
All logged in users will be given the capabilities of the role you specify here, at the site level, in ADDITION to any other roles they may have been given.  The default is the Authenticated user role.  Note that this will not conflict with other roles they have unless you prohibit capabilities, it just ensures that all users have capabilities that are not assignable at the course level (eg post blog entries, manage own calendar, etc).
     *
     * @var string defaultuserroleid Default role for all users
     */
    public $defaultuserroleid;

    /**
After this period, old not fully setup accounts are deleted.
     *
     * @var string deleteincompleteusers Delete incomplete users after
     */
    public $deleteincompleteusers;

    /**
If you are using email authentication, this is the period within which a response will be accepted from users.  After this period, old unconfirmed accounts are deleted.
     *
     * @var string deleteunconfirmed Delete not fully setup users after
     */
    public $deleteunconfirmed;

    /**
To deny email addresses from particular domains list them here in the same way.  All other domains will be accepted. To deny subdomains add the domain with a preceding \'.\'. eg <strong>hotmail.com yahoo.co.uk .live.com</strong>
     *
     * @var string denyemailaddresses Denied email domains
     */
    public $denyemailaddresses;

    /**
<p>By default, Moodle can detect devices of the type default (desktop PCs, laptops, etc), mobile (phones and small hand held devices), tablet (iPads, Android tablets) and legacy (Internet Explorer 6 users).  The theme selector can be used to apply separate themes to all of these.  This setting allows regular expressions that allow the detection of extra device types (these take precedence over the default types).</p>
<p>For example, you could enter the regular expression \'/(MIDP-1.0|Maemo|Windows CE)/\' to detect some commonly used feature phones add the return value \'featurephone\'.  This adds \'featurephone\' on the theme selector that would allow you to add a theme that would be used on these devices.  Other phones would still use the theme selected for the mobile device type.</p>
     *
     * @var string devicedetectregex Device detection regular expressions
     */
    public $devicedetectregex;

    /**
People who choose to have emails sent to them in digest form will be emailed the digest daily. This setting controls which time of day the daily mail will be sent (the next cron that runs after this hour will send it).
     *
     * @var string digestmailtime Hour to send digest emails
     */
    public $digestmailtime;

    /**
     * 
     *
     * @var string digestmailtimelast Digest mail time last
     */
    public $digestmailtimelast;

    /**
     * The following parameter sets the permissions of new directories
     *  created by Moodle within the data directory.  The format is in
     *  octal format (as used by the Unix utility chmod, for example).
     *  The default is usually OK, but you may want to change it to 0750
     *  if you are concerned about world-access to the files (you will need
     *  to make sure the web server process (eg Apache) can access the files.
     *  NOTE: the prefixed 0 is important, and don't use quotes.
     *
     * @var string directorypermissions Data file permissions
     */
    public $directorypermissions;

    /**

     *
     * @var string dirroot Moodle directory
     */
    public $dirroot;

    /**
     * 
     *
     * @var string disablebyteserving Disable byte serving
     */
    public $disablebyteserving;

    /**
     * Use the following flag to disable the warning on the system notifications page
     * about present development libraries. This flag will not disable the warning within
     * the security overview report. Use this flag only if you really have prohibited web
     * access to the development libraries in your webserver configuration.
     *
     * @var string disabledevlibdirscheck disabledevlibdirscheck
     */
    public $disabledevlibdirscheck;

    /**
Disable history tracking of changes in grades related tables. This may speed up the server a little and conserve space in database.
     *
     * @var string disablegradehistory Disable grade history
     */
    public $disablegradehistory;

    /**
     * 
     *
     * @var string disablemycourses Disable my courses
     */
    public $disablemycourses;

    /**
     * Use the following flag to completely disable the On-click add-on installation
     *  feature and hide it from the server administration UI.
     *
     * @var string disableonclickaddoninstall Disable on click addon install
     */
    public $disableonclickaddoninstall;

    /**
     * Prevent stats processing and hide the GUI
     *
     * @var string disablestatsprocessing Disable stats processing
     */
    public $disablestatsprocessing;

    /**
     * Use the following flag to completely disable the Automatic updates deployment
     *  feature and hide it from the server administration UI.
     *
     * @var string disableupdateautodeploy Disable update auto deploy
     */
    public $disableupdateautodeploy;

    /**
     * Use the following flag to completely disable the Available update notifications
     *  feature and hide it from the server administration UI.
     *
     * @var string disableupdatenotifications Disable update notifications
     */
    public $disableupdatenotifications;

    /**
     * Completely disable user creation when restoring a course, bypassing any
     *  permissions granted via roles and capabilities. Enabling this setting
     *  results in the restore process stopping when a user attempts to restore a
     *  course requiring users to be created.
     *
     * @var string disableusercreationonrestore Disable user creation on restore
     */
    public $disableusercreationonrestore;

    /**
Disable the ability for users to change user profile images.
     *
     * @var string disableuserimages Disable user profile images
     */
    public $disableuserimages;

    /**
This will display information to users about previous failed logins.
     *
     * @var string displayloginfailures Display login failures
     */
    public $displayloginfailures;

    /**
     * Except for certain email addresses you want to let through for testing. Accepts a comma separated list of regexes.
     *
     * @var string divertallemailsexcept divertallemailsexcept
     */
    public $divertallemailsexcept;

    /**
     *  Divert all outgoing emails to this address to test and debug emailing features
     *  $CFG->divertallemailsto = 'root@localhost.local'; // NOT FOR PRODUCTION SERVERS!
     *
     * @var string divertallemailsto Divert all email
     */
    public $divertallemailsto;

    /**
Enable or disable the dragging and dropping of text and links onto a course page, alongside the dragging and dropping of files. Note that the dragging of text into Firefox or between different browsers is unreliable and may result in no data being uploaded, or corrupted text being uploaded.
     *
     * @var string dndallowtextandlinks Drag and drop upload of text/links
     */
    public $dndallowtextandlinks;

    /**
This language will be used in links for the documentation pages.
     *
     * @var string doclang Language for docs
     */
    public $doclang;

    /**
Defines the path to the Moodle Docs for providing context-specific documentation via \'Moodle Docs for this page\' links in the footer of each page. If the field is left blank, links will not be displayed.
     *
     * @var string docroot Moodle Docs document root
     */
    public $docroot;

    /**
If you enable this, then links to Moodle Docs will be shown in a new window.
     *
     * @var string doctonewwindow Open in new window
     */
    public $doctonewwindow;

    /**
     * 
     *
     * @var string early_install_lang Early install language
     */
    public $early_install_lang;

    /**
     * Enable earlier profiling that causes more code to be covered
     *    on every request (db connections, config load, other inits...).
     *    Requires extra configuration to be defined in config.php like:
     *    profilingincluded, profilingexcluded, profilingautofrec,
     *    profilingallowme, profilingallowall, profilinglifetime
     *
     * @var string earlyprofilingenabled Early profiling
     */
    public $earlyprofilingenabled;

    /**
Require an email confirmation step when users change their email address in their profile.
     *
     * @var string emailchangeconfirmation Email change confirmation
     */
    public $emailchangeconfirmation;

    /**
     * Email database connection errors to someone.  If Moodle cannot connect to the
     *  database, then email this address with a notice.
     *
     * @var string emailconnectionerrorsto Email connection errors
     */
    public $emailconnectionerrorsto;

    /**
Add via information in the "From" section of outgoing email. This informs the recipient from where this email came from and also helps combat recipients accidentally replying to no-reply email addresses.
     *
     * @var string emailfromvia Email via information
     */
    public $emailfromvia;

    /**
     * 
     *
     * @var string embeddedsoforcelinktarget Embedded so force link target
     */
    public $embeddedsoforcelinktarget;

    /**
This form defines the emoticons (or smileys) used at your site. To remove a row from the table, save the form with an empty value in any of the required fields. To register a new emoticon, fill the fields in the last blank row. To reset all the fields into default values, follow the link above.

* Text (required) - This text will be replaced with the emoticon image. It must be at least two characters long.
* Image name (required) - The emoticon image file name without the extension, relative to the component pix folder.
* Image component (required) - The component providing the icon.
* Alternative text (optional) - String identifier and component of the alternative text of the emoticon.
     *
     * @var string emoticons Emoticons
     */
    public $emoticons;

    /**
When enabled, this lets you set conditions (based on date, grade, or completion) that control whether an activity or resource can be accessed.
     *
     * @var string enableavailability Enable restricted access
     */
    public $enableavailability;

    /**
When enabled, this feature lets you create badges and award them to site users.
     *
     * @var string enablebadges Enable badges
     */
    public $enablebadges;

    /**
This switch provides all site users with their own blog.
     *
     * @var string enableblogs Enable blogs
     */
    public $enableblogs;

    /**
Enable exporting or subscribing to calendars.
     *
     * @var string enablecalendarexport Enable calendar export
     */
    public $enablecalendarexport;

    /**
If enabled, activity completion conditions may be set in the activity settings and/or course completion conditions may be set. It is recommended to have this enabled so that meaningful data is displayed in the course overview on the Dashboard.
     *
     * @var string enablecompletion Enable completion tracking
     */
    public $enablecompletion;

    /**
This will allow any user to request a course be created.
     *
     * @var string enablecourserequests Enable course requests
     */
    public $enablecourserequests;

    /**
Enables detection of mobiles, smartphones, tablets or default devices (desktop PCs, laptops, etc) for the application of themes and other features.
     *
     * @var string enabledevicedetection Enable device detection
     */
    public $enabledevicedetection;

    /**
If enabled, data will be indexed and syncronised by a scheduled task.
     *
     * @var string enableglobalsearch Enable global search
     */
    public $enableglobalsearch;

    /**
When enabled Moodle will attempt to fetch a user profile picture from Gravatar if the user has not uploaded an image.
     *
     * @var string enablegravatar Enable Gravatar
     */
    public $enablegravatar;

    /**
Enable mobile service for the official Moodle app or other app requesting it. For more information, read the {$a}
     *
     * @var string enablemobilewebservice Enable web services for mobile devices
     */
    public $enablemobilewebservice;

    /**
Enable storing of notes about individual users.
     *
     * @var string enablenotes Enable notes
     */
    public $enablenotes;

    /**
If enabled, grade items may be graded using one or more scales tied to outcome statements.
     *
     * @var string enableoutcomes Enable outcomes
     */
    public $enableoutcomes;

    /**
This will allow administrators to configure plagiarism plugins (if installed)
     *
     * @var string enableplagiarism Enable plagiarism plugins
     */
    public $enableplagiarism;

    /**
     * If enabled, users can export content, such as forum posts and assignment submissions, to external portfolios or HTML pages.
     *
     * @var string enableportfolios Enable portfolios
     */
    public $enableportfolios;

    /**
If enabled, RSS feeds are generated by various features across the site, such as blogs, forums, database activities and glossaries. Note that RSS feeds also need to be enabled for the particular activity modules.
     *
     * @var string enablerssfeeds Enable RSS feeds
     */
    public $enablerssfeeds;

    /**
This adds the choice \'Require Safe Exam Browser\' to the \'Browser security\' field on the quiz settings form. See http://www.safeexambrowser.org/ for more information.
     *
     * @var string enablesafebrowserintegration Enable Safe Exam Browser integration
     */
    public $enablesafebrowserintegration;

    /**
If you choose \'yes\' here, Moodle\'s cronjob will process the logs and gather some statistics.  Depending on the amount of traffic on your site, this can take awhile. If you enable this, you will be able to see some interesting graphs and statistics about each of your courses, or on a sitewide basis.
     *
     * @var string enablestats Enable statistics
     */
    public $enablestats;

    /**
By default Moodle will always thoroughly clean text that comes from users to remove any possible bad scripts, media etc that could be a security risk.  The Trusted Content system is a way of giving particular users that you trust the ability to include these advanced features in their content without interference.  To enable this system, you need to first enable this setting, and then grant the Trusted Content permission to a specific Moodle role.  Texts created or uploaded by such users will be marked as trusted and will not be cleaned before display.
     *
     * @var string enabletrusttext Enable trusted content
     */
    public $enabletrusttext;

    /**
Web services enable other systems to log in to this Moodle and perform operations.  For extra security this feature should be disabled unless you are really using it.
     *
     * @var string enablewebservices Enable web services
     */
    public $enablewebservices;

    /**
Enable auto-generation of web services documentation. A user can access to his own documentation on his security keys page {$a}. It displays the documentation for the enabled protocols only.
     *
     * @var string enablewsdocumentation Web services documentation
     */
    public $enablewsdocumentation;

    /**
     * 
     *
     * @var string enrol_plugins_enabled Enrol plugins enabled
     */
    public $enrol_plugins_enabled;

    /**
     * 
     *
     * @var string errordocroot Error doc root
     */
    public $errordocroot;

    /**
Enable this setting to allow students to use any characters in their usernames (note this does not affect their actual names).  The default is "false" which restricts usernames to be alphanumeric lowercase characters, underscore (_), hyphen (-), period (.) or at symbol (@).
     *
     * @var string extendedusernamechars Allow extended characters in usernames
     */
    public $extendedusernamechars;

    /**
How often Moodle checks the external blogs for new entries.
     *
     * @var string externalblogcrontime External blog cron schedule
     */
    public $externalblogcrontime;

    /**
Some scripts like search, backup/restore or cron require more memory. Set higher values for large sites.
     *
     * @var string extramemorylimit Extra PHP memory limit
     */
    public $extramemorylimit;

    /**
If set to \'yes\', users can complete a feedback activity on the front page without being required to log in.
     *
     * @var string feedback_allowfullanonymous Allow full anonymous
     */
    public $feedback_allowfullanonymous;

    /**
     * 
     *
     * @var string file_lock_root File lock root 
     */
    public $file_lock_root;

    /**
     * for custom $CFG->filedir locations
     *
     * @var string filedir File dir
     */
    public $filedir;

    /**
     * Seconds for files to remain in caches. Decrease this if you are worried
     *  about students being served outdated versions of uploaded files.
     *
     * @var string filelifetime File lifetime
     */
    public $filelifetime;

    /**
     * 
     *
     * @var string filepermissions File permissions
     */
    public $filepermissions;

    /**
     * if you want to disable purging of trash put $CFG->fileslastcleanup=time(); into config.php
     * 
     *
     * @var string fileslastcleanup files last cleanup
     */
    public $fileslastcleanup;

    /**
     * Custom bad words list
     *
     * @var string filter_censor_badwords Filter censor bad words
     */
    public $filter_censor_badwords;

    /**
     * Multilang upgrade
     *
     * @var string filter_multilang_converted Filter multilang converted
     */
    public $filter_multilang_converted;

    /**
     * 
     *
     * @var string filter_multilang_force_old Filter multilang force old
     */
    public $filter_multilang_force_old;

    /**
Filter all strings, including headings, titles, navigation bar and so on.  This is mostly useful when using the multilang filter, otherwise it will just create extra load on your site for little gain.
     *
     * @var string filterall Show all
     */
    public $filterall;

    /**
Automatic linking filters will only generate a single link for the first matching text instance found on the complete page. All others are ignored.
     *
     * @var string filtermatchoneperpage Filter match once per page
     */
    public $filtermatchoneperpage;

    /**
Automatic linking filters will only generate a single link for the first matching text instance found in each item of text (e.g., resource, block) on the page. All others are ignored. This setting is ignored if the one per page setting is <i>yes</i>.
     *
     * @var string filtermatchonepertext Filter match once per text
     */
    public $filtermatchonepertext;

    /**
Process all uploaded HTML and text files with the filters before displaying them, only uploaded HTML files or none at all.
     *
     * @var string filteruploadedfiles Filter uploaded files
     */
    public $filteruploadedfiles;

    /**
     *  Plugin settings have to be put into a special array.
     *  Example:
     *    $CFG->forced_plugin_settings = array('pluginname'  => array('settingname' => 'value', 'secondsetting' => 'othervalue'),
     *                                         'otherplugin' => array('mysetting' => 'myvalue', 'thesetting' => 'thevalue'));
     *  Module default settings with advanced/locked checkboxes can be set too. To do this, add
     *  an extra config with '_adv' or '_locked' as a suffix and set the value to true or false.
     *  Example:
     *    $CFG->forced_plugin_settings = array('pluginname'  => array('settingname' => 'value', 'settingname_locked' => true, 'settingname_adv' => true));
     * 
     *
     * @var string forced_plugin_settings Forced plugin settings
     */
    public $forced_plugin_settings;

    /**
     * 
     *
     * @var string forcedefaultmymoodle Force default my moodle
     */
    public $forcedefaultmymoodle;

    /**
     * Modify the restore process in order to force the "user checks" to assume
     *  that the backup originated from a different site, so detection of matching
     *  users is performed with different (more "relaxed") rules. Note that this is
     *  only useful if the backup file has been created using Moodle < 1.9.4 and the
     *  site has been rebuilt from scratch using backup files (not the best way btw).
     *  If you obtain user conflicts on restore, rather than enabling this setting
     *  permanently, try restoring the backup on a different site, back it up again
     *  and then restore on the target server.
     *
     * @var string forcedifferentsitecheckingusersonrestore Force different site checking users on restore
     */
    public $forcedifferentsitecheckingusersonrestore;

    /**
     * A little hack to anonymise user names for all students.  If you set these
     *    then all non-teachers will always see these for every person.
     *
     * @var string forcefirstname Force displayed firstnames
     */
    public $forcefirstname;

    /**
     * A little hack to anonymise user names for all students.  If you set these
     *    then all non-teachers will always see these for every person.
     *
     * @var string forcelastname Force displayed lastnames
     */
    public $forcelastname;

    /**
Normally, the front page of the site and the course listings (but not courses) can be read by people without logging in to the site.  If you want to force people to log in before they do ANYTHING on the site, then you should enable this setting.
     *
     * @var string forcelogin Force users to log in
     */
    public $forcelogin;

    /**
If enabled, users must log in in order to view user profile pictures and the default user picture will be used in all notification emails.
     *
     * @var string forceloginforprofileimage Force users to log in to view user pictures
     */
    public $forceloginforprofileimage;

    /**
This setting forces people to log in as a real (non-guest) account before viewing any user\'s profile. If you disabled this setting, you may find that some users post advertising (spam) or other inappropriate content in their profiles, which is then visible to the whole world.
     *
     * @var string forceloginforprofiles Force users to log in for profiles
     */
    public $forceloginforprofiles;

    /**

     *
     * @var string forcetimezone Force timezone
     */
    public $forcetimezone;

    /**

     *
     * @var string forgottenpasswordurl Forgotten password URL
     */
    public $forgottenpasswordurl;

    /**
     * 
     *
     * @var string format_plugins_sortorder Format plugins sort order
     */
    public $format_plugins_sortorder;

    /**
     * Uncheck this setting to allow HTML tags in activity and resource names.
     *
     * @var string formatstringstriptags Remove HTML tags from all activity names
     */
    public $formatstringstriptags;

    /**
     * Allow forced read tracking
     *
     * @var string forum_allowforcedreadtracking Forum allow forced read tracking
     */
    public $forum_allowforcedreadtracking;

    /**
The default display mode for discussions if one isn\'t set.
     *
     * @var string forum_displaymode Display mode for the options
     */
    public $forum_displaymode;

    /**
If enabled, RSS feeds are generated by various features across the site, such as blogs, forums, database activities and glossaries. Note that RSS feeds also need to be enabled for the particular activity modules.
     *
     * @var string forum_enablerssfeeds Enable RSS feeds
     */
    public $forum_enablerssfeeds;

    /**
     * Timed posts
     *
     * @var string forum_enabletimedposts Enable timed posts on forum
     */
    public $forum_enabletimedposts;

    /**
     * 
     *
     * @var string forum_lastreadclean Forum last read clean
     */
    public $forum_lastreadclean;

    /**
Any post over this length (in characters not including HTML) is considered long. Posts displayed on the site front page, social format course pages, or user profiles are shortened to a natural break somewhere between the forum_shortpost and forum_longpost values.
     *
     * @var string forum_longpost Long post
     */
    public $forum_longpost;

    /**
Maximum number of discussions shown in a forum per page
     *
     * @var string forum_manydiscussions Discussions per page
     */
    public $forum_manydiscussions;

    /**
Default maximum number of attachments allowed per post.
     *
     * @var string forum_maxattachments Maximum number of attachments
     */
    public $forum_maxattachments;

    /**
This specifies a maximum size that uploaded files can be throughout the whole site. This setting is limited by the PHP settings post_max_size and upload_max_filesize, as well as the Apache setting LimitRequestBody. In turn, maxbytes limits the range of sizes that can be chosen at course level or module level. If \'Server Limit\' is chosen, the server maximum allowed by the server will be used.
     *
     * @var string forum_maxbytes Maximum uploaded file size
     */
    public $forum_maxbytes;

    /**
Number of days old any post is considered read.
     *
     * @var string forum_oldpostdays Read after days
     */
    public $forum_oldpostdays;

    /**
This setting specifies the number of glossary entry concepts to include in the RSS feed. Between 5 and 20 generally acceptable.
     *
     * @var string forum_rssarticles Number of RSS recent articles
     */
    public $forum_rssarticles;

    /**
To enable the RSS feed for this activity, select either concepts with author or concepts without author to be included in the feed.
     *
     * @var string forum_rsstype RSS feed for this activity
     */
    public $forum_rsstype;

    /**
Any post under this length (in characters not including HTML) is considered short (see below).
     *
     * @var string forum_shortpost Short post
     */
    public $forum_shortpost;

    /**
If you are subscribed to a forum it means you will receive notification of new forum posts. Usually you can choose whether you wish to be subscribed, though sometimes subscription is forced so that everyone receives notifications.
     *
     * @var string forum_subscription Subscription
     */
    public $forum_subscription;

    /**
Default setting for read tracking.
     *
     * @var string forum_trackingtype Read tracking
     */
    public $forum_trackingtype;

    /**
     * Track unread posts
     *
     * @var string forum_trackreadposts Track read posts on forum
     */
    public $forum_trackreadposts;

    /**
If \'yes\', the user must manually mark a post as read. If \'no\', when the post is viewed it is marked as read.
     *
     * @var string forum_usermarksread Manual message read marking
     */
    public $forum_usermarksread;

    /**
The items selected above will be displayed on the site\'s front page.
     *
     * @var string frontpage Front page
     */
    public $frontpage;

    /**
     * Maximum number of courses to be displayed on the site's front page in course listings.
     *
     * @var string frontpagecourselimit Maximum number of courses
     */
    public $frontpagecourselimit;

    /**
The items selected above will be displayed on the site\'s front page when a user is logged in.
     *
     * @var string frontpageloggedin Front page items when logged in
     */
    public $frontpageloggedin;

    /**
This defines how names are shown when they are displayed in full. The default value, "language", leaves it to the string "fullnamedisplay" in the current language pack to decide. Some languages have different name display conventions.

For most mono-lingual sites the most efficient setting is "firstname lastname", but you may choose to hide surnames altogether. Placeholders that can be used are: firstname, lastname, firstnamephonetic, lastnamephonetic, middlename, and alternatename.
     *
     * @var string fullnamedisplay Full name format
     */
    public $fullnamedisplay;

    /**
     * 
     *
     * @var string gdversion GD version
     */
    public $gdversion;

    /**

     *
     * @var string geoip2file 
     */
    public $geoip2file;

    /**
Location of GeoLite2 City binary data file. This file is not part of Moodle distribution and must be obtained separately from <a href="http://www.maxmind.com/">MaxMind</a>. You can either buy a commercial version or use the free version. Simply download <a href="http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz" >http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz</a> and extract it into "{$a}" directory on your server.
     *
     * @var string geoipfile GeoLite2 City MaxMind DB
     */
    public $geoipfile;

    /**
If your server is behind a reverse proxy, you can use this setting to specify which HTTP headers can be trusted to contain the remote IP address. The headers are read in order, using the first one that is available.
     *
     * @var string getremoteaddrconf Logged IP address source
     */
    public $getremoteaddrconf;

    /**
If enabled, all participants with permission to create comments will be able to add comments to glossary entries.
     *
     * @var string glossary_allowcomments Allow comments on entries
     */
    public $glossary_allowcomments;

    /**
This setting specifies whether matching exact upper and lower case is necessary when auto-linking to an entry.
     *
     * @var string glossary_casesensitive Case sensitivity
     */
    public $glossary_casesensitive;

    /**
If set to no, entries require approving by a teacher before they are viewable by everyone.
     *
     * @var string glossary_defaultapproval Approved by default
     */
    public $glossary_defaultapproval;

    /**
     * Duplicate entries allowed
     *
     * @var string glossary_dupentries Glossary duplicate entries
     */
    public $glossary_dupentries;

    /**
If enabled, RSS feeds are generated by various features across the site, such as blogs, forums, database activities and glossaries. Note that RSS feeds also need to be enabled for the particular activity modules.
     *
     * @var string glossary_enablerssfeeds Enable RSS feeds
     */
    public $glossary_enablerssfeeds;

    /**
     * Entries shown per page
     *
     * @var string glossary_entbypage Glossary enteries by page
     */
    public $glossary_entbypage;

    /**
This setting specifies whether only whole words will be linked, for example, a glossary entry named "construct" will not create a link inside the word "constructivism".
     *
     * @var string glossary_fullmatch Match whole words only
     */
    public $glossary_fullmatch;

    /**
     * Automatically link glossary entries
     *
     * @var string glossary_linkbydefault Glossary link by default
     */
    public $glossary_linkbydefault;

    /**
     * Automatically link glossary entries
     *
     * @var string glossary_linkentries Glossary link entries
     */
    public $glossary_linkentries;

    /**
You need to enter a special key to use Google Maps for IP address lookup visualization. You can obtain the key free of charge at <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank">https://developers.google.com/maps/documentation/javascript/tutorial#api_key</a>
     *
     * @var string googlemapkey3 Google Maps API V3 key
     */
    public $googlemapkey3;

    /**
An empty grade is a grade which is missing from the gradebook. It may be from an assignment submission which has not yet been graded or from a quiz which has not yet been attempted etc.

This setting determines whether empty grades are not included in the aggregation or are counted as minimal grades, for example 0 for an assignment graded between 0 and 100.
     *
     * @var string grade_aggregateonlygraded Exclude empty grades
     */
    public $grade_aggregateonlygraded;

    /**
     * 
     *
     * @var string grade_aggregateonlygraded_flag Grade aggregate only graded flag
     */
    public $grade_aggregateonlygraded_flag;

    /**
If enabled, outcomes are included in the aggregation. This may result in an unexpected category total.
     *
     * @var string grade_aggregateoutcomes Include outcomes in aggregation
     */
    public $grade_aggregateoutcomes;

    /**
     * 
     *
     * @var string grade_aggregateoutcomes_flag Grade aggregate out comes flag
     */
    public $grade_aggregateoutcomes_flag;

    /**
The aggregation determines how grades in a category are combined, such as

* Mean of grades - The sum of all grades divided by the total number of grades
* Median of grades - The middle grade when grades are arranged in order of size
* Lowest grade
* Highest grade
* Mode of grades - The grade that occurs the most frequently
* Natural - The sum of all grade values scaled by weight
     *
     * @var string grade_aggregation Aggregation
     */
    public $grade_aggregation;

    /**
     * 
     *
     * @var string grade_aggregation_flag Grade aggregation flag
     */
    public $grade_aggregation_flag;

    /**
This setting determines whether the category and course total columns are displayed first or last in the gradebook reports.
     *
     * @var string grade_aggregationposition Aggregation position
     */
    public $grade_aggregationposition;

    /**
     * Available aggregation types
     *
     * @var string grade_aggregations_visible Grade aggregations visible
     */
    public $grade_aggregations_visible;

    /**
This setting determines the number of decimal points to display for each grade. It has no effect on grade calculations, which are made with an accuracy of 5 decimal places.
     *
     * @var string grade_decimalpoints Overall decimal points
     */
    public $grade_decimalpoints;

    /**
     * Grade display type
     *
     * @var string grade_displaytype Grade display type
     */
    public $grade_displaytype;

    /**
This setting enables a specified number of the lowest grades to be excluded from the aggregation.
     *
     * @var string grade_droplow Drop the lowest
     */
    public $grade_droplow;

    /**
     * 
     *
     * @var string grade_droplow_flag Grade drop low flag
     */
    public $grade_droplow_flag;

    /**
     * Include these custom profile fields in the grade export, separated by commas.
     *
     * @var string grade_export_customprofilefields Grade export custom profile fields
     */
    public $grade_export_customprofilefields;

    /**
     * Grade export decimal points
     *
     * @var string grade_export_decimalpoints Grade export decimal points
     */
    public $grade_export_decimalpoints;

    /**
     * Grade export display type
     *
     * @var string grade_export_displaytype Grade export display type
     */
    public $grade_export_displaytype;

    /**
     * Include these user profile fields in the grade export, separated by commas.
     *
     * @var string grade_export_userprofilefields Grade export user profile fields
     */
    public $grade_export_userprofilefields;

    /**
If user can not see hidden grades show date of submission instead of \'-\'.
     *
     * @var string grade_hiddenasdate Show submitted date for hidden grades
     */
    public $grade_hiddenasdate;

    /**
Do not show forced settings in grading UI.
     *
     * @var string grade_hideforcedsettings Hide forced settings
     */
    public $grade_hideforcedsettings;

    /**
You can change whether scales are to be included as numbers in all aggregated grades across all gradebooks in all courses. CAUTION: changing this setting will force all aggregated grades to be recalculated.
     *
     * @var string grade_includescalesinaggregation Include scales in aggregation
     */
    public $grade_includescalesinaggregation;

    /**
     * Select all elements that should be displayed as advanced when editing grade items.
     *
     * @var string grade_item_advanced Advanced grade item options
     */
    public $grade_item_advanced;

    /**
If set, this option will only keep the X highest grades, X being the selected value for this option.
     *
     * @var string grade_keephigh Keep the highest
     */
    public $grade_keephigh;

    /**
     * 
     *
     * @var string grade_keephigh_flag Grade keep high flag
     */
    public $grade_keephigh_flag;

    /**
This setting determines whether to use the initial minimum and maximum grades from when the grade was given, or the minimum and maximum grades as specified in the settings for the grade item, when calculating the grade displayed in the gradebook.
     *
     * @var string grade_minmaxtouse Min and max grades used in calculation
     */
    public $grade_minmaxtouse;

    /**
     * [[mygrades]]
     *
     * @var string grade_mygrades_report Grade my grades report
     */
    public $grade_mygrades_report;

    /**
In Free navigation, questions may be answered in any order using navigation. In Sequential, questions must be answered in strict sequence.
     *
     * @var string grade_navmethod Navigation method
     */
    public $grade_navmethod;

    /**
Grade report used on user profile page.
     *
     * @var string grade_profilereport User profile report
     */
    public $grade_profilereport;

    /**
     * Show rank
     *
     * @var string grade_report_overview_showrank Grade report overview show rank
     */
    public $grade_report_overview_showrank;

    /**
     * 
     *
     * @var string grade_report_overview_showtotalsifcontainhidden Grade report overview show totals if contain hidden
     */
    public $grade_report_overview_showtotalsifcontainhidden;

    /**
     * Shows only active enrols on grade report
     *
     * @var string grade_report_showonlyactiveenrol Show only active enrol
     */
    public $grade_report_showonlyactiveenrol;

    /**
     * Shows user image on grade report
     *
     * @var string grade_report_showuserimage Show user image
     */
    public $grade_report_showuserimage;

    /**
     * Range decimal points
     *
     * @var string grade_report_user_rangedecimals Grade report user range decimals
     */
    public $grade_report_user_rangedecimals;

    /**
     * Show the average column? Students may be able to estimate other student's grades if the average is calculated from a small number of grades. For performance reasons the average is approximate if it is dependent on any hidden items.
     *
     * @var string grade_report_user_showaverage Show average
     */
    public $grade_report_user_showaverage;

    /**
     * Default ({$a})
     *
     * @var string grade_report_user_showcontributiontocoursetotal Grade report user show contribution to course total
     */
    public $grade_report_user_showcontributiontocoursetotal;

    /**
     * Show the feedback column?
     *
     * @var string grade_report_user_showfeedback Show feedback
     */
    public $grade_report_user_showfeedback;

    /**
     * Show the grade column?
     *
     * @var string grade_report_user_showgrade Show grades
     */
    public $grade_report_user_showgrade;

    /**
     * Whether hidden grade items are hidden entirely or if the names of hidden grade items are visible to students.
     * 
     * * Show hidden - Hidden grade item names are shown but student grades are hidden
     * * Only hidden until - Grade items with a "hide until" date set are hidden completely until the set date, after which the whole item is shown
     * * Do not show - Hidden grade items are completely hidden
     *
     * @var string grade_report_user_showhiddenitems Show hidden items
     */
    public $grade_report_user_showhiddenitems;

    /**
     * Show the letter grade column?
     *
     * @var string grade_report_user_showlettergrade Show letter grades
     */
    public $grade_report_user_showlettergrade;

    /**
     * Show the percentage value of each grade item?
     *
     * @var string grade_report_user_showpercentage Show percentage
     */
    public $grade_report_user_showpercentage;

    /**
     * Show the range column?
     *
     * @var string grade_report_user_showrange Show ranges
     */
    public $grade_report_user_showrange;

    /**
     * Show the position of the student in relation to the rest of the class for each grade item?
     *
     * @var string grade_report_user_showrank Show rank
     */
    public $grade_report_user_showrank;

    /**
     * Hide totals if they contain hidden items
     *
     * @var string grade_report_user_showtotalsifcontainhidden Grade report user show totals if contain hidden
     */
    public $grade_report_user_showtotalsifcontainhidden;

    /**
     * 
     *
     * @var string grade_report_user_showweight Grade report user show weight
     */
    public $grade_report_user_showweight;

    /**
This setting allows you to control who appears on the gradebook.  Users need to have at least one of these roles in a course to be shown in the gradebook for that course.
     *
     * @var string gradebookroles Graded roles
     */
    public $gradebookroles;

    /**
Choose which gradebook export formats are your primary methods for exporting grades.  Chosen plugins will then set and use a "last exported" field for every grade.  For example, this might result in exported records being identified as being "new" or "updated".  If you are not sure about this then leave everything unchecked.
     *
     * @var string gradeexport Primary grade export methods
     */
    public $gradeexport;

    /**
This specifies the length of time you want to keep history of changes in grade related tables. It is recommended to keep it as long as possible. If you experience performance problems or have limited database space, try to set lower value.
     *
     * @var string gradehistorylifetime Grade history lifetime
     */
    public $gradehistorylifetime;

    /**
     * 
     *
     * @var string gradeoverhundredprocentmax Grade over hundred procent max
     */
    public $gradeoverhundredprocentmax;

    /**
This setting determines the default value for the grade point value available in a grade item.
     *
     * @var string gradepointdefault Grade point default
     */
    public $gradepointdefault;

    /**
This setting determines the maximum grade point value available in an activity.
     *
     * @var string gradepointmax Grade point maximum
     */
    public $gradepointmax;

    /**
Enable publishing in exports and imports: Exported grades can be accessed by accessing a URL, without having to log on to a Moodle site. Grades can be imported by accessing such a URL (which means that a Moodle site can import grades published by another site). By default only administrators may use this feature, please educate users before adding required capabilities to other roles (dangers of bookmark sharing and download accelerators, IP restrictions, etc.).
     *
     * @var string gradepublishing Enable publishing
     */
    public $gradepublishing;

    /**
     * [[externalurl]]
     *
     * @var string gradereport_mygradeurl Grade my grade URL
     */
    public $gradereport_mygradeurl;

    /**
Gravatar needs a default image to display if it is unable to find a picture for a given user. Provide a full URL for an image. If you leave this setting empty, Moodle will attempt to use the most appropriate default image for the page you are viewing. Note also that Gravatar has a number of codes which can be used to <a href="https://en.gravatar.com/site/implement/images/#default-image">generate default images</a>.
     *
     * @var string gravatardefaulturl Gravatar default image URL
     */
    public $gravatardefaulturl;

    /**
Turning this on will make Moodle check group enrolment keys against a valid password policy.
     *
     * @var string groupenrolmentkeypolicy Group enrolment key policy
     */
    public $groupenrolmentkeypolicy;

    /**

     *
     * @var string guestloginbutton Guest login button
     */
    public $guestloginbutton;

    /**
This role is automatically assigned to the guest user. It is also temporarily assigned to not enrolled users that enter the course via guest enrolment plugin.
     *
     * @var string guestroleid Role for guest
     */
    public $guestroleid;

    /**
     * The following line is for handling email bounces
     *
     * @var string handlebounces Handle bounces
     */
    public $handlebounces;

    /**

     *
     * @var string headerloguser 
     */
    public $headerloguser;

    /**
Select which user information fields you wish to hide from other users other than course teachers/admins. This will increase student privacy. Hold CTRL key to select multiple fields.
     *
     * @var string hiddenuserfields Hide user fields
     */
    public $hiddenuserfields;

    /**
     * Set httpswwwroot default value (this variable will replace $CFG->wwwroot inside some URLs used in HTTPSPAGEREQUIRED pages.)
     *
     * @var string httpswwwroot HTPPS www root
     */
    public $httpswwwroot;

    /**
     * Allow user passwords to be included in backup files. Very dangerous
     *  setting as far as it publishes password hashes that can be unencrypted
     *  if the backup file is publicy available. Use it only if you can guarantee
     *  that all your backup files remain only privacy available and are never
     *  shared out from your site/institution!
     *
     * @var string includeuserpasswordsinbackup Include user passwords in backup
     */
    public $includeuserpasswordsinbackup;

    /**
When you click on an IP address (such as 34.12.222.93), such as in the logs, you are shown a map with a best guess of where that IP is located.  There are different plugins for this that you can choose from, each has benefits and disadvantages.
     *
     * @var string iplookup IP address lookup
     */
    public $iplookup;

    /**
The server to connect to to send jabber message notifications
     *
     * @var string jabberhost Jabber host
     */
    public $jabberhost;

    /**
The password to use when connecting to the Jabber server
     *
     * @var string jabberpassword Jabber password
     */
    public $jabberpassword;

    /**
The port to use when connecting to the Jabber server
     *
     * @var string jabberport Jabber port
     */
    public $jabberport;

    /**
XMPP host ID (can be left empty if the same as Jabber host)
     *
     * @var string jabberserver Jabber server
     */
    public $jabberserver;

    /**
The user name to use when connecting to the Jabber server
     *
     * @var string jabberusername Jabber user name
     */
    public $jabberusername;

    /**
     * When jsrev is positive, the function is minified and stored in a MUC cache for subsequent uses
     *
     * @var string jsrev JS rev
     */
    public $jsrev;

    /**
Check this if you want tag names to keep the original casing as entered by users who created them
     *
     * @var string keeptagnamecase Keep tag name casing
     */
    public $keeptagnamecase;

    /**
     *  Keep the temporary directories used by backup and restore without being
     *  deleted at the end of the process. Use it if you want to debug / view
     *  all the information stored there after the process has ended. Note that
     *  those directories may be deleted (after some ttl) both by cron and / or
     *  by new backup / restore invocations.
     *
     * @var string keeptempdirectoriesonbackup Keep temp directories on backup
     */
    public $keeptempdirectoriesonbackup;

    /**
Choose a default language for the whole site. Users can override this setting using the language menu or the setting in their personal profile.
     *
     * @var string lang Default language
     */
    public $lang;

    /**
Leave this blank to allow users to choose from any language you have in this installation of Moodle.  However, you can shorten the language menu by entering a comma-separated list of language codes that you want.  For example:  en,es_es,fr,it
     *
     * @var string langlist Languages on language menu
     */
    public $langlist;

    /**
     * 
     *
     * @var string langlocalroot Language local root
     */
    public $langlocalroot;

    /**
Choose whether or not you want to display the general-purpose language menu on the home page, login page etc.  This does not affect the user\'s ability to set the preferred language in their own profile.
     *
     * @var string langmenu Display language menu
     */
    public $langmenu;

    /**
     * 
     *
     * @var string langotherroot Language other root
     */
    public $langotherroot;

    /**
     * 
     *
     * @var string langrev Language rev
     */
    public $langrev;

    /**
Caches all the language strings into compiled files in the data directory.  If you are translating Moodle or changing strings in the Moodle source code then you may want to switch this off.  Otherwise leave it on to see performance benefits.
     *
     * @var string langstringcache Cache all language strings
     */
    public $langstringcache;

    /**
     * 
     *
     * @var string lastnotifyfailure Last notify failure
     */
    public $lastnotifyfailure;

    /**
If a course has legacy course files, allow new files and folders to be added to it.
     *
     * @var string legacyfilesaddallowed Allow adding to legacy course files
     */
    public $legacyfilesaddallowed;

    /**
By default, legacy course files areas are available in upgraded courses only. Please note that some features such as activity backup and restore are not compatible with this setting.
     *
     * @var string legacyfilesinnewcourses Legacy course files in new courses
     */
    public $legacyfilesinnewcourses;

    /**
     * Path to moodles library folder on servers filesystem.
     *
     * @var string libdir Lib directory
     */
    public $libdir;

    /**

     *
     * @var string licenses Licences
     */
    public $licenses;

    /**
If enabled the number of concurrent browser logins for each user is restricted. The oldest session is terminated after reaching the limit, please note that users may lose all unsaved work. This setting is not compatible with single sign-on (SSO) authentication plugins.
     *
     * @var string limitconcurrentlogins Limit concurrent logins
     */
    public $limitconcurrentlogins;

    /**
If enabled admin setting categories will be displayed as links in the navigation and will lead to the admin category pages.
     *
     * @var string linkadmincategories Link admin categories
     */
    public $linkadmincategories;

    /**
Always try to provide a link for course sections. Course sections are usually only shown as links if the course format displays a single section per page. If this setting is enabled a link will always be provided.
     *
     * @var string linkcoursesections Always link course sections
     */
    public $linkcoursesections;

    /**

     *
     * @var string linkcoursesectionsupgradescriptwasrun 
     */
    public $linkcoursesectionsupgradescriptwasrun;

    /**
     *  for custom $CFG->localcachedir locations
     *
     * @var string localcachedir Local cache dir
     */
    public $localcachedir;

    /**
     *  The $CFG->localcachedirpurged flag forces local directories to be purged on cluster nodes.
     * 
     *
     * @var string localcachedirpurged Local cached dir purged
     */
    public $localcachedirpurged;

    /**
Choose a sitewide locale - this will override the format and language of dates for all language packs (though names of days in calendar are not affected). You need to have this locale data installed on your operating system (eg for linux en_US.UTF-8 or es_ES.UTF-8). In most cases this field should be left blank.
     *
     * @var string locale en_AU.UTF-8
     */
    public $locale;

    /**
     *  Moodle 2.7 introduces a locking api for critical tasks (e.g. cron).
     *  The default locking system to use is DB locking for MySQL and Postgres, and File
     *  locking for Oracle and SQLServer. If $CFG->preventfilelocking is set, then the default
     *  will always be DB locking. It can be manually set to one of the lock
     *  factory classes listed below, or one of your own custom classes implementing the
     *  \core\lock\lock_factory interface.
     *   The list of available lock factories is:
     * 
     *  "\core\lock\file_lock_factory" - File locking
     *       Uses lock files stored by default in the dataroot. Whether this
     *       works on clusters depends on the file system used for the dataroot.
     * 
     *  "\core\lock\db_record_lock_factory" - DB locking based on table rows.
     * 
     *  "\core\lock\postgres_lock_factory" - DB locking based on postgres advisory locks.
     *
     * @var string lock_factory Lock factory
     */
    public $lock_factory;

    /**
Locked out account is automatically unlocked after this duration.
     *
     * @var string lockoutduration Account lockout duration
     */
    public $lockoutduration;

    /**
Select number of failed login attempts that result in account lockout. This feature may be abused in denial of service attacks.
     *
     * @var string lockoutthreshold Account lockout threshold
     */
    public $lockoutthreshold;

    /**
Observation time for lockout threshold, if there are no failed attempts the threshold counter is reset after this time.
     *
     * @var string lockoutwindow Account lockout observation window
     */
    public $lockoutwindow;

    /**
This setting enables logging of actions by guest account and not logged in users. High profile sites may want to disable this logging for performance reasons. It is recommended to keep this setting enabled on production sites.
     *
     * @var string logguests Log guest access
     */
    public $logguests;

    /**
Turning this on will make Moodle use a secure https connection just for the login page (providing a secure login), and then afterwards revert back to the normal http URL for general speed.  CAUTION: this setting REQUIRES https to be specifically enabled on the web server - if it is not then YOU COULD LOCK YOURSELF OUT OF YOUR SITE.
     *
     * @var string loginhttps Use HTTPS for logins
     */
    public $loginhttps;

    /**
Enabling this option improves usability of the login page, but automatically focusing fields may be considered an accessibility issue.
     *
     * @var string loginpageautofocus Autofocus login page form
     */
    public $loginpageautofocus;

    /**
This specifies the length of time you want to keep logs about user activity.  Logs that are older than this age are automatically deleted.  It is best to keep logs as long as possible, in case you need them, but if you have a very busy server and are experiencing performance problems, then you may want to lower the log lifetime. Values lower than 30 are not recommended because statistics may not work properly.
     *
     * @var string loglifetime Keep logs for
     */
    public $loglifetime;

    /**
     * The next line is needed for bounce handling and any other email to module processing.
     *             $CFG->maildomain = 'youremaildomain.com';
     *
     * @var string maildomain Mail domain
     */
    public $maildomain;

    /**
Newline characters used in mail messages. CRLF is required according to RFC 822bis, some mail servers do automatic conversion from LF to CRLF, other mail servers do incorrect conversion from CRLF to CRCRLF, yet others reject mails with bare LF (qmail for example). Try changing this setting if you are having problems with undelivered emails or double newlines.
     *
     * @var string mailnewline Newline characters in mail
     */
    public $mailnewline;

    /**
     * The next line is needed for bounce handling and any other email to module processing.
     *   mailprefix must be EXACTLY four characters.
     *   $CFG->mailprefix = 'mdl+'; // + is the separator for Exim and Postfix.
     *   $CFG->mailprefix = 'mdl-'; // - is the separator for qmail
     *
     * @var string mailprefix Mail prefix
     */
    public $mailprefix;

    /**
If enabled, the teacher will be able to create annotated PDF files when marking the assignments. This allows the teacher to add comments, drawing and stamps directly on top of the students work. The annotating is done in the browser and no extra software is required.
     *
     * @var string maintenance_enabled Enabled
     */
    public $maintenance_enabled;

    /**
     * status: CLI maintenance mode will be enabled on {$a}
     *
     * @var string maintenance_later Maintenance later
     */
    public $maintenance_later;

    /**
     * Optional maintenance message
     *
     * @var string maintenance_message Maintenance message
     */
    public $maintenance_message;

    /**
This specifies a maximum size that uploaded files can be throughout the whole site. This setting is limited by the PHP settings post_max_size and upload_max_filesize, as well as the Apache setting LimitRequestBody. In turn, maxbytes limits the range of sizes that can be chosen at course level or module level. If \'Server Limit\' is chosen, the server maximum allowed by the server will be used.
     *
     * @var string maxbytes Maximum uploaded file size
     */
    public $maxbytes;

    /**
     * This specifies the maximum depth of child categories expanded when displaying categories or combo list. Deeper level categories will appear as links and user can expand them with AJAX request.
     *
     * @var string maxcategorydepth Maximum category depth
     */
    public $maxcategorydepth;

    /**
Passwords must not have more than this number of consecutive identical characters. Use 0 to disable this check.
     *
     * @var string maxconsecutiveidentchars Consecutive identical characters
     */
    public $maxconsecutiveidentchars;

    /**
This specifies the amount of time people have to re-edit forum postings, glossary comments etc.  Usually 30 minutes is a good value.
     *
     * @var string maxeditingtime Maximum time to edit posts
     */
    public $maxeditingtime;

    /**
The number of external blogs each user is allowed to link to their Moodle blog.
     *
     * @var string maxexternalblogsperuser Maximum number of external blogs per user
     */
    public $maxexternalblogsperuser;

    /**
To restrict the maximum PHP execution time that Moodle will allow without any output being displayed, enter a value in seconds here. 0 means that Moodle default restrictions are used. If you have a front-end server with its own time limit, set this value lower to receive PHP errors in logs. Does not apply to CLI scripts.
     *
     * @var string maxtimelimit Maximum time limit
     */
    public $maxtimelimit;

    /**
Maximum number of users displayed within user selector in course, group, cohort, webservice etc.
     *
     * @var string maxusersperpage  Maximum users per page
     */
    public $maxusersperpage;

    /**

     *
     * @var string media_default_height 
     */
    public $media_default_height;

    /**

     *
     * @var string media_default_width 
     */
    public $media_default_width;

    /**

     *
     * @var string media_plugins_sortorder 
     */
    public $media_plugins_sortorder;

    /**
     * 
     *
     * @var string messageinbound_domain Message in bound domain
     */
    public $messageinbound_domain;

    /**
If enabled, the teacher will be able to create annotated PDF files when marking the assignments. This allows the teacher to add comments, drawing and stamps directly on top of the students work. The annotating is done in the browser and no extra software is required.
     *
     * @var string messageinbound_enabled Enabled
     */
    public $messageinbound_enabled;

    /**
     * 
     *
     * @var string messageinbound_host Message in bound host
     */
    public $messageinbound_host;

    /**
     * 
     *
     * @var string messageinbound_hostpass Message in bound host pass
     */
    public $messageinbound_hostpass;

    /**
     * 
     *
     * @var string messageinbound_hostssl Message in bound host ssl
     */
    public $messageinbound_hostssl;

    /**
     * 
     *
     * @var string messageinbound_hostuser Message in bound host user
     */
    public $messageinbound_hostuser;

    /**
     * 
     *
     * @var string messageinbound_mailbox Message in bound mailbox
     */
    public $messageinbound_mailbox;

    /**
Should the messaging system between site users be enabled?
     *
     * @var string messaging Enable messaging system
     */
    public $messaging;

    /**
Allow users to have email message notifications sent to an email address other than the email address in their profile
     *
     * @var string messagingallowemailoverride Notification email override
     */
    public $messagingallowemailoverride;

    /**
Read notifications can be deleted to save space. How long after a notification is read can it be deleted?
     *
     * @var string messagingdeletereadnotificationsdelay Delete read notifications
     */
    public $messagingdeletereadnotificationsdelay;

    /**
Hide read notifications of events like forum posts when viewing messaging history
     *
     * @var string messaginghidereadnotifications Hide read notifications
     */
    public $messaginghidereadnotifications;

    /**

     *
     * @var string messagingmaxpoll 
     */
    public $messagingmaxpoll;

    /**

     *
     * @var string messagingminpoll 
     */
    public $messagingminpoll;

    /**

     *
     * @var string messagingtimeoutpoll 
     */
    public $messagingtimeoutpoll;

    /**
     * The following line is for handling email bounces
     *
     * @var string minbounces Min bounces
     */
    public $minbounces;

    /**
Passwords must have at least these many digits.
     *
     * @var string minpassworddigits Digits
     */
    public $minpassworddigits;

    /**
Passwords must be at least these many characters long.
     *
     * @var string minpasswordlength Password length
     */
    public $minpasswordlength;

    /**
Passwords must have at least these many lower case letters.
     *
     * @var string minpasswordlower Lowercase letters
     */
    public $minpasswordlower;

    /**
Passwords must have at least these many non-alphanumeric characters.
     *
     * @var string minpasswordnonalphanum Non-alphanumeric characters
     */
    public $minpasswordnonalphanum;

    /**
Passwords must have at least these many upper case letters.
     *
     * @var string minpasswordupper Uppercase letters
     */
    public $minpasswordupper;

    /**

     *
     * @var string mlbackend_php_no_evaluation_limits 
     */
    public $mlbackend_php_no_evaluation_limits;

    /**
     * mlbackend_php_no_memory_limit if you are confident that your system can cope with a {$a} dataset.
     *
     * @var string mlbackend_php_no_memory_limit mlbackend_php_no_memory_limit
     */
    public $mlbackend_php_no_memory_limit;

    /**
     * 
     *
     * @var string mnet_all_hosts_id 
     */
    public $mnet_all_hosts_id;

    /**
     * MNet allows communication of this server with other servers or services.
     *
     * @var string mnet_dispatcher_mode Networking
     */
    public $mnet_dispatcher_mode;

    /**
     * 
     *
     * @var string mnet_localhost_id 
     */
    public $mnet_localhost_id;

    /**
     * 
     *
     * @var string mnet_register_allhosts 
     */
    public $mnet_register_allhosts;

    /**
     * 
     *
     * @var string mnet_rpcdebug 
     */
    public $mnet_rpcdebug;

    /**
     * Change the key pair lifetime for Moodle Networking
     * The default is 28 days. You would only want to change this if the key
     * was not getting regenerated for any reason. You would probably want
     * make it much longer. Note that youll need to delete and manually update
     * any existing key.
     *
     * @var string mnetkeylifetime Key pair lifetime for Moodle Networking
     */
    public $mnetkeylifetime;

    /**
     * Here you can configure the list of profile fields that are sent and received over MNet when user accounts are created, or updated.  You can also override this for each MNet peer individually. Note that the following fields are always sent and are not optional: {$a}
     *
     * @var string mnetprofileexportfields Fields to send
     */
    public $mnetprofileexportfields;

    /**
     * Here you can configure the list of profile fields that are sent and received over MNet when user accounts are created, or updated.  You can also override this for each MNet peer individually. Note that the following fields are always sent and are not optional: {$a}
     *
     * @var string mnetprofileimportfields Fields to import
     */
    public $mnetprofileimportfields;

    /**
A CSS file to customise your mobile app interface.
     *
     * @var string mobilecssurl CSS
     */
    public $mobilecssurl;

    /**
     * 
     *
     * @var string mod_lti_forcessl Mod lti forcessl
     */
    public $mod_lti_forcessl;

    /**
     * 
     *
     * @var string mod_lti_institution_name Mod lti institution name
     */
    public $mod_lti_institution_name;

    /**
     * 
     *
     * @var string mod_lti_log_users Mod lti log users
     */
    public $mod_lti_log_users;

    /**
Should the activity chooser be presented to users by default?
     *
     * @var string modchooserdefault Activity chooser default
     */
    public $modchooserdefault;

    /**
     * 
     *
     * @var string moddata Mod data
     */
    public $moddata;

    /**
     * You can specify a different class to be created for the $PAGE global, and to
     *  compute which blocks appear on each page. However, I cannot think of any good
     *  reason why you would need to change that. It just felt wrong to hard-code the
     *  the class name. You are strongly advised not to use these to settings unless
     *  you are absolutely sure you know what you are doing.
     *  $CFG->moodlepageclass = 'moodle_page';
     *
     * @var string moodlepageclass Moodle page class
     */
    public $moodlepageclass;

    /**
     * You can specify a different class to be created for the $PAGE global, and to
     *  compute which blocks appear on each page. However, I cannot think of any good
     *  reason why you would need to change that. It just felt wrong to hard-code the
     *  the class name. You are strongly advised not to use these to settings unless
     *  you are absolutely sure you know what you are doing.
     *   $CFG->moodlepageclassfile = "$CFG->dirroot/local/myplugin/mypageclass.php";
     *
     * @var string moodlepageclassfile Moodle page class file
     */
    public $moodlepageclassfile;

    /**

     *
     * @var string mtrace_wrapper 
     */
    public $mtrace_wrapper;

    /**
If enabled two links will be added to each user in the navigation to view discussions the user has started and posts the user has made in forums throughout the site or in specific courses.
     *
     * @var string navadduserpostslinks Add links to view user posts
     */
    public $navadduserpostslinks;

    /**
Limits the number of courses shown to the user in the navigation.
     *
     * @var string navcourselimit Course limit
     */
    public $navcourselimit;

    /**
This setting determines whether users who are enrolled in courses can see Courses (listing all courses) in the navigation, in addition to My Courses (listing courses in which they are enrolled).
     *
     * @var string navshowallcourses Show all courses
     */
    public $navshowallcourses;

    /**
Show course categories in the navigation bar and navigation blocks. This does not occur with courses the user is currently enrolled in, they will still be listed under mycourses without categories.
     *
     * @var string navshowcategories Show course categories
     */
    public $navshowcategories;

    /**
If enabled, front page activities will be shown on the navigation under site pages.
     *
     * @var string navshowfrontpagemods Show front page activities in the navigation
     */
    public $navshowfrontpagemods;

    /**
If enabled, course full names will be used in the navigation rather than short names.
     *
     * @var string navshowfullcoursenames Show course full names
     */
    public $navshowfullcoursenames;

    /**
If enabled courses in the users my courses branch will be shown in categories.
     *
     * @var string navshowmycoursecategories Show my course categories
     */
    public $navshowmycoursecategories;

    /**
This determines whether courses are listed under My courses according to the sort order (i.e. the order set in Site administration > Courses > Manage courses and categories) or alphabetically by course setting.
     *
     * @var string navsortmycoursessort Sort my courses
     */
    public $navsortmycoursessort;

    /**
     * When working with production data on test servers, no emails or other messages
     *  should ever be send to real users
     *  $CFG->noemailever = true;    // NOT FOR PRODUCTION SERVERS!
     *
     * @var string noemailever No email ever
     */
    public $noemailever;

    /**
     * This setting will cause the userdate() function not to fix %d in
     *  date strings, and just let them show with a zero prefix.
     *
     * @var string nofixday No fix day
     */
    public $nofixday;

    /**
     * 
     *
     * @var string nofixhour No fix hour
     */
    public $nofixhour;

    /**
     * do not save $CFG->nolastloggedin in database!
     * 
     *
     * @var string nolastloggedin No last logged in
     */
    public $nolastloggedin;

    /**
Emails are sometimes sent out on behalf of a user (eg forum posts). The email address you specify here will be used as the "From" address in those cases when the recipients should not be able to reply directly to the user (eg when a user chooses to keep their address private). This setting will also be used as the envelope sender when sending email.
     *
     * @var string noreplyaddress No-reply address
     */
    public $noreplyaddress;

    /**
     * Use the following flag to set userid for noreply user. If not set then moodle will
     *  create dummy user and use -ve value as user id.
     *
     * @var string noreplyuserid No reply user id
     */
    public $noreplyuserid;

    /**
Send login failure notification messages to these selected users. This requires an internal logstore (eg Standard Logstore) to be enabled.
     *
     * @var string notifyloginfailures Email login failures to
     */
    public $notifyloginfailures;

    /**
If notifications about failed logins are active, how many failed login attempts by one user or one IP address is it worth notifying about?
     *
     * @var string notifyloginthreshold Threshold for email notifications
     */
    public $notifyloginthreshold;

    /**
Users who are not logged in to the site will be treated as if they have this role granted to them at the site context.  Guest is almost always what you want here, but you might want to create roles that are less or more restrictive.  Things like creating posts still require the user to log in properly.
     *
     * @var string notloggedinroleid Role for visitors
     */
    public $notloggedinroleid;

    /**
     * Allow specification of openssl.cnf especially for Windows installs.
     *
     * @var string opensslcnf Open SSL config
     */
    public $opensslcnf;

    /**
If you enable this setting, then Google will be allowed to enter your site as a Guest.  In addition, people coming in to your site via a Google search will automatically be logged in as a Guest.  Note that this only provides transparent access to courses that already allow guest access.
     *
     * @var string opentogoogle Open to Google
     */
    public $opentogoogle;

    /**
     * 
     *
     * @var string os OS
     */
    public $os;

    /**
     *  Calculate and set $CFG->ostype to be used everywhere. Possible values are:
     *    $CFG->ostype = 'WINDOWS';
     *    $CFG->ostype = 'UNIX';
     * 
     *
     * @var string ostype OS type
     */
    public $ostype;

    /**

     *
     * @var string overridetossl HTTPS for logins has now been deprecated. This instance is now forced to SSL. To remedy this warning change your wwwroot in config.php to https://
     */
    public $overridetossl;

    /**

     *
     * @var string pagepath Page path
     */
    public $pagepath;

    /**
If enabled, when a password is changed, all browser sessions are terminated, apart from the one in which the new password is specified. (This setting does not affect password changes via bulk user upload.)
     *
     * @var string passwordchangelogout Log out after password change
     */
    public $passwordchangelogout;

    /**
If enabled, when a password is changed, all the user web service access tokens are deleted.
     *
     * @var string passwordchangetokendeletion Remove web service access tokens after password change
     */
    public $passwordchangetokendeletion;

    /**
Turning this on will make Moodle check user passwords against a valid password policy. Use the settings below to specify your policy (they will be ignored if you set this to \'No\').
     *
     * @var string passwordpolicy Password policy
     */
    public $passwordpolicy;

    /**
Number of times a user must change their password before they are allowed to reuse a password. Hashes of previously used passwords are stored in local database table. This feature might not be compatible with some external authentication plugins.
     *
     * @var string passwordreuselimit Password rotation limit
     */
    public $passwordreuselimit;

    /**
     * A site-wide password salt is no longer used in new installations.
     *  If upgrading from 2.6 or older, keep all existing salts in config.php file.
     * 
     *  $CFG->passwordsaltmain = 'a_very_long_random_string_of_characters#@6&*1';
     * 
     *  You may also have some alternative salts to allow migration from previously
     *  used salts.
     *
     * @var string passwordsaltmain Secret password salt
     */
    public $passwordsaltmain;

    /**
Path to dot. Probably something like /usr/bin/dot. To be able to generate graphics from DOT files, you must have installed the dot executable and point to it here. Note that, for now, this only used by the profiling features (Development->Profiling) built into Moodle.
     *
     * @var string pathtodot Path to dot
     */
    public $pathtodot;

    /**
Path to du. Probably something like /usr/bin/du. If you enter this, pages that display directory contents will run much faster for directories with a lot of files.
     *
     * @var string pathtodu Path to du
     */
    public $pathtodu;

    /**
On most Linux installs, this can be left as \'/usr/bin/gs\'. On Windows it will be something like \'c:\\gs\\bin\\gswin32c.exe\' (make sure there are no spaces in the path - if necessary copy the files \'gswin32c.exe\' and \'gsdll32.dll\' to a new folder without a space in the path)
     *
     * @var string pathtogs Path to ghostscript
     */
    public $pathtogs;

    /**

     *
     * @var string pathtopython Path to Python
     */
    public $pathtopython;

    /**
Path to unoconv document converter. This is an executable that is capable of converting between document formats supported by LibreOffice. This is optional, but if specified, Moodle will use it to automatically convert between document formats. This is used to support a wider range of input files for the assignment annotate PDF feature.
     *
     * @var string pathtounoconv Path to unoconv document converter
     */
    public $pathtounoconv;

    /**
If you turn this on, performance info will be printed in the footer of the standard theme
     *
     * @var string perfdebug Performance info
     */
    public $perfdebug;

    /**
     *  $CFG->phpunit_dataroot = '/home/example/phpu_moodledata';
     * 
     *
     * @var string phpunit_dataroot PHP unit dataroot
     */
    public $phpunit_dataroot;

    /**
Type database server IP address or host name. Use a system DSN name if using ODBC. Use a PDO DSN if using PDO.
     *
     * @var string phpunit_dbhost Host server
     */
    public $phpunit_dbhost;

    /**
     * 
     *
     * @var string phpunit_dblibrary PHP unit database library
     */
    public $phpunit_dblibrary;

    /**
Leave empty if using a DSN name in database host.
     *
     * @var string phpunit_dbname Database name
     */
    public $phpunit_dbname;

    /**
     * 
     *
     * @var string phpunit_dboptions PHP unit database options
     */
    public $phpunit_dboptions;

    /**
     * 
     *
     * @var string phpunit_dbpass PHP unit database password
     */
    public $phpunit_dbpass;

    /**
ADOdb database driver name, type of the external database engine.
     *
     * @var string phpunit_dbtype Type
     */
    public $phpunit_dbtype;

    /**
     * 
     *
     * @var string phpunit_dbuser PHP unit database user
     */
    public $phpunit_dbuser;

    /**
     * $CFG->phpunit_directorypermissions = 02777; // optional
     *
     * @var string phpunit_directorypermissions PHP unit directory permissions
     */
    public $phpunit_directorypermissions;

    /**
     * 
     *
     * @var string phpunit_extra_drivers PHP unit extra drivers
     */
    public $phpunit_extra_drivers;

    /**
This prefix is used for all key names on the Redis server.
* If you only have one Moodle instance using this server, you can leave this value default.
* Due to key length restrictions, a maximum of 5 characters is permitted.
     *
     * @var string phpunit_prefix Key prefix
     */
    public $phpunit_prefix;

    /**
If you enable this setting, then profiling will be available in this site and you will be able to define its behavior by configuring the next options.
     *
     * @var string phpunit_profilingenabled Enable profiling
     */
    public $phpunit_profilingenabled;

    /**
     * 
     *
     * @var string phpunit_test_get_config_1 PHP unit test get config 1
     */
    public $phpunit_test_get_config_1;

    /**
     * 
     *
     * @var string phpunit_test_get_config_5 PHP unit test get config 5
     */
    public $phpunit_test_get_config_5;

    /**
     * 
     *
     * @var string portfolio_high_dbsize_threshold Portfolio high databaze size threshold
     */
    public $portfolio_high_dbsize_threshold;

    /**
     * 
     *
     * @var string portfolio_moderate_dbsize_threshold Portfolio moderate database size threshold
     */
    public $portfolio_moderate_dbsize_threshold;

    /**
     * This setting will make some graphs (eg user logs) use lines instead of bars
     *
     * @var string preferlinegraphs Prefer line graphs
     */
    public $preferlinegraphs;

    /**
This prefix is used for all key names on the Redis server.
* If you only have one Moodle instance using this server, you can leave this value default.
* Due to key length restrictions, a maximum of 5 characters is permitted.
     *
     * @var string prefix Key prefix
     */
    public $prefix;

    /**
     * Some administration options allow setting the path to executable files. This can
     *  potentially cause a security risk. Set this option to true to disable editing
     *  those config settings via the web. They will need to be set explicitly in the
     *  config.php file
     *
     * @var string preventexecpath Prevent exec path
     */
    public $preventexecpath;

    /**
     * Some filesystems such as NFS may not support file locking operations.
     *  Locking resolves race conditions and is strongly recommended for production servers.
     *
     * @var string preventfilelocking Prevent file locking
     */
    public $preventfilelocking;

    /**
     * Use the following flag to disable modifications to scheduled tasks
     *  whilst still showing the state of tasks.
     *
     * @var string preventscheduledtaskchanges Prevent scheduled task changes
     */
    public $preventscheduledtaskchanges;

    /**
List of roles that are visible on user profiles and participation page.
     *
     * @var string profileroles Profile visible roles
     */
    public $profileroles;

    /**
To prevent misuse by spammers, profile descriptions of users who are not yet enrolled in any course are hidden. New users must enrol in at least one course before they can add a profile description.
     *
     * @var string profilesforenrolledusersonly Profiles for enrolled users only
     */
    public $profilesforenrolledusersonly;

    /**
If you enable this setting, then, at any moment, you can use the PROFILEALL parameter anywhere (PGC) to enable profiling for all the executed scripts along the Moodle session life. Analogously, you can use the PROFILEALLSTOP parameter to stop it.
     *
     * @var string profilingallowall Continuous profiling
     */
    public $profilingallowall;

    /**
If you enable this setting, then, selectively, you can use the PROFILEME parameter anywhere (PGC) and profiling for that script will happen. Analogously, you can use the DONTPROFILEME parameter to prevent profiling to happen
     *
     * @var string profilingallowme Selective profiling
     */
    public $profilingallowme;

    /**
By configuring this setting, some request (randomly, based on the frequency specified - 1 of N) will be picked and automatically profiled, storing results for further analysis. Note that this way of profiling observes the include/exclude settings. Set it to 0 to disable automatic profiling.
     *
     * @var string profilingautofrec Automatic profiling
     */
    public $profilingautofrec;

    /**
If you enable this setting, then profiling will be available in this site and you will be able to define its behavior by configuring the next options.
     *
     * @var string profilingenabled Enable profiling
     */
    public $profilingenabled;

    /**
List of (comma separated, absolute skipping wwwroot, callable) URLs that will be excluded from being profiled from the ones defined by \'Profile these\' setting.
     *
     * @var string profilingexcluded Exclude profiling
     */
    public $profilingexcluded;

    /**
For easier detection, all the imported profiling runs will be prefixed with the value specified here.
     *
     * @var string profilingimportprefix Profiling import prefix
     */
    public $profilingimportprefix;

    /**
List of (comma separated, absolute skipping wwwroot, callable) URLs that will be automatically profiled. Examples: /index.php, /course/view.php. Also accepts the * wildchar at any position. Examples: /mod/forum/*, /mod/* /view.php.
     *
     * @var string profilingincluded Profile these
     */
    public $profilingincluded;

    /**
Specify the time you want to keep information about old profiling runs. Older ones will be pruned periodically. Note that this excludes any profiling run marked as \'reference run\'.
     *
     * @var string profilinglifetime Keep profiling runs
     */
    public $profilinglifetime;

    /**
By default forget_password.php does not display any hints that would allow guessing of usernames or email addresses.
     *
     * @var string protectusernames Protect usernames
     */
    public $protectusernames;

    /**
Comma separated list of (partial) hostnames or IPs that should bypass proxy (e.g., 192.168., .mydomain.com)
     *
     * @var string proxybypass Proxy bypass hosts
     */
    public $proxybypass;

    /**
If this <b>server</b> needs to use a proxy computer (eg a firewall) to access the Internet, then provide the proxy hostname here.  Otherwise leave it blank.
     *
     * @var string proxyhost Proxy host
     */
    public $proxyhost;

    /**
Password needed to access internet through proxy if required, empty if none (PHP cURL extension required).
     *
     * @var string proxypassword Proxy password
     */
    public $proxypassword;

    /**
If this server needs to use a proxy computer, then provide the proxy port here.
     *
     * @var string proxyport Proxy port
     */
    public $proxyport;

    /**
Type of web proxy (PHP5 and cURL extension required for SOCKS5 support).
     *
     * @var string proxytype Proxy type
     */
    public $proxytype;

    /**
Username needed to access internet through proxy if required, empty if none (PHP cURL extension required).
     *
     * @var string proxyuser Proxy username
     */
    public $proxyuser;

    /**
     * 
     *
     * @var string pwresettime Password reset time
     */
    public $pwresettime;

    /**
     * 
     *
     * @var string questionbankcolumns Qestion bank columns
     */
    public $questionbankcolumns;

    /**
     * 
     *
     * @var string quizquestionbankcolumns Quiz question bank columns
     */
    public $quizquestionbankcolumns;

    /**
String of characters (private key) used to communicate between your Moodle server and the recaptcha server. ReCAPTCHA keys can be obtained from <a target="_blank" href="https://www.google.com/recaptcha">Google reCAPTCHA</a>.
     *
     * @var string recaptchaprivatekey ReCAPTCHA secret key
     */
    public $recaptchaprivatekey;

    /**
String of characters (public key) used to display the reCAPTCHA element in the signup form. ReCAPTCHA keys can be obtained from <a target="_blank" href="https://www.google.com/recaptcha">Google reCAPTCHA</a>.
     *
     * @var string recaptchapublickey ReCAPTCHA site key
     */
    public $recaptchapublickey;

    /**
By default recover old grades when re-enrolling a user in a course.
     *
     * @var string recovergradesdefault Recover grades default
     */
    public $recovergradesdefault;

    /**
     * If an authentication plugin, such as email-based self-registration, is selected, then it enables potential users to register themselves and create accounts. This results in the possibility of spammers creating accounts in order to use forum posts, blog entries etc. for spam. To avoid this risk, self-registration should be disabled or limited by <em>Allowed email domains</em> setting.
     *
     * @var string registerauth Register authorization
     */
    public $registerauth;

    /**

     *
     * @var string registrationpending 
     */
    public $registrationpending;

    /**

     *
     * @var string release Release
     */
    public $release;

    /**
Enable if you want to store permanent cookies with usernames during user login. Permanent cookies may be considered a privacy issue if used without consent.
     *
     * @var string rememberusername Remember username
     */
    public $rememberusername;

    /**
     * 
     *
     * @var string repository_no_delete Repository no delete
     */
    public $repository_no_delete;

    /**
     * The amount of time that file listings are cached locally (in seconds) when browsing external repositories.
     *
     * @var string repositorycacheexpire Cache expire
     */
    public $repositorycacheexpire;

    /**
     * Timeout in seconds for downloading the external file into moodle
     *
     * @var string repositorygetfiletimeout Repository get file timeout
     */
    public $repositorygetfiletimeout;

    /**
     * Timeout in seconds for syncronising the external file size
     *
     * @var string repositorysyncfiletimeout Repository sync file timeout
     */
    public $repositorysyncfiletimeout;

    /**
     * Timeout in seconds for downloading an image file from external repository during syncronisation
     *
     * @var string repositorysyncimagetimeout Repositoy sync image timeout
     */
    public $repositorysyncimagetimeout;

    /**
Allow the selection of a category when requesting a course.
     *
     * @var string requestcategoryselection Enable category selection
     */
    public $requestcategoryselection;

    /**
If enabled, users will be forced to enter a description for each activity.
     *
     * @var string requiremodintro Require activity description
     */
    public $requiremodintro;

    /**
If the user does not already have the permission to manage the newly restored course, the user is automatically assigned this role and enrolled if necessary. Select "None" if you do not want restorers to be able to manage every restored course.
     *
     * @var string restorernewroleid Restorers\' role in courses
     */
    public $restorernewroleid;

    /**

     *
     * @var string reverseproxy Reverse proxy
     */
    public $reverseproxy;

    /**
     * 
     *
     * @var string rolesactive Roles active
     */
    public $rolesactive;

    /**
     * 
     *
     * @var string running_installer Running installer
     */
    public $running_installer;

    /**
     * 
     *
     * @var string scorm_updatetimelast Scorm update time last
     */
    public $scorm_updatetimelast;

    /**

     *
     * @var string searchengine Search engine
     */
    public $searchengine;

    /**
Allows the scheduled task to build the search index even when search is disabled. This is useful if you want to build the index before the search facility appears to students.
     *
     * @var string searchindexwhendisabled Index when disabled
     */
    public $searchindexwhendisabled;

    /**
     * 
     *             $CFG->session_memcached_acquire_lock_timeout = 120;
     * 
     *
     * @var string session_database_acquire_lock_timeout Session database acquire lock timeout
     */
    public $session_database_acquire_lock_timeout;

    /**
     * $CFG->session_file_save_path = $CFG->dataroot.'/sessions';
     * 
     *
     * @var string session_file_save_path Session file save patch
     */
    public $session_file_save_path;

    /**
     * Following settings may be used to select session driver:
     *              Database session handler (not compatible with MyISAM):
     *       $CFG->session_handler_class = '\core\session\database';
     *      
     * 
     *    File session handler (file system locking required):
     *       $CFG->session_handler_class = '\core\session\file';
     *     
     * 
     *    Memcached session handler (requires memcached server and extension):
     *       $CFG->session_handler_class = '\core\session\memcached';
     *      
     *    Memcache session handler (requires memcached server and memcache extension):
     *       $CFG->session_handler_class = '\core\session\memcache';
     *      
     *       ** NOTE: Memcache extension has less features than memcached and may be
     *          less reliable. Use memcached where possible or if you encounter
     *          session problems. **
     *             
     *
     * @var string session_handler_class Session handler class
     */
    public $session_handler_class;

    /**
     * $CFG->session_memcache_acquire_lock_timeout = 120;
     *
     * @var string session_memcache_acquire_lock_timeout Session memcache qcquire lock timeout
     */
    public $session_memcache_acquire_lock_timeout;

    /**
     * $CFG->session_memcache_save_path = '127.0.0.1:11211';
     *
     * @var string session_memcache_save_path Session memcache save path
     */
    public $session_memcache_save_path;

    /**
     * $CFG->session_memcached_acquire_lock_timeout = 120;
     * 
     *
     * @var string session_memcached_acquire_lock_timeout Session memcached acquire lock timeout
     */
    public $session_memcached_acquire_lock_timeout;

    /**
     * $CFG->session_memcached_lock_expire = 7200;       // Ignored if PECL memcached is below version 2.2.0
     *
     * @var string session_memcached_lock_expire Session memcached lock expire
     */
    public $session_memcached_lock_expire;

    /**
     *  Spin-lock retry sleeptime (msec). Only effective for tuning php-memcached 3.0.x (PHP 7)
     *
     * @var string session_memcached_lock_retry_sleep session_memcached_lock_retry_sleep
     */
    public $session_memcached_lock_retry_sleep;

    /**
     * $CFG->session_memcached_prefix = 'memc.sess.key.';
     * 
     *
     * @var string session_memcached_prefix Session memcached prefix
     */
    public $session_memcached_prefix;

    /**
     * $CFG->session_memcached_save_path = '127.0.0.1:11211';
     * 
     *
     * @var string session_memcached_save_path Session memchaed save path
     */
    public $session_memcached_save_path;

    /**
     * session_redis_acquire_lock_timeout
     *
     * @var string session_redis_acquire_lock_timeout session_redis_acquire_lock_timeout
     */
    public $session_redis_acquire_lock_timeout;

    /**
     * Optional, default is dont set one.
     *
     * @var string session_redis_auth session_redis_auth
     */
    public $session_redis_auth;

    /**
     * Optional, default is db 0.
     *
     * @var string session_redis_database session_redis_database
     */
    public $session_redis_database;

    /**
     * session_redis_host
     *
     * @var string session_redis_host session_redis_host
     */
    public $session_redis_host;

    /**
     * session_redis_lock_expire
     *
     * @var string session_redis_lock_expire session_redis_lock_expire
     */
    public $session_redis_lock_expire;

    /**
     * session_redis_port
     *
     * @var string session_redis_port session_redis_port
     */
    public $session_redis_port;

    /**
     *  Optional, default is dont set one.
     *
     * @var string session_redis_prefix session_redis_prefix
     */
    public $session_redis_prefix;

    /**
     * Optional, default is PHP builtin serializer.
     *
     * @var string session_redis_serializer_use_igbinary session_redis_serializer_use_igbinary
     */
    public $session_redis_serializer_use_igbinary;

    /**
     * Following setting allows you to alter how frequently is timemodified updated in sessions table.
     *
     * @var string session_update_timemodified_frequency Session update time modified frequency
     */
    public $session_update_timemodified_frequency;

    /**
This setting customises the name of the cookie used for Moodle sessions.  This is optional, and only useful to avoid cookies being confused when there is more than one copy of Moodle running within the same web site.
     *
     * @var string sessioncookie Cookie prefix
     */
    public $sessioncookie;

    /**
This allows you to change the domain that the Moodle cookies are available from. This is useful for Moodle customisations (e.g. authentication or enrolment plugins) that need to share Moodle session information with a web application on another subdomain. <strong>WARNING: it is strongly recommended to leave this setting at the default (empty) - an incorrect value will prevent all logins to the site.</strong>
     *
     * @var string sessioncookiedomain Cookie domain
     */
    public $sessioncookiedomain;

    /**
If you need to change where browsers send the Moodle cookies, you can change this setting to specify a subdirectory of your web site.  Otherwise the default \'/\' should be fine.
     *
     * @var string sessioncookiepath Cookie path
     */
    public $sessioncookiepath;

    /**
If people logged in to this site are idle for a long time (without loading pages) then they are automatically logged out (their session is ended).  This variable specifies how long this time should be.
     *
     * @var string sessiontimeout Timeout
     */
    public $sessiontimeout;

    /**
     * Force developer level debug and add debug info to the output of cron
     *  $CFG->showcrondebugging = true;
     *
     * @var string showcrondebugging Show cron debugging
     */
    public $showcrondebugging;

    /**
     * Add SQL queries to the output of cron, just before their execution
     * $CFG->showcronsql = true;
     *
     * @var string showcronsql Show cron SQL
     */
    public $showcronsql;

    /**
When selecting or searching for users, and when displaying lists of users, these fields may be shown in addition to their full name. The fields are only shown to users who have the moodle/site:viewuseridentity capability; by default, teachers and managers. (This option makes most sense if you choose one or two fields that are mandatory at your institution.)
     *
     * @var string showuseridentity Show user identity
     */
    public $showuseridentity;

    /**
     * [[administrationsite]]
     *
     * @var string siteadmins Site admins
     */
    public $siteadmins;

    /**
     * The default licence for publishing content on this site
     *
     * @var string sitedefaultlicense Default site license
     */
    public $sitedefaultlicense;

    /**
     * 
     *
     * @var string siteguest 
     */
    public $siteguest;

    /**
     * 
     *
     * @var string siteidentifier Site iidentifier
     */
    public $siteidentifier;

    /**
This setting specifies the default charset for all emails sent from the site.
     *
     * @var string sitemailcharset Character set
     */
    public $sitemailcharset;

    /**
If you have a site policy that all registered users must see and agree to before using this site, then specify the URL to it here, otherwise leave this field blank. This setting can contain any public URL.
     *
     * @var string sitepolicy Site policy URL
     */
    public $sitepolicy;

    /**
If you have a site policy that all guests must see and agree to before using this site, then specify the URL to it here, otherwise leave this field blank. This setting can contain any public URL. Note: access of not-logged-in users may be prevented with forcelogin setting.
     *
     * @var string sitepolicyguest Site policy URL for guests
     */
    public $sitepolicyguest;

    /**
     * 
     *
     * @var string skiplangupgrade Skip language upgrade
     */
    public $skiplangupgrade;

    /**
\'Slash arguments\' (using <em>PATH_INFO</em>) is required for SCORM packages and multiple-file resources to display correctly. If your web server doesn\'t support \'slash arguments\' and you are unable to configure it, this setting can be disabled, though it will result in things not working.<br />Note: The use of \'slash arguments\' will be required in future versions of Moodle.
     *
     * @var string slasharguments Use slash arguments
     */
    public $slasharguments;

    /**
This sets the authentication type to use on smtp server.
     *
     * @var string smtpauthtype SMTP Auth Type
     */
    public $smtpauthtype;

    /**
Give the full name of one or more local SMTP servers that Moodle should use to send mail (eg \'mail.a.com\' or \'mail.a.com;mail.b.com\'). To specify a non-default port (i.e other than port 25), you can use the [server]:[port] syntax (eg \'mail.a.com:587\'). For secure connections, port 465 is usually used with SSL, port 587 is usually used with TLS, specify security protocol below if required. If you leave this field blank, Moodle will use the PHP default method of sending mail.
     *
     * @var string smtphosts SMTP hosts
     */
    public $smtphosts;

    /**
Maximum number of messages sent per SMTP session. Grouping messages may speed up the sending of emails. Values lower than 2 force creation of new SMTP session for each email.
     *
     * @var string smtpmaxbulk SMTP session limit
     */
    public $smtpmaxbulk;

    /**

     *
     * @var string smtppass SMTP password
     */
    public $smtppass;

    /**
If SMTP server requires secure connection, specify the correct protocol type.
     *
     * @var string smtpsecure SMTP security
     */
    public $smtpsecure;

    /**
If you have specified an SMTP server above, and the server requires authentication, then enter the username and password here.
     *
     * @var string smtpuser SMTP username
     */
    public $smtpuser;

    /**
     * Enable when using external SSL appliance for performance reasons.
     *   Please note that site may be accessible via http: or https:, but not both!
     *
     * @var string sslproxy SSL proxy
     */
    public $sslproxy;

    /**
This specifies how far back the logs should be processed <b>the first time</b> the cronjob wants to process statistics. If you have a lot of traffic and are on shared hosting, it\'s probably not a good idea to go too far back, as it could take a long time to run and be quite resource intensive. (Note that for this setting, 1 month = 28 days. In the graphs and reports generated, 1 month = 1 calendar month.)
     *
     * @var string statsfirstrun Maximum processing interval
     */
    public $statsfirstrun;

    /**
     * 
     *
     * @var string statslastdaily Stats last daily
     */
    public $statslastdaily;

    /**
Stats processing can be quite intensive, so use a combination of this field and the next one to specify when it will run and how long for.
     *
     * @var string statsmaxruntime Maximum runtime
     */
    public $statsmaxruntime;

    /**
This specifies the maximum number of days processed in each statistics execution. Once the statistics are up-to-date, only one day will be processed, so adjust this value depending of your server load, reducing it if shorter cron executions are needed.
     *
     * @var string statsruntimedays Days to process
     */
    public $statsruntimedays;

    /**
     * What time should the cronjob that does the statistics processing start? Specifying different times is recommended if there are multiple Moodle sites on one server.
     *
     * @var string statsruntimestarthour Run at
     */
    public $statsruntimestarthour;

    /**
     * What time should the cronjob that does the statistics processing start? Specifying different times is recommended if there are multiple Moodle sites on one server.
     *
     * @var string statsruntimestartminute Run at
     */
    public $statsruntimestartminute;

    /**
This setting specifies the minimum number of enrolled users for a course to be included in statistics calculations.
     *
     * @var string statsuserthreshold User threshold
     */
    public $statsuserthreshold;

    /**
If enabled, users are prevented from entering a space or line break only in required fields in forms.
     *
     * @var string strictformsrequired Strict validation of required fields
     */
    public $strictformsrequired;

    /**
     *  These one is managed in a strange way by the filters setting page, so have to be initialised in install.php.
     *
     * @var string stringfilters String filters
     */
    public $stringfilters;

    /**
This email address will be published to users of this site as the one to email when they need general help (for example, when new users create their own accounts).  If this email is left blank then no such helpful email address is supplied.
     *
     * @var string supportemail Support email
     */
    public $supportemail;

    /**
This is the name of a person or other entity offering general help via the support email or web address.
     *
     * @var string supportname Support name
     */
    public $supportname;

    /**
This web address will be published to users of this site as the one to go to when they need general help (for example, when new users create their own accounts).  If this address is left blank then no link will be supplied.
     *
     * @var string supportpage Support page
     */
    public $supportpage;

    /**
     * As of version 2.6 Moodle supports admin to set support user. If not set, all mails
     *  will be sent to supportemail.
     *
     * @var string supportuserid Support user id
     */
    public $supportuserid;

    /**
     * As of version 2.4 Moodle serves icons as SVG images if the users browser appears
     *  to support SVG.
     *  For those wanting to control the serving of SVG images the following setting can
     *  be defined in your config.php.
     *  If it is not defined then the default (browser detection) will occur.
     *
     * @var string svgicons SVG icons
     */
    public $svgicons;

    /**

     *
     * @var string tagsort Sort the tag display by
     */
    public $tagsort;

    /**
     * Installation
     *
     * @var string target_release Moodle {$a} command line installation program
     */
    public $target_release;

    /**
Remove temporary data files from the data folder that are older than the selected time.
     *
     * @var string tempdatafoldercleanup Clean up temporary data files older than
     */
    public $tempdatafoldercleanup;

    /**
     * for custom $CFG->tempdir locations
     *
     * @var string tempdir Temp dir
     */
    public $tempdir;

    /**
     * 
     *
     * @var string texteditors Text ediotors
     */
    public $texteditors;

    /**

     *
     * @var string theme Theme
     */
    public $theme;

    /**
Normally all theme images and style sheets are cached in browsers and on the server for a very long time, for performance. If you are designing themes or developing code then you probably want to turn this mode on so that you are not served cached versions.  Warning: this will make your site slower for all users!  Alternatively, you can also reset the theme caches manually from the Theme selection page.
     *
     * @var string themedesignermode Theme designer mode
     */
    public $themedesignermode;

    /**
     * It is possible to add extra themes directory stored outside of $CFG->dirroot.
     *  This local directory does not have to be accessible from internet.
     *
     * @var string themedir Theme directory
     */
    public $themedir;

    /**
Leave this blank to allow any valid theme to be used.  If you want to shorten the theme menu, you can specify a comma-separated list of names here (Don\'t use spaces!).
For example:  standard,orangewhite.
     *
     * @var string themelist Theme list
     */
    public $themelist;

    /**
     * Set the priority of themes from highest to lowest. This is useful (for
     *  example) in sites where the user theme should override all other theme
     *  settings for accessibility reasons. You can also disable types of themes
     *  (other than site)  by removing them from the array. The default setting is:
     *       $CFG->themeorder = array('course', 'category', 'session', 'user', 'site');
     *  NOTE: course, category, session, user themes still require the
     *  respective settings to be enabled
     *
     * @var string themeorder Theme order
     */
    public $themeorder;

    /**
     * 
     *
     * @var string themerev Theme rev
     */
    public $themerev;

    /**
This is the default timezone for displaying dates - each user can override this setting in their profile. Cron tasks and other server settings are specified in this timezone. You should change the setting if it shows as "Invalid timezone"
     *
     * @var string timezone Default timezone
     */
    public $timezone;

    /**
Length of time for which a web services token created by a user (for example via the mobile app) is valid.
     *
     * @var string tokenduration User created token duration
     */
    public $tokenduration;

    /**
     * 
     *
     * @var string tool_dbransfer_migration_running Tool dbransfer migration running
     */
    public $tool_dbransfer_migration_running;

    /**
     * The developer data generator tool is intended to be used only in development or testing sites and
     *  it's usage in production environments is not recommended; if it is used to create JMeter test plans
     *  is even less recommended as JMeter needs to log in as site course users. JMeter needs to know the
     *  users passwords but would be dangerous to have a default password as everybody would know it, which would
     *  be specially dangerouse if somebody uses this tool in a production site, so in order to prevent unintended
     *  uses of the tool and undesired accesses as well, is compulsory to set a password for the users
     *  generated by this tool, but only in case you want to generate a JMeter test. The value should be a string.
     *  Example:
     *    $CFG->tool_generator_users_password = 'examplepassword';
     *
     * @var string tool_generator_users_password Tool generator users password
     */
    public $tool_generator_users_password;

    /**
     * If this setting is set to true, then Moodle will track the IP of the
     *  current user to make sure it hasn't changed during a session.  This
     *  will prevent the possibility of sessions being hijacked via XSS, but it
     *  may break things for users coming using proxies that change all the time,
     *  like AOL.
     *
     * @var string tracksessionip Track session IP
     */
    public $tracksessionip;

    /**
     * 
     *
     * @var string trashdir Trash directory
     */
    public $trashdir;

    /**
     * 
     *
     * @var string umaskpermissions Umask permissions
     */
    public $umaskpermissions;

    /**
     * List of undeletable block types
     *
     * @var string undeletableblocktypes Undeletable block types
     */
    public $undeletableblocktypes;

    /**
     * 
     *
     * @var string unittestprefix Unit test prefix
     */
    public $unittestprefix;

    /**
By default, grades are limited by the maximum and minimum values of the grade item. Enabling this setting removes this limit, and allows grades of over 100% to be entered directly in the gradebook.
     *
     * @var string unlimitedgrades Unlimited grades
     */
    public $unlimitedgrades;

    /**
If enabled, your site will automatically check for available updates for both Moodle code and all additional plugins. If there is a new update available, a notification will be sent to site admins.
     *
     * @var string updateautocheck Automatically check for available updates
     */
    public $updateautocheck;

    /**
     * 
     *
     * @var string updatecronoffset Update cron offset
     */
    public $updatecronoffset;

    /**
Notify about available updates only if the available code has the selected maturity level at least. Updates for plugins that do not declare their code maturity level are always reported regardless this setting.
     *
     * @var string updateminmaturity Required code maturity
     */
    public $updateminmaturity;

    /**
If enabled, the available update for Moodle code is also reported when a new build for the current version is available. Builds are continuous improvements of a given Moodle version. They are generally released every week. If disabled, the available update will be reported only when there is a higher version of Moodle released. Checks for plugins are not affected by this setting.
     *
     * @var string updatenotifybuilds Notify about new builds
     */
    public $updatenotifybuilds;

    /**
     * 
     *
     * @var string upgrade_calculatedgradeitemsonlyregrade Upgrade calculate grade items only regrade
     */
    public $upgrade_calculatedgradeitemsonlyregrade;

    /**
     * Upgrade key (leave empty to not set it)
     *
     * @var string upgradekey Upgrade key required
     */
    public $upgradekey;

    /**

     *
     * @var string upgraderunning Site is being upgraded, please retry later.
     */
    public $upgraderunning;

    /**
     * Since 2.0 sql queries are not shown during upgrade by default.
     *  Please note that this setting may produce very long upgrade page on large sites.
     *  $CFG->upgradeshowsql = true; // NOT FOR PRODUCTION SERVERS!
     *
     * @var string upgradeshowsql Upgrade show sql
     */
    public $upgradeshowsql;

    /**

     *
     * @var string urlrewriteclass 
     */
    public $urlrewriteclass;

    /**
Enables the association of blog entries with courses and course modules.
     *
     * @var string useblogassociations Enable blog associations
     */
    public $useblogassociations;

    /**
     * Enable comments
     *
     * @var string usecomments Enable comments
     */
    public $usecomments;

    /**
Enables users to specify external blog feeds. Moodle regularly checks these blog feeds and copies new entries to the local blog of that user.
     *
     * @var string useexternalblogs Enable external blogs
     */
    public $useexternalblogs;

    /**
Instead of using local files, use online files available on Yahoo&#145;s servers. WARNING: This requires an internet connection, or no AJAX will work on your site. This setting is not compatible with sites using https.
     *
     * @var string useexternalyui Use online YUI libraries
     */
    public $useexternalyui;

    /**
     * 
     *
     * @var string usepaypalsandbox Use paypal sandbox
     */
    public $usepaypalsandbox;

    /**
The maximum number of bytes that a user can store in their own private file area. {$a->bytes} bytes == {$a->displaysize}
     *
     * @var string userquota User quota
     */
    public $userquota;

    /**
If enabled the site\'s shortname will be used for the site pages node in the navigation rather than the string \'Site pages\'
     *
     * @var string usesitenameforsitepages Use site name for site pages
     */
    public $usesitenameforsitepages;

    /**
Should tags functionality across the site be enabled?
     *
     * @var string usetags Enable tags functionality
     */
    public $usetags;

    /**
     * Force the backup system to continue to create backups in the legacy zip
     *  format instead of the new tgz format. Does not affect restore, which
     *  auto-detects the underlying file format.
     *
     * @var string usezipbackups Use zip backups
     */
    public $usezipbackups;

    /**
Enables verification of changed email addresses using allowed and denied email domains settings. If this setting is disabled the domains are enforced only when creating new users.
     *
     * @var string verifychangedemail Restrict domains when changing email
     */
    public $verifychangedemail;

    /**

     *
     * @var string version Version
     */
    public $version;

    /**
     * Manage protocols
     *
     * @var string webserviceprotocols Web service protocols
     */
    public $webserviceprotocols;

    /**
     * Results of searching user profiles containing:
     *
     * @var string wordlist Word list
     */
    public $wordlist;

    /**

     *
     * @var string wwwroot Web address
     */
    public $wwwroot;

    /**
     * Uncomment if you want to allow empty comments when modifying install.xml files.
     *  $CFG->xmldbdisablecommentchecking = true;    // NOT FOR PRODUCTION SERVERS!
     *
     * @var string xmldbdisablecommentchecking XML db disable comment checking
     */
    public $xmldbdisablecommentchecking;

    /**
     * Some web servers can offload the file serving from PHP process
     *
     * @var string xsendfile X send file
     */
    public $xsendfile;

    /**
     * If your X-Sendfile implementation (usually Nginx) uses directory aliases specify them
     *  in the following array setting:
     *      $CFG->xsendfilealiases = array(
     *          '/dataroot/' => $CFG->dataroot,
     *          '/cachedir/' => '/var/www/moodle/cache',    // for custom $CFG->cachedir locations
     *          '/localcachedir/' => '/var/local/cache',    // for custom $CFG->localcachedir locations
     *          '/tempdir/'  => '/var/www/moodle/temp',     // for custom $CFG->tempdir locations
     *          '/filedir'   => '/var/www/moodle/filedir',  // for custom $CFG->filedir locations
     *      );
     *
     * @var string xsendfilealiases X send file aliases
     */
    public $xsendfilealiases;

    /**
     * 
     *
     * @var string xx XX
     */
    public $xx;

    /**
     * 
     *
     * @var string yui2version YUI 2 version
     */
    public $yui2version;

    /**
     * 
     *
     * @var string yui3version YUI 3 version
     */
    public $yui3version;

    /**
This options enables combined file loading optimisation for YUI libraries. This setting should be enabled on production sites for performance reasons.
     *
     * @var string yuicomboloading YUI combo loading
     */
    public $yuicomboloading;

    /**
     * Restrict which YUI logging statements are shown in the browser console.
     *  For details see the upstream documentation:
     *  http://yuilibrary.com/yui/docs/api/classes/config.html#property_logInclude
     *  http://yuilibrary.com/yui/docs/api/classes/config.html#property_logExclude
     *  $CFG->yuiloginclude = array(
     *
     * @var string yuilogexclude yuilogexclude
     */
    public $yuilogexclude;

    /**
     * Restrict which YUI logging statements are shown in the browser console.
     *  For details see the upstream documentation:
     *    http://yuilibrary.com/yui/docs/api/classes/config.html#property_logInclude
     *     $CFG->yuiloginclude = array(
     *     'moodle-core-dock-loader' => true,
     *     'moodle-course-categoryexpander' => true,
     *
     * @var string yuiloginclude YUI log include
     */
    public $yuiloginclude;

    /**
     * Set the minimum log level for YUI logging statements.
     *  For details see the upstream documentation:
     *    http://yuilibrary.com/yui/docs/api/classes/config.html#property_logLevel
     *  $CFG->yuiloglevel = 'debug';
     *
     * @var string yuiloglevel YUI log level
     */
    public $yuiloglevel;

    /**
     * List of YUI patched modules
     *
     * @var string yuipatchedmodules YUI patched modules
     */
    public $yuipatchedmodules;

    /**
     * If we need to patch a YUI modules between official YUI releases, the yuipatchlevel will need to be manually
     * 
     *
     * @var string yuipatchlevel YUI patch level
     */
    public $yuipatchlevel;

    /**
     * YUI caching may be sometimes improved by slasharguments:
     *      $CFG->yuislasharguments = 1;
     *
     * @var string yuislasharguments YUI slash arguments
     */
    public $yuislasharguments;
}
$CFG = new moodle_config();