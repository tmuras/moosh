<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Gradebook;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * gradebook:import implementation for Moodle 5.1.
 */
class GradebookImport52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::REQUIRED, 'Path to CSV file to import')
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID (or idnumber with --course-idnumber)')
            ->addOption('map-users-by', null, InputOption::VALUE_REQUIRED, 'Map users by: email or idnumber', 'email')
            ->addOption('course-idnumber', null, InputOption::VALUE_NONE, 'Treat courseid argument as course idnumber');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB, $USER;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $filePath = $input->getArgument('file');
        $courseArg = $input->getArgument('courseid');
        $mapUsersBy = $input->getOption('map-users-by');
        $useIdnumber = $input->getOption('course-idnumber');

        if (!in_array($mapUsersBy, ['email', 'idnumber'], true)) {
            $output->writeln("<error>Invalid --map-users-by value '$mapUsersBy'. Use 'email' or 'idnumber'.</error>");
            return Command::FAILURE;
        }

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/course/lib.php';
        require_once $CFG->libdir . '/gradelib.php';
        require_once $CFG->dirroot . '/grade/lib.php';
        require_once $CFG->dirroot . '/grade/import/lib.php';
        require_once $CFG->libdir . '/csvlib.class.php';

        // Read CSV file.
        if (!file_exists($filePath)) {
            $output->writeln("<error>File not found: $filePath</error>");
            return Command::FAILURE;
        }

        $text = file_get_contents($filePath);
        if (!$text) {
            $output->writeln("<error>No data in file '$filePath'.</error>");
            return Command::FAILURE;
        }

        // Resolve course.
        if ($useIdnumber) {
            $course = $DB->get_record('course', ['idnumber' => $courseArg]);
        } else {
            $course = $DB->get_record('course', ['id' => (int) $courseArg]);
        }
        if (!$course) {
            $output->writeln("<error>Course '$courseArg' not found.</error>");
            return Command::FAILURE;
        }

        $verbose->step("Importing grades into course '{$course->shortname}' (ID={$course->id})");

        // Parse CSV.
        $iid = \csv_import_reader::get_new_iid('moosh-gradebook');
        $csvimport = new \csv_import_reader($iid, 'moosh-gradebook');
        $csvimport->load_csv_content($text, 'utf-8', 'comma');
        $header = $csvimport->get_columns();

        if (!$header) {
            $output->writeln('<error>Could not parse CSV headers.</error>');
            return Command::FAILURE;
        }

        // Find user-mapping column.
        if ($mapUsersBy === 'idnumber') {
            $userMapCol = array_search('ID number', $header);
            if ($userMapCol === false) {
                $output->writeln("<error>Column 'ID number' not found in CSV headers.</error>");
                return Command::FAILURE;
            }
        } else {
            $userMapCol = array_search('Email address', $header);
            if ($userMapCol === false) {
                $output->writeln("<error>Column 'Email address' not found in CSV headers.</error>");
                return Command::FAILURE;
            }
        }

        // Auto-map CSV columns to grade items.
        $gradeItems = \grade_item::fetch_all(['courseid' => $course->id]);
        $map = [];

        if ($gradeItems) {
            foreach ($gradeItems as $gradeItem) {
                if ($gradeItem->itemtype === 'course' || $gradeItem->itemtype === 'category') {
                    continue;
                }

                if (!empty($gradeItem->itemmodule)) {
                    $displayString = get_string('modulename', $gradeItem->itemmodule) . ': ' . $gradeItem->get_name();
                } else {
                    $displayString = $gradeItem->get_name();
                }

                $pos = array_search($displayString, $header);
                // Also try matching with display type suffix from grade export (e.g., " (Real)").
                if ($pos === false) {
                    foreach ($header as $i => $col) {
                        if (preg_match('/^' . preg_quote($displayString, '/') . ' \((Real|Percentage|Letter)\)$/', $col)) {
                            $pos = $i;
                            break;
                        }
                    }
                }
                if ($pos !== false) {
                    $map[$pos] = $gradeItem->id;
                    $output->writeln("Mapped CSV column '{$header[$pos]}' → grade item '$displayString'");
                } else {
                    $output->writeln("No mapping for grade item '$displayString'");
                }
            }
        }

        if (empty($map)) {
            $output->writeln('<error>No CSV columns could be mapped to grade items.</error>');
            return Command::FAILURE;
        }

        // Iterate CSV rows and build grade records.
        $csvimport->init();
        $newgrades = [];
        $userCount = 0;

        while ($line = $csvimport->next()) {
            $userIdentifier = $line[$userMapCol];

            if ($mapUsersBy === 'idnumber') {
                $user = $DB->get_record('user', ['idnumber' => $userIdentifier]);
            } else {
                $user = $DB->get_record('user', ['email' => $userIdentifier]);
            }

            if (!$user) {
                $output->writeln("<comment>Warning: User '$userIdentifier' not found, skipping.</comment>");
                continue;
            }

            $userCount++;
            $verbose->info("Processing user {$user->email} (ID={$user->id})");

            foreach ($map as $colIndex => $gradeItemId) {
                $gradeItem = $gradeItems[$gradeItemId];
                $value = $line[$colIndex];

                $newgrade = new \stdClass();
                $newgrade->itemid = $gradeItem->id;
                $newgrade->userid = $user->id;
                $newgrade->importer = $USER->id;

                // Handle scale grades.
                if ($gradeItem->gradetype == GRADE_TYPE_SCALE) {
                    $scale = $gradeItem->load_scale();
                    $scales = explode(',', $scale->scale);
                    $scales = array_map('trim', $scales);
                    array_unshift($scales, '-'); // Scales start at key 1.
                    $key = array_search($value, $scales);
                    if ($key === false) {
                        $output->writeln("<comment>  Scale value '$value' not found for '{$gradeItem->get_name()}'.</comment>");
                        continue;
                    }
                    $value = $key;
                } else {
                    if ($value === '' || $value === '-') {
                        $value = null;
                    }
                }

                $newgrade->finalgrade = $value;
                $newgrades[] = $newgrade;
            }
        }

        $output->writeln('');
        $output->writeln("Users matched: $userCount");
        $output->writeln("Grade values to import: " . count($newgrades));

        if (!$runMode) {
            $output->writeln('<info>Dry run — no changes made (use --run to execute).</info>');
            return Command::SUCCESS;
        }

        // Import grades using Moodle's temporary table mechanism.
        $verbose->step('Importing grades');
        $importcode = get_new_importcode();

        foreach ($newgrades as $newgrade) {
            $newgrade->importcode = $importcode;
            $DB->insert_record('grade_import_values', $newgrade);
        }

        ob_start();
        grade_import_commit($course->id, $importcode);
        ob_end_clean();

        $output->writeln("Imported " . count($newgrades) . " grade(s) for $userCount user(s).");

        return Command::SUCCESS;
    }
}
