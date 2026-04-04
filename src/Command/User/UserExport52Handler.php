<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\User;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserExport52Handler extends BaseHandler
{
    private const STANDARD_FIELDS = [
        'id', 'username', 'email', 'firstname', 'lastname', 'idnumber',
        'institution', 'department', 'phone1', 'phone2', 'city', 'country',
        'auth', 'suspended', 'lastaccess',
    ];

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::OPTIONAL, 'Output CSV file (default: stdout)')
            ->addOption('userid', null, InputOption::VALUE_REQUIRED, 'Export single user by username (or by ID with --by-id)')
            ->addOption('by-id', null, InputOption::VALUE_NONE, 'Treat --userid value as numeric ID')
            ->addOption('course', null, InputOption::VALUE_REQUIRED, 'Export users enrolled in this course ID');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $filePath = $input->getArgument('file');
        $userIdent = $input->getOption('userid');
        $byId = $input->getOption('by-id');
        $courseId = $input->getOption('course');

        require_once $CFG->dirroot . '/user/profile/lib.php';

        // Discover custom profile fields.
        $verbose->step('Loading profile fields');
        $profileFields = $DB->get_records('user_info_field', null, 'sortorder ASC');
        $customFieldNames = [];
        foreach ($profileFields as $field) {
            $customFieldNames[] = 'profile_field_' . $field->shortname;
        }

        $headers = array_merge(self::STANDARD_FIELDS, $customFieldNames);

        // Build user list.
        $verbose->step('Fetching users');

        if ($userIdent !== null) {
            $users = $this->fetchSingleUser($userIdent, $byId, $output);
        } elseif ($courseId !== null) {
            $users = $this->fetchCourseUsers((int) $courseId, $output);
        } else {
            $users = $this->fetchAllUsers();
        }

        if ($users === null) {
            return Command::FAILURE;
        }

        if (empty($users)) {
            $output->writeln('No users found.');
            return Command::SUCCESS;
        }

        $verbose->done('Found ' . count($users) . ' user(s)');

        // Build CSV.
        $verbose->step('Building CSV');

        $fh = fopen('php://temp', 'r+');
        fputcsv($fh, $headers);

        foreach ($users as $user) {
            profile_load_data($user);

            $row = [];
            foreach (self::STANDARD_FIELDS as $field) {
                $value = $user->$field ?? '';
                if ($field === 'lastaccess' && $value) {
                    $value = date('Y-m-d H:i:s', (int) $value);
                }
                $row[] = (string) $value;
            }

            foreach ($profileFields as $pf) {
                $key = 'profile_field_' . $pf->shortname;
                $row[] = (string) ($user->$key ?? '');
            }

            fputcsv($fh, $row);
        }

        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        if ($filePath !== null) {
            file_put_contents($filePath, $csv);
            $output->writeln("<info>Exported " . count($users) . " user(s) to $filePath</info>");
        } else {
            $output->write($csv);
        }

        return Command::SUCCESS;
    }

    private function fetchSingleUser(string $ident, bool $byId, OutputInterface $output): ?array
    {
        global $DB;

        if ($byId) {
            $user = $DB->get_record('user', ['id' => (int) $ident, 'deleted' => 0]);
        } else {
            $user = $DB->get_record('user', ['username' => $ident, 'deleted' => 0]);
        }

        if (!$user) {
            $output->writeln("<error>User '$ident' not found.</error>");
            return null;
        }

        return [$user];
    }

    private function fetchCourseUsers(int $courseId, OutputInterface $output): ?array
    {
        global $DB;

        if (!$DB->record_exists('course', ['id' => $courseId])) {
            $output->writeln("<error>Course $courseId not found.</error>");
            return null;
        }

        $sql = 'SELECT DISTINCT u.*
                FROM {user} u
                JOIN {user_enrolments} ue ON ue.userid = u.id
                JOIN {enrol} e ON e.id = ue.enrolid
                WHERE e.courseid = ? AND u.deleted = 0
                ORDER BY u.lastname, u.firstname';

        return array_values($DB->get_records_sql($sql, [$courseId]));
    }

    private function fetchAllUsers(): array
    {
        global $DB;

        return array_values($DB->get_records('user', ['deleted' => 0], 'lastname, firstname'));
    }
}
