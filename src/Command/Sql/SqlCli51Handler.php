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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * sql:cli implementation for Moodle 5.1.
 */
class SqlCli51Handler extends BaseHandler
{
    use DbConnectionTrait;

    public function getBootstrapLevel(): ?BootstrapLevel
    {
        return BootstrapLevel::Config;
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);

        try {
            $dbType = $this->getDbType();
        } catch (\RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        if ($dbType === 'pgsql') {
            $this->setPgPassword();
            $command = $this->getPgsqlCliCommand();
        } else {
            $command = $this->getMysqlCliCommand();
        }

        $verbose->step("Opening $dbType CLI");
        $verbose->info("Command: $command");

        $process = proc_open($command, [0 => STDIN, 1 => STDOUT, 2 => STDERR], $pipes);
        if (!is_resource($process)) {
            $output->writeln('<error>Failed to open database CLI process.</error>');
            return Command::FAILURE;
        }

        $status = proc_get_status($process);
        $exitCode = proc_close($process);

        return ($status['running'] ? $exitCode : $status['exitcode']) === 0
            ? Command::SUCCESS
            : Command::FAILURE;
    }
}
