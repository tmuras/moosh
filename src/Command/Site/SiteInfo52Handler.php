<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Site;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SiteInfo52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        // No additional arguments or options — output format comes from global --output.
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');

        $data = [];

        // ── Site identity ──────────────────────────────────────────
        $verbose->step('Collecting site identity');

        $data['Site name'] = $CFG->sitename ?? $DB->get_field('course', 'fullname', ['id' => 1]);
        $data['URL'] = $CFG->wwwroot;
        $data['Moodle version'] = $CFG->release ?? '';
        $data['Moodle branch'] = $CFG->branch ?? '';
        $data['Moodle build'] = $CFG->version ?? '';
        $data['Database type'] = $CFG->dbtype;
        $data['Database name'] = $CFG->dbname;
        $data['PHP version'] = phpversion();
        $data['Server OS'] = php_uname('s') . ' ' . php_uname('r');
        $data['Dataroot'] = $CFG->dataroot;

        // ── Entity counts ──────────────────────────────────────────
        $verbose->step('Counting entities');

        $data['Courses'] = $DB->count_records('course') - 1; // exclude site course
        $data['Categories'] = $DB->count_records('course_categories');
        $data['Users (total)'] = $DB->count_records('user', ['deleted' => 0]);
        $data['Users (active)'] = $DB->count_records_select('user', 'deleted = 0 AND suspended = 0');
        $data['Users (suspended)'] = $DB->count_records_select('user', 'deleted = 0 AND suspended = 1');
        $data['Enrolments'] = $DB->count_records('user_enrolments');
        $data['Activities'] = $DB->count_records('course_modules');
        $data['Roles'] = $DB->count_records('role');
        $data['Cohorts'] = $DB->count_records('cohort');
        $data['Groups'] = $DB->count_records('groups');
        $data['Badges'] = $DB->count_records('badge');

        // ── Files ──────────────────────────────────────────────────
        $verbose->step('Collecting file statistics');

        $data['File references'] = $DB->count_records('files');
        $uniqueFiles = $DB->get_record_sql(
            'SELECT COUNT(DISTINCT contenthash) AS cnt FROM {files} WHERE filename <> \'.\'',
        );
        $data['Unique files'] = $uniqueFiles->cnt ?? 0;

        $totalSize = $DB->get_record_sql(
            'SELECT SUM(filesize) AS total FROM {files}',
        );
        $data['Total file size (refs)'] = $this->formatBytes((int) ($totalSize->total ?? 0));

        $uniqueSize = $DB->get_record_sql(
            'SELECT SUM(filesize) AS total FROM (SELECT DISTINCT contenthash, filesize FROM {files} WHERE filename <> \'.\') sub',
        );
        $data['Total file size (unique)'] = $this->formatBytes((int) ($uniqueSize->total ?? 0));

        // ── Database size ──────────────────────────────────────────
        $verbose->step('Collecting database statistics');

        $dbStats = $this->getDatabaseSize($CFG->dbtype, $CFG->dbname);
        if ($dbStats !== null) {
            $data['Database size'] = $this->formatBytes($dbStats['size']);
            $data['Database tables'] = $dbStats['tables'];
            $data['Database rows'] = $dbStats['rows'];
            if (!empty($dbStats['top_tables'])) {
                foreach ($dbStats['top_tables'] as $i => $table) {
                    $n = $i + 1;
                    $data["Largest table #$n"] = "{$table['name']} ({$this->formatBytes($table['size'])}, {$table['rows']} rows)";
                }
            }
        }

        // ── Disk usage ─────────────────────────────────────────────
        $verbose->step('Checking disk usage');

        $datarootSize = $this->getDirectorySize($CFG->dataroot);
        if ($datarootSize !== null) {
            $data['Dataroot disk usage'] = $this->formatBytes($datarootSize);
        }

        $filedir = $CFG->dataroot . '/filedir';
        if (is_dir($filedir)) {
            $filedirSize = $this->getDirectorySize($filedir);
            if ($filedirSize !== null) {
                $data['Filedir disk usage'] = $this->formatBytes($filedirSize);
            }
        }

        // ── Logs ───────────────────────────────────────────────────
        $verbose->step('Collecting log statistics');

        if ($DB->get_manager()->table_exists('logstore_standard_log')) {
            $data['Log entries'] = $DB->count_records('logstore_standard_log');

            $lastLog = $DB->get_record_sql(
                'SELECT MAX(timecreated) AS ts FROM {logstore_standard_log}',
            );
            if ($lastLog && $lastLog->ts) {
                $data['Last log entry'] = date('Y-m-d H:i:s', (int) $lastLog->ts);
            }
        }

        // ── Activity / Cron ────────────────────────────────────────
        $verbose->step('Checking recent activity');

        $lastCron = $DB->get_record_sql(
            'SELECT MAX(lastruntime) AS ts FROM {task_scheduled}',
        );
        if ($lastCron && $lastCron->ts) {
            $data['Last cron run'] = date('Y-m-d H:i:s', (int) $lastCron->ts);
        }

        $fiveMinAgo = time() - 300;
        if ($DB->get_manager()->table_exists('logstore_standard_log')) {
            $online = $DB->get_record_sql(
                'SELECT COUNT(DISTINCT userid) AS cnt FROM {logstore_standard_log} WHERE timecreated > ?',
                [$fiveMinAgo],
            );
            $data['Online users (5 min)'] = $online->cnt ?? 0;
        }

        // ── Plugins ────────────────────────────────────────────────
        $verbose->step('Counting plugins');

        $pluginManager = \core_plugin_manager::instance();
        $allPlugins = $pluginManager->get_plugins();
        $total = 0;
        $contrib = 0;
        foreach ($allPlugins as $type => $plugins) {
            foreach ($plugins as $plugin) {
                $total++;
                if (!$plugin->is_standard()) {
                    $contrib++;
                }
            }
        }
        $data['Plugins (total)'] = $total;
        $data['Plugins (contrib)'] = $contrib;

        // ── Output ─────────────────────────────────────────────────
        $verbose->done('Site info collected');

        $headers = ['Metric', 'Value'];
        $rows = [];
        foreach ($data as $metric => $value) {
            $rows[] = [$metric, (string) $value];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));
        $i = min($i, count($units) - 1);

        return round($bytes / (1024 ** $i), 2) . ' ' . $units[$i];
    }

    /**
     * @return array{size: int, tables: int, rows: int, top_tables: list<array{name: string, size: int, rows: int}>}|null
     */
    private function getDatabaseSize(string $dbtype, string $dbname): ?array
    {
        global $DB;

        if (str_contains($dbtype, 'pgsql') || str_contains($dbtype, 'postgres')) {
            return $this->getPostgresStats($dbname);
        }

        if (str_contains($dbtype, 'mysql') || str_contains($dbtype, 'maria')) {
            return $this->getMysqlStats($dbname);
        }

        return null;
    }

    private function getPostgresStats(string $dbname): array
    {
        global $DB;

        $sizeRecord = $DB->get_record_sql(
            "SELECT pg_database_size(?) AS dbsize",
            [$dbname],
        );
        $size = (int) ($sizeRecord->dbsize ?? 0);

        $tables = $DB->get_records_sql(
            "SELECT relname AS name,
                    pg_total_relation_size(quote_ident(relname)) AS size,
                    GREATEST(reltuples::bigint, 0) AS rowcount
             FROM pg_class
             WHERE relkind = 'r' AND relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = 'public')
             ORDER BY size DESC",
        );

        $tableCount = count($tables);
        $totalRows = 0;
        $topTables = [];
        $i = 0;

        foreach ($tables as $t) {
            $totalRows += (int) $t->rowcount;
            if ($i < 5) {
                $topTables[] = ['name' => $t->name, 'size' => (int) $t->size, 'rows' => (int) $t->rowcount];
            }
            $i++;
        }

        return ['size' => $size, 'tables' => $tableCount, 'rows' => $totalRows, 'top_tables' => $topTables];
    }

    private function getMysqlStats(string $dbname): array
    {
        global $DB;

        $tables = $DB->get_records_sql(
            "SELECT table_name AS name,
                    ROUND(data_length + index_length) AS size,
                    table_rows AS rowcount
             FROM information_schema.TABLES
             WHERE table_schema = ?
             ORDER BY size DESC",
            [$dbname],
        );

        $totalSize = 0;
        $totalRows = 0;
        $tableCount = 0;
        $topTables = [];

        foreach ($tables as $t) {
            $totalSize += (int) $t->size;
            $totalRows += (int) $t->rowcount;
            $tableCount++;
            if (count($topTables) < 5) {
                $topTables[] = ['name' => $t->name, 'size' => (int) $t->size, 'rows' => (int) $t->rowcount];
            }
        }

        return ['size' => $totalSize, 'tables' => $tableCount, 'rows' => $totalRows, 'top_tables' => $topTables];
    }

    private function getDirectorySize(string $path): ?int
    {
        if (!is_dir($path)) {
            return null;
        }

        $result = @exec("du -sb " . escapeshellarg($path) . " 2>/dev/null", $outputLines, $exitCode);
        if ($exitCode === 0 && !empty($outputLines)) {
            $parts = preg_split('/\s+/', trim($outputLines[0]), 2);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                return (int) $parts[0];
            }
        }

        return null;
    }
}
