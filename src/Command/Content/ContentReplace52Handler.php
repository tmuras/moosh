<?php
namespace Moosh2\Command\Content;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ContentReplace52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('search', InputArgument::REQUIRED, 'Text to search for')
            ->addArgument('replace', InputArgument::REQUIRED, 'Replacement text')
            ->addOption('skip-tables', null, InputOption::VALUE_REQUIRED, 'Comma-separated table names to skip');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $search = $input->getArgument('search');
        $replace = $input->getArgument('replace');
        $skipTables = $input->getOption('skip-tables');

        require_once $CFG->libdir . '/adminlib.php';

        $additionalSkip = '';
        if ($skipTables !== null) {
            $additionalSkip = $skipTables;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would replace all occurrences of:</info>");
            $output->writeln("  Search:  \"$search\"");
            $output->writeln("  Replace: \"$replace\"");
            if ($additionalSkip) {
                $output->writeln("  Skip tables: $additionalSkip");
            }
            $output->writeln("<info>Use --run to execute. This is irreversible!</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Replacing '$search' with '$replace' across database");

        ob_start();
        $result = db_replace($search, $replace, $additionalSkip);
        ob_end_clean();

        if ($result) {
            $output->writeln("Replacement complete. All occurrences of '$search' replaced with '$replace'.");
        } else {
            $output->writeln('<error>Replacement failed.</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
