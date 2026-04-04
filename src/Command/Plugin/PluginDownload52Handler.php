<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Plugin;

use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseHandler;
use Moosh2\Service\PluginApiClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PluginDownload52Handler extends BaseHandler
{
    private ?MoodleVersion $moodleVersion;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->moodleVersion = $moodleVersion;
    }

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('plugin', InputArgument::REQUIRED, 'Frankenstyle plugin name (e.g. mod_attendance)')
            ->addOption('moodle-version', null, InputOption::VALUE_REQUIRED, 'Moodle major version for compatibility (e.g. 4.5). Auto-detected if inside a Moodle directory.')
            ->addOption('url', null, InputOption::VALUE_NONE, 'Only display the download URL, do not download')
            ->addOption('proxy', null, InputOption::VALUE_REQUIRED, 'Proxy URI (e.g. tcp://user:pass@host:port)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $pluginName = $input->getArgument('plugin');
        $moodleRelease = $input->getOption('moodle-version');
        $urlOnly = $input->getOption('url');
        $proxy = $input->getOption('proxy');

        if ($moodleRelease === null && $this->moodleVersion !== null) {
            // Extract major version from branch (e.g. "501" → "5.0", "510" → "5.1")
            $branch = $this->moodleVersion->getBranch();
            $major = (int) substr($branch, 0, 1);
            $minor = (int) substr($branch, 1);
            $moodleRelease = "$major.$minor";
        }

        if ($moodleRelease === null) {
            $output->writeln('<error>Cannot determine Moodle version. Use --version to specify it (e.g. --version 4.5).</error>');
            return Command::FAILURE;
        }

        $client = new PluginApiClient($proxy);

        try {
            $version = $client->findBestVersion($pluginName, $moodleRelease);
        } catch (\RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        if ($urlOnly) {
            $output->writeln($version->downloadurl);
            return Command::SUCCESS;
        }

        $targetFile = getcwd() . '/' . $pluginName . '.zip';

        try {
            $client->downloadFile($version->downloadurl, $targetFile);
        } catch (\RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln("Downloaded:");
        $output->writeln("  plugin:  $pluginName");
        $output->writeln("  version: {$version->version}");
        $output->writeln("  release: $moodleRelease");
        $output->writeln("  file:    $targetFile");

        return Command::SUCCESS;
    }
}
