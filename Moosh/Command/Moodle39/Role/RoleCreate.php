<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Role;
use Moosh\MooshCommand;

class RoleCreate extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('create', 'role');

        $this->addOption('n|name:');
        $this->addOption('d|description:');
        $this->addOption('a|archetype:');
        $this->addOption('c|context:');
        $this->addArgument('shortname');
    }

    public function execute()
    {
        global $CFG, $DB;
        require_once($CFG->libdir . DIRECTORY_SEPARATOR . "testing". DIRECTORY_SEPARATOR . "generator" . DIRECTORY_SEPARATOR . "data_generator.php");
        $generator = new \testing_data_generator();
        $options = $this->expandedOptions;
        $arguments = $this->arguments;
        $list_of_contexts=['system','user','category','course','activity','block'];
        $array=(explode(',',$options['context']));
        $context=[];
        foreach($array as $value)
        {
            if(in_array($value,$list_of_contexts))
            {
                if(array_keys($list_of_contexts,$value)[0]==0)array_push($context,10);
                if(array_keys($list_of_contexts,$value)[0]==1)array_push($context,30);
                if(array_keys($list_of_contexts,$value)[0]==2)array_push($context,40);
                if(array_keys($list_of_contexts,$value)[0]==3)array_push($context,50);
                if(array_keys($list_of_contexts,$value)[0]==4)array_push($context,70);
                if(array_keys($list_of_contexts,$value)[0]==5)array_push($context,80);
            }
        }
        //don't create if already exists
        $role = $DB->get_record('role', array('shortname' => $arguments[0]));
        if ($role) {
            echo "Role '" . $arguments[0] . "' already exists!\n";
            exit(0);
        }
        if($options['context'] && $options['archetype'])
        {
            #echo ("You can use only one of this switch (-c, -a)");
            cli_error('You can use only one of this switch (-c, -a)');
        }

        $options['shortname'] = $arguments[0];
        print_r($options);
        $newroleid = $generator->create_role($options);
        if($options['context']) {
            set_role_contextlevels($newroleid, $context);
        }
        else if(!$options['archetype']){
            set_role_contextlevels($newroleid, [1=>10]);
        }
        echo "$newroleid\n";
    }
}
