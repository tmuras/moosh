<?php
/**
 * Delete user by username.
 * moosh user-delete      [<username1> <username2> ...]
 *
 * @copyright  2013 onwards Matteo Mosangini
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\User;
use Moosh\MooshCommand;

class UserDelete extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete', 'user');

        $this->addOption('i|id', 'use numerical id instead of username');
        $this->addArgument('user');
        $this->maxArguments = 255;

    }

    public function execute()
    {
        global $CFG, $DB;
        require_once $CFG->dirroot . '/user/lib.php';

        $options = $this->expandedOptions;
      
        foreach ($this->arguments as $argument) {
            
            try {
                if ($options['id']) {
                    $user = $DB->get_record('user', array('id' => $argument));
                } else {
                    $user = $DB->get_record('user', array('username' => $argument));
                }
            } catch (Exception $e) {
                print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
            }
           
            if ($user instanceof \stdClass) {
                user_delete_user($user);
            } else {
                print "User not found\n";
            }
        }
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_FULL_NO_ADMIN_CHECK;
    }
}
