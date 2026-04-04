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

class ProfileFieldImport51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument('file', InputArgument::REQUIRED, 'Path to CSV file to import');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $output->writeln("<error>File not found: $filePath</error>");
            return Command::FAILURE;
        }

        $verbose->step('Reading CSV file');

        $fh = fopen($filePath, 'r');
        if (!$fh) {
            $output->writeln("<error>Cannot open file: $filePath</error>");
            return Command::FAILURE;
        }

        $headers = fgetcsv($fh);
        if (!$headers) {
            $output->writeln('<error>Empty CSV file or invalid format.</error>');
            fclose($fh);
            return Command::FAILURE;
        }

        $rows = [];
        while (($row = fgetcsv($fh)) !== false) {
            if (count($row) === count($headers)) {
                $rows[] = array_combine($headers, $row);
            }
        }
        fclose($fh);

        $verbose->done('Read ' . count($rows) . ' field(s) from CSV');

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following fields would be imported (use --run to execute):</info>');
            foreach ($rows as $row) {
                $shortname = $row['shortname'] ?? '?';
                $exists = $DB->record_exists('user_info_field', ['shortname' => $shortname]);
                $status = $exists ? '(SKIP - exists)' : '(NEW)';
                $output->writeln("  $shortname $status");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Importing fields');
        $created = 0;
        $skipped = 0;
        $categoryCache = [];

        foreach ($rows as $row) {
            $shortname = $row['shortname'] ?? '';
            if (!$shortname) {
                continue;
            }

            // Skip if already exists.
            if ($DB->record_exists('user_info_field', ['shortname' => $shortname])) {
                $verbose->info("Skipping existing field: $shortname");
                $skipped++;
                continue;
            }

            // Get or create category.
            $categoryName = $row['categoryname'] ?? 'Other fields';
            if (!isset($categoryCache[$categoryName])) {
                $cat = $DB->get_record('user_info_category', ['name' => $categoryName]);
                if (!$cat) {
                    $catData = new \stdClass();
                    $catData->name = $categoryName;
                    $catData->sortorder = $DB->count_records('user_info_category') + 1;
                    $catData->id = $DB->insert_record('user_info_category', $catData);
                    $cat = $catData;
                    $verbose->info("Created category: $categoryName");
                }
                $categoryCache[$categoryName] = $cat->id;
            }

            $field = new \stdClass();
            $field->shortname = $shortname;
            $field->name = $row['name'] ?? $shortname;
            $field->datatype = $row['datatype'] ?? 'text';
            $field->description = $row['description'] ?? '';
            $field->descriptionformat = (int) ($row['descriptionformat'] ?? 1);
            $field->categoryid = $categoryCache[$categoryName];
            $field->sortorder = (int) ($row['sortorder'] ?? $DB->count_records('user_info_field', ['categoryid' => $field->categoryid]) + 1);
            $field->required = (int) ($row['required'] ?? 0);
            $field->locked = (int) ($row['locked'] ?? 0);
            $field->visible = (int) ($row['visible'] ?? 2);
            $field->forceunique = (int) ($row['forceunique'] ?? 0);
            $field->signup = (int) ($row['signup'] ?? 0);
            $field->defaultdata = $row['defaultdata'] ?? '';
            $field->defaultdataformat = (int) ($row['defaultdataformat'] ?? 0);
            $field->param1 = $row['param1'] ?? '';
            $field->param2 = $row['param2'] ?? '';
            $field->param3 = $row['param3'] ?? '';
            $field->param4 = $row['param4'] ?? '';
            $field->param5 = $row['param5'] ?? '';

            $newId = $DB->insert_record('user_info_field', $field);
            $verbose->info("Created field: $shortname (ID=$newId)");
            $created++;
        }

        $output->writeln("Imported $created field(s), skipped $skipped existing.");

        return Command::SUCCESS;
    }
}
