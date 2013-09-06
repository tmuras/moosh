<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Config;
use Moosh\MooshCommand;

class MaintenanceOff extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('off', 'maintenance');
    }

    public function execute()
    {
        set_config('maintenance_enabled', 0);
        echo "Maintenance Mode Disabled\n";
    }
}
