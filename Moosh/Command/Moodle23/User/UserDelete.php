<?php
/**
 * Delete user by username.
 * moosh user-delete      [<username1> <username2> ...]
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\User;
use Moosh\MooshCommand;

class UserDelete extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('delete', 'user');
      
        $this->addArgument('username');
        $this->maxArguments = 255;

    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/user/lib.php';
        
         foreach ($this->arguments as $argument) {
             
             $this->expandOptionsManually(array($argument));
             $options = $this->expandedOptions;
             $user = new \stdClass();
                       
             try {
                $user = $DB->get_record('user', array('username' => $argument));
                user_delete_user($user);
            } catch (Exception $e) {
                print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
            }
             
         }
         
    }
}
