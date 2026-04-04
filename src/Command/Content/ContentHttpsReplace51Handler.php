<?php
namespace Moosh2\Command\Content;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ContentHttpsReplace51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addOption('list', null, InputOption::VALUE_NONE, 'List HTTP domains found (do not replace)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');
        $listOnly = $input->getOption('list');

        $classFile = $CFG->dirroot . '/admin/tool/httpsreplace/classes/url_finder.php';
        if (!file_exists($classFile)) {
            $output->writeln('<error>The httpsreplace tool is not available in this Moodle version.</error>');
            return Command::FAILURE;
        }

        require_once $classFile;

        $finder = new \tool_httpsreplace\url_finder();

        // The url_finder expects a progress object with an update() method.
        $progress = new class extends \core\progress\none {
            public function update($cur = 0, $max = 0, $msg = '') {}
        };

        if ($listOnly || !$runMode) {
            $verbose->step('Scanning for HTTP URLs');
            $stats = $finder->http_link_stats($progress);

            if (empty($stats)) {
                $output->writeln('No HTTP URLs found in the database.');
                return Command::SUCCESS;
            }

            $headers = ['domain', 'count'];
            $rows = [];
            foreach ($stats as $domain => $count) {
                $rows[] = [$domain, $count];
            }

            $output->writeln('<info>HTTP URLs found by domain:</info>');
            $formatter = new ResultFormatter($output, $format);
            $formatter->display($headers, $rows);

            if (!$runMode) {
                $output->writeln('');
                $output->writeln('<info>Use --run to perform the replacement.</info>');
            }
            return Command::SUCCESS;
        }

        $verbose->step('Replacing HTTP URLs with HTTPS');
        $finder->upgrade_http_links($progress);
        $output->writeln('HTTP to HTTPS URL replacement complete.');

        return Command::SUCCESS;
    }
}
