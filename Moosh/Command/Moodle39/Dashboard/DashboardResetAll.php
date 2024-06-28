<?php

/**
 * moosh - Moodle Shell
 *
 * 2021 unistra {@link http://unistra.fr}
 * @author 2021 Céline Perves <cperves@unistra.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Dashboard;
use Moosh\MooshCommand;

class DashboardResetAll extends MooshCommand
{
    public function __construct() {
        parent::__construct('reset-all', 'dashboard');
    }
    public function execute()
    {
        global $CFG;
        require_once($CFG->dirroot.'/my/lib.php');
        my_reset_page_for_all_users(MY_PAGE_PRIVATE, 'my-index');
    }

    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "Reset all dashboards";

        return $help;
    }
}