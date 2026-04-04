<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Enrol;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * enrol:list implementation for Moodle 5.1.
 */
class EnrolList52Handler extends BaseHandler
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

        require_once $CFG->libdir . '/enrollib.php';

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course with ID $courseId not found.</error>");
            return Command::FAILURE;
        }

        $verbose->step('Fetching enrolment instances');
        $instances = enrol_get_instances($courseId, false);

        if (empty($instances)) {
            $output->writeln('No enrolment methods found.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            $ids = array_column($instances, 'id');
            $output->writeln(implode(' ', $ids));
            return Command::SUCCESS;
        }

        $headers = ['id', 'enrol', 'name', 'status', 'roleid', 'enrolments'];
        $rows = [];
        foreach ($instances as $instance) {
            $enrolCount = $DB->count_records('user_enrolments', ['enrolid' => $instance->id]);
            $rows[] = [
                $instance->id,
                $instance->enrol,
                $instance->name ?: '(default)',
                $instance->status == ENROL_INSTANCE_ENABLED ? 'enabled' : 'disabled',
                $instance->roleid,
                $enrolCount,
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
