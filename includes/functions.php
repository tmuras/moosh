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
        'qtype' => '|question/type/([^/]+)|',
        'qbehaviour' => '|question/behaviour/([^/]+)|',
        'qformat' => '|question/format/([^/]+)|',
        'filter' => '|filter/([^/]+)|',
        'enrol' => '|enrol/([^/]+)|',
        'auth' => '|auth/([^/]+)|',
        'message' => '|message/output/([^/]+)|',
        'editor' => '|lib/editor/([^/]+)|',
        'format' => '|course/format/([^/]+)|',
        'profilefield' => '|user/profile/field/([^/]+)|',
        'report' => '|report/([^/]+)|',
        'coursereport' => '|course/report/([^/]+)|',
        'gradeexport' => '|grade/export/([^/]+)|',
        'gradeimport' => '|grade/import/([^/]+)|',
        'gradereport' => '|grade/report/([^/]+)|',
        'gradingform' => '|grade/grading/form/([^/]+)|',
        'mnetservice' => '|mnet/service/([^/]+)|',
        'webservice' => '|webservice/([^/]+)|',
        'repository' => '|repository/([^/]+)|',
        'portfolio' => '|portfolio/([^/]+)|',
        'plagiarism' => '|plagiarism/([^/]+)|',
        'tool' => '|admin/tool/([^/]+)|',
        'cachestore' => '|cache/stores/([^/]+)|',
        'cachelock' => '|cache/locks/([^/]+)|',
        'theme' => '|theme/([^/]+)|',
        'assignsubmission' => '|mod/assign/submission/([^/]+)|',
        'assignfeedback' => '|mod/assign/feedback/([^/]+)|',
        'assignment' => '|mod/assignment/type/([^/]+)|',
        'booktool' => '|mod/book/tool/([^/]+)|',
        'datafield' => '|mod/data/field/([^/]+)|',
        'datapreset' => '|mod/data/preset/([^/]+)|',
        'quiz' => '|mod/quiz/report/([^/]+)|',
        'quizaccess' => '|mod/quiz/accessrule/([^/]+)|',
        'scormreport' => '|mod/scorm/report/([^/]+)|',
        'workshopform' => '|mod/workshop/form/([^/]+)|',
        'workshopallocation' => '|mod/workshop/allocation/([^/]+)|',
        'workshopeval' => '|mod/workshop/eval/([^/]+)|',
        'tinymce' => '|lib/editor/tinymce/plugins/moodlemedia/([^/]+)|',
        'mod' => '|mod/([^/]+)|',
        'block' => '|blocks/([^/]+)|',
    );

    foreach($templates as $name => $template) {
        $matches = null;
        if(preg_match($template,$dir,$matches)) {
            return array ('type'=>$name, 'name'=>$matches[1]);
        }
    }

    return array ('type'=>'unknown', 'name'=>'unknown');
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
