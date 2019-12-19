<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle19\Course;
use Moosh\MooshCommand;

class CourseBackup extends MooshCommand {
    
    public function __construct()
    {
        parent::__construct('backup', 'course');

        $this->addOption('f|filename:', 'path to filename to save the course backup');

        $this->addArgument('id');
    }
    
    public function execute()
    {
        global $CFG;
        require_once($CFG->dirroot . '/backup/backup_scheduled.php');
        require_once($CFG->dirroot . '/backup/backuplib.php');
        require_once($CFG->dirroot . '/backup/lib.php');
        
        error_reporting(0);
        $CFG->debugdisplay = 0;
        
        $status = true;

        $emailpending = false;
        
        $config = backup_get_config();

        //Check for required functions...
        if(!function_exists('utf8_encode')) {
            echo "        ERROR: You need to add XML support to your PHP installation!";
            exit(1);
        }

        //Get now
        $now = time();

        //First of all, we have to see if the scheduled is active and detect
        //that there isn't another cron running
        echo "    Checking backup status...\n";

        //check if course id exists

        if ($course = get_record('course', 'id', $this->arguments[0])) {
            $this->setDefaultParams();
            $backup_config = backup_get_config();
            backup_set_config("backup_sche_running","1");

            //Now we get the main admin user (we'll use his timezone, mail...)
            $admin = get_admin();
            if (!$admin) {
                $status = false;
            }

            if ($status) {
                echo "    Checking course";
                //Now process existing course
                //For each course, we check (insert, update) the backup_course table
                //with needed data
                if ($status) {
                    echo " '$course->fullname'\n";
                    //We check if the course exists in backup_course
                    if (get_record("backup_courses","courseid",$course->id)) {
                        delete_records('backup_courses', 'courseid', "$course->id");
                    }
                    $temp_backup_course->courseid = $course->id;
                    $newid = insert_record("backup_courses",$temp_backup_course);
                    //And get it from db
                    $backup_course = get_record("backup_courses","id",$newid);
                    //If it doesn't exist now, error
                    if (!$backup_course) {
                        echo "            ERROR (in backup_courses detection)";
                        $status = false;
                    }

                    //Now we backup every non-skipped course
                    //We have to send a email because we have included at least one backup
                    $emailpending = true;
                    //Only make the backup if laststatus isn't 2-UNFINISHED (uncontrolled error)
                    if ($backup_course->laststatus != 2) {
                        //Set laststarttime
                        $starttime = time();
                        set_field("backup_courses","laststarttime",$starttime,"courseid",$backup_course->courseid);
                        //Set course status to unfinished, the process will reset it
                        set_field("backup_courses","laststatus","2","courseid",$backup_course->courseid);
                        //Launch backup
                        $course_status = schedule_backup_launch_backup($course,$starttime);
                        //Set lastendtime
                        set_field("backup_courses","lastendtime",time(),"courseid",$backup_course->courseid);
                        //Set laststatus
                        if ($course_status) {
                            set_field("backup_courses","laststatus","1","courseid",$backup_course->courseid);
                        } else {
                            set_field("backup_courses","laststatus","0","courseid",$backup_course->courseid);
                        }
                    }

                    //Save it to db
                    set_field("backup_courses","nextstarttime",0,"courseid",$backup_course->courseid);
                }
            }

            //Delete old logs
            if (!empty($CFG->loglifetime)) {
                mtrace("    Deleting old logs");
                $loglifetime = $now - ($CFG->loglifetime * 86400);
                delete_records_select("backup_log", "laststarttime < '$loglifetime' AND courseid = {$course->id}");
            }

            //Everything is finished stop backup_sche_running
            backup_set_config("backup_sche_running","0");
            $this->resetParams($config);
        }
    }
    
    private function setDefaultParams() {
        $options = $this->expandedOptions;
        if (!$options['filename']) {
            $options['filename'] = $this->cwd;
        } elseif ($options['filename'][0] != '/') {
            $options['filename'] = $this->cwd .'/' .$options['filename'];
        }
       
        backup_set_config("backup_sche_modules", "1");
        backup_set_config("backup_sche_withuserdata", "0");
        backup_set_config("backup_sche_metacourse", "1");
        backup_set_config("backup_sche_users", "999");
        backup_set_config("backup_sche_logs", "0");
        backup_set_config("backup_sche_userfiles", "0");
        backup_set_config("backup_sche_coursefiles", "1");
        backup_set_config("backup_sche_sitefiles", "1");
        backup_set_config("backup_sche_gradebook_history", "0");
        backup_set_config("backup_sche_messages", "0");
        backup_set_config("backup_sche_blogs", "0");
        backup_set_config("backup_sche_keep", "1");
        backup_set_config("backup_sche_active", "0");
        backup_set_config("backup_sche_weekdays", "0000000");
        backup_set_config("backup_sche_hour", "0");
        backup_set_config("backup_sche_minute", "0");
        backup_set_config("backup_sche_destination", $options['filename']);
    }

    private function resetParams($config) {
        foreach ($config as $name => $value) {
            backup_set_config($name, $value);
        }
    }

}
