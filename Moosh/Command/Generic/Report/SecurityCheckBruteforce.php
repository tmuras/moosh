<?php
/**
 * Check for brute-force login attempts and identify if any succeeded.
 *
 * Analyzes failed login attempts from log store and cross-references with successful
 * logins to detect potential security breaches. Can also display password policy settings.
 *
 * Usage:
 * moosh -n security-check-bruteforce
 * moosh -n security-check-bruteforce -d=30 -m=10
 * moosh -n security-check-bruteforce --ip=192.168.1.1
 * moosh -n security-check-bruteforce --password-policy
 *
 * Options:
 * -d, --days          Number of days to look back (default: 30)
 * -m, --min-attempts  Minimum failed attempts to flag (default: 10)
 * -i, --ip            Filter by specific IP address(es), comma-separated
 * -p, --password-policy  Show current password policy settings
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @introduced 2025-12-02
 * @author     Fabien Dallet
 */

namespace Moosh\Command\Generic\Report;

use Moosh\MooshCommand;
class SecurityCheckBruteforce extends MooshCommand {

    const DATE_FORMAT = "Y-m-d H:i:s";

    public function __construct() {
        parent::__construct('check-bruteforce', 'security');

        $this->addOption('d|days:', 'number of days to look back (default: 30)', 30);
        $this->addOption('m|min-attempts:', 'minimum failed attempts to consider (default: 10)', 10);
        $this->addOption('i|ip:', 'filter results by specific IP address(es), comma-separated');
        $this->addOption('p|password-policy', 'show current password policy settings');
    }

    public function bootstrapLevel() {
        return self::$BOOTSTRAP_FULL_NO_ADMIN_CHECK;
    }

    /**
     * Get IPs with failed login attempts and their counts
     */
    private function getFailedLoginIPs($daysback, $minattempts, $filterips = null) {
        global $DB;

        $timethreshold = time() - ($daysback * DAYSECS);

        $whereip = '';
        $params = [
            'eventname' => '\\core\\event\\user_login_failed',
            'timethreshold' => $timethreshold,
            'minattempts' => $minattempts
        ];

        // Add IP filter if provided
        if ($filterips !== null && !empty($filterips)) {
            list($insql, $inparams) = $DB->get_in_or_equal($filterips, SQL_PARAMS_NAMED);
            $whereip = " AND ip $insql";
            $params = array_merge($params, $inparams);
        }

        $sql = "SELECT ip,
                       COUNT(*) as failed_count,
                       MIN(timecreated) as first_attempt,
                       MAX(timecreated) as last_attempt
                FROM {logstore_standard_log}
                WHERE eventname = :eventname
                      AND timecreated >= :timethreshold
                      AND ip IS NOT NULL
                      AND ip != ''
                      $whereip
                GROUP BY ip
                HAVING COUNT(*) >= :minattempts
                ORDER BY failed_count DESC";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Check if an IP had successful logins
     */
    private function getSuccessfulLoginsForIP($ip, $daysback) {
        global $DB;

        $timethreshold = time() - ($daysback * DAYSECS);

        $sql = "SELECT id, userid, timecreated, other
                FROM {logstore_standard_log}
                WHERE eventname = :eventname
                      AND ip = :ip
                      AND timecreated >= :timethreshold
                ORDER BY timecreated DESC";

        return $DB->get_records_sql($sql, [
            'eventname' => '\\core\\event\\user_loggedin',
            'ip' => $ip,
            'timethreshold' => $timethreshold
        ]);
    }

    /**
     * Get all successful and failed login details for a given IP
     */
    private function getLoginDetailsForIP($ip, $daysback) {
        global $DB;

        $timethreshold = time() - ($daysback * DAYSECS);

        $sql = "SELECT failed.ip,
                       COUNT(DISTINCT failed.id) as failed_attempts,
                       COUNT(DISTINCT success.id) as successful_logins,
                       MIN(failed.timecreated) as first_failed_attempt,
                       MAX(failed.timecreated) as last_failed_attempt,
                       GROUP_CONCAT(DISTINCT success.userid) as successful_user_ids
                FROM {logstore_standard_log} failed
                LEFT JOIN {logstore_standard_log} success
                    ON failed.ip = success.ip
                    AND success.eventname = :successevent
                    AND success.timecreated >= :timethreshold
                WHERE failed.eventname = :failedevent
                      AND failed.timecreated >= :timethreshold2
                      AND failed.ip = :ip
                      AND failed.ip IS NOT NULL
                      AND failed.ip != ''
                GROUP BY failed.ip";

        return $DB->get_record_sql($sql, [
            'successevent' => '\\core\\event\\user_loggedin',
            'failedevent' => '\\core\\event\\user_login_failed',
            'timethreshold' => $timethreshold,
            'timethreshold2' => $timethreshold,
            'ip' => $ip
        ]);
    }

    /**
     * Format timestamp for display
     */
    private function formatTime($timestamp) {
        return date(self::DATE_FORMAT, $timestamp);
    }

    /**
     * Display password policy settings
     */
    private function displayPasswordPolicy() {
        global $CFG;

        echo "Password Policy Settings:\n";

        // Get password policy settings
        $minlength = $CFG->minpasswordlength;
        $mindigits = $CFG->minpassworddigits;
        $minlower = $CFG->minpasswordlower;
        $minupper = $CFG->minpasswordupper;
        $minnonalpha = $CFG->minpasswordnonalphanum;
        $maxconsecutive = $CFG->maxconsecutiveidentchars;

        echo "  Minimum length: " . $minlength . "\n";
        echo "  Minimum digits: " . $mindigits . "\n";
        echo "  Minimum lowercase: " . $minlower . "\n";
        echo "  Minimum uppercase: " . $minupper . "\n";
        echo "  Minimum special chars: " . $minnonalpha . "\n";
        echo "  Max consecutive chars: " . ($maxconsecutive > 0 ? $maxconsecutive : 'unlimited') . "\n";

        // Check password reset/expiry
        if (isset($CFG->passwordpolicy) && !$CFG->passwordpolicy) {
            echo "  Policy enabled: NO\n";
        } else {
            echo "  Policy enabled: yes\n";
        }

        // Check for password expiry
        if (isset($CFG->passwordexpiry) && $CFG->passwordexpiry > 0) {
            $days = round($CFG->passwordexpiry / DAYSECS);
            echo "  Password expiry: " . $days . " days\n";
        } else {
            echo "  Password expiry: disabled\n";
        }

        // Security warnings
        $warnings = [];
        if ($minlength < 8) {
            $warnings[] = "length < 8";
        }
        if ($mindigits == 0 && $minnonalpha == 0) {
            $warnings[] = "no digits/special chars required";
        }
        if ($minlower == 0 && $minupper == 0) {
            $warnings[] = "no case requirements";
        }
        if (isset($CFG->passwordpolicy) && !$CFG->passwordpolicy) {
            $warnings[] = "policy disabled";
        }

        if (!empty($warnings)) {
            echo "  Warnings: " . implode(', ', $warnings) . "\n";
        }

        echo "\n";
    }

    public function execute() {
        $options = $this->expandedOptions;
        $daysback = $options['days'];
        $minattempts = $options['min-attempts'];
        $ipfilter = $options['ip'];
        $showpolicy = $options['password-policy'];

        // Show password policy if requested
        if ($showpolicy) {
            $this->displayPasswordPolicy();
        }

        // Parse IP filter if provided
        $filterips = null;
        if (!empty($ipfilter)) {
            $filterips = array_map('trim', explode(',', $ipfilter));
        }

        echo "Analyzing last $daysback days for IPs with $minattempts+ failed attempts\n";
        if ($filterips) {
            echo "Filtering for IP(s): " . implode(', ', $filterips) . "\n";
        }
        echo "Time range: " . $this->formatTime(time() - ($daysback * DAYSECS)) . " to " . $this->formatTime(time()) . "\n";

        // Get IPs with failed attempts
        $failedips = $this->getFailedLoginIPs($daysback, $minattempts, $filterips);

        if (empty($failedips)) {
            if ($filterips) {
                echo "No matching IPs found with $minattempts or more failed login attempts in the last $daysback days.\n";
            } else {
                echo "No IPs found with $minattempts or more failed login attempts in the last $daysback days.\n";
            }
            return;
        }

        echo "Found " . count($failedips) . " IP(s) with suspicious activity\n";

        $breachdetected = false;
        $breachips = [];

        foreach ($failedips as $failedip) {
            $details = $this->getLoginDetailsForIP($failedip->ip, $daysback);

            echo "\nIP: " . $failedip->ip . "\n";
            echo "  Failed attempts: " . intval($failedip->failed_count) . "\n";
            echo "  First failed: " . $this->formatTime($failedip->first_attempt) . "\n";
            echo "  Last failed:  " . $this->formatTime($failedip->last_attempt) . "\n";

            if ($details->successful_logins > 0) {
                echo "  Successful logins: " . intval($details->successful_logins) . "\n";
                echo "  User IDs: " . $details->successful_user_ids . "\n";
                $breachdetected = true;
                $breachips[] = $failedip->ip;

                if ($this->verbose) {
                    $successfullogins = $this->getSuccessfulLoginsForIP($failedip->ip, $daysback);
                    echo "  Successful login details:\n";
                    foreach ($successfullogins as $login) {
                        echo "    - User ID " . intval($login->userid) . " at " . $this->formatTime($login->timecreated) . "\n";
                    }
                }
            } else {
                echo "  No successful logins from this IP\n";
            }
        }

        // Summary
        if ($breachdetected) {
            echo "\nWARNING: " . count($breachips) . " IP(s) with failed attempts also had successful logins\n";
            echo "IPs: " . implode(', ', $breachips) . "\n";
        }

        echo "\n";
    }
}
