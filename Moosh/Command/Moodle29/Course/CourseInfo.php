<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2016 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle29\Course;

use Moosh\MooshCommand;
use Moosh\MoodleMetaData;

class CourseInfo extends MooshCommand
{
    private $contexts = array();

    private $contextbylevel = array();

    private $contextbydepth = array();

    private $contextbymodule = array();

    private $capabilityoverwrites = array();

    private $course;

    private $usersbyrole;

    private $rolesassigned = array();

    private $groupsnumber;
    private $groupsmin = NULL;
    private $groupsmax = 0;
    private $groupsavg = 0;

    private $modinfosize;

    private $sectionsnumber;
    private $sectionsvisible;
    private $sectionshidden;
    private $sectionsmin = NULL;
    private $sectionsmax = 0;
    private $sectionsavg = 0;

    private $gradesnumber;

    private $filesnumber;
    private $filesize;

    public function __construct()
    {
        global $DB;
        parent::__construct('info', 'course');

        $this->addArgument('courseid');

        $this->addOption('j|json', 'export as json array');
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
        $courseid = $this->arguments[0];
        $this->course = get_course($courseid);

        $coursecontext = \context_course::instance($courseid, MUST_EXIST);

        // Get # of contexts with break-down.
        $sql = "SELECT * FROM {context} WHERE path LIKE '{$coursecontext->path}/%'";
        $dbcontexts = $DB->get_records_sql($sql);
        foreach ($dbcontexts as $dbcontext) {
            $context = \context::instance_by_id($dbcontext->id, MUST_EXIST);
            $this->contexts[$dbcontext->id] = $context;

            $this->inc($this->contextbylevel, $context->contextlevel);

            /** @var \context $context */
            if ($this->verbose) {
                echo $context->get_context_name();
            }

            if (is_a($context, "context_module")) {
                $cm = $DB->get_record('course_modules', array('id' => $context->instanceid));
                $this->inc($this->contextbymodule, $cm->module);
//                var_dump($context); var_dump($cm); die();
            }
//            echo $this->context_info($context->id) . "\n";
            // What is in mdl_role_capabilities for this context
//            $capabilities = $DB->get_records('role_capabilities',array('contextid'=>$context->id));
//            foreach($capabilities as $cap) {
//                echo $cap->roleid . ' ' . $cap->capability . ' ' .$cap->permission . " | ";
//            }
        }
        ksort($this->contextbylevel);
        ksort($this->contextbymodule);

        // Get # of role permission overwrites for this course.
        foreach ($dbcontexts as $dbcontext) {
            $capabilities = $DB->get_records('role_capabilities', array('contextid' => $dbcontext->id));
            if ($capabilities) {
                $this->capabilityoverwrites[$dbcontext->id] = count($capabilities);
            }
        }

        // Locally assigned roles.
        foreach ($dbcontexts as $dbcontext) {
            $assignments = $DB->get_records('role_assignments', array('contextid' => $dbcontext->id));
            if ($assignments) {
                $this->rolesassigned[$dbcontext->id] = count($assignments);
            }
        }

        // Get users enrolled.
        $usersbyrole = $DB->get_records_sql("SELECT roleid, COUNT(*) AS c FROM {role_assignments} WHERE contextid = ?", array($coursecontext->id));
        foreach ($usersbyrole as $u) {
            $this->usersbyrole[$u->roleid] = $u->c;
        }

        // Get # of groups. Get min, max and avg number of users per group.
        $groups = $DB->get_records_sql("SELECT g.id, COUNT(m.id) AS c FROM {groups} g LEFT JOIN {groups_members} m ON g.id = m.groupid WHERE g.courseid = ? GROUP BY g.id", array($courseid));
        $this->groupsnumber = count($groups);
        $sum = 0;
        foreach ($groups as $group) {
            $sum += $group->c;
            if ($this->groupsmax < $group->c) {
                $this->groupsmax = $group->c;
            }
            if ($this->groupsmin === NULL || $this->groupsmin > $group->c) {
                $this->groupsmin = $group->c;
            }
        }
        $this->groupsavg = intval($sum / $this->groupsnumber);

        // Get size of modinfo course structure.
        $modinfo = get_fast_modinfo($this->course);
        $this->modinfosize = strlen(serialize($modinfo));

        // Get # of sections.
        $sections = $DB->get_records('course_sections',array('course'=>$courseid));
        $this->sectionsnumber = count($sections);
        $modstotal = 0;
        foreach($sections as $section) {
            $this->sectionsvisible += $section->visible;
            if(!$section->sequence) {
                $mods = 0;
            } else {
                $mods = substr_count($section->sequence, ',') + 1;
            }
            $modstotal += $mods;
            if($mods > $this->sectionsmax) {
                $this->sectionsmax = $mods;
            }
            if($this->sectionsmin === NULL || $mods < $this->sectionsmin) {
                $this->sectionsmin = $mods;
            }
        }
        $this->sectionsavg = intval($modstotal / $this->sectionsnumber);
        $this->sectionshidden = $this->sectionsnumber - $this->sectionsvisible;


        // Get # of grades.
        $this->gradesnumber = $DB->get_record_sql("SELECT COUNT(*) c FROM {grade_items} i JOIN {grade_grades} g ON i.id = g.itemid WHERE i.courseid = ?", array($courseid));
        $this->gradesnumber = $this->gradesnumber->c;

        // Get # of log entries.
        $this->logsnumber = $DB->get_record("logstore_standard_log",array('courseid' =>$courseid),'COUNT(*) c');
        $this->logsnumber = $this->logsnumber->c;

        // Get # and size of files.

        $results = $DB->get_records_sql("SELECT * FROM {context} WHERE path LIKE '" . $context->get_course_context()->path . "/%'");
        foreach ($results as $result) {
            $contexts[] = $result->id;
        }
        list($sql, $params) = $DB->get_in_or_equal($contexts);

        $files = $DB->get_record_sql("SELECT COUNT(*) c FROM {files} WHERE filename <> '.' AND contextid IN (SELECT id FROM {context} WHERE path LIKE '{$coursecontext->path}/%' )");
        $this->filesnumber = $files->c;

        $files = $DB->get_record_sql("SELECT SUM(filesize) s FROM {files} WHERE filename <> '.' AND contextid IN (SELECT id FROM {context} WHERE path LIKE '{$coursecontext->path}/%' )");
        $this->filesize = $files->s;

        $this->display();
    }

    private function inc(&$array, $key)
    {
        if (!isset($array[$key])) {
            $array[$key] = 0;
        }
        $array[$key]++;
    }

    private function display()
    {
        $meta = new MoodleMetaData();

        echo "Course ID: " . $this->course->id . "\n";
        echo "No of contexts: " . count($this->contexts) . "\n";
        echo "Context by level:\n";
        foreach ($this->contextbylevel as $level => $count) {
            echo "\t" . context_level_to_name($level) . " ($level):\t$count\n";
        }
        echo "Context by module:\n";
        foreach ($this->contextbymodule as $module => $count) {
            echo "\t" . $meta->moduleName($module) . " ($module):\t$count\n";
        }
        echo "Role capability overwrites by context:\n";
        foreach ($this->capabilityoverwrites as $contextid => $count) {
            echo "\t" . $this->contexts[$contextid]->get_context_name() . " ($contextid):\t$count\n";
        }
        echo "Locally assigned roles by context:\n";
        foreach ($this->rolesassigned as $contextid => $count) {
            echo "\t" . $this->contexts[$contextid]->get_context_name() . " ($contextid):\t$count\n";
        }
        echo "Users enrolled by role:\n";
        foreach ($this->usersbyrole as $roleid => $count) {
            echo "\t" . $meta->roleName($roleid) . " ($roleid):\t$count\n";
        }

        echo "Number of groups: {$this->groupsnumber}\n";
        echo "\tMin number of members in a group: {$this->groupsmin}\n";
        echo "\tMax number of members in a group: {$this->groupsmax}\n";
        echo "\tAvg number of members in a group: {$this->groupsavg}\n";

        echo "Course modinfo size: " . display_size($this->modinfosize) . "\n";

        echo "Sections: {$this->sectionsnumber} (visible:{$this->sectionsvisible}/hidden:{$this->sectionshidden})\n";
        echo "\tMin number of modules in a section: {$this->sectionsmin}\n";
        echo "\tMax number of modules in a section: {$this->sectionsmax}\n";
        echo "\tAvg number of modules in a section: {$this->sectionsavg}\n";

        echo "Number of grades: {$this->gradesnumber}\n";
        echo "Number of log entries: {$this->logsnumber}\n";
        echo "Number of files: {$this->filesnumber}\n";
        echo "Total file size: " . display_size($this->filesize);

    }
}
