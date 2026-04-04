<?php
namespace Moosh2\Command\Database;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseCheck52Handler extends BaseHandler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $verbose->step('Checking database schema');

        $dbman = $DB->get_manager();
        $schema = $dbman->get_install_xml_schema();

        $issues = [];
        foreach ($schema->getTables() as $table) {
            $tableName = $table->getName();

            if (!$dbman->table_exists($table)) {
                $issues[] = "Missing table: $tableName";
                continue;
            }

            foreach ($table->getFields() as $field) {
                if (!$dbman->field_exists($table, $field)) {
                    $issues[] = "Missing field: {$tableName}.{$field->getName()}";
                }
            }
        }

        if (empty($issues)) {
            $output->writeln('Database schema is consistent. No issues found.');
            return Command::SUCCESS;
        }

        $output->writeln('Found ' . count($issues) . ' schema issue(s):');
        foreach ($issues as $issue) {
            $output->writeln("  - $issue");
        }

        return Command::FAILURE;
    }
}
