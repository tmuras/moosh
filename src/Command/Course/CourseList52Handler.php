<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Course;

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
 * course:list implementation for Moodle 5.2.
 *
 * TODO: Replace with Moodle 5.2-specific implementation.
 * Currently identical to the 5.1 handler.
 */
class CourseList52Handler extends BaseHandler
{
    use CourseListHelperTrait;
    use BooleanFilterTrait;
    use NumericFilterTrait;

    /** @var string[]|null Cached list of installed module names. */
    private ?array $installedModules = null;

    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    /**
     * Return installed Moodle module names, cached for the lifetime of the handler.
     *
     * @return string[]
     */
    private function getInstalledModules(): array
    {
        if ($this->installedModules === null) {
            global $DB;
            $this->installedModules = $DB->get_fieldset_select('modules', 'name', '');
        }

        return $this->installedModules;
    }

    protected function supportedBooleanFlags(): array
    {
        return [
            'visible' => 'Course is visible',
            'empty' => 'Course has no content',
            'active' => 'Course has log activity in the last month',
        ];
    }

    protected function supportedNumericMetrics(): array
    {
        return [
            'users-enrolled' => 'Number of enrolled users',
            'questions' => 'Number of Question Bank questions',
            'activities' => 'Number of course activities (forum, assign, quiz, etc.)',
            'mod-NAME' => 'Number of a specific activity type (e.g. mod-forum, mod-quiz, mod-assign)',
        ];
    }

    protected function isMetricSupported(string $metric): bool
    {
        if (str_starts_with($metric, 'mod-') && strlen($metric) > 4) {
            $modName = substr($metric, 4);

            return in_array($modName, $this->getInstalledModules(), true);
        }

        return array_key_exists($metric, $this->supportedNumericMetrics());
    }

    protected function resolveNumericMetric(string $metric, int $courseId): int
    {
        global $DB;

        if (str_starts_with($metric, 'mod-')) {
            $modName = substr($metric, 4);
            return (int) $DB->count_records_sql(
                "SELECT COUNT(cm.id)
                   FROM {course_modules} cm
                   JOIN {modules} m ON m.id = cm.module
                  WHERE cm.course = ?
                    AND m.name = ?",
                [$courseId, $modName],
            );
        }

        return match ($metric) {
            'users-enrolled' => (int) $DB->count_records_sql(
                "SELECT COUNT(DISTINCT ue.userid)
                   FROM {user_enrolments} ue
                   JOIN {enrol} e ON e.id = ue.enrolid
                  WHERE e.courseid = ?",
                [$courseId],
            ),
            'questions' => (int) $DB->count_records_sql(
                "SELECT COUNT(qbe.id)
                   FROM {question_bank_entries} qbe
                   JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                   JOIN {context} ctx ON ctx.id = qc.contextid
                  WHERE ctx.contextlevel = 50
                    AND ctx.instanceid = ?",
                [$courseId],
            ),
            'activities' => (int) $DB->count_records('course_modules', ['course' => $courseId]),
            default => throw new \InvalidArgumentException("Unknown metric '$metric'"),
        };
    }

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument(
                'search',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'SQL WHERE fragments to filter courses',
            )
            ->addOption('idnumber', null, InputOption::VALUE_NONE, 'Include the idnumber column')
            ->addOption('id-only', 'i', InputOption::VALUE_NONE, 'Display only course IDs')
            ->addOption('category', 'c', InputOption::VALUE_REQUIRED, 'Limit to courses in this category ID (includes subcategories)')
            ->addOption('fields', 'f', InputOption::VALUE_REQUIRED, 'Comma-separated list of fields to show')
            ->addOption('sql', null, InputOption::VALUE_REQUIRED, 'SQL WHERE fragment to filter courses (e.g. "shortname = \'TC101\'")')
            ->addOption('stdin', null, InputOption::VALUE_NONE, 'Read space-separated course IDs from stdin to filter results');
        $this->configureBooleanFilters($command);
        $this->configureNumericFilters($command);
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);

        $verbose->step('Loading Moodle course library');
        require_once $CFG->dirroot . '/course/lib.php';

        $showIdnumber = $input->getOption('idnumber');
        $idOnly = $input->getOption('id-only');
        $categoryId = $input->getOption('category');
        $fieldsRaw = $input->getOption('fields');
        $sqlOption = $input->getOption('sql');
        $searchFragments = $input->getArgument('search');
        if ($sqlOption !== null) {
            $searchFragments[] = $sqlOption;
        }

        $verbose->section('Query Construction');

        $filters = $this->parseBooleanFilters($input);
        $visible = $filters['visible'];
        $empty = $filters['empty'];
        $active = $filters['active'];

        $verbose->detail('Filter: visible', $visible === null ? 'any' : ($visible ? 'yes' : 'no'));
        $verbose->detail('Filter: empty', $empty === null ? 'any' : ($empty ? 'yes' : 'no'));
        $verbose->detail('Filter: active', $active === null ? 'any' : ($active ? 'yes' : 'no'));
        if ($categoryId !== null) {
            $verbose->detail('Category ID', $categoryId);
        }
        if ($searchFragments) {
            $verbose->detail('Search fragments', implode(' ', $searchFragments));
        }

        $fields = $fieldsRaw ? array_map('trim', explode(',', $fieldsRaw)) : null;
        if ($fields) {
            $verbose->detail('Custom fields', implode(', ', $fields));
        }

        $verbose->step('Building SQL query');

        // Build query.
        $select = ['c.id', 'c.category'];
        if ($showIdnumber) {
            $select[] = 'c.idnumber';
        }
        if ($empty !== null) {
            $select[] = 'COUNT(m.id) AS modules';
        }
        $select[] = 'c.shortname';
        $select[] = 'c.fullname';
        $select[] = 'c.visible';

        $sql = 'SELECT ' . implode(', ', $select) . ' FROM {course} c';

        if ($empty !== null) {
            $sql .= ' LEFT JOIN {course_modules} m ON c.id = m.course';
        }

        $where = ["'1' = '1'"];
        $params = [];

        if ($categoryId !== null) {
            $verbose->info('Resolving category tree for ID ' . $categoryId);
            $category = \core_course_category::get((int) $categoryId);
            $categoryIds = $this->getCategoryIds($category);
            $verbose->detail('Category IDs (incl. children)', implode(', ', $categoryIds));
            [$inSql, $inParams] = $DB->get_in_or_equal($categoryIds);
            $where[] = "c.category $inSql";
            $params = array_merge($params, $inParams);
        }

        if ($searchFragments) {
            $where[] = '(' . implode(' ', $searchFragments) . ')';
        }

        if ($active !== null) {
            $cutoff = $this->clock->now()->modify('-1 month')->getTimestamp();
            $existsSql = 'EXISTS (SELECT 1 FROM {logstore_standard_log} l WHERE l.courseid = c.id AND l.timecreated >= ?)';
            $where[] = $active ? $existsSql : "NOT $existsSql";
            $params[] = $cutoff;
            $verbose->detail('Activity cutoff', date('Y-m-d H:i:s', $cutoff));
        }

        $sql .= ' WHERE ' . implode(' AND ', $where);

        if ($empty === true) {
            $sql .= ' GROUP BY c.id HAVING COUNT(m.id) = 0';
        } elseif ($empty === false) {
            $sql .= ' GROUP BY c.id HAVING COUNT(m.id) > 0';
        }

        $verbose->done('SQL query built');
        $verbose->info('SQL: ' . $sql);
        if ($params) {
            $verbose->info('Params: ' . implode(', ', array_map('strval', $params)));
        }

        $verbose->step('Executing database query');
        $courses = $DB->get_records_sql($sql, $params ?: null);
        $verbose->done('Query returned ' . count($courses) . ' course(s)');

        // Secondary filter for truly empty courses (no non-empty sections).
        if ($empty === true) {
            $verbose->step('Applying secondary empty-course filter (checking sections)');
            $beforeCount = count($courses);
            $sectionSql = "SELECT COUNT(*) AS c FROM {course_sections} WHERE course = ? AND summary <> ''";
            foreach ($courses as $id => $course) {
                $sections = $DB->get_record_sql($sectionSql, [$course->id]);
                if ($sections->c > 0) {
                    unset($courses[$id]);
                }
            }
            $verbose->done('Filtered out ' . ($beforeCount - count($courses)) . ' non-empty course(s)');
        }

        $numericFilters = $this->parseNumericFilters($input);
        if (!empty($numericFilters)) {
            $verbose->step('Applying numeric filters');
            $courses = $this->applyNumericFilters($courses, $numericFilters);
            $verbose->done(count($courses) . ' course(s) remaining after numeric filters');
        }

        $stdinIds = $this->readStdinIds($input);
        if ($stdinIds !== null) {
            $verbose->step('Filtering by stdin IDs: ' . implode(', ', $stdinIds));
        }
        $courses = $this->filterByStdinIds($courses, $stdinIds);

        $verbose->step('Resolving category paths');
        $courses = $this->resolveCategoryPaths($courses);
        $verbose->done('Category paths resolved');

        $format = $idOnly ? 'oneline' : $input->getOption('output');
        $verbose->step('Rendering output in "' . $format . '" format (' . count($courses) . ' courses)');
        $this->displayCourses($courses, $input, $output, $idOnly, $visible, $fields);

        return Command::SUCCESS;
    }
}
