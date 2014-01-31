<?php
/**
 * moosh - Moodle Shell
 *
 * List users.
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\User;
use Moosh\MooshCommand;
use context_system;

class UserList extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('list', 'user');

        $this->addOption('n|limit:', 'display max n users');
        $this->addOption('i|id', 'display id only column');
        $this->addOption('s|sort:', 'sort by (id, username, email or idnumber)');
        $this->addOption('d|descending', 'sort in descending order');

        $this->addArgument('sql expression');
    }

    public function execute()
    {
        global $DB;

        $options = $this->expandedOptions;

        $users = ("SELECT * FROM {user} WHERE " . $this->arguments[0]);

        $sort = "id";
        if($options['sort'] == 'id' || $options['sort'] == 'username' || $options['sort'] == 'email' || $options['sort'] == 'idnumber'){
            $sort = $options['sort'];
        }

        $dir = 'ASC';
        if($options['descending']){
            $dir = 'DESC';
        }

        $users .= " ORDER BY $sort $dir";
 
        if($options['limit'] && preg_match('/^\d+$/', $options['limit'])){
            $users .= " LIMIT " . $options['limit'];
        }

        $users = $DB->get_records_sql($users);

        foreach ($users as $user) {
            if($options['id']) {
                echo "$user->id \n";
                continue;
            }
            $to_print = $user->username . " ({$user->id}), " . $user->email . ", ";
            echo $to_print . "\n";
        }
    }
}
