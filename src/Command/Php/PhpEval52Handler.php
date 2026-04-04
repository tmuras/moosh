<?php
namespace Moosh2\Command\Php;

use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpEval52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument('code', InputArgument::REQUIRED, 'PHP code to evaluate');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB, $SESSION, $USER, $SITE, $PAGE, $COURSE, $OUTPUT;

        $code = $input->getArgument('code');

        // Ensure code ends with semicolon.
        $code = rtrim($code, "; \t\n\r");

        try {
            eval($code . ';');
        } catch (\Throwable $e) {
            $output->writeln("<error>Error: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
