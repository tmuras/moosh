<?php
/**
 * This command show currently online user list.
 * In a contrast to TOP command this command uses Fetcher API and queries user table
 * instead of standard log store.
 *
 * @example `moosh user-online`
 *
 * @copyright  2012 onwards Tomasz Muras
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\User;

use block_online_users\fetcher;
use Moosh\MooshCommand;

class UserOnline extends MooshCommand {
    public function __construct() {
        parent::__construct('online', 'user');
        $this->addOption('t|time:', 'Show users online in last N seconds. Default 15 sec.', 15);
        $this->addOption('l|limit:', 'Show maximum number of users. If empty all users are fetched.', 0);
        $this->addOption('e|hideheader', 'Print header with table column names.', false);
    }

    public function execute() {
        $timetoshowusers = (int) $this->expandedOptions['time'];
        $now = time();

        $onlineusers = new fetcher(null, $now, $timetoshowusers, \context_system::instance());

        $usercount = $onlineusers->count_users();
        $userlimit = $this->expandedOptions['limit'];
        $users = $onlineusers->get_users($userlimit);

        if (!$this->expandedOptions['hideheader']) {
            printf(sprintf("Maximum number of users in last %d sec: %d\n", $timetoshowusers, $usercount));
            printf(sprintf("Last check: %s\n\n", date('Y-m-h H:i:s', $now)));

            printf(" %-7s | %-15s | %-25s | %-30s | %-20s\n",
                'ID',
                'USERNAME',
                'NAME',
                'EMAIL',
                'LASTACCESS'
            );
        }

        foreach ($users as $user) {
            printf(" %-7d | %-15s | %-25s | %-30s | %-20s\n",
                $user->id,
                $user->username,
                $user->lastname . ' ' . $user->firstname,
                $user->email,
                isset($user->lastaccess) ? date('Y-m-s H:i:s', $user->lastaccess) : '-'
            );
        }

        echo PHP_EOL;
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_FULL_NO_ADMIN_CHECK;
    }
}
