<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Plugin;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Service\PluginApiClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PluginList52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'Filter by plugin type prefix (e.g. mod, block, auth)')
            ->addOption('name-only', null, InputOption::VALUE_NONE, 'Display only frankenstyle plugin names')
            ->addOption('refresh', null, InputOption::VALUE_NONE, 'Force re-download of the plugin cache')
            ->addOption('proxy', null, InputOption::VALUE_REQUIRED, 'Proxy URI (e.g. tcp://user:pass@host:port)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $format = $input->getOption('output');
        $typeFilter = $input->getOption('type');
        $nameOnly = $input->getOption('name-only');
        $refresh = $input->getOption('refresh');
        $proxy = $input->getOption('proxy');

        $client = new PluginApiClient($proxy);
        $data = $client->getPluginList($refresh);

        $rows = [];

        foreach ($data->plugins as $plugin) {
            if (empty($plugin->component)) {
                continue;
            }

            if ($typeFilter !== null) {
                $pluginType = explode('_', $plugin->component, 2)[0];
                if ($pluginType !== $typeFilter) {
                    continue;
                }
            }

            if ($nameOnly) {
                $output->writeln($plugin->component);
                continue;
            }

            $moodleReleases = [];
            $latestUrl = '';
            $highestVersion = 0;

            foreach ($plugin->versions as $version) {
                if ($version->version >= $highestVersion) {
                    $highestVersion = $version->version;
                    $latestUrl = $version->downloadurl;
                }
                foreach ($version->supportedmoodles as $supported) {
                    $moodleReleases[$supported->release] = true;
                }
            }

            $releases = array_keys($moodleReleases);
            sort($releases);

            $rows[] = [
                'component' => $plugin->component,
                'versions' => implode(', ', $releases),
                'url' => $latestUrl,
            ];
        }

        if ($nameOnly) {
            return Command::SUCCESS;
        }

        usort($rows, fn(array $a, array $b) => strcmp($a['component'], $b['component']));

        $formatter = new ResultFormatter($output, $format);
        $formatter->display(['component', 'versions', 'url'], $rows);

        return Command::SUCCESS;
    }
}
