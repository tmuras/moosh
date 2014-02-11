<?php
/**
 * Returns the userid of users.
 * moosh user-getidbyname
 *      -f --fname
 *      -l --lname
 *      [<username1> <username2> ...]
 *
 * @copyright  2013 onwards Mirko Otto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\User;
use Moosh\MooshCommand;

class UserGetidbyname extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('getidbyname', 'user');

        $this->addOption('f|fname:','first name');
        $this->addOption('l|lname:','last name');
    }

    public function execute()
    {
        echo "This command has been deprecated. Use user-list instead. \n";
        return(0);
    }
}
