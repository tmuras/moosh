<?php
/**
 * Swap user role from student to waitlist in all courses in one category.
 * moosh course-swapuserrole
 *      -i --id
 *      -r --enrole
 *      coid
 *
 * @copyright  2013 onwards Mirko Otto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class CourseSwapUserRole extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('swapuserrole', 'course');
        $this->addOption('i|cat-id', 'use numeric category-ID instead of category name');
        $this->addOption('r|enrole', 'enrole method name');

        //possible other options
        //duration
        //startdate
        //recovergrades

        //$this->addArgument('courseid');
        //$this->addArgument('enrol');
        $this->maxArguments = 255;
    }

    public function execute()
    {

        //echo "asdf" . "\n";
        global $CFG, $DB, $PAGE, $USER;

        require_once($CFG->dirroot . '/enrol/externallib.php');
        require_once($CFG->dirroot . '/enrol/locallib.php');
        //require_once($CFG->dirroot . '/group/lib.php');

        $options = $this->expandedOptions;
        $arguments = $this->arguments;


        $courses = $DB->get_records('course', array('category' => $arguments[0]));
        if($courses) {
            foreach($courses as $cour) {
                echo "category: " . $cour->category . " , courseid: " . $cour->id . "\n";

                $instances = $DB->get_records('enrol', array('enrol' => 'waitlist', 'courseid' => $cour->id));
                //$instances = $DB->get_records('enrol', array('enrol' => 'waitlist'));
                if($instances) {
                    foreach($instances as $instance) {
                        
                        $context = context_course::instance($instance->courseid);

                        // get all waitlist role assignments
                        $role = new stdClass;
                        //$role->id = $instance->customchar1;
                        $role->id = $instance->roleid;
                        $role_assigns = get_users_from_role_on_context($role, $context);
                        
                        $count = count($role_assigns);
                        echo "count role_assigns: " . $count . "\n";
                        
                        if($role_assigns) {
                            $uids = array();
                            foreach($role_assigns as $ra) {
                                $uids[] = $ra->userid;                        
                            }
                            //get those waitlist enrolments and order them by timecreated (first come, first served)
                            list($qrypart, $params) = $DB->get_in_or_equal($uids);
                            $sql = 'SELECT
                                        *
                                    FROM
                                        {user_enrolments} ue
                                    WHERE
                                        userid '.$qrypart.'
                                    AND
                                        enrolid = ?
                                    ORDER BY
                                        timecreated ASC';
                            $params[] = $instance->id;
                            $waitlist_enrolments = $DB->get_records_sql($sql, $params);

                            foreach($waitlist_enrolments as $en) {
                                echo "userid: " . $en->userid . " , contextid: " . $context->id . "\n";
                                role_unassign($instance->roleid, $en->userid, $context->id);
                                role_assign($instance->customchar1, $en->userid, $context->id);
                            }
                        }
                    }
                }
            }
        }
    }
} 
