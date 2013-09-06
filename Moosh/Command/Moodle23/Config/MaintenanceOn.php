<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Config;
use Moosh\MooshCommand;

class MaintenanceOn extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('on', 'maintenance');

        $this->addOption('m|message:', 'Maintenance message.');
    }

    public function execute()
    {
        $options = $this->expandedOptions;
        
        // if an optional message was passed, set that first
        if($options['message']){
            set_config('maintenance_message', $options['message']);
        }
        
        // then enable maintenance mode
        set_config('maintenance_enabled', 1);
        
        echo "Maintenance Mode Enabled\n";
    }
}
