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
        $this->addOption('i|idnumber', 'display idnumber column');
        $this->addOption('s|sort:', 'sort by (id, username, email or idnumber)');
        $this->addOption('d|descending', 'sort in descending order');
    }

    public function execute()
    {
        global $DB;

        $options = $this->expandedOptions;

        $sort = "id";
        if($options['sort'] == 'id' || $options['sort'] == 'username' || $options['sort'] == 'email' || $options['sort'] == 'idnumber'){
            $sort = $options['sort'];
        }
        $dir = 'ASC';
        if($options['descending']){
            $dir = 'DESC';
        }

        $select_sql = "SELECT * FROM {user} WHERE confirmed = 1 AND deleted = 0 ORDER BY $sort $dir";
        if($options['limit'] && preg_match('/^\d+$/', $options['limit'])){
            $select_sql .= " LIMIT " . $options['limit'];
        }
        $users = $DB->get_records_sql($select_sql);

        foreach($users as $user) {
            $to_print = $user->username . " ({$user->id}), " . $user->email . ", ";
            if($options['idnumber']){
                $to_print .= $user->idnumber . ", ";
            }
            echo $to_print . "\n";
        }
    }
}
