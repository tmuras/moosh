<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Badge;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * badge:info implementation for Moodle 5.1.
 */
class BadgeInfo51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'badgeid',
            InputArgument::REQUIRED,
            'The ID of the badge to inspect',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $badgeId = (int) $input->getArgument('badgeid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/badgeslib.php';
        require_once $CFG->dirroot . '/badges/classes/badge.php';

        $record = $DB->get_record('badge', ['id' => $badgeId]);
        if (!$record) {
            $output->writeln("<error>Badge with ID $badgeId not found.</error>");
            return Command::FAILURE;
        }

        $badge = new \badge($badgeId);

        $data = [];

        // --- Basic info ---
        $verbose->step('Collecting badge information');
        $data['Badge ID'] = $record->id;
        $data['Name'] = $record->name;
        $data['Description'] = $record->description;
        $data['Version'] = $record->version;
        $data['Language'] = $record->language;

        $data['Type'] = $record->type == BADGE_TYPE_COURSE ? 'course' : 'site';
        if ($record->courseid) {
            $course = $DB->get_record('course', ['id' => $record->courseid], 'shortname, fullname');
            $data['Course ID'] = $record->courseid;
            $data['Course name'] = $course ? $course->fullname : '(unknown)';
        }

        // Status.
        $statusMap = [
            BADGE_STATUS_INACTIVE => 'inactive',
            BADGE_STATUS_ACTIVE => 'active',
        ];
        // Constants 2 and 3 exist for archived states.
        $data['Status'] = $statusMap[$record->status] ?? "status-{$record->status}";

        // --- Issuer ---
        $data['Issuer name'] = $record->issuername;
        $data['Issuer URL'] = $record->issuerurl;
        $data['Issuer contact'] = $record->issuercontact;

        // --- Dates ---
        $data['Created'] = date('Y-m-d H:i:s', $record->timecreated);
        $data['Modified'] = date('Y-m-d H:i:s', $record->timemodified);

        if ($record->expiredate) {
            $data['Expire date'] = date('Y-m-d H:i:s', $record->expiredate);
        } elseif ($record->expireperiod) {
            $days = (int) ($record->expireperiod / 86400);
            $data['Expire period'] = "$days day(s)";
        } else {
            $data['Expiry'] = 'never';
        }

        // --- Creator ---
        $creator = $DB->get_record('user', ['id' => $record->usercreated], 'username');
        $data['Created by'] = $creator ? $creator->username : "(ID {$record->usercreated})";

        // --- Awards ---
        $verbose->step('Counting awards');
        $awards = $DB->count_records('badge_issued', ['badgeid' => $badgeId]);
        $data['Times awarded'] = $awards;

        // --- Criteria ---
        $verbose->step('Checking criteria');
        $criteria = $DB->get_records('badge_criteria', ['badgeid' => $badgeId]);
        $data['Criteria count'] = count($criteria);

        if (!empty($criteria)) {
            foreach ($criteria as $criterion) {
                $typeName = match ((int) $criterion->criteriatype) {
                    0 => 'Overall',
                    1 => 'Activity completion',
                    2 => 'Manual',
                    4 => 'Course completion',
                    5 => 'Competency',
                    6 => 'Profile fields',
                    7 => 'Badge awarded',
                    8 => 'Cohort membership',
                    default => "type-{$criterion->criteriatype}",
                };
                $data["Criterion: $typeName"] = "method={$criterion->method}";
            }
        }

        // --- Message ---
        $data['Message subject'] = $record->messagesubject;
        $data['Notification'] = (int) $record->notification;
        $data['Attachment'] = (int) $record->attachment;

        // --- Render output ---
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
