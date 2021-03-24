<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Course;
use Moosh\MooshCommand;

class GradebookImport extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('import', 'gradebook');

        $this->addArgument('gradebook.csv');
        $this->addArgument('course_id');

        $this->addOption('t|test', 'only test and show summary of actions, don\'t do any writes');
        $this->addOption('u|map-users-by:', 'what to use for map users from CSV. Possible values: email (default), idnumber (for ID number profile field)', 'email');
        $this->addOption('c|course-idnumber', 'instead of course id take mdl_course.idnumber as argument for selecting the course');

    }

    public function execute()
    {
        global $CFG, $DB, $USER;

        require_once $CFG->dirroot . '/course/lib.php';
        require_once($CFG->libdir . '/gradelib.php');
        require_once($CFG->dirroot . '/grade/lib.php');
        require_once($CFG->dirroot . '/grade/import/lib.php');
        require_once($CFG->libdir . '/csvlib.class.php');

        $options = $this->expandedOptions;
        
        $USER = $this->user;

        $text = file_get_contents($this->arguments[0]);
        if (!$text) {
            cli_error("No data in file '{$this->arguments[0]}''");
        }

        if ($options['course-idnumber']) {
            $course = $DB->get_record('course', array('idnumber' => $this->arguments[1]), '*', MUST_EXIST);
        } else {
            $course = $DB->get_record('course', array('id' => $this->arguments[1]), '*', MUST_EXIST);
        }

        $iid = \csv_import_reader::get_new_iid('moosh-gradebook');
        $csvimport = new \csv_import_reader($iid, 'moosh-gradebook');

        $csvimport->load_csv_content($text, 'utf-8', 'comma');

        $header = $csvimport->get_columns();

        //use "Email address" or "ID number" for mapping users
        if ($options['map-users-by'] == 'idnumber') {
            $usermap = array_search('ID number', $header);

            if ($usermap === false) {
                cli_error("Didn't find column called 'ID number' for mapping users");
            }
        } elseif ($options['map-users-by'] == 'email') {
            $usermap = array_search('Email address', $header);

            if ($usermap === false) {
                cli_error("Didn't find column called 'Email address' for mapping users");
            }
        } else {
            cli_error(' Wrong map-users-by value');
        }
        $map = array();

        //Try to automatically map columns in CSV file onto activities with the same name
        $grade_items = \grade_item::fetch_all(array('courseid' => $course->id));
        foreach ($grade_items as $grade_item) {
            // Skip course type and category type.
            if ($grade_item->itemtype == 'course' || $grade_item->itemtype == 'category') {
                continue;
            }

            $displaystring = null;
            if (!empty($grade_item->itemmodule)) {
                $displaystring = get_string('modulename', $grade_item->itemmodule) . ': ' . $grade_item->get_name();
            } else {
                $displaystring = $grade_item->get_name();
            }
            //echo $displaystring . "\n";

            $pos = array_search($displaystring, $header);
            if ($pos !== false) {
                $map[$pos] = $grade_item->id;
                echo "CSV column '{$header[$pos]}' will be mapped to grade item '$displaystring'\n";
            } else {
                echo "No mapping for gradebook item '$displaystring'\n";
            }

        }


        //iterate over all CSV records
        $csvimport->init();

        $newgrades = array();
        while ($line = $csvimport->next()) {
            //first find user
            if ($options['map-users-by'] == 'idnumber') {
                if (!$user = $DB->get_record('user', array('idnumber' => $line[$usermap]))) {
                    cli_error("Couldn't find user with idnumber '{$line[$usermap]}'");
                }
            } elseif ($options['map-users-by'] == 'email') {
                if (!$user = $DB->get_record('user', array('email' => $line[$usermap]))) {
                    cli_error("Couldn't find user with email '{$line[$usermap]}'");
                }
            }

            echo "Processing user {$user->email} ({$user->id},{$user->idnumber})\n";
            foreach ($map as $k => $v) {
                $gradeitem = $grade_items[$v];
                $value = $line[$k];

                $newgrade = new \stdClass();
                $newgrade->itemid = $gradeitem->id;

                //handle scales
                if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
                    $scale = $gradeitem->load_scale();
                    $scales = explode(',', $scale->scale);
                    $scales = array_map('trim', $scales); //hack - trim whitespace around scale options
                    array_unshift($scales, '-'); // scales start at key 1
                    $key = array_search($value, $scales);
                    if ($key === false) {
                        echo "\tThe correct scale value '$value' for item '{$gradeitem->get_name()}' could not be found.\n";
                    } else {
                        echo "\tMapped value '$value' to '$key' as scale is used for '{$gradeitem->get_name()}'\n";
                        $value = $key;
                    }
                } else {
                    if ($value === '' or $value == '-') {
                        $value = null; // no grade
                    }
                }

                echo "\tGrade for '{$gradeitem->get_name()}', type {$gradeitem->gradetype} will be set to '$value'\n";
                $newgrade->finalgrade = $value;
                $newgrade->userid = $user->id;
                $newgrade->importer = $USER->id;
                $newgrades[] = $newgrade;
            }
        }

        if ($options['test']) {
            echo "Test mode - exiting without performing import.\n";
        }

        //we will use safer method of importing useing temporary table
        $importcode = get_new_importcode();
        foreach ($newgrades as $newgrade) {
            $newgrade->importcode = $importcode;
            $DB->insert_record('grade_import_values', $newgrade);
        }

        grade_import_commit($course->id, $importcode);
    }
}

