<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Sql;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * sql:dump implementation for Moodle 5.1.
 */
class SqlDump52Handler extends BaseHandler
{
    use DbConnectionTrait;

    public function getBootstrapLevel(): ?BootstrapLevel
    {
        return BootstrapLevel::Config;
    }

    public function configureCommand(\Symfony\Component\Console\Command\Command $command): void
    {
        $command
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Write dump to file instead of stdout')
            ->addOption('gzip', null, InputOption::VALUE_NONE, 'Compress output with gzip')
            ->addOption('tables', null, InputOption::VALUE_REQUIRED, 'Comma-separated table names to dump (without prefix)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $filePath = $input->getOption('file');
        $gzip = $input->getOption('gzip');
        $tablesOpt = $input->getOption('tables');

        try {
            $dbType = $this->getDbType();
        } catch (\RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        // Resolve table names with prefix.
        $tables = [];
        if ($tablesOpt !== null) {
            $prefix = $this->getTablePrefix();
            foreach (explode(',', $tablesOpt) as $name) {
                $name = trim($name);
                if ($name !== '') {
                    $tables[] = $prefix . $name;
                }
            }
        }

        if ($dbType === 'pgsql') {
            $this->setPgPassword();
            $command = $this->getPgDumpCommand($tables);
        } else {
            $command = $this->getMysqldumpCommand($tables);
        }

        // Add gzip and file redirection.
        if ($gzip && $filePath) {
            $command .= ' | gzip > ' . escapeshellarg($filePath);
        } elseif ($gzip) {
            $command .= ' | gzip';
        } elseif ($filePath) {
            $command .= ' > ' . escapeshellarg($filePath);
        }

        $verbose->step("Running $dbType dump");
        $verbose->info("Command: $command");

        $returnCode = 0;
        passthru($command, $returnCode);

        if ($returnCode !== 0) {
            $output->writeln("<error>Dump command exited with code $returnCode.</error>");
            return Command::FAILURE;
        }

        if ($filePath) {
            $output->writeln("Database dumped to $filePath");
        }

        return Command::SUCCESS;
    }
}
