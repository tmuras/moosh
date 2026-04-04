<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Enrol;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * enrol:mod implementation for Moodle 5.1.
 */
class EnrolMod51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('instanceid', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Enrolment instance ID(s)')
            ->addOption('enabled', null, InputOption::VALUE_REQUIRED, 'Set status: 1=enable, 0=disable')
            ->addOption('roleid', null, InputOption::VALUE_REQUIRED, 'Set default role ID')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Set instance name');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $instanceIds = $input->getArgument('instanceid');
        $newEnabled = $input->getOption('enabled');
        $newRoleId = $input->getOption('roleid');
        $newName = $input->getOption('name');

        require_once $CFG->libdir . '/enrollib.php';

        $hasChanges = $newEnabled !== null || $newRoleId !== null || $newName !== null;
        if (!$hasChanges) {
            $output->writeln('<error>No modifications specified. Use --enabled, --roleid, or --name.</error>');
            return Command::FAILURE;
        }

        // Validate all instances.
        $instances = [];
        foreach ($instanceIds as $id) {
            $id = (int) $id;
            $instance = $DB->get_record('enrol', ['id' => $id]);
            if (!$instance) {
                $output->writeln("<error>Enrolment instance with ID $id not found.</error>");
                return Command::FAILURE;
            }
            $instances[] = $instance;
        }

        // Build changes summary.
        $changes = [];
        if ($newEnabled !== null) {
            $label = (int) $newEnabled ? 'enabled' : 'disabled';
            $changes[] = "status → $label";
        }
        if ($newRoleId !== null) {
            $changes[] = "roleid → $newRoleId";
        }
        if ($newName !== null) {
            $changes[] = "name → \"$newName\"";
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following modification(s) would be applied (use --run to execute):</info>');
            foreach ($instances as $inst) {
                $output->writeln("  ID={$inst->id} ({$inst->enrol}): " . implode(', ', $changes));
            }
            return Command::SUCCESS;
        }

        $verbose->step('Modifying enrolment instance(s)');
        $rows = [];

        foreach ($instances as $inst) {
            $plugin = enrol_get_plugin($inst->enrol);

            if ($newEnabled !== null && $plugin) {
                $status = (int) $newEnabled ? ENROL_INSTANCE_ENABLED : ENROL_INSTANCE_DISABLED;
                $plugin->update_status($inst, $status);
                // Reload after status change.
                $inst = $DB->get_record('enrol', ['id' => $inst->id]);
            }

            if ($newRoleId !== null) {
                $DB->set_field('enrol', 'roleid', (int) $newRoleId, ['id' => $inst->id]);
            }

            if ($newName !== null) {
                $DB->set_field('enrol', 'name', $newName, ['id' => $inst->id]);
            }

            $inst = $DB->get_record('enrol', ['id' => $inst->id]);
            $rows[] = [
                $inst->id,
                $inst->enrol,
                $inst->name ?: '(default)',
                $inst->status == ENROL_INSTANCE_ENABLED ? 'enabled' : 'disabled',
                $inst->roleid,
                $inst->courseid,
            ];
        }

        $headers = ['id', 'enrol', 'name', 'status', 'roleid', 'courseid'];
        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
