<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Block;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * block:create implementation for Moodle 5.1.
 */
class BlockCreate52Handler extends BaseHandler
{
    private const VALID_MODES = ['course', 'category', 'categorycourses', 'site'];

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('blocktype', InputArgument::REQUIRED, 'Block type name (e.g. calendar_month, online_users, html)')
            ->addArgument('target', InputArgument::REQUIRED, 'Target ID: course ID, category ID, or any value for site mode')
            ->addOption('mode', null, InputOption::VALUE_REQUIRED, 'Target mode: course, category, categorycourses, site', 'course')
            ->addOption('region', null, InputOption::VALUE_REQUIRED, 'Block region (side-pre, side-post, content)', 'side-pre')
            ->addOption('weight', null, InputOption::VALUE_REQUIRED, 'Sort order within region', '0')
            ->addOption('pagetypepattern', null, InputOption::VALUE_REQUIRED, 'Page type pattern (e.g. course-view-*, mod-*, *)', '*')
            ->addOption('subpagepattern', null, InputOption::VALUE_REQUIRED, 'Subpage pattern (optional)')
            ->addOption('showinsubcontexts', null, InputOption::VALUE_REQUIRED, 'Set showinsubcontexts (1 or 0)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $blocktype = $input->getArgument('blocktype');
        $target = $input->getArgument('target');
        $mode = $input->getOption('mode');
        $region = $input->getOption('region');
        $weight = (int) $input->getOption('weight');
        $pagetypepattern = $input->getOption('pagetypepattern');
        $subpagepattern = $input->getOption('subpagepattern');
        $showinsubcontexts = $input->getOption('showinsubcontexts') !== null ? (int) $input->getOption('showinsubcontexts') : 0;

        if (!in_array($mode, self::VALID_MODES, true)) {
            $output->writeln("<error>Invalid mode '$mode'. Use one of: " . implode(', ', self::VALID_MODES) . "</error>");
            return Command::FAILURE;
        }

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/lib/blocklib.php';

        // Validate block type exists.
        $block = $DB->get_record('block', ['name' => $blocktype]);
        if (!$block) {
            $output->writeln("<error>Unknown block type '$blocktype'.</error>");
            return Command::FAILURE;
        }

        // Resolve target contexts.
        $contexts = $this->resolveContexts($mode, $target, $output);
        if ($contexts === null) {
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following block(s) would be created (use --run to execute):</info>');
            foreach ($contexts as $ctx) {
                $output->writeln("  $blocktype in {$ctx['label']} (region=$region, weight=$weight, pattern=$pagetypepattern)");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Creating block instance(s)');
        $rows = [];

        foreach ($contexts as $ctx) {
            $blockinstance = new \stdClass();
            $blockinstance->blockname = $blocktype;
            $blockinstance->parentcontextid = $ctx['contextid'];
            $blockinstance->showinsubcontexts = $showinsubcontexts;
            $blockinstance->pagetypepattern = $pagetypepattern;
            $blockinstance->subpagepattern = $subpagepattern;
            $blockinstance->defaultregion = $region;
            $blockinstance->defaultweight = $weight;
            $blockinstance->configdata = '';
            $blockinstance->timecreated = time();
            $blockinstance->timemodified = time();
            $blockinstance->id = $DB->insert_record('block_instances', $blockinstance);

            // Create block context.
            \context_block::instance($blockinstance->id);

            // Allow block to do additional setup.
            $blockObj = block_instance($blocktype, $blockinstance);
            if ($blockObj) {
                $blockObj->instance_create();
            }

            $verbose->info("Created block instance {$blockinstance->id} in {$ctx['label']}");
            $rows[] = [$blockinstance->id, $blocktype, $ctx['label'], $region, $weight, $pagetypepattern];
        }

        $headers = ['id', 'blocktype', 'target', 'region', 'weight', 'pagetypepattern'];
        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    /**
     * Resolve target contexts based on mode.
     *
     * @return array<array{contextid: int, label: string}>|null  Null on error.
     */
    private function resolveContexts(string $mode, string $target, OutputInterface $output): ?array
    {
        global $DB;

        $contexts = [];

        switch ($mode) {
            case 'course':
                $courseId = (int) $target;
                $course = $DB->get_record('course', ['id' => $courseId]);
                if (!$course) {
                    $output->writeln("<error>Course with ID $courseId not found.</error>");
                    return null;
                }
                $ctx = \context_course::instance($courseId);
                $contexts[] = ['contextid' => $ctx->id, 'label' => "course {$course->shortname} (ID=$courseId)"];
                break;

            case 'category':
                $catId = (int) $target;
                $category = $DB->get_record('course_categories', ['id' => $catId]);
                if (!$category) {
                    $output->writeln("<error>Category with ID $catId not found.</error>");
                    return null;
                }
                $ctx = \context_coursecat::instance($catId);
                $contexts[] = ['contextid' => $ctx->id, 'label' => "category {$category->name} (ID=$catId)"];
                break;

            case 'categorycourses':
                $catId = (int) $target;
                $category = $DB->get_record('course_categories', ['id' => $catId]);
                if (!$category) {
                    $output->writeln("<error>Category with ID $catId not found.</error>");
                    return null;
                }
                $courses = get_courses($catId);
                if (empty($courses)) {
                    $output->writeln("<error>No courses found in category $catId.</error>");
                    return null;
                }
                foreach ($courses as $course) {
                    $ctx = \context_course::instance($course->id);
                    $contexts[] = ['contextid' => $ctx->id, 'label' => "course {$course->shortname} (ID={$course->id})"];
                }
                break;

            case 'site':
                $ctx = \context_course::instance(SITEID);
                $contexts[] = ['contextid' => $ctx->id, 'label' => 'site front page'];
                break;
        }

        return $contexts;
    }
}
