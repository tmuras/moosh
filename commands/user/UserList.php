<?php
/**
 * moosh - Moodle Shell
 *
 * List users.
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class UserList extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('list', 'user');

        $this->addOption('n|limit:', 'display max n users');

        //TODO
        //$this->addOption('s|sort:', 'sort by');
    }

    public function execute()
    {
        global $CFG, $USER;

        $USER->country = "PL";
        require_once($CFG->libdir.'/adminlib.php');
        require_once($CFG->dirroot.'/user/filters/lib.php');

        // Carry on with the user listing
        $context = context_system::instance();
        $options = $this->expandedOptions;

        $extrasql = '';
        $params = array();
        $sort = "id";
        $dir = 'ASC';
        $start = 0;

        $users = get_users_listing($sort, $dir, $start, $options['limit'], '', '', '',
            $extrasql, $params, $context);

        foreach($users as $user) {
            echo $user->username . " ({$user->id}), " . $user->email . ", " . "\n";
        }
    }
}
