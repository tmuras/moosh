<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2025 Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle45\Course;

use Moosh\MooshCommand;

class FindBigImages extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('find-big-images', 'course');

        $this->addOption('s|size:', 'Minimum image size in MB (default: 1)');
    }

    public function execute()
    {
        global $CFG, $DB;

        $this->expandOptions();

        $size = isset($this->expandedOptions['size']) ? (int)$this->expandedOptions['size'] : 1;
        if ($size < 1) {
            $size = 1;
        }

        $sizeBytes = $size * 1024 * 1024;
        $siteUrl = rtrim($CFG->wwwroot, '/');

        $sql = "SELECT ROUND(f.filesize / 1024 / 1024) AS sizemb,
                       f.filename,
                       c.id AS course_id,
                       c.fullname AS course_name,
                       f.contextid,
                       c.id AS cid
                FROM {files} f
                JOIN {context} ctx ON ctx.id = f.contextid
                JOIN {course} c ON c.id = ctx.instanceid
                WHERE f.component = 'course'
                  AND f.filearea = 'overviewfiles'
                  AND ctx.contextlevel = 50
                  AND f.filename != '.'
                  AND f.mimetype LIKE 'image/%'
                  AND f.filesize > ?
                ORDER BY f.filesize DESC";

        $records = $DB->get_records_sql($sql, [$sizeBytes]);

        if (!$records) {
            echo "No course overview images found bigger than {$size} MB.\n";
            return;
        }

        printf("%-10s %-40s %-10s %-30s %-60s %s\n",
            "Size(MB)", "Filename", "CourseID", "Course Name", "Image URL", "Course Settings URL");

        foreach ($records as $record) {
            $imageUrl = "{$siteUrl}/pluginfile.php/{$record->contextid}/course/overviewfiles/{$record->filename}";
            $settingsUrl = "{$siteUrl}/course/edit.php?id={$record->course_id}";

            printf("%-10s %-40s %-10s %-30s %-60s %s\n",
                $record->sizemb,
                $record->filename,
                $record->course_id,
                $record->course_name,
                $imageUrl,
                $settingsUrl);
        }
    }
}
