<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Report;

use Moosh\MooshCommand;

class DataStats extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('stats', 'data');

        $this->addOption('j|json', 'generate output using json format');
        $this->addOption('H|no-human-readable', 'Do not display sizes in human-readble strings');
        $this->addOption('O|output:', 'output only the values for the selected fields. Values separated by a comma. ');
    }

    public function execute()
    {
        global $CFG, $DB;

        $options = $this->expandedOptions;

        $dataroot = run_external_command("du -bs $CFG->dataroot", "Couldn't find dataroot directory");
        $pattern = '/\d*/';
        preg_match($pattern, $dataroot[0], $matches);

        $filedir = run_external_command("du -bs $CFG->dataroot/filedir", "Couldn't find filedir directory");
        preg_match($pattern, $filedir[0], $dir_matches);

        // CREATE TEMPORARY TABLE tmpfiles AS ( SELECT DISTINCT contenthash, component, filearea, filesize, contextid FROM mdl_files WHERE filesize >= 1024);
        $DB->execute("CREATE TEMPORARY TABLE tmpfiles AS (SELECT COUNT(*) AS repeats, contenthash, filesize FROM {files} WHERE filesize >= 1024 GROUP BY contenthash, filesize)");
        $DB->execute("ALTER TABLE `tmpfiles` ADD INDEX(`contenthash`)");

        $sql = "SELECT SUM(filesize) AS  total FROM tmpfiles";
        $distinctfilestotal = $DB->get_record_sql($sql)->total;

        $sql = "SELECT SUM(filesize) AS total FROM {files}";
        $filestotalsize = $DB->get_record_sql($sql)->total;
        $data = array('dataroot' => $matches[0],
            'filedir' => $dir_matches[0],
            'files total' => $filestotalsize,
            'distinct files total' => $distinctfilestotal);

        $filesbycourse = array();
        $courses = $this->getAllCourses();
        foreach ($courses as $course) {
            $subcontexts = $this->get_sub_context_ids($course->ctxpath);
            $subcontextssql = '(' . implode(',', $subcontexts) . ')';

            $filesbycourse[$course->id] = array('non_unique' => 0, 'unique' => 0, 'shared_outside_course' => 0, 'all' => 0, 'free_on_deletion' => 0);

            $sql = "SELECT SUM(t.filesize) AS size FROM tmpfiles t JOIN {files} f ON t.contenthash = f.contenthash WHERE f.contextid IN (" . implode(',', $subcontexts) . ") AND t.repeats = 1";
            $filesbycourse[$course->id]['unique'] = (int)$DB->get_record_sql($sql)->size;

            $sql = "SELECT SUM(t.filesize) AS size FROM tmpfiles t JOIN {files} f ON t.contenthash = f.contenthash WHERE f.contextid IN (" . implode(',', $subcontexts) . ") AND t.repeats > 1";
            $filesbycourse[$course->id]['non_unique'] = (int)$DB->get_record_sql($sql)->size;

            // How many files are linked to mdl_files.ctxid outside of this course.
            $sql = "CREATE TEMPORARY TABLE tmpcoursefiles (contenthash VARCHAR(40), repeats INT, drafts INT) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            $DB->execute($sql);
            $DB->execute("INSERT INTO tmpcoursefiles (contenthash, repeats) SELECT t.contenthash, t.repeats FROM tmpfiles t JOIN mdl_files f ON t.contenthash = f.contenthash WHERE f.contextid IN $subcontextssql AND t.repeats > 1 ");

            // How many times each contenthash is in mdl_files with component = user AND filearea=draft.
            $sql = "UPDATE tmpcoursefiles t SET t.drafts = (SELECT COUNT(*) FROM mdl_files f WHERE f.contenthash = t.contenthash AND f.component = 'user' AND f.filearea = 'draft')";
            $DB->execute($sql);

            // How many bytes of course files are shared outside the course context.
            $subsql = "SELECT contenthash FROM tmpcoursefiles";
            $subsql2 = "SELECT f.contenthash FROM mdl_files f WHERE f.contextid NOT IN $subcontextssql AND f.contenthash IN ($subsql)";
            $sql = "SELECT SUM(t.filesize) AS size FROM tmpfiles t WHERE t.contenthash IN ($subsql2)";
            $filesbycourse[$course->id]['shared_outside_course'] = (int)$DB->get_record_sql($sql)->size;

            // How many bytes of course files are shared only between course and drafts (component = user AND filearea=draft)
            $subsql = "SELECT contenthash FROM tmpcoursefiles WHERE repeats - drafts > 1";
            $subsql2 = "SELECT f.contenthash FROM mdl_files f WHERE f.contextid NOT IN $subcontextssql AND f.contenthash IN ($subsql)";
            $sql = "SELECT SUM(t.filesize) AS size FROM tmpfiles t WHERE t.contenthash IN ($subsql2)";
            $filesbycourse[$course->id]['shared_outside_course_minus_drafts'] = (int)$DB->get_record_sql($sql)->size;

            $DB->execute("DROP TABLE tmpcoursefiles");

            $filesbycourse[$course->id]['all'] = $filesbycourse[$course->id]['non_unique'] + $filesbycourse[$course->id]['unique'];
            $filesbycourse[$course->id]['free_on_deletion'] = $filesbycourse[$course->id]['all'] - $filesbycourse[$course->id]['shared_outside_course'];
            $filesbycourse[$course->id]['free_after_drafts_removal_and_course_deletion'] = $filesbycourse[$course->id]['all'] - $filesbycourse[$course->id]['shared_outside_course_minus_drafts'];

            if($filesbycourse[$course->id]['all'] == 0) {
                unset($filesbycourse[$course->id]);
            }
        }

        // Order  array descending using ['all'] index by custom function
        usort($filesbycourse, function ($item1, $item2) {
            return $item2['all'] <=> $item1['all'];
        });

        $i = 0;
        foreach ($filesbycourse as $courseid => $values) {
            $i++;
            $data["Course $i id"] = $courseid . " (mdl_course.id)";
            $data["Course $i files total"] = strval($values['all']);
            $data["Course $i files unique"] = strval($values['unique']);
            $data["Course $i files non_unique"] = strval($values['non_unique']);
            $data["Course $i files shared outside course"] = strval($values['shared_outside_course']);
            $data["Course $i files shared outside course minus drafts"] = strval($values['shared_outside_course_minus_drafts']);
            $data["Course $i storage freed when deleted"] = strval($values['free_on_deletion']);
            $data["Course $i storage freed when deleted and user drafts removed"] = strval($values['free_after_drafts_removal_and_course_deletion']);
        }

        $sql = "SELECT f.userid, SUM(f.filesize) AS backupsize 
                FROM {files} f
                WHERE f.filearea = 'backup' OR f.component = 'backup'
                GROUP BY f.userid
                ORDER BY backupsize DESC LIMIT 10";

        $backups =  $DB->get_records_sql($sql);
        $i = 0;
        foreach ($backups as $key => $values) {
            $i++;
            $data["Backup $i user ID"] = $values->userid . " (mdl_user.id)";
            $data["Backup $i size"] = strval($values->backupsize);
        }


        // SELECT top components and components / fileareas
        $data += $this->getComponentStorageUsage();
        $data += $this->getFileAreaAndComponentStorageUsage();


        $this->display($data, $options['json'], !$options['no-human-readable']);
    }

    protected function getComponentStorageUsage(): array
    {
        global $DB;
        $data = ['Storage usage by component' => null];

        $components = $DB->get_records_sql("SELECT component AS name FROM {files} GROUP BY component ORDER BY sum(filesize) DESC LIMIT 5;");
        foreach ($components as $component) {
            $sum = $DB->get_record_sql("SELECT SUM(f.max_filesize) AS total FROM (SELECT MAX(filesize) AS max_filesize FROM {files} WHERE component = :component GROUP BY contenthash) f",
                ['component' => $component->name]);
            $data['- ' . $component->name] = $sum->total;
        }

        return $data;
    }


    protected function getFileAreaAndComponentStorageUsage(): array
    {
        global $DB;
        $data = ['Storage usage by component and file area' => null];

        $usageRecords = $DB->get_records_sql("SELECT CONCAT(component, filearea) AS uniquekey, filearea, component, SUM(filesize) AS size FROM (SELECT DISTINCT contenthash, component, filearea, filesize FROM {files}) AS files GROUP BY filearea, component ORDER BY size DESC LIMIT 10");

        foreach ($usageRecords as $record) {
            $data['- ' . $record->component . ', ' . $record->filearea] = $record->size;
        }

        return $data;
    }

    protected function getAllCourses()
    {
        global $DB;
        return $DB->get_records_sql('SELECT c.id, ctx.path AS ctxpath FROM {course} c LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = ' . CONTEXT_COURSE . ') WHERE c.id != 1');
    }

    protected function get_sub_context_ids($path)
    {
        global $DB;

        $sql = "SELECT ctx.id, ctx.id AS id2 FROM {context} ctx WHERE ";
        $sql_like = $DB->sql_like('ctx.path', ':path');
        $contextids = $DB->get_records_sql_menu($sql . $sql_like, array('path' => $path . '%'));
        return $contextids;
    }
}
