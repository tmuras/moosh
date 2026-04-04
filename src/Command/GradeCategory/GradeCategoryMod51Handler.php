<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\GradeCategory;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * gradecategory:mod implementation for Moodle 5.1.
 */
class GradeCategoryMod51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('categoryid', InputArgument::REQUIRED, 'Grade category ID')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Set category name')
            ->addOption('aggregation', null, InputOption::VALUE_REQUIRED, 'Set aggregation type')
            ->addOption('keephigh', null, InputOption::VALUE_REQUIRED, 'Keep only N highest')
            ->addOption('droplow', null, InputOption::VALUE_REQUIRED, 'Drop N lowest')
            ->addOption('hidden', null, InputOption::VALUE_REQUIRED, 'Set hidden (1 or 0)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $catId = (int) $input->getArgument('categoryid');
        $newName = $input->getOption('name');
        $newAggregation = $input->getOption('aggregation');
        $newKeephigh = $input->getOption('keephigh');
        $newDroplow = $input->getOption('droplow');
        $newHidden = $input->getOption('hidden');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/gradelib.php';
        require_once $CFG->libdir . '/grade/grade_category.php';
        require_once $CFG->libdir . '/grade/grade_item.php';

        $gc = \grade_category::fetch(['id' => $catId]);
        if (!$gc) {
            $output->writeln("<error>Grade category with ID $catId not found.</error>");
            return Command::FAILURE;
        }

        if ($newName === null && $newAggregation === null && $newKeephigh === null && $newDroplow === null && $newHidden === null) {
            $output->writeln('<error>No modifications specified. Use --name, --aggregation, --keephigh, --droplow, or --hidden.</error>');
            return Command::FAILURE;
        }

        // Build changes summary.
        $changes = [];
        if ($newName !== null) {
            $changes[] = "fullname: \"{$gc->fullname}\" → \"$newName\"";
        }
        if ($newAggregation !== null) {
            $changes[] = "aggregation: {$gc->aggregation} → $newAggregation";
        }
        if ($newKeephigh !== null) {
            $changes[] = "keephigh: {$gc->keephigh} → $newKeephigh";
        }
        if ($newDroplow !== null) {
            $changes[] = "droplow: {$gc->droplow} → $newDroplow";
        }
        if ($newHidden !== null) {
            $changes[] = "hidden: {$gc->hidden} → $newHidden";
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would modify grade category '{$gc->fullname}' (ID=$catId) (use --run to execute):</info>");
            foreach ($changes as $change) {
                $output->writeln("  $change");
            }
            return Command::SUCCESS;
        }

        $verbose->step("Modifying grade category '$gc->fullname' (ID=$catId)");

        if ($newName !== null) {
            $gc->fullname = $newName;
        }
        if ($newAggregation !== null) {
            $gc->aggregation = (int) $newAggregation;
        }
        if ($newKeephigh !== null) {
            $gc->keephigh = (int) $newKeephigh;
        }
        if ($newDroplow !== null) {
            $gc->droplow = (int) $newDroplow;
        }
        if ($newHidden !== null) {
            $gc->hidden = (int) $newHidden;
        }

        $gc->update('moosh');

        $gc = \grade_category::fetch(['id' => $catId]);
        $headers = ['id', 'fullname', 'parent', 'depth', 'aggregation', 'keephigh', 'droplow', 'hidden'];
        $rows = [[$gc->id, $gc->fullname, $gc->parent ?? 0, $gc->depth, $gc->aggregation, $gc->keephigh, $gc->droplow, $gc->hidden]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
