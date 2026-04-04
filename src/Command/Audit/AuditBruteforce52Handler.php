<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Audit;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * audit:bruteforce implementation for Moodle 5.1.
 */
class AuditBruteforce52Handler extends BaseHandler
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('days', 'd', InputOption::VALUE_REQUIRED, 'Number of days to look back', '30')
            ->addOption('min-attempts', 'm', InputOption::VALUE_REQUIRED, 'Minimum failed attempts to flag', '10')
            ->addOption('ip', null, InputOption::VALUE_REQUIRED, 'Filter by IP address(es), comma-separated')
            ->addOption('password-policy', null, InputOption::VALUE_NONE, 'Show current password policy settings')
            ->addOption('targeted-users', null, InputOption::VALUE_NONE, 'Show usernames that were targeted by failed logins');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $days = (int) $input->getOption('days');
        $minAttempts = (int) $input->getOption('min-attempts');
        $ipFilter = $input->getOption('ip');
        $showPolicy = $input->getOption('password-policy');
        $showTargeted = $input->getOption('targeted-users');

        if ($showPolicy) {
            $this->displayPasswordPolicy($output);
        }

        if ($showTargeted) {
            return $this->displayTargetedUsers($days, $format, $verbose, $output);
        }

        // Parse IP filter.
        $filterIps = null;
        if ($ipFilter !== null) {
            $filterIps = array_map('trim', explode(',', $ipFilter));
        }

        $timeThreshold = time() - ($days * DAYSECS);

        $verbose->step("Analyzing last $days days for IPs with $minAttempts+ failed attempts");

        // Get IPs with failed login attempts.
        $failedIps = $this->getFailedLoginIPs($days, $minAttempts, $filterIps);

        if (empty($failedIps)) {
            if (!$showPolicy) {
                $output->writeln("No IPs found with $minAttempts or more failed login attempts in the last $days days.");
            }
            return Command::SUCCESS;
        }

        $verbose->done('Found ' . count($failedIps) . ' suspicious IP(s)');

        // Build result table.
        $headers = ['ip', 'failed_attempts', 'first_failed', 'last_failed', 'successful_logins', 'compromised_users'];
        $rows = [];
        $breachCount = 0;

        foreach ($failedIps as $record) {
            $successfulLogins = $this->getSuccessfulLoginsForIP($record->ip, $days);
            $successCount = count($successfulLogins);

            $userIds = [];
            foreach ($successfulLogins as $login) {
                $userIds[$login->userid] = $login->userid;
            }

            $compromisedUsers = '';
            if (!empty($userIds)) {
                $breachCount++;
                $users = $DB->get_records_list('user', 'id', array_values($userIds), '', 'id, username');
                $usernames = array_map(fn($u) => $u->username, $users);
                $compromisedUsers = implode(', ', $usernames);
            }

            $rows[] = [
                $record->ip,
                (int) $record->failed_count,
                date(self::DATE_FORMAT, $record->first_attempt),
                date(self::DATE_FORMAT, $record->last_attempt),
                $successCount,
                $compromisedUsers,
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        // Summary line (only for table format).
        if ($format === 'table') {
            $output->writeln('');
            $output->writeln(sprintf(
                '<info>%d suspicious IP(s) found, %d with successful logins after failed attempts.</info>',
                count($failedIps),
                $breachCount,
            ));

            if ($breachCount > 0) {
                $output->writeln('<error>WARNING: Potential breach detected — review compromised_users column.</error>');
            }
        }

        return $breachCount > 0 ? 1 : Command::SUCCESS;
    }

    /**
     * Get IPs with failed login attempts exceeding the threshold.
     */
    private function getFailedLoginIPs(int $days, int $minAttempts, ?array $filterIps): array
    {
        global $DB;

        $timeThreshold = time() - ($days * DAYSECS);

        $whereIp = '';
        $params = [
            'eventname' => '\\core\\event\\user_login_failed',
            'timethreshold' => $timeThreshold,
        ];

        if ($filterIps !== null && !empty($filterIps)) {
            [$inSql, $inParams] = $DB->get_in_or_equal($filterIps, SQL_PARAMS_NAMED);
            $whereIp = " AND ip $inSql";
            $params = array_merge($params, $inParams);
        }

        $sql = "SELECT ip,
                       COUNT(*) AS failed_count,
                       MIN(timecreated) AS first_attempt,
                       MAX(timecreated) AS last_attempt
                FROM {logstore_standard_log}
                WHERE eventname = :eventname
                      AND timecreated >= :timethreshold
                      AND ip IS NOT NULL
                      AND ip != ''
                      $whereIp
                GROUP BY ip
                HAVING COUNT(*) >= $minAttempts
                ORDER BY failed_count DESC";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get successful logins from a specific IP.
     */
    private function getSuccessfulLoginsForIP(string $ip, int $days): array
    {
        global $DB;

        $timeThreshold = time() - ($days * DAYSECS);

        return $DB->get_records_sql(
            "SELECT id, userid, timecreated
             FROM {logstore_standard_log}
             WHERE eventname = :eventname
                   AND ip = :ip
                   AND timecreated >= :timethreshold
             ORDER BY timecreated DESC",
            [
                'eventname' => '\\core\\event\\user_loggedin',
                'ip' => $ip,
                'timethreshold' => $timeThreshold,
            ],
        );
    }

    /**
     * Display usernames targeted by failed login attempts.
     */
    private function displayTargetedUsers(
        int $days,
        string $format,
        VerboseLogger $verbose,
        OutputInterface $output,
    ): int {
        global $DB;

        $timeThreshold = time() - ($days * DAYSECS);

        $verbose->step('Querying targeted usernames');

        // The 'other' field in login_failed events contains serialized data with the username.
        // We query by userid — userid=0 means the username doesn't exist in the system.
        $sql = "SELECT
                    CASE WHEN userid = 0 THEN CONCAT('(unknown) ID:0') ELSE u.username END AS username,
                    l.userid,
                    COUNT(*) AS failed_count,
                    COUNT(DISTINCT l.ip) AS distinct_ips,
                    MIN(l.timecreated) AS first_attempt,
                    MAX(l.timecreated) AS last_attempt
                FROM {logstore_standard_log} l
                LEFT JOIN {user} u ON u.id = l.userid AND l.userid != 0
                WHERE l.eventname = :eventname
                      AND l.timecreated >= :timethreshold
                GROUP BY l.userid, u.username
                ORDER BY failed_count DESC";

        $records = $DB->get_records_sql($sql, [
            'eventname' => '\\core\\event\\user_login_failed',
            'timethreshold' => $timeThreshold,
        ]);

        $verbose->done('Found ' . count($records) . ' targeted user(s)');

        $headers = ['username', 'user_id', 'failed_attempts', 'distinct_ips', 'first_attempt', 'last_attempt'];
        $rows = [];
        foreach ($records as $record) {
            $rows[] = [
                $record->username ?? '(unknown)',
                (int) $record->userid,
                (int) $record->failed_count,
                (int) $record->distinct_ips,
                date(self::DATE_FORMAT, $record->first_attempt),
                date(self::DATE_FORMAT, $record->last_attempt),
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    /**
     * Display Moodle password policy settings.
     */
    private function displayPasswordPolicy(OutputInterface $output): void
    {
        global $CFG;

        $output->writeln('<info>Password Policy Settings:</info>');

        $policyEnabled = !isset($CFG->passwordpolicy) || $CFG->passwordpolicy;
        $output->writeln('  Policy enabled:      ' . ($policyEnabled ? 'yes' : 'NO'));
        $output->writeln('  Minimum length:      ' . ($CFG->minpasswordlength ?? 'n/a'));
        $output->writeln('  Minimum digits:      ' . ($CFG->minpassworddigits ?? 'n/a'));
        $output->writeln('  Minimum lowercase:   ' . ($CFG->minpasswordlower ?? 'n/a'));
        $output->writeln('  Minimum uppercase:   ' . ($CFG->minpasswordupper ?? 'n/a'));
        $output->writeln('  Minimum special:     ' . ($CFG->minpasswordnonalphanum ?? 'n/a'));

        $maxConsec = $CFG->maxconsecutiveidentchars ?? 0;
        $output->writeln('  Max consecutive:     ' . ($maxConsec > 0 ? $maxConsec : 'unlimited'));

        if (isset($CFG->passwordreuselimit) && $CFG->passwordreuselimit > 0) {
            $output->writeln('  Reuse limit:         ' . $CFG->passwordreuselimit . ' previous passwords');
        }

        // Warnings.
        $warnings = [];
        if (!$policyEnabled) {
            $warnings[] = 'policy disabled';
        }
        if (($CFG->minpasswordlength ?? 0) < 8) {
            $warnings[] = 'length < 8';
        }
        if (($CFG->minpassworddigits ?? 0) == 0 && ($CFG->minpasswordnonalphanum ?? 0) == 0) {
            $warnings[] = 'no digits/special chars required';
        }
        if (($CFG->minpasswordlower ?? 0) == 0 && ($CFG->minpasswordupper ?? 0) == 0) {
            $warnings[] = 'no case requirements';
        }

        if (!empty($warnings)) {
            $output->writeln('  <error>Warnings: ' . implode(', ', $warnings) . '</error>');
        }

        $output->writeln('');
    }
}
