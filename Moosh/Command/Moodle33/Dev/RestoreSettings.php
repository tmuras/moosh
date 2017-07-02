<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle33\Dev;
use Moosh\MooshCommand;

class RandomLabel extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('settings', 'restore');

        //$this->addArgument();

        //$this->addOption('i|include-text:', 'make sure this piece of text is included in the random content', NULL);

    }


    public function execute()
    {
        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . "/backup/util/includes/backup_includes.php");
        require_once($CFG->dirroot . "/backup/util/includes/restore_includes.php");


        $rc = new restore_controller('/tmp', 1, backup::INTERACTIVE_NO,
                backup::MODE_GENERAL, $USER->id, 0);
        $plan = $rc->get_plan();
    }
}


