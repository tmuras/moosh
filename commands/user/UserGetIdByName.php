<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class UserGetidbyname extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('getidbyname', 'user');

        $this->addOption('f|fname:','first name');
        $this->addOption('l|lname:','last name');

        $this->addArgument('username');
        $this->minArguments = 0;
        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/user/lib.php';
        
        $arguments = $this->arguments;
        $options = $this->expandedOptions;
       
        //print_r ($arguments);
        //print_r ($options);

        //get userid from firstname AND lastname
        //check, if fname and lname set
        if($options['fname'] and $options['lname']) {
            try {
                $user = $DB->get_record('user', array('firstname'=>$options['fname'], 'lastname'=>$options['lname']),'*', MUST_EXIST); 
                echo $user->id."\n";
            } catch (Exception $e) {
                print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
            }
        }
    
        //get userid from username
        foreach ($this->arguments as $argument) {
            $this->expandOptionsManually(array($argument));
            $options = $this->expandedOptions;
            
            try {
                $user = $DB->get_record('user', array('username' => $argument));
                echo $user->id."\n";
            } catch (Exception $e) {
                print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
            }
        }
        return(0);
    }
}
