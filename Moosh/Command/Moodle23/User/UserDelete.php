<?php
/**
 * moosh - Moodle Shell
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
                echo $user->id."\n";
            } catch (Exception $e) {
                print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
            }
             
         }
         
        
//some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed

        #$options = $this->expandedOptions;

        /* if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now";
        }
        */
    }
}
