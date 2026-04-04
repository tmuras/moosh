<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Plugin;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * plugin:usage implementation for Moodle 5.1.
 */
class PluginUsage52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('contrib-only', 'c', InputOption::VALUE_NONE, 'Show only non-standard (contributed) plugins')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'Show only a specific plugin type: activity, block, format, enrol, auth, qtype, filter');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $contribOnly = $input->getOption('contrib-only');
        $typeFilter = $input->getOption('type');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/questionlib.php';
        require_once $CFG->dirroot . '/course/lib.php';
        require_once $CFG->libdir . '/filterlib.php';

        $rows = [];

        $types = $typeFilter !== null
            ? [$typeFilter]
            : ['activity', 'block', 'format', 'enrol', 'auth', 'qtype', 'filter'];

        foreach ($types as $type) {
            $verbose->step("Collecting $type plugin usage");
            $typeRows = match ($type) {
                'activity' => $this->collectActivities($contribOnly, $verbose),
                'block' => $this->collectBlocks($contribOnly, $verbose),
                'format' => $this->collectFormats($contribOnly, $verbose),
                'enrol' => $this->collectEnrolments($contribOnly, $verbose),
                'auth' => $this->collectAuthentication($contribOnly, $verbose),
                'qtype' => $this->collectQuestionTypes($contribOnly, $verbose),
                'filter' => $this->collectFilters($contribOnly, $verbose),
                default => [],
            };
            $rows = array_merge($rows, $typeRows);
        }

        $verbose->step('Rendering output');

        $headers = ['type', 'plugin', 'name', 'count', 'status'];

        if ($format === 'table') {
            $table = new Table($output);
            $table->setHeaders($headers);
            $table->setRows($rows);
            $table->render();
        } else {
            $formatter = new ResultFormatter($output, $format);
            $formatter->display($headers, $rows);
        }

        return Command::SUCCESS;
    }

    private function isStandard(string $type, string $name): bool
    {
        $standard = \core_plugin_manager::standard_plugins_list($type);
        return $standard !== false && in_array($name, $standard, true);
    }

    /**
     * @return array<int, array{string, string, string, int|string, string}>
     */
    private function collectActivities(bool $contribOnly, VerboseLogger $verbose): array
    {
        global $DB;

        $rows = [];
        $modules = $DB->get_records('modules', [], 'name ASC');

        foreach ($modules as $module) {
            if ($contribOnly && $this->isStandard('mod', $module->name)) {
                continue;
            }

            try {
                $count = $DB->count_records_select($module->name, 'course <> 0');
            } catch (\Throwable $e) {
                $count = 0;
            }

            $displayName = get_string_manager()->string_exists('modulename', $module->name)
                ? get_string('modulename', $module->name)
                : $module->name;

            $status = $module->visible ? 'enabled' : 'disabled';
            $rows[] = ['activity', $module->name, $displayName, $count, $status];
        }

        return $rows;
    }

    /**
     * @return array<int, array{string, string, string, int, string}>
     */
    private function collectBlocks(bool $contribOnly, VerboseLogger $verbose): array
    {
        global $CFG, $DB;

        $rows = [];
        $blocks = $DB->get_records('block', [], 'name ASC');

        foreach ($blocks as $block) {
            if ($contribOnly && $this->isStandard('block', $block->name)) {
                continue;
            }

            $count = $DB->count_records('block_instances', ['blockname' => $block->name]);

            if (file_exists("$CFG->dirroot/blocks/{$block->name}/block_{$block->name}.php")
                && get_string_manager()->string_exists('pluginname', 'block_' . $block->name)) {
                $displayName = get_string('pluginname', 'block_' . $block->name);
            } else {
                $displayName = $block->name;
            }

            $status = $block->visible ? 'enabled' : 'disabled';
            $rows[] = ['block', $block->name, $displayName, $count, $status];
        }

        return $rows;
    }

    /**
     * @return array<int, array{string, string, string, int, string}>
     */
    private function collectFormats(bool $contribOnly, VerboseLogger $verbose): array
    {
        global $DB;

        $rows = [];
        $courseFormats = get_sorted_course_formats(true);
        $usages = $DB->get_records_sql(
            "SELECT format, COUNT(*) AS count FROM {course} WHERE id > 1 GROUP BY format",
        );

        foreach ($courseFormats as $formatKey) {
            if ($contribOnly && $this->isStandard('format', $formatKey)) {
                continue;
            }

            $count = isset($usages[$formatKey]) ? (int) $usages[$formatKey]->count : 0;
            $displayName = get_string_manager()->string_exists('pluginname', "format_$formatKey")
                ? get_string('pluginname', "format_$formatKey")
                : $formatKey;

            $rows[] = ['format', $formatKey, $displayName, $count, 'enabled'];
        }

        return $rows;
    }

    /**
     * @return array<int, array{string, string, string, int, string}>
     */
    private function collectEnrolments(bool $contribOnly, VerboseLogger $verbose): array
    {
        global $DB;

        $rows = [];
        $all = enrol_get_plugins(false);
        $enabled = enrol_get_plugins(true);

        foreach (array_keys($all) as $enrol) {
            if ($contribOnly && $this->isStandard('enrol', $enrol)) {
                continue;
            }

            $count = $DB->count_records('enrol', ['enrol' => $enrol]);

            $displayName = get_string_manager()->string_exists('pluginname', 'enrol_' . $enrol)
                ? get_string('pluginname', 'enrol_' . $enrol)
                : $enrol;

            $status = isset($enabled[$enrol]) ? 'enabled' : 'disabled';
            $rows[] = ['enrol', $enrol, $displayName, $count, $status];
        }

        return $rows;
    }

    /**
     * @return array<int, array{string, string, string, int, string}>
     */
    private function collectAuthentication(bool $contribOnly, VerboseLogger $verbose): array
    {
        global $CFG, $DB;

        $rows = [];
        $available = \core_component::get_plugin_list('auth');

        $enabledList = !empty($CFG->auth) ? explode(',', $CFG->auth) : [];

        foreach (array_keys($available) as $auth) {
            if ($contribOnly && $this->isStandard('auth', $auth)) {
                continue;
            }

            $count = $DB->count_records('user', ['auth' => $auth, 'deleted' => 0]);

            $authPlugin = get_auth_plugin($auth);
            $displayName = $authPlugin->get_title();

            $status = in_array($auth, $enabledList, true) || $auth === 'manual' ? 'enabled' : 'disabled';
            $rows[] = ['auth', $auth, $displayName, $count, $status];
        }

        return $rows;
    }

    /**
     * @return array<int, array{string, string, string, int, string}>
     */
    private function collectQuestionTypes(bool $contribOnly, VerboseLogger $verbose): array
    {
        global $DB;

        $rows = [];
        // Moodle 5.x uses question_bank_entries + question_versions.
        $counts = $DB->get_records_sql(
            "SELECT qbe.questioncategoryid AS qcat, q.qtype, COUNT(q.id) AS numquestions
               FROM {question} q
               JOIN {question_versions} qv ON qv.questionid = q.id
               JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
              WHERE qv.status = 'ready'
              GROUP BY q.qtype, qbe.questioncategoryid",
        );
        // Aggregate by qtype.
        $qtypeCounts = [];
        foreach ($counts as $row) {
            if (!isset($qtypeCounts[$row->qtype])) {
                $qtypeCounts[$row->qtype] = 0;
            }
            $qtypeCounts[$row->qtype] += (int) $row->numquestions;
        }
        $qtypes = \question_bank::get_all_qtypes();

        foreach ($qtypes as $qtypeName => $qtype) {
            if ($contribOnly && $this->isStandard('qtype', $qtypeName)) {
                continue;
            }

            $count = $qtypeCounts[$qtypeName] ?? 0;

            $displayName = $qtype->local_name();
            $rows[] = ['qtype', $qtypeName, $displayName, $count, 'enabled'];
        }

        return $rows;
    }

    /**
     * @return array<int, array{string, string, string, int|string, string}>
     */
    private function collectFilters(bool $contribOnly, VerboseLogger $verbose): array
    {
        $rows = [];
        $pluginInfos = \core_plugin_manager::instance()->get_plugins_of_type('filter');
        $states = filter_get_global_states();

        foreach ($pluginInfos as $pluginName => $pluginInfo) {
            if ($contribOnly && $this->isStandard('filter', $pluginName)) {
                continue;
            }

            $active = isset($states[$pluginName]) && $states[$pluginName]->active != TEXTFILTER_DISABLED;
            $status = $active ? 'enabled' : 'disabled';
            $displayName = $pluginInfo->displayname;

            // Filters don't have a simple "count" — use 1/0 for active status.
            $rows[] = ['filter', $pluginName, $displayName, $active ? 1 : 0, $status];
        }

        return $rows;
    }
}
