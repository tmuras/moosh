<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\ProfileField;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProfileFieldDelete51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'fieldid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Profile field ID(s) to delete',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $fieldIds = $input->getArgument('fieldid');

        // Validate all IDs.
        foreach ($fieldIds as $id) {
            $field = $DB->get_record('user_info_field', ['id' => (int) $id]);
            if (!$field) {
                $output->writeln("<error>Profile field with ID $id not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following fields would be deleted (use --run to execute):</info>');
            foreach ($fieldIds as $id) {
                $field = $DB->get_record('user_info_field', ['id' => (int) $id]);
                $output->writeln("  ID=$id, shortname=\"{$field->shortname}\", name=\"{$field->name}\"");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($fieldIds) . ' field(s)');

        foreach ($fieldIds as $id) {
            $id = (int) $id;
            $field = $DB->get_record('user_info_field', ['id' => $id]);

            $verbose->info("Deleting field \"{$field->shortname}\" (ID=$id)");

            // Delete user data for this field.
            $dataCount = $DB->count_records('user_info_data', ['fieldid' => $id]);
            $DB->delete_records('user_info_data', ['fieldid' => $id]);

            // Delete the field itself.
            $DB->delete_records('user_info_field', ['id' => $id]);

            $output->writeln("Deleted field \"{$field->shortname}\" (ID=$id, $dataCount data record(s) removed).");
        }

        return Command::SUCCESS;
    }
}
