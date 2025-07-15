<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @author     Lai Wei <lai.wei@enovation.ie>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle45\Course;
use moodle_exception;
use Moosh\MooshCommand;
use stdClass;
use backup_controller;

/**
 * Command to copy a course in Moodle.
 *
 * This command allows users to create a copy of an existing course with various options
 * for customization such as name, shortname, category, visibility, idnumber, startdate,
 * enddate, and user data copying.
 *
 * @package    Moosh\Command\Moodle45\Course
 */
class CourseCopy extends MooshCommand {
    /**
     * Constructor for the CourseCopy command.
     *
     * Initializes the command with options for course copying.
     * Options include name, shortname, category, visibility, idnumber, startdate, enddate, user data copying, and role IDs.
     */
    public function __construct() {
        parent::__construct('copy', 'course');

        $this->addOption('v|visible:', 'set visibility of the new course (1 for visible, 0 for hidden)');
        $this->addOption('i|idnumber:', 'idnumber of the new course');
        $this->addOption('s|startdate:', 'start date of the new course (timestamp)');
        $this->addOption('e|enddate:', 'end date of the new course (timestamp)');
        $this->addOption('u|userdata:', 'copy user data from the original course (1 for yes, 0 for no)');
        $this->addOption('r|role:', 'only users with the specified role IDs are copied (comma-separated role IDs)');

        $this->addArgument('id');
        $this->addArgument('name');
        $this->addArgument('shortname');
        $this->addArgument('category');
    }

    /**
     * Execute the course copy command.
     *
     * This method retrieves the course details, prepares the form data for copying,
     * and processes the copy using the copy_helper class.
     *
     * @throws moodle_exception If the course does not exist or if there are issues with the copy process.
     */
    public function execute() {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . '/backup/util/helper/copy_helper.class.php');
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

        // Check if the course id exists.
        $course = $DB->get_record('course', ['id' => $this->arguments[0]], '*', MUST_EXIST);
        $shortname = str_replace(' ', '_', $course->shortname);

        $options = $this->expandedOptions;

        // Prepare form data for course copy.
        $formdata = new stdClass();
        $formdata->courseid = $this->arguments[0];
        $formdata->fullname = $this->arguments[1];
        $formdata->shortname = $this->arguments[2];
        $formdata->category = $this->arguments[3];

        if ($DB->get_record('course', ['shortname' => $formdata->shortname])) {
            throw new moodle_exception('shortnametaken', 'error', '', $formdata->shortname);
        }

        if (!$DB->record_exists('course_categories', ['id' => $formdata->category])) {
            throw new moodle_exception('unknowcategory', 'error');
        }

        if ($options['visible']) {
            $formdata->visible = $options['visible'];
        } else {
            $formdata->visible = $course->visible;
        }

        if ($options['idnumber']) {
            $formdata->idnumber = $options['idnumber'];
        } else {
            $formdata->idnumber = $course->idnumber;
        }

        if ($options['startdate']) {
            if (is_int($options['startdate'])) {
                // Ensure startdate is a valid timestamp.
                $formdata->startdate = $options['startdate'];
            }
        }
        // Fallback to course start date if not provided.
        if (!isset($formdata->startdate) || !$formdata->startdate) {
            $formdata->startdate = $course->startdate;
        }

        if ($options['enddate']) {
            if (is_int($options['enddate']) && $options['enddate'] > $formdata->startdate) {
                // Ensure enddate is a valid timestamp.
                $formdata->enddate = $options['enddate'];
            }
        }
        if (!isset($formdata->enddate) || !$formdata->enddate) {
            // Fallback to course end date if not provided.
            $formdata->enddate = 0;
        }

        if ($options['userdata']) {
            $formdata->userdata = $options['userdata'];
        } else {
            $formdata->userdata = 0; // Default to not copying user data.
        }

        if ($options['role']) {
            $roles = explode(',', $options['role']);
            foreach ($roles as $roleid) {
                if (is_numeric($roleid) && $DB->record_exists('role', ['id' => $roleid])) {
                    $key = 'role_' . $roleid;
                    $formdata->$key = $roleid; // Store role ID in form data.
                }
            }
        }

        $copydata = \copy_helper::process_formdata($formdata);
        \copy_helper::create_copy($copydata);

        echo "Course copy ad-hoc task added to the queue.\n";

        exit(0);
    }

    /**
     * Get help text for the command arguments.
     *
     * This method provides detailed help information about the command's arguments,
     * including examples of how to use the command effectively.
     *
     * @return string Help text for the command arguments.
     */
    protected function getArgumentsHelp() {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= "This command creates a copy of an existing course with the specified options.\n";
        $help .= "You must provide the course ID, new course full name, new course shortname, and destination category ID in this particular order.\n\n";
        $help .= "e.g. to copy create a copy of course 2 in course category 1:\n";
        $help .= "    moosh -n course-copy 2 \"New Course Full Name\" \"new_course_shortname\" 1 \n";
        $help .= "or with all options:\n";
        $help .= "    moosh -n course-copy --visible=1 --idnumber=\"new_idnumber\" --startdate=1700000000 --enddate=1710000000 --userdata=1 --role=3,4 2 \"New Course Full Name\" \"new_course_shortname\" 1 \n";
        return $help;
    }
}
