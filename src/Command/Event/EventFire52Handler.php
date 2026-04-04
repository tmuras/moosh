<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Event;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EventFire52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('eventname', InputArgument::REQUIRED, 'Event class name (e.g. \\core\\event\\course_viewed)')
            ->addOption('data', null, InputOption::VALUE_REQUIRED, 'JSON-encoded event data')
            ->addOption('contextid', null, InputOption::VALUE_REQUIRED, 'Context ID')
            ->addOption('objectid', null, InputOption::VALUE_REQUIRED, 'Object ID')
            ->addOption('courseid', null, InputOption::VALUE_REQUIRED, 'Course ID');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $eventName = $input->getArgument('eventname');
        $jsonData = $input->getOption('data');
        $contextId = $input->getOption('contextid');
        $objectId = $input->getOption('objectid');
        $courseId = $input->getOption('courseid');

        // Normalize classname.
        if ($eventName[0] !== '\\') {
            $eventName = '\\' . $eventName;
        }

        if (!class_exists($eventName)) {
            $output->writeln("<error>Event class '$eventName' not found.</error>");
            return Command::FAILURE;
        }

        if (!is_subclass_of($eventName, \core\event\base::class)) {
            $output->writeln("<error>'$eventName' is not a valid event class.</error>");
            return Command::FAILURE;
        }

        // Build data array.
        $data = [];
        if ($jsonData !== null) {
            $decoded = json_decode($jsonData, true);
            if ($decoded === null && $jsonData !== 'null') {
                $output->writeln('<error>Invalid JSON data.</error>');
                return Command::FAILURE;
            }
            $data = $decoded;
        }

        if ($contextId !== null) {
            $data['contextid'] = (int) $contextId;
        } elseif ($courseId !== null && !isset($data['context']) && !isset($data['contextid'])) {
            // Auto-set course context when courseid is given.
            $data['context'] = \context_course::instance((int) $courseId);
        } elseif (!isset($data['context']) && !isset($data['contextid'])) {
            $data['context'] = \context_system::instance();
        }

        if ($objectId !== null) {
            $data['objectid'] = (int) $objectId;
        }
        if ($courseId !== null) {
            $data['courseid'] = (int) $courseId;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would fire event '$eventName' (use --run to execute):</info>");
            foreach ($data as $key => $value) {
                if (is_object($value)) {
                    $value = get_class($value);
                }
                $display = is_array($value) ? json_encode($value) : $value;
                $output->writeln("  $key: $display");
            }
            return Command::SUCCESS;
        }

        $verbose->step("Firing event '$eventName'");

        try {
            $event = $eventName::create($data);
            $event->trigger();
        } catch (\Throwable $e) {
            $output->writeln("<error>Failed to fire event: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        $output->writeln("Fired event '$eventName' (ID={$event->get_data()['timecreated']}).");

        return Command::SUCCESS;
    }
}
