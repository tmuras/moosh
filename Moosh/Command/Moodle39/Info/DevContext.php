<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Info;

use Moosh\MooshCommand;

class DevContext extends MooshCommand
{
    public function __construct()
    {
        global $DB;
        parent::__construct('context', 'info');

        $this->addArgument('contextid');

        //$this->addOption('t|test', 'option with no value');
        //$this->addOption('o|option:', 'option with value and default', 'default');

    }

    public function execute()
    {
        global $CFG, $DB;
        require_once($CFG->libdir . '/accesslib.php');

        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed
        //$this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'

        $options = $this->expandedOptions;
        $contextid = $this->arguments[0];
        //$contextpath = $this->arguments[0];

        // Get all contexts under given one
        $context = \context::instance_by_id($contextid, MUST_EXIST);

        echo $this->context_info($contextid);
        echo "\n";
        //var_dump($context);
        //echo $context->get_context_name();
        return ;

        $sql = "SELECT * FROM {context} WHERE path LIKE '$contextpath/%'";
        $contexts = $DB->get_records_sql($sql);
        foreach ($contexts as $context) {
            /** @var \context $context */
            echo $this->context_info($context->id) . "\n";
            // What is in mdl_role_capabilities for this context
            $capabilities = $DB->get_records('role_capabilities',array('contextid'=>$context->id));
            foreach($capabilities as $cap) {
                echo $cap->roleid . ' ' . $cap->capability . ' ' .$cap->permission . " | ";
            }
            echo "\n";
        }
    }

    private function context_info($contextid)
    {
        global $DB;

        $context = \context::instance_by_id($contextid, MUST_EXIST);
        $out = '';
//var_dump($context);
        if (is_a($context, "context_module")) {
            /** @var \context_module $context */
            $out .= "$contextid, module: " . $context->get_context_name() . "\n";
            $coursemodule = $DB->get_record('course_modules', ['id'=>$context->instanceid]);
            $out .= "Course: {$coursemodule->module}\n";
            $out .= "Section: {$coursemodule->section}\n";
            $out .= $context->get_url() . "\n";
        } else {
            $out .= $context->get_context_name(); 
        }  


        return $out;
    }
}
