<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Auth;
use Moosh\MooshCommand;

class AuthList extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('list', 'auth');

        $this->addOption('a|all', 'display all auth plugins, by default lists enabled only');
    }

    public function execute()
    {
        if ($this->expandedOptions['all']) {
            echo "Not implemented yet.\n";
        } else {
            $this->list_enabled_auth_plugins();
        }
    }

    private function list_enabled_auth_plugins()
    {
        $plugins = get_enabled_auth_plugins();

        echo "\nList of enabled auth plugins:\n\n";

        for ($i=0; $i < count($plugins); $i++) { 
            echo $i+1 . ". ". $plugins[$i] . "\n";
        }
    }
}