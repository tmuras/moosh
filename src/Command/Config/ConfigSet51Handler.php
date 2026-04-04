<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Config;

use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigSet51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'Setting name')
            ->addArgument('value', InputArgument::REQUIRED, 'Setting value')
            ->addOption('plugin', null, InputOption::VALUE_REQUIRED, 'Plugin component (e.g. mod_forum). Omit or use "core" for core settings.');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $runMode = $input->getOption('run');
        $name = $input->getArgument('name');
        $value = $input->getArgument('value');
        $plugin = $input->getOption('plugin');

        // Normalize: "core" and "moodle" both mean core config (null)
        if ($plugin === 'core' || $plugin === 'moodle') {
            $plugin = null;
        }

        $pluginLabel = $plugin ?? 'core';

        // Show current value
        $current = get_config($plugin ?? '', $name);
        $currentDisplay = $current === false ? '(not set)' : (string) $current;

        if (!$runMode) {
            $output->writeln('<info>Dry run — would set the following value (use --run to execute):</info>');
            $output->writeln("  Plugin:  $pluginLabel");
            $output->writeln("  Name:    $name");
            $output->writeln("  Current: $currentDisplay");
            $output->writeln("  New:     $value");
            return Command::SUCCESS;
        }

        set_config($name, $value, $plugin);

        $newValue = get_config($plugin ?? '', $name);
        $output->writeln("Set $pluginLabel/$name = $newValue");

        return Command::SUCCESS;
    }
}
