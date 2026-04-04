<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Grouping;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GroupingList52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('id-only', 'i', InputOption::VALUE_NONE, 'Display IDs only');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('id-only');

        $courseId = (int) $input->getArgument('courseid');

        require_once $CFG->dirroot . '/group/lib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        $groupings = groups_get_all_groupings($courseId);

        if (empty($groupings)) {
            $output->writeln('No groupings found.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            $ids = array_column((array) $groupings, 'id');
            $output->writeln(implode(' ', $ids));
            return Command::SUCCESS;
        }

        $headers = ['id', 'name', 'idnumber', 'groups'];
        $rows = [];
        foreach ($groupings as $grouping) {
            $groupCount = $DB->count_records('groupings_groups', ['groupingid' => $grouping->id]);
            $rows[] = [$grouping->id, $grouping->name, $grouping->idnumber ?? '', $groupCount];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
