<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Activity;

use core_courseformat\formatactions;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * activity:delete implementation for Moodle 5.2.
 *
 * Uses core_courseformat\formatactions API instead of deprecated course_delete_module().
 */
class ActivityDelete52Handler extends ActivityDelete51Handler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $cmids = $input->getArgument('cmid');

        // Validate all cmids first.
        foreach ($cmids as $cmid) {
            $cmid = (int) $cmid;
            if ($cmid <= 0) {
                $output->writeln("<error>Invalid course module ID: $cmid</error>");
                return Command::FAILURE;
            }
            $cm = $DB->get_record('course_modules', ['id' => $cmid]);
            if (!$cm) {
                $output->writeln("<error>Course module with ID $cmid not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following activities would be deleted (use --run to execute):</info>');
            foreach ($cmids as $cmid) {
                $cm = $DB->get_record('course_modules', ['id' => (int) $cmid]);
                $module = $DB->get_record('modules', ['id' => $cm->module]);
                $output->writeln("  cmid=$cmid (type: {$module->name}, course: {$cm->course})");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($cmids) . ' activity(ies)');

        foreach ($cmids as $cmid) {
            $cmid = (int) $cmid;
            $cm = $DB->get_record('course_modules', ['id' => $cmid]);
            $module = $DB->get_record('modules', ['id' => $cm->module]);

            $verbose->info("Deleting {$module->name} (cmid=$cmid) from course {$cm->course}");
            formatactions::cm($cm->course)->delete($cmid);
            $verbose->done("Deleted cmid=$cmid");

            $output->writeln("Deleted {$module->name} activity (cmid=$cmid) from course {$cm->course}.");
        }

        return Command::SUCCESS;
    }
}
