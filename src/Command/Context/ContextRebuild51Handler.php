<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Context;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * context:rebuild implementation for Moodle 5.1.
 */
class ContextRebuild51Handler extends BaseHandler
{
    private const LEVEL_NAMES = [
        10 => 'System',
        30 => 'User',
        40 => 'Course category',
        50 => 'Course',
        70 => 'Module',
        80 => 'Block',
    ];

    public function configureCommand(Command $command): void
    {
        // No arguments or options needed.
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/accesslib.php';

        // Pre-rebuild statistics.
        $verbose->step('Collecting pre-rebuild statistics');
        $totalBefore = $DB->count_records('context');
        $emptyPathsBefore = $DB->count_records_select('context', "path IS NULL OR path = ''");
        $frozenBefore = $DB->count_records_select('context', 'locked = 1');

        $output->writeln('<info>Before rebuild:</info>');
        $output->writeln("  Total contexts: $totalBefore");
        $output->writeln("  Contexts with empty paths: $emptyPathsBefore");
        $output->writeln("  Frozen contexts: $frozenBefore");

        $this->showLevelBreakdown($output, $DB, 'before');

        // Increase memory limit for large sites.
        raise_memory_limit(MEMORY_EXTRA);

        // Step 1: Clean up orphaned instances.
        $verbose->step('Cleaning up orphaned context instances');
        \context_helper::cleanup_instances();
        $output->writeln('Cleaned up orphaned context instances.');

        // Step 2: Rebuild all paths.
        $verbose->step('Rebuilding all context paths (this may take a while on large sites)');
        \context_helper::build_all_paths(true);
        $output->writeln('Rebuilt all context paths.');

        // Post-rebuild statistics.
        $verbose->step('Collecting post-rebuild statistics');
        $totalAfter = $DB->count_records('context');
        $emptyPathsAfter = $DB->count_records_select('context', "path IS NULL OR path = ''");
        $frozenAfter = $DB->count_records_select('context', 'locked = 1');

        $output->writeln('');
        $output->writeln('<info>After rebuild:</info>');
        $output->writeln("  Total contexts: $totalAfter");
        $output->writeln("  Contexts with empty paths: $emptyPathsAfter");
        $output->writeln("  Frozen contexts: $frozenAfter");

        $this->showLevelBreakdown($output, $DB, 'after');

        // Summary of changes.
        $removed = $totalBefore - $totalAfter;
        $pathsFixed = $emptyPathsBefore - $emptyPathsAfter;

        if ($removed > 0 || $pathsFixed > 0) {
            $output->writeln('');
            $output->writeln('<info>Changes:</info>');
            if ($removed > 0) {
                $output->writeln("  Orphaned contexts removed: $removed");
            }
            if ($pathsFixed > 0) {
                $output->writeln("  Empty paths fixed: $pathsFixed");
            }
        } else {
            $output->writeln('');
            $output->writeln('<info>No changes were needed — all context paths were already correct.</info>');
        }

        return Command::SUCCESS;
    }

    private function showLevelBreakdown(OutputInterface $output, object $DB, string $label): void
    {
        $byLevel = $DB->get_records_sql(
            "SELECT contextlevel, COUNT(*) AS c FROM {context} GROUP BY contextlevel ORDER BY contextlevel",
        );

        foreach ($byLevel as $row) {
            $name = self::LEVEL_NAMES[$row->contextlevel] ?? "Level {$row->contextlevel}";
            $output->writeln("  {$name}: {$row->c}");
        }
    }
}
