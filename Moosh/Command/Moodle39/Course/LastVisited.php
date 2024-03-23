<?php

namespace Moosh\Command\Moodle39\Course;

use Moosh\MooshCommand;

class LastVisited extends MooshCommand {
    public function __construct() {
        global $DB;
        parent::__construct('last-visited', 'course');

        $this->addArgument('courseid');

        //$this->addOption('o|option:', 'option with value and default', 'default');

    }

    public function execute() {
        global $CFG, $DB;
        require_once($CFG->libdir . '/accesslib.php');

        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed
        //$this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'

        $options = $this->expandedOptions;
        $courseid = $this->arguments[0];

        // Just to check if course exists.
        $coursecontext = \context_course::instance($courseid, MUST_EXIST);

        $sql = "SELECT timeaccess FROM {user_lastaccess} WHERE courseid = ? ORDER BY timeaccess DESC LIMIT 1";
        $lastaccess = $DB->get_record_sql($sql, array($courseid));

        if (!$lastaccess) {
            return;
        }
        // How many hours have passed from $lastaccess to now.
        $hours = (int)((time() - $lastaccess->timeaccess) / 3600);
        echo "$hours\n";
    }
}