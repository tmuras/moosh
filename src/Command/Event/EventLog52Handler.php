<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Event;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EventLog52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('userid', null, InputOption::VALUE_REQUIRED, 'Filter by user ID')
            ->addOption('courseid', null, InputOption::VALUE_REQUIRED, 'Filter by course ID')
            ->addOption('component', null, InputOption::VALUE_REQUIRED, 'Filter by component')
            ->addOption('eventname', null, InputOption::VALUE_REQUIRED, 'Filter by event classname')
            ->addOption('action', null, InputOption::VALUE_REQUIRED, 'Filter by action (created, viewed, deleted, etc.)')
            ->addOption('since', null, InputOption::VALUE_REQUIRED, 'Events after this time (strtotime-parseable)')
            ->addOption('until', null, InputOption::VALUE_REQUIRED, 'Events before this time (strtotime-parseable)')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Max results', '50')
            ->addOption('id-only', 'i', InputOption::VALUE_NONE, 'Show event IDs only');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('id-only');

        $filterUserId = $input->getOption('userid');
        $filterCourseId = $input->getOption('courseid');
        $filterComponent = $input->getOption('component');
        $filterEventName = $input->getOption('eventname');
        $filterAction = $input->getOption('action');
        $since = $input->getOption('since');
        $until = $input->getOption('until');
        $limit = (int) $input->getOption('limit');

        $verbose->step('Querying event log');

        // Check if standard log store table exists.
        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('logstore_standard_log')) {
            $output->writeln('<error>Standard log store table not found. Is the logstore_standard plugin enabled?</error>');
            return Command::FAILURE;
        }

        $conditions = [];
        $params = [];

        if ($filterUserId !== null) {
            $conditions[] = 'userid = ?';
            $params[] = (int) $filterUserId;
        }
        if ($filterCourseId !== null) {
            $conditions[] = 'courseid = ?';
            $params[] = (int) $filterCourseId;
        }
        if ($filterComponent !== null) {
            $conditions[] = 'component = ?';
            $params[] = $filterComponent;
        }
        if ($filterEventName !== null) {
            $name = $filterEventName;
            if ($name[0] !== '\\') {
                $name = '\\' . $name;
            }
            $conditions[] = 'eventname = ?';
            $params[] = $name;
        }
        if ($filterAction !== null) {
            $conditions[] = 'action = ?';
            $params[] = $filterAction;
        }
        if ($since !== null) {
            $ts = strtotime($since);
            if ($ts === false) {
                $output->writeln("<error>Invalid --since value: $since</error>");
                return Command::FAILURE;
            }
            $conditions[] = 'timecreated >= ?';
            $params[] = $ts;
        }
        if ($until !== null) {
            $ts = strtotime($until);
            if ($ts === false) {
                $output->writeln("<error>Invalid --until value: $until</error>");
                return Command::FAILURE;
            }
            $conditions[] = 'timecreated <= ?';
            $params[] = $ts;
        }

        $where = empty($conditions) ? '1=1' : implode(' AND ', $conditions);
        $sql = "SELECT id, timecreated, userid, eventname, component, action, target, courseid, objectid, origin, ip
                  FROM {logstore_standard_log}
                 WHERE $where
                 ORDER BY timecreated DESC
                 LIMIT $limit";

        $records = $DB->get_records_sql($sql, $params);

        if (empty($records)) {
            $output->writeln('No log entries found.');
            return Command::SUCCESS;
        }

        if ($idOnly) {
            $ids = array_column((array) $records, 'id');
            $output->writeln(implode(' ', $ids));
            return Command::SUCCESS;
        }

        $headers = ['id', 'time', 'userid', 'eventname', 'component', 'action', 'courseid', 'origin', 'ip'];
        $rows = [];
        foreach ($records as $r) {
            $rows[] = [
                $r->id,
                date('Y-m-d H:i:s', $r->timecreated),
                $r->userid,
                $r->eventname,
                $r->component,
                $r->action,
                $r->courseid ?: '',
                $r->origin ?? '',
                $r->ip ?? '',
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
