<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Category;

use Moosh2\Command\BaseHandler;
use Moosh2\Command\BooleanFilterTrait;
use Moosh2\Command\NumericFilterTrait;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * category:list implementation for Moodle 5.1.
 */
class CategoryList52Handler extends BaseHandler
{
    use CategoryListHelperTrait;
    use BooleanFilterTrait;
    use NumericFilterTrait;

    protected function supportedBooleanFlags(): array
    {
        return [
            'visible' => 'Category is visible',
            'empty' => 'Category has no courses',
            'top-level' => 'Category is at the top level (no parent)',
        ];
    }

    protected function supportedNumericMetrics(): array
    {
        return [
            'courses' => 'Number of courses in the category',
            'subcategories' => 'Number of direct subcategories',
        ];
    }

    protected function resolveNumericMetric(string $metric, int $categoryId): int
    {
        global $DB;

        return match ($metric) {
            'courses' => (int) $DB->count_records('course', ['category' => $categoryId]),
            'subcategories' => (int) $DB->count_records('course_categories', ['parent' => $categoryId]),
            default => throw new \InvalidArgumentException("Unknown metric '$metric'"),
        };
    }

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument(
                'search',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'SQL WHERE fragments to filter categories',
            )
            ->addOption('id-only', 'i', InputOption::VALUE_NONE, 'Display only category IDs')
            ->addOption('parent', null, InputOption::VALUE_REQUIRED, 'Filter to categories with this parent ID')
            ->addOption('fields', 'f', InputOption::VALUE_REQUIRED, 'Comma-separated list of fields to show')
            ->addOption('sql', null, InputOption::VALUE_REQUIRED, 'SQL WHERE fragment to filter categories')
            ->addOption('stdin', null, InputOption::VALUE_NONE, 'Read space-separated category IDs from stdin to filter results');
        $this->configureBooleanFilters($command);
        $this->configureNumericFilters($command);
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);

        $verbose->section('Query Construction');

        $idOnly = $input->getOption('id-only');
        $parentId = $input->getOption('parent');
        $fieldsRaw = $input->getOption('fields');
        $sqlOption = $input->getOption('sql');
        $searchFragments = $input->getArgument('search');

        if ($sqlOption !== null) {
            $searchFragments[] = $sqlOption;
        }

        $filters = $this->parseBooleanFilters($input);
        $visible = $filters['visible'];
        $empty = $filters['empty'];
        $topLevel = $filters['top-level'];

        $verbose->detail('Filter: visible', $visible === null ? 'any' : ($visible ? 'yes' : 'no'));
        $verbose->detail('Filter: empty', $empty === null ? 'any' : ($empty ? 'yes' : 'no'));
        $verbose->detail('Filter: top-level', $topLevel === null ? 'any' : ($topLevel ? 'yes' : 'no'));

        $fields = $fieldsRaw ? array_map('trim', explode(',', $fieldsRaw)) : null;

        $verbose->step('Building SQL query');

        // Build SELECT.
        $select = ['cc.id', 'cc.name', 'cc.parent', 'cc.depth', 'cc.path', 'cc.coursecount', 'cc.visible'];
        $sql = 'SELECT ' . implode(', ', $select) . ' FROM {course_categories} cc';
        $params = [];

        // Build WHERE.
        $where = ['1 = 1'];

        if ($parentId !== null) {
            $where[] = 'cc.parent = ?';
            $params[] = (int) $parentId;
        }

        if ($visible === true) {
            $where[] = 'cc.visible = 1';
        } elseif ($visible === false) {
            $where[] = 'cc.visible = 0';
        }

        if ($empty === true) {
            $where[] = 'cc.coursecount = 0';
        } elseif ($empty === false) {
            $where[] = 'cc.coursecount > 0';
        }

        if ($topLevel === true) {
            $where[] = 'cc.parent = 0';
        } elseif ($topLevel === false) {
            $where[] = 'cc.parent > 0';
        }

        if ($searchFragments) {
            $where[] = '(' . implode(' ', $searchFragments) . ')';
        }

        $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY cc.sortorder';

        $verbose->done('SQL query built');
        $verbose->info('SQL: ' . $sql);

        $verbose->step('Executing database query');
        $categories = $DB->get_records_sql($sql, $params ?: null);
        $verbose->done('Query returned ' . count($categories) . ' category(ies)');

        // Apply numeric filters.
        $numericFilters = $this->parseNumericFilters($input);
        if (!empty($numericFilters)) {
            $verbose->step('Applying numeric filters');
            $categories = $this->applyNumericFilters($categories, $numericFilters);
            $verbose->done(count($categories) . ' category(ies) remaining after numeric filters');
        }

        // Stdin filtering.
        $stdinIds = $this->readStdinIds($input);
        if ($stdinIds !== null) {
            $verbose->step('Filtering by stdin IDs: ' . implode(', ', $stdinIds));
        }
        $categories = $this->filterByStdinIds($categories, $stdinIds);

        // Resolve path names.
        foreach ($categories as $cat) {
            $cat->path = $this->resolvePathNames($cat->path);
        }

        // Display.
        $format = $idOnly ? 'oneline' : $input->getOption('output');
        $verbose->step('Rendering output in "' . $format . '" format (' . count($categories) . ' categories)');
        $this->displayCategories($categories, $input, $output, $idOnly, $fields);

        return Command::SUCCESS;
    }
}
