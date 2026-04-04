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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * badge:add implementation for Moodle 5.1.
 */
class BadgeAdd52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'Badge name')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Badge description', '')
            ->addOption('course', 'c', InputOption::VALUE_REQUIRED, 'Course ID (creates a course badge instead of site badge)')
            ->addOption('image', null, InputOption::VALUE_REQUIRED, 'Path to badge image file');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB, $USER;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $runMode = $input->getOption('run');

        $name = $input->getArgument('name');
        $description = $input->getOption('description');
        $courseId = $input->getOption('course');
        $imagePath = $input->getOption('image');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/badgeslib.php';

        $type = $courseId !== null ? BADGE_TYPE_COURSE : BADGE_TYPE_SITE;

        if ($courseId !== null) {
            $course = $DB->get_record('course', ['id' => (int) $courseId]);
            if (!$course) {
                $output->writeln("<error>Course with ID $courseId not found.</error>");
                return Command::FAILURE;
            }
        }

        if ($imagePath !== null && !file_exists($imagePath)) {
            $output->writeln("<error>Image file not found: $imagePath</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $typeLabel = $type === BADGE_TYPE_COURSE ? "course (ID: $courseId)" : 'site';
            $output->writeln("<info>Dry run — would create $typeLabel badge \"$name\" (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step('Creating badge');

        $now = time();
        $url = parse_url($CFG->wwwroot);

        $badge = new \stdClass();
        $badge->name = $name;
        $badge->description = $description;
        $badge->version = '';
        $badge->language = 'en';
        $badge->timecreated = $now;
        $badge->timemodified = $now;
        $badge->usercreated = $USER->id;
        $badge->usermodified = $USER->id;
        $badge->issuername = $CFG->badges_defaultissuername ?? get_string('defaultissuername', 'badges');
        $badge->issuerurl = ($url['scheme'] ?? 'https') . '://' . ($url['host'] ?? 'localhost');
        $badge->issuercontact = $CFG->badges_defaultissuercontact ?? '';
        $badge->expiredate = null;
        $badge->expireperiod = null;
        $badge->type = $type;
        $badge->courseid = $type === BADGE_TYPE_COURSE ? (int) $courseId : null;
        $badge->messagesubject = get_string('messagesubject', 'badges');
        $badge->message = get_string('messagebody', 'badges',
            \html_writer::link($CFG->wwwroot . '/badges/mybadges.php', get_string('managebadges', 'badges')));
        $badge->attachment = 1;
        $badge->notification = 0;
        $badge->status = BADGE_STATUS_INACTIVE;
        $badge->imagecaption = '';

        $newId = $DB->insert_record('badge', $badge, true);

        // Process image if provided.
        if ($imagePath !== null) {
            $verbose->info('Processing badge image');
            require_once $CFG->dirroot . '/badges/classes/badge.php';
            $newBadge = new \badge($newId);
            badges_process_badge_image($newBadge, $imagePath);
        }

        $verbose->done("Created badge with ID $newId");

        $typeLabel = $type === BADGE_TYPE_COURSE ? 'course' : 'site';
        $headers = ['id', 'name', 'type', 'courseid', 'status'];
        $rows = [[$newId, $name, $typeLabel, $badge->courseid ?? '', 'inactive']];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
