<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Activity;

use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * activity:add implementation for Moodle 5.2.
 */
class ActivityAdd52Handler extends ActivityAdd51Handler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $runMode = $input->getOption('run');

        $type = $input->getArgument('type');
        $courseId = (int) $input->getArgument('courseid');
        $name = $input->getOption('name');
        $section = (int) $input->getOption('section');
        $idnumber = $input->getOption('idnumber');

        // Validate module type exists.
        $module = $DB->get_record('modules', ['name' => $type]);
        if (!$module) {
            $output->writeln("<error>Unknown activity type: $type</error>");
            return Command::FAILURE;
        }

        // Validate course exists.
        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $displayName = $name ?? "New $type";
            $output->writeln("<info>Dry run — would create $type activity \"$displayName\" in course $courseId section $section (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/course/lib.php';
        require_once $CFG->dirroot . '/course/modlib.php';

        $verbose->step("Creating $type activity in course $courseId");

        $moduleRecord = $DB->get_record('modules', ['name' => $type], '*', MUST_EXIST);

        $moduleInfo = new \stdClass();
        $moduleInfo->modulename = $type;
        $moduleInfo->module = $moduleRecord->id;
        $moduleInfo->visible = 1;
        $moduleInfo->section = $section;
        $moduleInfo->name = $name ?? "New $type";
        $moduleInfo->cmidnumber = $idnumber ?? '';

        // Provide intro fields expected by most activity types.
        $moduleInfo->introeditor = [
            'text' => '',
            'format' => FORMAT_HTML,
            'itemid' => 0,
        ];

        $this->applyModuleDefaults($type, $moduleInfo);

        $result = $this->applySetOptions($input, $output, $moduleInfo);
        if ($result !== null) {
            return $result;
        }

        $instance = add_moduleinfo($moduleInfo, $course);

        $verbose->done("Created $type with course module ID {$instance->coursemodule}");

        $headers = ['cmid', 'module', 'instance', 'course', 'section'];
        $rows = [[$instance->coursemodule, $type, $instance->instance, $courseId, $section]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function applyModuleDefaults(string $type, \stdClass $moduleInfo): void
    {
        match ($type) {
            'forum' => $this->applyForumDefaults($moduleInfo),
            'assign' => $this->applyAssignDefaults($moduleInfo),
            'page' => $this->applyPageDefaults($moduleInfo),
            'url' => $this->applyUrlDefaults($moduleInfo),
            'choice' => $this->applyChoiceDefaults($moduleInfo),
            'feedback' => $this->applyFeedbackDefaults($moduleInfo),
            'folder' => $this->applyFolderDefaults($moduleInfo),
            'glossary' => $this->applyGlossaryDefaults($moduleInfo),
            'lesson' => $this->applyLessonDefaults($moduleInfo),
            'lti' => $this->applyLtiDefaults($moduleInfo),
            'quiz' => $this->applyQuizDefaults($moduleInfo),
            'resource' => $this->applyResourceDefaults($moduleInfo),
            'scorm' => $this->applyScormDefaults($moduleInfo),
            'wiki' => $this->applyWikiDefaults($moduleInfo),
            'workshop' => $this->applyWorkshopDefaults($moduleInfo),
            'h5pactivity' => $this->applyH5pactivityDefaults($moduleInfo),
            default => null,
        };
    }

    private function applyForumDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->type = 'general';
        $moduleInfo->grade_forum = 0;
        $moduleInfo->scale = 0;
    }

    private function applyAssignDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->submissiondrafts = 0;
        $moduleInfo->requiresubmissionstatement = 0;
        $moduleInfo->sendnotifications = 0;
        $moduleInfo->sendlatenotifications = 0;
        $moduleInfo->sendstudentnotifications = 1;
        $moduleInfo->duedate = 0;
        $moduleInfo->cutoffdate = 0;
        $moduleInfo->gradingduedate = 0;
        $moduleInfo->allowsubmissionsfromdate = 0;
        $moduleInfo->grade = 100;
        $moduleInfo->teamsubmission = 0;
        $moduleInfo->requireallteammemberssubmit = 0;
        $moduleInfo->blindmarking = 0;
        $moduleInfo->markingworkflow = 0;
        $moduleInfo->markingallocation = 0;
    }

    private function applyPageDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->display = 0; // RESOURCELIB_DISPLAY_AUTO
        $moduleInfo->printintro = 1;
        $moduleInfo->printlastmodified = 1;
        $moduleInfo->content = '';
        $moduleInfo->contentformat = FORMAT_HTML;
    }

    private function applyUrlDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->display = 0; // RESOURCELIB_DISPLAY_AUTO
        $moduleInfo->externalurl = '';
    }

    private function applyChoiceDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->option = [];
    }

    private function applyFeedbackDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->page_after_submit = '';
        $moduleInfo->page_after_submit_editor = [
            'text' => '',
            'format' => FORMAT_HTML,
            'itemid' => 0,
        ];
    }

    private function applyFolderDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->files = 0;
    }

    private function applyGlossaryDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->displayformat = 'dictionary';
        $moduleInfo->assessed = 0;
    }

    private function applyLessonDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->mediafile = 0;
        $moduleInfo->available = 0;
        $moduleInfo->deadline = 0;
        $moduleInfo->practice = 0;
        $moduleInfo->grade = 100;
    }

    private function applyLtiDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->toolurl = '';
        $moduleInfo->urlmatchedtypeid = 0;
    }

    private function applyQuizDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->quizpassword = '';
        $moduleInfo->timeopen = 0;
        $moduleInfo->timeclose = 0;
        $moduleInfo->grade = 0;
        $moduleInfo->questiondecimalpoints = -1;
        $moduleInfo->decimalpoints = 2;
    }

    private function applyResourceDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->display = 0; // RESOURCELIB_DISPLAY_AUTO
        $moduleInfo->files = 0;
    }

    private function applyScormDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->width = 100;
        $moduleInfo->height = 500;
        $moduleInfo->packageurl = '';
        $moduleInfo->scormtype = 'local';
    }

    private function applyWikiDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->wikimode = 'collaborative';
        $moduleInfo->firstpagetitle = $moduleInfo->name;
    }

    private function applyWorkshopDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->grade = 80;
        $moduleInfo->gradinggrade = 20;
        $moduleInfo->gradecategory = 0;
        $moduleInfo->gradinggradecategory = 0;
        $moduleInfo->submissionstart = 0;
        $moduleInfo->submissionend = 0;
        $moduleInfo->assessmentstart = 0;
        $moduleInfo->assessmentend = 0;
        $moduleInfo->instructauthorseditor = [
            'text' => '',
            'format' => FORMAT_HTML,
            'itemid' => 0,
        ];
        $moduleInfo->instructreviewerseditor = [
            'text' => '',
            'format' => FORMAT_HTML,
            'itemid' => 0,
        ];
        $moduleInfo->conclusioneditor = [
            'text' => '',
            'format' => FORMAT_HTML,
            'itemid' => 0,
        ];
    }

    private function applyH5pactivityDefaults(\stdClass $moduleInfo): void
    {
        $moduleInfo->grade = 100;
    }
}
