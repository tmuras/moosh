<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Report;

use Moosh\MooshCommand;

class DataStats extends MooshCommand {
    public function __construct() {
        parent::__construct('stats', 'data');

        $this->addOption('j|json', 'generate output using json format');
        $this->addOption('H|no-human-readable', 'Do not display sizes in human-readble strings');
        $this->addOption('O|output:', 'output only the values for the selected fields. Values separated by a comma. ');
    }

    public function execute() {
        global $CFG, $DB;

        $options = $this->expandedOptions;

        $dataroot = run_external_command("du -bs $CFG->dataroot", "Couldn't find dataroot directory");
        $pattern = '/\d*/';
        preg_match($pattern, $dataroot[0], $matches);

        $filedir = run_external_command("du -bs $CFG->dataroot/filedir", "Couldn't find filedir directory");
        preg_match($pattern, $filedir[0], $dir_matches);

        $sql_query = "SELECT SUM(filesize) AS total FROM {files}";
        $all_files = $DB->get_record_sql($sql_query);

        $sql = "SELECT SUM(filesize) AS 'total' FROM (SELECT filesize FROM mdl_files GROUP BY contenthash,filesize) sizes ";
        $distinctfilestotal = $DB->get_record_sql($sql)->total;

        // TODO: get sizes of:
        // all files, including the duplicates.
        // all files counting the duplicate only once.
        // all unique files - that are not shared outside of the course. Deleting this course should free up this much storage.

        $filesbycourse = array();
        if ($courses = get_all_courses()) {
            foreach ($courses as $course) {
                $subcontexts = get_sub_context_ids($course->ctxpath);
                $filesbycourse[$course->id] = array('unique' => 0, 'uniquedistinct' => 0, 'distinct' => 0, 'all' => 0);
                foreach ($subcontexts as $subcontext) {
                    if ($files = get_files($subcontext->id)) {
                        foreach ($files as $file) {
                            $filesbycourse[$course->id]['unique'] += file_is_unique($file->contenthash, $subcontext->id) ?
                                    $file->filesize : 0;
                            $filesbycourse[$course->id]['all'] += $file->filesize;
                        }
                    }
                    if ($files = get_distinct_files($subcontext->id)) {
                        foreach ($files as $file) {
                            $filesbycourse[$course->id]['distinct'] += $file->filesize;
                            $filesbycourse[$course->id]['uniquedistinct'] += file_is_unique($file->contenthash, $subcontext->id) ?
                                    $file->filesize : 0;
                            $filesbycourse[$course->id]['all'] += $file->filesize;
                        }
                    }
                }
            }
        }
        $sortarray = higher_size($filesbycourse);
        $backups = backup_size();

        $data = array('dataroot' => $matches[0],
                'filedir' => $dir_matches[0],
                'files total' => $all_files->total,
                'distinct files total' => $distinctfilestotal);

        $i = 0;
        foreach ($sortarray as $courseid => $values) {
            $i++;
            $data["Course $i id"] = 'id ' . $courseid;
            $data["Course $i files total"] = strval($values['all']);
            $data["Course $i files unique"] = strval($values['unique']);
            $data["Course $i files distinct"] = strval($values['distinct']);
            $data["Course $i files unique and distinct"] = strval($values['uniquedistinct']);
        }

        $i = 0;
        foreach ($backups as $key => $values) {
            $i++;
            $data["Backup $i user name"] = $values->username;
            $data["Backup $i size"] = strval($values->backupsize);
        }

        $this->display($data, $options['json'], !$options['no-human-readable']);
    }
}
