<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\GradeCategory;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * gradecategory:delete implementation for Moodle 5.1.
 */
class GradeCategoryDelete51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'categoryid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Grade category ID(s) to delete',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $ids = $input->getArgument('categoryid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/gradelib.php';
        require_once $CFG->libdir . '/grade/grade_category.php';
        require_once $CFG->libdir . '/grade/grade_item.php';

        // Validate all IDs first.
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid grade category ID: $id</error>");
                return Command::FAILURE;
            }
            $gc = \grade_category::fetch(['id' => $id]);
            if (!$gc) {
                $output->writeln("<error>Grade category with ID $id not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following grade categories would be deleted (use --run to execute):</info>');
            foreach ($ids as $id) {
                $gc = \grade_category::fetch(['id' => (int) $id]);
                $output->writeln("  ID=$id, name=\"{$gc->fullname}\"");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($ids) . ' grade category(ies)');

        foreach ($ids as $id) {
            $id = (int) $id;
            $gc = \grade_category::fetch(['id' => $id]);

            $verbose->info("Deleting grade category '{$gc->fullname}' (ID=$id)");
            $gc->delete('moosh');
            $verbose->done("Deleted grade category ID=$id");

            $output->writeln("Deleted grade category '{$gc->fullname}' (ID=$id).");
        }

        return Command::SUCCESS;
    }
}
