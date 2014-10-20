<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Kacper Golewski <k.golewski@gmail.com>
 */


namespace Moosh\Command\Moodle25\User;
use Moosh\MooshCommand;

class UserMod extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('mod', 'user');

        $this->addOption('g|global:', 'user name to be set as super user.', false);
    }

    public function execute()
    {   
        global $CFG;
        $options = $this->expandedOptions;
        if ($username = $options['global']) {
            $user = get_user_by_name($username);
        }

        if ($user) {
            $user_id = $user->id;
        } else {
            cli_error("No user found.");
        }

        if ($options['global']) {
            foreach(explode(',', $CFG->siteadmins) as $admin) {
                $admin = (int)$admin;
                if ($admin) {
                    $admins[$admin] = $admin;
                }
            }

            if (isset($admins[$user_id])) {
                unset($admins[$user_id]);
            }
            array_unshift($admins, $user_id);
            set_config('siteadmins', implode(',', $admins));
            purge_all_caches();

            echo "User $user->username is set as global super user.\n";
        }


        
    }
}
