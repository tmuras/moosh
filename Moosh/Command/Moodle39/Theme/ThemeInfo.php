<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Theme;
use Moosh\MooshCommand;

class ThemeInfo extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('info', 'theme');

        //$this->addArgument('name');

        //$this->addOption('t|test', 'option with no value');
        //$this->addOption('o|option:', 'option with value and default', 'default');

    }

    public function execute()
    {
        global $DB, $CFG;
        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed

        //$options = $this->expandedOptions;

        //get curse themes
        if ($CFG->allowcoursethemes) {
            echo "Course theme overrides enabled\n";
        } else {
            echo "Course theme overrides disabled\n";
        }
        $results = $DB->get_records_sql("SELECT theme, COUNT(*) AS n  FROM {course} WHERE theme <> '' GROUP BY theme");

        if ($results) {
            echo "name\ttimes used\n";
            foreach ($results as $result) {
                echo $result->theme . "\t" . $result->n . "\n";
            }
        } else {
            echo "No theme overrides on the course level\n";
        }

        echo "\n";
        //get category themes
        if ($CFG->allowcategorythemes) {
            echo "Category theme overrides enabled\n";
        } else {
            echo "Category theme overrides disabled\n";
        }
        $results = $DB->get_records_sql("SELECT theme, COUNT(*) AS n  FROM {course_categories} WHERE theme <> '' GROUP BY theme");
        if ($results) {
            echo "name\ttimes used\n";
            foreach ($results as $result) {
                echo $result->theme . "\t" . $result->n . "\n";
            }
        } else {
            echo "No theme overrides on the category level\n";
        }

        echo "\n";

        //get user themes
        if ($CFG->allowcategorythemes) {
            echo "User theme overrides enabled\n";
        } else {
            echo "User theme overrides disabled\n";
        }
        $results = $DB->get_records_sql("SELECT theme, COUNT(*) AS n FROM {user} WHERE theme <> '' GROUP BY theme");
        if ($results) {
            echo "name\ttimes used\n";
            foreach ($results as $result) {
                echo $result->theme . "\t" . $result->n . "\n";
            }
        } else {
            echo "No theme overrides on the user level\n";
        }


        //site themes
        $devices = \core_useragent::get_device_type_list();
        echo "\nSite themes:\n";
        foreach ($devices as $device) {
            echo "$device: ";
            $themename = \core_useragent::get_device_type_cfg_var_name($device);
            if (isset($CFG->$themename)) {
                echo $CFG->$themename . "\n";
            } else {
                echo "<not set>\n";
            }
        }

    }
}
