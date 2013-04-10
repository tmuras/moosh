<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Detect what kind of plugin (if any) is being worked on
 * based on the current directory.
 * @param $dir
 */
function detect_plugin($dir) {
    $templates = array(
        'qtype' => array('dir'=>'question/type'),
        'qbehaviour' => array('dir'=>'question/behaviour'),
        'qformat' => array('dir'=>'question/format'),
        'filter' => array('dir'=>'filter'),
        'enrol' => array('dir'=>'enrol'),
        'auth' => array('dir'=>'auth'),
        'message' => array('dir'=>'message/output'),
        'editor' => array('dir'=>'lib/editor'),
        'format' => array('dir'=>'course/format'),
        'profilefield' => array('dir'=>'user/profile/field'),
        'report' => array('dir'=>'report'),
        'coursereport' => array('dir'=>'course/report'),
        'gradeexport' => array('dir'=>'grade/export'),
        'gradeimport' => array('dir'=>'grade/import'),
        'gradereport' => array('dir'=>'grade/report'),
        'gradingform' => array('dir'=>'grade/grading/form'),
        'mnetservice' => array('dir'=>'mnet/service'),
        'webservice' => array('dir'=>'webservice'),
        'repository' => array('dir'=>'repository'),
        'portfolio' => array('dir'=>'portfolio'),
        'plagiarism' => array('dir'=>'plagiarism'),
        'tool' => array('dir'=>'admin/tool'),
        'cachestore' => array('dir'=>'cache/stores'),
        'cachelock' => array('dir'=>'cache/locks'),
        'theme' => array('dir'=>'theme'),
        'assignsubmission' => array('dir'=>'mod/assign/submission'),
        'assignfeedback' => array('dir'=>'mod/assign/feedback'),
        'assignment' => array('dir'=>'mod/assignment/type'),
        'booktool' => array('dir'=>'mod/book/tool'),
        'datafield' => array('dir'=>'mod/data/field'),
        'datapreset' => array('dir'=>'mod/data/preset'),
        'quiz' => array('dir'=>'mod/quiz/report'),
        'quizaccess' => array('dir'=>'mod/quiz/accessrule'),
        'scormreport' => array('dir'=>'mod/scorm/report'),
        'workshopform' => array('dir'=>'mod/workshop/form'),
        'workshopallocation' => array('dir'=>'mod/workshop/allocation'),
        'workshopeval' => array('dir'=>'mod/workshop/eval'),
        'tinymce' => array('dir'=>'lib/editor/tinymce/plugins/moodlemedia'),
        'mod' => array('dir'=>'mod'),
        'block' => array('dir'=>'blocks'),
    );

    foreach($templates as $key => $value) {
        $templates[$key]['regex'] = '|' .$templates[$key]['dir'].'/([^/]+)|';
    }

    foreach($templates as $name => $template) {
        $matches = null;
        if(preg_match($template['regex'],$dir,$matches)) {
            return array ('dir'=>$template['dir'],'type'=>$name, 'name'=>$matches[1]);
        }
    }

    return array ('dir'=>'unknown', 'type'=>'unknown', 'name'=>'unknown');
}

/**
 * Find top level Moodle directory going from current directory up.
 * @param $dir
 */
function find_top_moodle_dir($dir)
{
    //don't try to go up more than this
    //the deepest directory I have found in Moodle 2.4.0 was:
    //lib/editor/tinymce/tiny_mce/3.5.7b/themes/simple/skins/o2k7/img
    $max = 10;
    $found = false;
    $up = 0;
    while(!$found) {
        if(is_top_moodle_dir($dir)) {
            return $dir;
        }
        if(++$up > $max || $dir == '/') {
            break;
        }

        $dir = dirname($dir);
    }

    return false;
}

/**
 * Returns true if $dir is top-level Moodle directory
 */
function is_top_moodle_dir($dir)
{
    return file_exists("$dir/config.php") && file_exists("$dir/brokenfile.php") && file_exists("$dir/install.php");
}

function home_dir()
{
    // getenv('HOME') isn't set on windows and generates a Notice.
    $home = getenv('HOME');
    if (empty($home)) {
        if (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH'])) {
            // home on windows
            $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
        }
    }
    return empty($home) ? NULL : $home;
}


/**
 * Write error notification
 * @param $text
 * @return void
 */
function cli_problem($text) {
    fwrite(STDERR, $text."\n");
}

/**
 * Write to standard out and error with exit in error.
 *
 * @param string $text
 * @param int $errorcode
 * @return void (does not return)
 */
function cli_error($text, $errorcode=1) {
    fwrite(STDERR, $text);
    fwrite(STDERR, "\n");
    die($errorcode);
}

function array_merge_recursive_distinct ( array &$array1, array &$array2 )
{
    $merged = $array1;

    foreach ( $array2 as $key => &$value )
    {
        if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
        {
            $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
        }
        else
        {
            $merged [$key] = $value;
        }
    }

    return $merged;
}

function restore_course($tempdir, $categoryid, $fullname, $shortname)
{
    $admin = get_admin();
    if (!$admin) {
        echo "Error: No admin account was found";
        exit(1);
    }

    $courseid = restore_dbops::create_new_course($fullname, $shortname, $categoryid);
    $rc = new restore_controller($tempdir, $courseid, backup::INTERACTIVE_NO,
        backup::MODE_GENERAL, $admin->id, backup::TARGET_NEW_COURSE);
    $plan = $rc->get_plan();
    $tasks = $plan->get_tasks();
    foreach ($tasks as &$task) {
        if ($task instanceof restore_root_task || $task instanceof restore_course_task) {
            /*
            $settings = $task->get_settings();
            foreach ($settings as &$setting) {
                if ($setting->get_ui_name() == 'setting_course_course_fullname') {
                    $setting->set_value($fullname);
                } else if ($setting->get_ui_name() == 'setting_course_course_shortname') {
                    $setting->set_value($shortname);
                }
            }
            */
        }
    }
    $rc->execute_precheck();
    $rc->execute_plan();
    $rc->destroy();
    unset($rc);
    return $courseid;
}
