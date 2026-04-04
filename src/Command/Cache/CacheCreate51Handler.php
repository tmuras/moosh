<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Cache;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * cache:create implementation for Moodle 5.1.
 */
class CacheCreate51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('plugin', InputArgument::REQUIRED, 'Store plugin: redis, memcached, apcu, file')
            ->addArgument('name', InputArgument::REQUIRED, 'Store instance name')
            ->addOption('server', null, InputOption::VALUE_REQUIRED, 'Server address (redis/memcached)')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Server password')
            ->addOption('prefix', null, InputOption::VALUE_REQUIRED, 'Key prefix')
            ->addOption('compression', null, InputOption::VALUE_NONE, 'Enable compression (memcached)')
            ->addOption('serialiser', null, InputOption::VALUE_REQUIRED, 'Serialiser type (memcached)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $plugin = $input->getArgument('plugin');
        $name = $input->getArgument('name');
        $server = $input->getOption('server');
        $password = $input->getOption('password');
        $prefix = $input->getOption('prefix');
        $compression = $input->getOption('compression');
        $serialiser = $input->getOption('serialiser');



        // Validate plugin.
        $storeDir = $CFG->dirroot . '/cache/stores/' . $plugin;
        if (!is_dir($storeDir)) {
            $output->writeln("<error>Cache store plugin '$plugin' not found.</error>");
            return Command::FAILURE;
        }

        // Check name isn't already taken.
        $config = \cache_config::instance();
        $stores = $config->get_all_stores();
        if (isset($stores[$name])) {
            $output->writeln("<error>Cache store '$name' already exists.</error>");
            return Command::FAILURE;
        }

        // Build configuration based on plugin type.
        $storeConfig = $this->buildConfig($plugin, $server, $password, $prefix, $compression, $serialiser);

        if (!$runMode) {
            $output->writeln("<info>Dry run — would create $plugin store '$name' (use --run to execute):</info>");
            foreach ($storeConfig as $key => $value) {
                $display = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                $output->writeln("  $key: $display");
            }
            return Command::SUCCESS;
        }

        $verbose->step("Creating $plugin cache store '$name'");

        $writer = \cache_config_writer::instance();
        $writer->add_store_instance($name, $plugin, $storeConfig);

        $verbose->done("Created store '$name'");

        $headers = ['name', 'plugin', 'server', 'prefix'];
        $rows = [[$name, $plugin, $server ?? '', $prefix ?? '']];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }

    private function buildConfig(string $plugin, ?string $server, ?string $password, ?string $prefix, bool $compression, ?string $serialiser): array
    {
        $config = [];

        switch ($plugin) {
            case 'redis':
                $config['server'] = $server ?? '127.0.0.1';
                if ($password !== null) {
                    $config['password'] = $password;
                }
                if ($prefix !== null) {
                    $config['prefix'] = $prefix;
                }
                break;

            case 'memcached':
                $config['servers'] = $server ?? '127.0.0.1:11211';
                if ($password !== null) {
                    $config['password'] = $password;
                }
                if ($prefix !== null) {
                    $config['prefix'] = $prefix;
                }
                if ($compression) {
                    $config['compression'] = 1;
                }
                if ($serialiser !== null) {
                    $config['serialiser'] = (int) $serialiser;
                }
                break;

            case 'apcu':
                if ($prefix !== null) {
                    $config['prefix'] = $prefix;
                }
                break;

            case 'file':
                // File store uses default paths.
                break;
        }

        return $config;
    }
}
