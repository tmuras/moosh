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

class GroupCreate52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Group name(s) followed by course ID (last argument)')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Group description', '')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'ID number')
            ->addOption('enrolmentkey', null, InputOption::VALUE_REQUIRED, 'Enrolment key')
            ->addOption('visibility', null, InputOption::VALUE_REQUIRED, 'Visibility: 0=all, 1=members, 2=own, 3=none', '0');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $args = $input->getArgument('name');
        if (count($args) < 2) {
            $output->writeln('<error>Provide at least a group name and a course ID.</error>');
            return Command::FAILURE;
        }

        $courseId = (int) array_pop($args);
        $names = $args;
        $description = $input->getOption('description');
        $idnumber = $input->getOption('idnumber');
        $enrolmentkey = $input->getOption('enrolmentkey');
        $visibility = (int) $input->getOption('visibility');

        require_once $CFG->dirroot . '/group/lib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following group(s) would be created (use --run to execute):</info>');
            foreach ($names as $name) {
                $output->writeln("  \"$name\" in course $courseId (visibility=$visibility)");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Creating group(s)');
        $rows = [];

        foreach ($names as $i => $name) {
            $data = new \stdClass();
            $data->courseid = $courseId;
            $data->name = $name;
            $data->description = $description;
            $data->descriptionformat = FORMAT_HTML;
            $data->visibility = $visibility;
            if ($idnumber !== null) {
                $data->idnumber = count($names) === 1 ? $idnumber : $idnumber . '_' . $i;
            }
            if ($enrolmentkey !== null) {
                $data->enrolmentkey = $enrolmentkey;
            }

            $groupId = groups_create_group($data);
            $verbose->info("Created group '$name' (ID=$groupId)");

            $rows[] = [$groupId, $name, $data->idnumber ?? '', $courseId, $visibility];
        }

        $headers = ['id', 'name', 'idnumber', 'courseid', 'visibility'];
        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
