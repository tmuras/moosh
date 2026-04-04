<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Content;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ContentReplaceEncoded51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('search', InputArgument::REQUIRED, 'Text to search for')
            ->addArgument('replace', InputArgument::REQUIRED, 'Replacement text')
            ->addArgument('table', InputArgument::REQUIRED, 'Database table name (without prefix)')
            ->addArgument('column', InputArgument::REQUIRED, 'Column containing base64-encoded serialized data');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $search = $input->getArgument('search');
        $replace = $input->getArgument('replace');
        $table = $input->getArgument('table');
        $column = $input->getArgument('column');

        // Validate table and column exist.
        $dbman = $DB->get_manager();
        if (!$dbman->table_exists($table)) {
            $output->writeln("<error>Table '{$table}' does not exist.</error>");
            return Command::FAILURE;
        }

        $verbose->step("Scanning {$table}.{$column} for encoded data containing \"{$search}\"");

        $records = $DB->get_recordset_select($table, "$column IS NOT NULL AND $column != ''", null, '', "id, $column");

        $matchCount = 0;
        $updateCount = 0;

        foreach ($records as $record) {
            $raw = $record->$column;

            $decoded = @base64_decode($raw, true);
            if ($decoded === false) {
                continue;
            }

            // Suppress unserialize warnings for malformed data.
            $unserialized = @unserialize($decoded);
            if ($unserialized === false && $decoded !== serialize(false)) {
                continue;
            }

            $changed = false;

            if (is_object($unserialized)) {
                $changed = $this->replaceInObject($unserialized, $search, $replace);
            } elseif (is_array($unserialized)) {
                $changed = $this->replaceInArray($unserialized, $search, $replace);
            } elseif (is_string($unserialized) && str_contains($unserialized, $search)) {
                $unserialized = str_replace($search, $replace, $unserialized);
                $changed = true;
            }

            if (!$changed) {
                continue;
            }

            $matchCount++;
            $newEncoded = base64_encode(serialize($unserialized));

            if (!$runMode) {
                $output->writeln("  Would update row ID={$record->id}");
                $verbose->info("  Decoded contains match for \"{$search}\"");
            } else {
                $DB->set_field($table, $column, $newEncoded, ['id' => $record->id]);
                $updateCount++;
                $verbose->info("Updated row ID={$record->id}");
            }
        }

        $records->close();

        if ($matchCount === 0) {
            $output->writeln('No matching encoded data found.');
            return Command::SUCCESS;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — found $matchCount row(s) to update (use --run to execute).</info>");
        } else {
            $output->writeln("Updated $updateCount row(s) in {$table}.{$column}.");
        }

        return Command::SUCCESS;
    }

    private function replaceInObject(object $obj, string $search, string $replace): bool
    {
        $changed = false;

        foreach (get_object_vars($obj) as $prop => $value) {
            if (is_string($value) && str_contains($value, $search)) {
                $obj->$prop = str_replace($search, $replace, $value);
                $changed = true;
            } elseif (is_array($value)) {
                if ($this->replaceInArray($value, $search, $replace)) {
                    $obj->$prop = $value;
                    $changed = true;
                }
            } elseif (is_object($value)) {
                if ($this->replaceInObject($value, $search, $replace)) {
                    $changed = true;
                }
            }
        }

        return $changed;
    }

    private function replaceInArray(array &$arr, string $search, string $replace): bool
    {
        $changed = false;

        foreach ($arr as $key => &$value) {
            if (is_string($value) && str_contains($value, $search)) {
                $value = str_replace($search, $replace, $value);
                $changed = true;
            } elseif (is_array($value)) {
                if ($this->replaceInArray($value, $search, $replace)) {
                    $changed = true;
                }
            } elseif (is_object($value)) {
                if ($this->replaceInObject($value, $search, $replace)) {
                    $changed = true;
                }
            }
        }
        unset($value);

        return $changed;
    }
}
