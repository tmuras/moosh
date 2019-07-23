<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Auth;
use Moosh\MooshCommand;

class AuthManage extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('manage', 'auth');
        $this->addArgument('action');
        $this->addArgument('pluginname');
    }

    public function execute()
    {
        global $CFG;

        $action = $this->arguments[0];
        $pluginname = $this->arguments[1];

        // Does the authentication module exist?
        if(!exists_auth_plugin($pluginname)) {
            print_error('pluginnotinstalled', 'auth', '', $pluginname);
        }   

        // Get enabled plugins.
        $authsenabled = get_enabled_auth_plugins(true);
        if (empty($CFG->auth)) {
            $authsenabled = array();
        } else {
            $authsenabled = explode(',', $CFG->auth);
        }   

        switch($action) {
            case 'disable':
                $key = array_search($pluginname, $authsenabled);
                if ($key !== false) {
                    unset($authsenabled[$key]);
                    set_config('auth', implode(',', $authsenabled));
                }
                break;
            case 'down':
                $key = array_search($pluginname, $authsenabled);
                if ($key !== false && $key < (count($authsenabled) - 1)) {
                    $fsave = $authsenabled[$key];
                    $authsenabled[$key] = $authsenabled[$key + 1]; 
                    $authsenabled[$key + 1] = $fsave;
                    set_config('auth', implode(',', $authsenabled));
                }
            case 'enable':
                if(!in_array($pluginname, $authsenabled)) {
                    $authsenabled[] = $pluginname;
                    $authsenabled = array_unique($authsenabled);
                    set_config('auth', implode(',', $authsenabled));
                }   
                break;
            case 'up':
                $key = array_search($pluginname, $authsenabled);
                if ($key !== false && $key >= 1) {
                    $fsave = $authsenabled[$key];
                    $authsenabled[$key] = $authsenabled[$key - 1]; 
                    $authsenabled[$key - 1] = $fsave;
                    set_config('auth', implode(',', $authsenabled));
                }
                break;
        }
        echo "Auth modules enabled: " . implode(',', $authsenabled) . "\n";

    }
}

