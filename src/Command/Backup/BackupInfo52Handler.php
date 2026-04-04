<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Backup;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * backup:info implementation for Moodle 5.1.
 *
 * Does not require Moodle bootstrap — reads .mbz files directly.
 */
class BackupInfo52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'file',
            InputArgument::REQUIRED,
            'Path to the .mbz backup file',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            $output->writeln("<error>File not found: $file</error>");
            return Command::FAILURE;
        }

        $verbose->step('Detecting backup format');
        $archiveType = $this->detectFormat($file);
        if ($archiveType === null) {
            $output->writeln('<error>Unknown archive format. Expected gzip or zip.</error>');
            return Command::FAILURE;
        }

        $data = [];

        // --- File info ---
        $data['File'] = basename($file);
        $data['File size (bytes)'] = filesize($file);
        $data['Archive type'] = $archiveType;

        // --- moodle_backup.xml ---
        $verbose->step('Reading moodle_backup.xml');
        $backupXml = $this->extractFile($file, $archiveType, 'moodle_backup.xml');
        if ($backupXml === null) {
            $output->writeln('<error>Could not read moodle_backup.xml from backup.</error>');
            return Command::FAILURE;
        }

        $xml = @simplexml_load_string($backupXml);
        if ($xml === false) {
            $output->writeln('<error>Failed to parse moodle_backup.xml.</error>');
            return Command::FAILURE;
        }

        $info = $xml->information;
        $data['Backup name'] = (string) $info->name;
        $data['Moodle version'] = (string) $info->moodle_version;
        $data['Moodle release'] = (string) $info->moodle_release;
        $data['Backup version'] = (string) $info->backup_version;
        $data['Backup release'] = (string) $info->backup_release;

        $backupDate = (int) (string) $info->backup_date;
        $data['Backup date'] = $backupDate > 0 ? date('Y-m-d H:i:s', $backupDate) : '';

        $data['Original WWW root'] = (string) $info->original_wwwroot;
        $data['Original course ID'] = (string) $info->original_course_id;
        $data['Course fullname'] = (string) $info->original_course_fullname;
        $data['Course shortname'] = (string) $info->original_course_shortname;
        $data['Course format'] = (string) $info->original_course_format;

        $startDate = (int) (string) $info->original_course_startdate;
        $data['Course start date'] = $startDate > 0 ? date('Y-m-d H:i:s', $startDate) : '';
        $endDate = (int) (string) $info->original_course_enddate;
        $data['Course end date'] = $endDate > 0 ? date('Y-m-d H:i:s', $endDate) : 'none';

        $data['Includes files'] = (string) $info->include_files;

        // Activities.
        $verbose->step('Counting activities');
        $activityCount = 0;
        $activityTypes = [];
        if (isset($info->contents->activities->activity)) {
            foreach ($info->contents->activities->activity as $activity) {
                $activityCount++;
                $modName = (string) $activity->modulename;
                $activityTypes[$modName] = ($activityTypes[$modName] ?? 0) + 1;
            }
        }
        $data['Activities'] = $activityCount;
        foreach ($activityTypes as $type => $count) {
            $data["Activities: $type"] = $count;
        }

        // Sections.
        $sectionCount = 0;
        if (isset($info->contents->sections->section)) {
            foreach ($info->contents->sections->section as $section) {
                $sectionCount++;
            }
        }
        $data['Sections'] = $sectionCount;

        // Backup settings.
        $verbose->step('Reading backup settings');
        $settingKeys = [
            'users', 'anonymize', 'role_assignments', 'activities',
            'blocks', 'files', 'filters', 'comments', 'badges',
            'calendarevents', 'userscompletion', 'logs', 'grade_histories',
            'groups', 'competencies',
        ];
        foreach ($settingKeys as $key) {
            $value = $this->getSettingValue($info, $key);
            if ($value !== null) {
                $label = 'Includes ' . str_replace('_', ' ', $key);
                $data[$label] = $value;
            }
        }

        // --- users.xml ---
        $verbose->step('Reading users.xml');
        $usersXml = $this->extractFile($file, $archiveType, 'users.xml');
        if ($usersXml !== null) {
            $usersDoc = @simplexml_load_string($usersXml);
            if ($usersDoc !== false && isset($usersDoc->user)) {
                $userCount = count($usersDoc->user);
                $data['Users'] = $userCount;
            } else {
                $data['Users'] = 0;
            }
        } else {
            $data['Users'] = 'not included';
        }

        // --- roles.xml ---
        $verbose->step('Reading roles.xml');
        $rolesXml = $this->extractFile($file, $archiveType, 'roles.xml');
        if ($rolesXml !== null) {
            $rolesDoc = @simplexml_load_string($rolesXml);
            if ($rolesDoc !== false) {
                $roleNames = [];
                foreach ($rolesDoc->role as $role) {
                    $roleNames[] = (string) $role->shortname;
                }
                $data['Roles defined'] = $roleNames ? implode(', ', $roleNames) : 'none';
            }
        }

        // --- course/enrolments.xml ---
        $verbose->step('Reading enrolments.xml');
        $enrolXml = $this->extractFile($file, $archiveType, 'course/enrolments.xml');
        if ($enrolXml !== null) {
            $enrolDoc = @simplexml_load_string($enrolXml);
            if ($enrolDoc !== false && isset($enrolDoc->enrols->enrol)) {
                $enrolMethods = [];
                $totalEnrolments = 0;
                foreach ($enrolDoc->enrols->enrol as $enrol) {
                    $method = (string) $enrol->enrol;
                    $userCount = isset($enrol->user_enrolments->enrolment)
                        ? count($enrol->user_enrolments->enrolment)
                        : 0;
                    $enrolMethods[$method] = ($enrolMethods[$method] ?? 0) + $userCount;
                    $totalEnrolments += $userCount;
                }
                $data['Total enrolments'] = $totalEnrolments;
                foreach ($enrolMethods as $method => $count) {
                    $data["Enrolments: $method"] = $count;
                }
            }
        }

        // --- gradebook.xml ---
        $verbose->step('Reading gradebook.xml');
        $gradebookXml = $this->extractFile($file, $archiveType, 'gradebook.xml');
        if ($gradebookXml !== null) {
            $gradebookDoc = @simplexml_load_string($gradebookXml);
            if ($gradebookDoc !== false) {
                $gradeItems = isset($gradebookDoc->grade_items->grade_item)
                    ? count($gradebookDoc->grade_items->grade_item)
                    : 0;
                $data['Grade items'] = $gradeItems;
            }
        }

        // --- questions.xml ---
        $verbose->step('Reading questions.xml');
        $questionsXml = $this->extractFile($file, $archiveType, 'questions.xml');
        if ($questionsXml !== null) {
            $questionsDoc = @simplexml_load_string($questionsXml);
            if ($questionsDoc !== false) {
                $questionCount = 0;
                if (isset($questionsDoc->question_categories->question_category)) {
                    foreach ($questionsDoc->question_categories->question_category as $cat) {
                        if (isset($cat->questions->question)) {
                            $questionCount += count($cat->questions->question);
                        }
                    }
                }
                $data['Questions'] = $questionCount;
            }
        }

        // --- files.xml ---
        $verbose->step('Reading files.xml');
        $filesXml = $this->extractFile($file, $archiveType, 'files.xml');
        if ($filesXml !== null) {
            $filesDoc = @simplexml_load_string($filesXml);
            if ($filesDoc !== false && isset($filesDoc->file)) {
                $fileCount = 0;
                $totalSize = 0;
                foreach ($filesDoc->file as $f) {
                    $fname = (string) $f->filename;
                    if ($fname !== '.') {
                        $fileCount++;
                        $totalSize += (int) (string) $f->filesize;
                    }
                }
                $data['Files in backup'] = $fileCount;
                $data['Files total size (bytes)'] = $totalSize;
            }
        }

        // --- groups.xml ---
        $verbose->step('Reading groups.xml');
        $groupsXml = $this->extractFile($file, $archiveType, 'groups.xml');
        if ($groupsXml !== null) {
            $groupsDoc = @simplexml_load_string($groupsXml);
            if ($groupsDoc !== false) {
                $groupCount = isset($groupsDoc->group) ? count($groupsDoc->group) : 0;
                $data['Groups'] = $groupCount;
            }
        }

        // --- Render output ---
        $verbose->step('Rendering output');

        if ($format === 'table') {
            $table = new Table($output);
            $table->setHeaders(['Metric', 'Value']);
            foreach ($data as $key => $value) {
                $table->addRow([$key, $value]);
            }
            $table->render();
        } else {
            $formatter = new ResultFormatter($output, $format);
            $headers = array_keys($data);
            $formatter->display($headers, [array_values($data)]);
        }

        return Command::SUCCESS;
    }

    private function detectFormat(string $file): ?string
    {
        $mime = mime_content_type($file);
        if ($mime === 'application/gzip' || $mime === 'application/x-gzip') {
            return 'gzip';
        }
        if ($mime === 'application/zip') {
            return 'zip';
        }

        // Fallback: check magic bytes.
        $fh = fopen($file, 'rb');
        $bytes = fread($fh, 4);
        fclose($fh);

        if (substr($bytes, 0, 2) === "\x1f\x8b") {
            return 'gzip';
        }
        if (substr($bytes, 0, 4) === "PK\x03\x04") {
            return 'zip';
        }

        return null;
    }

    private function extractFile(string $archive, string $type, string $innerPath): ?string
    {
        if ($type === 'gzip') {
            $cmd = 'tar -xzOf ' . escapeshellarg($archive) . ' ' . escapeshellarg($innerPath) . ' 2>/dev/null';
        } else {
            $cmd = 'unzip -p ' . escapeshellarg($archive) . ' ' . escapeshellarg($innerPath) . ' 2>/dev/null';
        }

        $result = shell_exec($cmd);
        return $result !== null && $result !== '' ? $result : null;
    }

    private function getSettingValue(\SimpleXMLElement $info, string $name): ?string
    {
        if (!isset($info->settings->setting)) {
            return null;
        }

        foreach ($info->settings->setting as $setting) {
            if ((string) $setting->level === 'root' && (string) $setting->name === $name) {
                return (string) $setting->value;
            }
        }

        return null;
    }
}
