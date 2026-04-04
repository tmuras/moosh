<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\GradeItem;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * gradeitem:delete implementation for Moodle 5.1.
 */
class GradeItemDelete52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'itemid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Grade item ID(s) to delete',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $ids = $input->getArgument('itemid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/gradelib.php';
        require_once $CFG->libdir . '/grade/grade_item.php';

        // Validate all IDs first.
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid grade item ID: $id</error>");
                return Command::FAILURE;
            }
            $gi = \grade_item::fetch(['id' => $id]);
            if (!$gi) {
                $output->writeln("<error>Grade item with ID $id not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following grade items would be deleted (use --run to execute):</info>');
            foreach ($ids as $id) {
                $gi = \grade_item::fetch(['id' => (int) $id]);
                $output->writeln("  ID=$id, name=\"{$gi->itemname}\"");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($ids) . ' grade item(s)');

        foreach ($ids as $id) {
            $id = (int) $id;
            $gi = \grade_item::fetch(['id' => $id]);

            $verbose->info("Deleting grade item '{$gi->itemname}' (ID=$id)");
            $gi->delete('moosh');
            $verbose->done("Deleted grade item ID=$id");

            $output->writeln("Deleted grade item '{$gi->itemname}' (ID=$id).");
        }

        return Command::SUCCESS;
    }
}
