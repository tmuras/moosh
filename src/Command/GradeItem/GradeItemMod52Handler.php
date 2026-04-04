<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\GradeItem;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * gradeitem:mod implementation for Moodle 5.1.
 */
class GradeItemMod52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('itemid', InputArgument::REQUIRED, 'Grade item ID')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Set item name')
            ->addOption('grademax', null, InputOption::VALUE_REQUIRED, 'Set maximum grade')
            ->addOption('grademin', null, InputOption::VALUE_REQUIRED, 'Set minimum grade')
            ->addOption('gradepass', null, InputOption::VALUE_REQUIRED, 'Set grade to pass')
            ->addOption('category', null, InputOption::VALUE_REQUIRED, 'Move to grade category ID')
            ->addOption('hidden', null, InputOption::VALUE_REQUIRED, 'Set hidden (1 or 0)')
            ->addOption('locked', null, InputOption::VALUE_REQUIRED, 'Set locked (1 or 0)')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'Set ID number');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $itemId = (int) $input->getArgument('itemid');
        $newName = $input->getOption('name');
        $newGradeMax = $input->getOption('grademax');
        $newGradeMin = $input->getOption('grademin');
        $newGradePass = $input->getOption('gradepass');
        $newCategory = $input->getOption('category');
        $newHidden = $input->getOption('hidden');
        $newLocked = $input->getOption('locked');
        $newIdNumber = $input->getOption('idnumber');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/gradelib.php';
        require_once $CFG->libdir . '/grade/grade_item.php';
        require_once $CFG->libdir . '/grade/grade_category.php';

        $gi = \grade_item::fetch(['id' => $itemId]);
        if (!$gi) {
            $output->writeln("<error>Grade item with ID $itemId not found.</error>");
            return Command::FAILURE;
        }

        $hasChanges = $newName !== null || $newGradeMax !== null || $newGradeMin !== null
            || $newGradePass !== null || $newCategory !== null || $newHidden !== null
            || $newLocked !== null || $newIdNumber !== null;

        if (!$hasChanges) {
            $output->writeln('<error>No modifications specified. Use --name, --grademax, --grademin, --gradepass, --category, --hidden, --locked, or --idnumber.</error>');
            return Command::FAILURE;
        }

        // Validate category if changing.
        if ($newCategory !== null) {
            $cat = \grade_category::fetch(['id' => (int) $newCategory, 'courseid' => $gi->courseid]);
            if (!$cat) {
                $output->writeln("<error>Grade category $newCategory not found in course {$gi->courseid}.</error>");
                return Command::FAILURE;
            }
        }

        // Build changes summary.
        $changes = [];
        if ($newName !== null) {
            $changes[] = "itemname: \"{$gi->itemname}\" → \"$newName\"";
        }
        if ($newGradeMax !== null) {
            $changes[] = "grademax: {$gi->grademax} → $newGradeMax";
        }
        if ($newGradeMin !== null) {
            $changes[] = "grademin: {$gi->grademin} → $newGradeMin";
        }
        if ($newGradePass !== null) {
            $changes[] = "gradepass: {$gi->gradepass} → $newGradePass";
        }
        if ($newCategory !== null) {
            $changes[] = "categoryid: {$gi->categoryid} → $newCategory";
        }
        if ($newHidden !== null) {
            $changes[] = "hidden: {$gi->hidden} → $newHidden";
        }
        if ($newLocked !== null) {
            $changes[] = "locked: {$gi->locked} → $newLocked";
        }
        if ($newIdNumber !== null) {
            $changes[] = "idnumber: \"{$gi->idnumber}\" → \"$newIdNumber\"";
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would modify grade item '{$gi->itemname}' (ID=$itemId) (use --run to execute):</info>");
            foreach ($changes as $change) {
                $output->writeln("  $change");
            }
            return Command::SUCCESS;
        }

        $verbose->step("Modifying grade item '{$gi->itemname}' (ID=$itemId)");

        if ($newName !== null) {
            $gi->itemname = $newName;
        }
        if ($newGradeMax !== null) {
            $gi->grademax = (float) $newGradeMax;
        }
        if ($newGradeMin !== null) {
            $gi->grademin = (float) $newGradeMin;
        }
        if ($newGradePass !== null) {
            $gi->gradepass = (float) $newGradePass;
        }
        if ($newCategory !== null) {
            $gi->categoryid = (int) $newCategory;
        }
        if ($newHidden !== null) {
            $gi->set_hidden((int) $newHidden);
        }
        if ($newLocked !== null) {
            $gi->set_locked((int) $newLocked);
        }
        if ($newIdNumber !== null) {
            $gi->idnumber = $newIdNumber;
        }

        $gi->update('moosh');

        $gi = \grade_item::fetch(['id' => $itemId]);
        $headers = ['id', 'itemname', 'itemtype', 'categoryid', 'gradetype', 'grademax', 'grademin', 'gradepass', 'hidden', 'locked'];
        $rows = [[$gi->id, $gi->itemname, $gi->itemtype, $gi->categoryid, $gi->gradetype, $gi->grademax, $gi->grademin, $gi->gradepass, $gi->hidden, $gi->locked]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
