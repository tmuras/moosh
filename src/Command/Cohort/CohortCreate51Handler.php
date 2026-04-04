<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Cohort;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CohortCreate51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Cohort name(s)')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Cohort description', '')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'ID number')
            ->addOption('category', null, InputOption::VALUE_REQUIRED, 'Category ID for context (default: system)')
            ->addOption('visible', null, InputOption::VALUE_REQUIRED, 'Visible (1 or 0)', '1');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $names = $input->getArgument('name');
        $description = $input->getOption('description');
        $idnumber = $input->getOption('idnumber');
        $categoryId = $input->getOption('category');
        $visible = (int) $input->getOption('visible');

        require_once $CFG->dirroot . '/cohort/lib.php';

        // Resolve context.
        if ($categoryId !== null) {
            $cat = $DB->get_record('course_categories', ['id' => (int) $categoryId]);
            if (!$cat) {
                $output->writeln("<error>Category $categoryId not found.</error>");
                return Command::FAILURE;
            }
            $contextId = \context_coursecat::instance((int) $categoryId)->id;
        } else {
            $contextId = \context_system::instance()->id;
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following cohort(s) would be created (use --run to execute):</info>');
            foreach ($names as $name) {
                $output->writeln("  \"$name\" (context=$contextId, visible=$visible)");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Creating cohort(s)');
        $rows = [];

        foreach ($names as $name) {
            $cohort = new \stdClass();
            $cohort->contextid = $contextId;
            $cohort->name = $name;
            $cohort->description = $description;
            $cohort->descriptionformat = FORMAT_HTML;
            $cohort->visible = $visible;
            if ($idnumber !== null) {
                $cohort->idnumber = count($names) === 1 ? $idnumber : $idnumber . '_' . count($rows);
            }

            $cohort->id = cohort_add_cohort($cohort);
            $verbose->info("Created cohort '{$name}' (ID={$cohort->id})");

            $rows[] = [$cohort->id, $name, $cohort->idnumber ?? '', $contextId, $visible];
        }

        $headers = ['id', 'name', 'idnumber', 'contextid', 'visible'];
        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
