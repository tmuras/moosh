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

        $this->addOption('f|firstname:', 'firstname');
        $this->addOption('l|lastname:', 'lastname');

        $this->addArgument('args');
        $this->minArguments = 0;
        $this->maxArguments = 255;
    }

    public function execute()
    {
        global $CFG, $DB;

        require_once $CFG->dirroot . '/user/lib.php';
        //unset($CFG->passwordpolicy);

        $arguments = $this->arguments;
        $options = $this->expandedOptions;

        //echo $options['firstname'], $options['lastname'];

        if($options['firstname'] and $options['lastname']){
            $user = $DB->get_record('user', array('firstname'=>$options['firstname'], 'lastname'=>$options['lastname']),'*', MUST_EXIST); 
        }
        
        if(!$user) {
            return(-1);
        }

        //print_r($user);

        //print_r($arguments);
        //print_r($options);

        echo $user->id;
        //return($user->id);
        
        return(0);

    }
}
