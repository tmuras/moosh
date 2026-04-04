<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\QuestionCategory;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * questioncategory:delete implementation for Moodle 5.1.
 */
class QuestionCategoryDelete51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'categoryid',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Question category ID(s) to delete',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $ids = $input->getArgument('categoryid');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/questionlib.php';

        // Validate all IDs first.
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                $output->writeln("<error>Invalid question category ID: $id</error>");
                return Command::FAILURE;
            }
            $cat = $DB->get_record('question_categories', ['id' => $id]);
            if (!$cat) {
                $output->writeln("<error>Question category with ID $id not found.</error>");
                return Command::FAILURE;
            }
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following question categories would be deleted (use --run to execute):</info>');
            foreach ($ids as $id) {
                $cat = $DB->get_record('question_categories', ['id' => (int) $id]);
                $output->writeln("  ID=$id, name=\"{$cat->name}\"");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($ids) . ' question category(ies)');

        foreach ($ids as $id) {
            $id = (int) $id;
            $cat = $DB->get_record('question_categories', ['id' => $id]);

            $verbose->info("Deleting question category '{$cat->name}' (ID=$id)");
            $manager = new \core_question\category_manager();
            $manager->delete_category($id);
            $verbose->done("Deleted question category ID=$id");

            $output->writeln("Deleted question category '{$cat->name}' (ID=$id).");
        }

        return Command::SUCCESS;
    }
}
