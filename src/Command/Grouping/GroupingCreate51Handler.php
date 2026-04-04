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

class GroupingCreate51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'Grouping name')
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Description', '')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'ID number');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $name = $input->getArgument('name');
        $courseId = (int) $input->getArgument('courseid');
        $description = $input->getOption('description');
        $idnumber = $input->getOption('idnumber');

        require_once $CFG->dirroot . '/group/lib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would create grouping \"$name\" in course $courseId (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $data = new \stdClass();
        $data->courseid = $courseId;
        $data->name = $name;
        $data->description = $description;
        $data->descriptionformat = FORMAT_HTML;
        if ($idnumber !== null) {
            $data->idnumber = $idnumber;
        }

        $groupingId = groups_create_grouping($data);
        $verbose->done("Created grouping '$name' (ID=$groupingId)");

        $headers = ['id', 'name', 'idnumber', 'courseid'];
        $rows = [[$groupingId, $name, $idnumber ?? '', $courseId]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
