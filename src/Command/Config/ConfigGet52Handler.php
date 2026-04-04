<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Config;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigGet52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::OPTIONAL, 'Setting name (omit to show all settings for the plugin)')
            ->addOption('plugin', null, InputOption::VALUE_REQUIRED, 'Plugin component (e.g. mod_forum, core). Omit or use "core" for core settings.');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $format = $input->getOption('output');
        $name = $input->getArgument('name');
        $plugin = $input->getOption('plugin');

        // Normalize: "core" and "moodle" both mean core config (null)
        if ($plugin === 'core' || $plugin === 'moodle') {
            $plugin = null;
        }

        $result = get_config($plugin ?? '', $name);

        if ($name !== null) {
            // Single value
            if ($result === false) {
                $output->writeln("<error>Setting '$name' not found" . ($plugin ? " for plugin '$plugin'" : '') . ".</error>");
                return Command::FAILURE;
            }
            $output->writeln((string) $result);
            return Command::SUCCESS;
        }

        // All settings for plugin — render as table
        if ($result === false || (is_object($result) && empty((array) $result))) {
            $pluginLabel = $plugin ?? 'core';
            $output->writeln("<info>No settings found for '$pluginLabel'.</info>");
            return Command::SUCCESS;
        }

        $rows = [];
        foreach ((array) $result as $key => $value) {
            $rows[] = ['name' => $key, 'value' => (string) $value];
        }

        usort($rows, fn(array $a, array $b) => strcmp($a['name'], $b['name']));

        $formatter = new ResultFormatter($output, $format);
        $formatter->display(['name', 'value'], $rows);

        return Command::SUCCESS;
    }
}
