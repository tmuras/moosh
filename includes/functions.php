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
    return file_exists("$dir/config.php") && file_exists("$dir/version.php") && file_exists("$dir/install.php");
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

/**
 * Get the moodle version from the branch variable in version.php
 * If not run from inside a Moodle directory, it will return the $default value.
 *
 * @param string $topdir moodle directory
 * @return string a branch version (e.g. 23, 24, 25, etc.)
 */
function moosh_moodle_version($topdir, $default=23)
{
    if ($topdir && is_dir($topdir) && file_exists($topdir . '/version.php')) {

        // use the branch defined in version.php
        $lines = file($topdir . '/version.php');
        foreach ($lines as $line) {
            $matches = array();
            if (preg_match('/^\$branch\s+=\s+\'(\d\d)\'.*/', $line, $matches)) {
                return $matches[1];
            }
        }

        // If the file was there and we couldn't parse out the branch, there was a problem.
        throw new Exception("Unable to determine branch version from Moode's version.php");
    }
    return $default;
}

function moosh_generate_version_list($upto, $from=23)
{
    $upto = intval($upto);
    $from = intval($from);
    if (!($from && $upto) || $from > $upto) {
        throw new Exception("Invalid from or upto value; they must both be > 0 and from must be <= upto");
    }
    return range($from, $upto);
}

/**
 * Return full namespaced classname of all moosh commands.

 * The command for the latest $viable_version will be used.
 * For example, if viable versions contains 25, and these commands exist:
 *   * Moosh/Command/Moodle23/Category/CategoryCreate.php
 *   * Moosh/Command/Moodle25/Category/CategoryCreate.php
 * then the Moodle25 version will be used.
 *
 * @param string $srcdir directory containing the Moosh directory
 * @param array $viable_versions array of ascending branch numbers representing Moodle versions, i.e (23, 24, 25, 26) that can be used
 */
function moosh_load_all_commands($srcdir, $viable_versions)
{
    //load all commands
    $classnames = array();
    foreach ($viable_versions as $version) {
        $moodle_version = 'Moodle' . $version;
        $command_files = glob("$srcdir/Moosh/Command/$moodle_version/*/*.php");
        foreach ($command_files as $filename) {
            $classname = basename($filename, '.php');
            $full_classname = "Moosh\\Command\\$moodle_version\\" . basename(dirname($filename)) .  '\\' . $classname;
            // Later vesions overwrite earlier ones (e.g. a 23 version will be overwritten by a 26 version, if present).
            // Like this, the most recent appropriate version available will be used.
            $classnames[$classname] = $full_classname;
        }
    }

    // the classnames as keys served their purpose to provide uniqueness, but are not really necessary, so remove them.
    return array_values($classnames);
}
