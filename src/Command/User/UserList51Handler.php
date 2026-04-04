<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\User;

use Moosh2\Command\BaseHandler;
use Moosh2\Command\BooleanFilterTrait;
use Moosh2\Command\NumericFilterTrait;
use Moosh2\Output\VerboseLogger;
use Moosh2\Service\ClockInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * user:list implementation for Moodle 5.1.
 */
class UserList51Handler extends BaseHandler
{
    use UserListHelperTrait;
    use BooleanFilterTrait;
    use NumericFilterTrait;

    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    protected function supportedBooleanFlags(): array
    {
        return [
            'suspended' => 'User account is suspended',
            'confirmed' => 'User account is confirmed',
            'deleted' => 'User account is deleted',
        ];
    }

    protected function supportedNumericMetrics(): array
    {
        return [
            'courses-enrolled' => 'Number of courses the user is enrolled in',
        ];
    }

    protected function resolveNumericMetric(string $metric, int $userId): int
    {
        global $DB;

        return match ($metric) {
            'courses-enrolled' => (int) $DB->count_records_sql(
                "SELECT COUNT(DISTINCT e.courseid)
                   FROM {user_enrolments} ue
                   JOIN {enrol} e ON e.id = ue.enrolid
                  WHERE ue.userid = ?",
                [$userId],
            ),
            default => throw new \InvalidArgumentException("Unknown metric '$metric'"),
        };
    }

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument(
                'search',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'SQL WHERE fragments to filter users',
            )
            ->addOption('id-only', 'i', InputOption::VALUE_NONE, 'Display only user IDs')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum number of users to display')
            ->addOption('sort', 's', InputOption::VALUE_REQUIRED, 'Sort by field: username, email, idnumber, lastaccess')
            ->addOption('descending', 'd', InputOption::VALUE_NONE, 'Reverse sort direction')
            ->addOption('course', null, InputOption::VALUE_REQUIRED, 'Filter to users enrolled in this course ID')
            ->addOption('course-inactive', null, InputOption::VALUE_NONE, 'Only users who never accessed the specified course (requires --course)')
            ->addOption('course-role', null, InputOption::VALUE_REQUIRED, 'Filter by role shortname in the specified course (requires --course)')
            ->addOption('course-enrol-plugin', null, InputOption::VALUE_REQUIRED, 'Filter by enrolment method plugin name (requires --course)')
            ->addOption('fields', 'f', InputOption::VALUE_REQUIRED, 'Comma-separated list of fields to show')
            ->addOption('sql', null, InputOption::VALUE_REQUIRED, 'SQL WHERE fragment to filter users (e.g. "u.username = \'admin\'")')
            ->addOption('stdin', null, InputOption::VALUE_NONE, 'Read space-separated user IDs from stdin to filter results');
        $this->configureBooleanFilters($command);
        $this->configureNumericFilters($command);
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);

        $verbose->section('Query Construction');

        $idOnly = $input->getOption('id-only');
        $limit = $input->getOption('limit');
        $sort = $input->getOption('sort');
        $descending = $input->getOption('descending');
        $courseId = $input->getOption('course');
        $courseInactive = $input->getOption('course-inactive');
        $courseRole = $input->getOption('course-role');
        $courseEnrolPlugin = $input->getOption('course-enrol-plugin');
        $fieldsRaw = $input->getOption('fields');
        $sqlOption = $input->getOption('sql');
        $searchFragments = $input->getArgument('search');

        if ($sqlOption !== null) {
            $searchFragments[] = $sqlOption;
        }

        // Validate: course-dependent options require --course.
        if (($courseInactive || $courseRole !== null || $courseEnrolPlugin !== null) && $courseId === null) {
            $output->writeln('<error>--course-inactive, --course-role, and --course-enrol-plugin require --course</error>');
            return Command::FAILURE;
        }

        $filters = $this->parseBooleanFilters($input);

        $verbose->detail('Filter: suspended', $filters['suspended'] === null ? 'any' : ($filters['suspended'] ? 'yes' : 'no'));
        $verbose->detail('Filter: confirmed', $filters['confirmed'] === null ? 'any' : ($filters['confirmed'] ? 'yes' : 'no'));
        $verbose->detail('Filter: deleted', $filters['deleted'] === null ? 'any' : ($filters['deleted'] ? 'yes' : 'no'));

        $fields = $fieldsRaw ? array_map('trim', explode(',', $fieldsRaw)) : null;
        if ($fields) {
            $verbose->detail('Custom fields', implode(', ', $fields));
        }

        $verbose->step('Building SQL query');

        // Build SELECT.
        if ($fields !== null) {
            $select = array_map(fn(string $f) => "u.$f", $fields);
            // Ensure id is always selected for filtering.
            if (!in_array('u.id', $select, true)) {
                array_unshift($select, 'u.id');
            }
        } else {
            $select = ['u.id', 'u.username', 'u.email'];
        }
        $sql = 'SELECT ' . implode(', ', $select) . ' FROM {user} u';
        $params = [];

        // Course enrollment JOINs.
        if ($courseId !== null) {
            $sql .= ' JOIN {user_enrolments} ue ON ue.userid = u.id';
            $sql .= ' JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = ?';
            $params[] = (int) $courseId;

            if ($courseEnrolPlugin !== null) {
                $sql .= ' AND e.enrol = ?';
                $params[] = $courseEnrolPlugin;
            }

            if ($courseRole !== null) {
                $sql .= ' JOIN {context} ctx ON ctx.instanceid = e.courseid AND ctx.contextlevel = 50';
                $sql .= ' JOIN {role_assignments} ra ON ra.contextid = ctx.id AND ra.userid = u.id';
                $sql .= ' JOIN {role} r ON r.id = ra.roleid AND r.shortname = ?';
                $params[] = $courseRole;
            }
        }

        // Build WHERE.
        $where = ['u.id > 0'];

        // Boolean filters map directly to user columns.
        foreach (['suspended', 'confirmed', 'deleted'] as $flag) {
            if ($filters[$flag] === true) {
                $where[] = "u.$flag = 1";
            } elseif ($filters[$flag] === false) {
                $where[] = "u.$flag = 0";
            }
        }

        // Course-inactive filter.
        if ($courseInactive) {
            $where[] = 'NOT EXISTS (SELECT 1 FROM {user_lastaccess} ul WHERE ul.userid = u.id AND ul.courseid = ?)';
            $params[] = (int) $courseId;
        }

        // SQL fragments from arguments and --sql.
        if ($searchFragments) {
            $where[] = '(' . implode(' ', $searchFragments) . ')';
        }

        $sql .= ' WHERE ' . implode(' AND ', $where);

        // ORDER BY.
        if ($sort !== null) {
            $allowedSorts = ['username', 'email', 'idnumber', 'lastaccess'];
            if (!in_array($sort, $allowedSorts, true)) {
                $output->writeln("<error>Invalid sort field '$sort'. Allowed: " . implode(', ', $allowedSorts) . '</error>');
                return Command::FAILURE;
            }
            $direction = $descending ? 'DESC' : 'ASC';
            $sql .= " ORDER BY u.$sort $direction";
        }

        $verbose->done('SQL query built');
        $verbose->info('SQL: ' . $sql);
        if ($params) {
            $verbose->info('Params: ' . implode(', ', array_map('strval', $params)));
        }

        // Execute query.
        $verbose->step('Executing database query');
        $limitInt = $limit !== null ? (int) $limit : 0;
        $users = $DB->get_records_sql($sql, $params ?: null, 0, $limitInt ?: 0);
        $verbose->done('Query returned ' . count($users) . ' user(s)');

        // Apply numeric filters.
        $numericFilters = $this->parseNumericFilters($input);
        if (!empty($numericFilters)) {
            $verbose->step('Applying numeric filters');
            $users = $this->applyNumericFilters($users, $numericFilters);
            $verbose->done(count($users) . ' user(s) remaining after numeric filters');
        }

        // Stdin filtering.
        $stdinIds = $this->readStdinIds($input);
        if ($stdinIds !== null) {
            $verbose->step('Filtering by stdin IDs: ' . implode(', ', $stdinIds));
        }
        $users = $this->filterByStdinIds($users, $stdinIds);

        // Display.
        $format = $idOnly ? 'oneline' : $input->getOption('output');
        $verbose->step('Rendering output in "' . $format . '" format (' . count($users) . ' users)');
        $this->displayUsers($users, $input, $output, $idOnly, $fields);

        return Command::SUCCESS;
    }
}
