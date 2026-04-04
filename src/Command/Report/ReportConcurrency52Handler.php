<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Report;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * report:concurrency implementation for Moodle 5.1.
 */
class ReportConcurrency52Handler extends BaseHandler
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private const WEEKDAYS = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday',
        5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
    ];

    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'From date (YYYY-MM-DD), default: 1 month ago')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'To date (YYYY-MM-DD), default: today')
            ->addOption('period', null, InputOption::VALUE_REQUIRED, 'Time period in minutes for counting concurrent users', '5')
            ->addOption('timezone', null, InputOption::VALUE_REQUIRED, 'Timezone for dates', 'UTC')
            ->addOption('work-hours-from', null, InputOption::VALUE_REQUIRED, 'Start hour (0-23) for work-hours stats', '0')
            ->addOption('work-hours-to', null, InputOption::VALUE_REQUIRED, 'End hour (0-23) for work-hours stats', '0')
            ->addOption('work-days', null, InputOption::VALUE_REQUIRED, 'Working days as digits 1-7 (Mon-Sun)', '1234567')
            ->addOption('timeseries', null, InputOption::VALUE_NONE, 'Output per-period time-series instead of summary');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $timeseries = $input->getOption('timeseries');

        // Parse options.
        $tz = new \DateTimeZone($input->getOption('timezone'));
        $period = (int) $input->getOption('period') * 60;
        $workHoursFrom = (int) $input->getOption('work-hours-from');
        $workHoursTo = (int) $input->getOption('work-hours-to');
        $workDays = $input->getOption('work-days');

        $fromOpt = $input->getOption('from');
        if ($fromOpt) {
            $fromDate = new \DateTime($fromOpt, $tz);
        } else {
            $fromDate = new \DateTime('now', $tz);
            $fromDate->sub(new \DateInterval('P1M'));
        }
        $fromDate->setTime(0, 0, 0);

        $toOpt = $input->getOption('to');
        if ($toOpt) {
            $toDate = new \DateTime($toOpt, $tz);
        } else {
            $toDate = new \DateTime('now', $tz);
        }
        $toDate->setTime(23, 59, 59);

        if ($toDate < $fromDate) {
            $output->writeln('<error>"to" date must be later than "from" date.</error>');
            return Command::FAILURE;
        }

        $tsFrom = $fromDate->getTimestamp();
        $tsTo = $toDate->getTimestamp();

        $verbose->detail('From', $fromDate->format(self::DATE_FORMAT));
        $verbose->detail('To', $toDate->format(self::DATE_FORMAT));
        $verbose->detail('Period', ($period / 60) . ' minutes');

        // Query concurrent users per period.
        $verbose->step('Querying log data');

        $sql = "SELECT
                  ROUND(timecreated / $period) AS period,
                  COUNT(DISTINCT userid) AS online_users,
                  COUNT(id) AS number_actions
                FROM {logstore_standard_log}
                WHERE timecreated >= ? AND timecreated < ?
                  AND origin IN ('web', 'ws')
                GROUP BY period
                ORDER BY period";
        $results = $DB->get_records_sql($sql, [$tsFrom, $tsTo]);

        $verbose->done('Retrieved ' . count($results) . ' period(s) with activity');

        // Build full data with timezone conversion.
        $fullData = [];
        foreach ($results as $row) {
            $unixtime = (int) $row->period * $period;
            $date = new \DateTime('@' . $unixtime);
            $date->setTimezone($tz);

            if ($date < $fromDate || $date > $toDate) {
                continue;
            }

            $fullData[$unixtime] = [
                'date' => $date,
                'users' => (int) $row->online_users,
                'actions' => (int) $row->number_actions,
            ];
        }
        unset($results);

        // Time-series output mode.
        if ($timeseries) {
            return $this->outputTimeseries($fullData, $format, $output);
        }

        // Summary mode.
        return $this->outputSummary(
            $fullData, $tsFrom, $tsTo, $tz, $fromDate, $toDate,
            $workHoursFrom, $workHoursTo, $workDays,
            $format, $output, $verbose,
        );
    }

    private function outputTimeseries(array $fullData, string $format, OutputInterface $output): int
    {
        $headers = ['datetime', 'users', 'actions'];
        $rows = [];

        ksort($fullData);
        foreach ($fullData as $row) {
            $rows[] = [$row['date']->format(self::DATE_FORMAT), $row['users'], $row['actions']];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);
        return Command::SUCCESS;
    }

    private function outputSummary(
        array $fullData,
        int $tsFrom,
        int $tsTo,
        \DateTimeZone $tz,
        \DateTime $fromDate,
        \DateTime $toDate,
        int $workHoursFrom,
        int $workHoursTo,
        string $workDays,
        string $format,
        OutputInterface $output,
        VerboseLogger $verbose,
    ): int {
        global $DB;

        $data = [];
        $data['Period from'] = $fromDate->format(self::DATE_FORMAT);
        $data['Period to'] = $toDate->format(self::DATE_FORMAT);
        $data['Timezone'] = $tz->getName();

        // Active users.
        $verbose->step('Counting active users');
        $activeUsers = $DB->get_record_sql(
            "SELECT COUNT(DISTINCT userid) AS c FROM {logstore_standard_log} WHERE timecreated BETWEEN ? AND ?",
            [$tsFrom, $tsTo],
        );
        $data['Active users'] = (int) $activeUsers->c;

        // Total log entries.
        $totalLogs = $DB->get_record_sql(
            "SELECT COUNT(*) AS c FROM {logstore_standard_log} WHERE timecreated >= ? AND timecreated < ?",
            [$tsFrom, $tsTo],
        );
        $data['Total log entries'] = (int) $totalLogs->c;

        // Max concurrent.
        $verbose->step('Calculating concurrency statistics');
        $maxUsers = 0;
        $maxDate = null;
        $globalSum = 0;
        $globalCount = 0;

        // Per-weekday tracking.
        $daily = [];

        foreach ($fullData as $row) {
            $users = $row['users'];
            $date = $row['date'];

            $globalSum += $users;
            $globalCount++;

            if ($users > $maxUsers) {
                $maxUsers = $users;
                $maxDate = $date;
            }

            // Per-day tracking for weekday averages.
            $dayOfYear = $date->format('z');
            $dayOfWeek = $date->format('N');
            $hour = (int) $date->format('G');

            // Skip non-work days/hours for work-hours stats.
            if (strpos($workDays, (string) $dayOfWeek) === false) {
                continue;
            }
            if ($workHoursFrom && $hour < $workHoursFrom) {
                continue;
            }
            if ($workHoursTo && $hour >= $workHoursTo) {
                continue;
            }

            if (!isset($daily[$dayOfYear])) {
                $daily[$dayOfYear] = ['count' => 0, 'sum' => 0, 'date' => $date];
            }
            $daily[$dayOfYear]['count']++;
            $daily[$dayOfYear]['sum'] += $users;
        }

        $data['Max concurrent users'] = $maxUsers;
        if ($maxDate) {
            $weekday = self::WEEKDAYS[(int) $maxDate->format('N')] ?? '';
            $data['Max concurrent at'] = $weekday . ', ' . $maxDate->format(self::DATE_FORMAT);
        }
        $data['Global average concurrent'] = $globalCount > 0 ? round($globalSum / $globalCount, 2) : 0;

        // Per-weekday averages.
        $verbose->step('Calculating weekday averages');
        $weekdayStats = [];
        foreach ($daily as $dayData) {
            $dayAvg = $dayData['count'] > 0 ? $dayData['sum'] / $dayData['count'] : 0;
            $dow = (int) $dayData['date']->format('N');
            if (!isset($weekdayStats[$dow])) {
                $weekdayStats[$dow] = ['count' => 0, 'sum' => 0];
            }
            $weekdayStats[$dow]['count']++;
            $weekdayStats[$dow]['sum'] += $dayAvg;
        }
        ksort($weekdayStats);

        foreach ($weekdayStats as $dow => $stats) {
            $avg = $stats['count'] > 0 ? round($stats['sum'] / $stats['count'], 2) : 0;
            $dayName = self::WEEKDAYS[$dow] ?? "Day $dow";
            $data["Avg concurrent ($dayName)"] = $avg;
        }

        // Work-hours average.
        $workSum = 0;
        $workCount = 0;
        foreach ($daily as $dayData) {
            if ($dayData['count'] > 0) {
                $workCount++;
                $workSum += $dayData['sum'] / $dayData['count'];
            }
        }
        $data['Work-hours average concurrent'] = $workCount > 0 ? round($workSum / $workCount, 2) : 0;

        // Render.
        $verbose->step('Rendering output');

        if ($format === 'table') {
            $table = new Table($output);
            $table->setHeaders(['Metric', 'Value']);
            foreach ($data as $key => $value) {
                $table->addRow([$key, $value]);
            }
            $table->render();
        } else {
            $formatter = new ResultFormatter($output, $format);
            $headers = array_keys($data);
            $formatter->display($headers, [array_values($data)]);
        }

        return Command::SUCCESS;
    }
}
