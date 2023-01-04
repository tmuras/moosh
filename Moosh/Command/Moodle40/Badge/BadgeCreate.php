<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle40\Badge;

use Moosh\MooshCommand;

class BadgeCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'badge');

        $this->addOption('n|name:','badge name');
        $this->addOption('d|description:', 'badge description');
        $this->addOption('p|path:', 'img path');
        $this->addOption('t|type:', 'badge type', 1);

        $this->addArgument('course');

    }

    /**
     * Execute the command
     *
     * @return void
     * @throws \coding_exception
     * @throws \invalid_dataroot_permissions
     */
    public function execute()
    {
        global $CFG, $DB, $USER;
        // Some variables you may want to use
        //  $this->cwd - the directory where moosh command was executed
        //  $this->mooshDir - moosh installation directory
        //  $this->expandedOptions - commandline provided options, merged with defaults
        //  $this->topDir - top Moodle directory
        //  $this->arguments[0] - first argument passed
        //  $this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'
        //  $this->verbose - if set to true, then "moosh -v" was run - add more verbose / debug information

        require_once($CFG->libdir.'/badgeslib.php');
        require_once($CFG->dirroot.'/badges/classes/badge.php');

        $options = $this->expandedOptions;

        $badgename = $options['name'];
        $badgedesc =  $options['description'];
        $fullpath = $this->checkPathArg($options['path']);

        $badgetype = $options['type'];
        $courseid = $this->arguments[0];

        $parts = explode('.', $fullpath);
        $extention = end($parts);

        $now = time();

        $fordb = new \stdClass();

        $fordb->name = $badgename;
        $fordb->version = '';
        $fordb->language = 'en';
        $fordb->description = $badgedesc;
        $fordb->imageauthorname = '';
        $fordb->imageauthoremail = '';
        $fordb->imageauthorurl = '';
        $fordb->imagecaption = '';
        $fordb->timecreated = $now;
        $fordb->timemodified = $now;
        $fordb->usercreated = $USER->id;
        $fordb->usermodified = $USER->id;
        $url = parse_url($CFG->wwwroot);
        $fordb->issuerurl = $url['scheme'] . '://' . $url['host'];
        $fordb->issuername = $CFG->badges_defaultissuername;
        $fordb->issuercontact = $CFG->badges_defaultissuercontact;

        $fordb->expiredate = null;
        $fordb->expireperiod = null;
        $fordb->type = $badgetype;
        $fordb->courseid = ($badgetype == 2) ? $courseid : null;
        $fordb->messagesubject = get_string('messagesubject', 'badges');
        $fordb->message = get_string('messagebody', 'badges',
            \html_writer::link($CFG->wwwroot . '/badges/mybadges.php', get_string('managebadges', 'badges')));
        $fordb->attachment = 1;
        $fordb->notification = 0;
        $fordb->status = 0;

        $newid = $DB->insert_record('badge', $fordb, true);

        make_writable_directory($CFG->dataroot.'/tempimgs', true);
        $newfilepath = $CFG->dataroot.'/tempimgs/badge' . $this->generaterandomstring(15) . '.' . $extention;
        copy($fullpath, $newfilepath);

        $newbadge = new \badge($newid);
        badges_process_badge_image($newbadge, $newfilepath);
    }

    /**
     * Generate random string with given string length
     *
     * @param $length
     * @return string
     */
    private function generaterandomstring($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characterslength = strlen($characters);
        $randomstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randomstring .= $characters[rand(0, $characterslength - 1)];
        }
        return $randomstring;
    }
}
