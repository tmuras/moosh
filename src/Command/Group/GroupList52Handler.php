<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Group;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GroupList52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('grouping', null, InputOption::VALUE_REQUIRED, 'Filter by grouping ID')
            ->addOption('id-only', 'i', InputOption::VALUE_NONE, 'Display IDs only');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('id-only');

        $courseId = (int) $input->getArgument('courseid');
        $groupingId = $input->getOption('grouping');

        require_once $CFG->dirroot . '/group/lib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        $groups = groups_get_all_groups($courseId, 0, $groupingId ? (int) $groupingId : 0);

        if (empty($groups)) {
            $output->writeln('No groups found.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            $ids = array_column((array) $groups, 'id');
            $output->writeln(implode(' ', $ids));
            return Command::SUCCESS;
        }

        $headers = ['id', 'name', 'idnumber', 'visibility', 'members'];
        $rows = [];
        foreach ($groups as $group) {
            $memberCount = $DB->count_records('groups_members', ['groupid' => $group->id]);
            $rows[] = [$group->id, $group->name, $group->idnumber ?? '', $group->visibility ?? 0, $memberCount];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
