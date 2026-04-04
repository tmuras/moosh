<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Auth;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * auth:mod implementation for Moodle 5.1.
 */
class AuthMod51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('action', InputArgument::REQUIRED, 'Action: enable, disable, up, down')
            ->addArgument('plugin', InputArgument::REQUIRED, 'Auth plugin name (e.g. email, ldap, oauth2)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $action = $input->getArgument('action');
        $pluginName = $input->getArgument('plugin');

        $allowedActions = ['enable', 'disable', 'up', 'down'];
        if (!in_array($action, $allowedActions, true)) {
            $output->writeln("<error>Invalid action '$action'. Allowed: " . implode(', ', $allowedActions) . '</error>');
            return Command::FAILURE;
        }

        if ($pluginName === 'manual') {
            $output->writeln('<error>The manual auth plugin cannot be modified — it is always active.</error>');
            return Command::FAILURE;
        }

        if (!exists_auth_plugin($pluginName)) {
            $output->writeln("<error>Authentication plugin '$pluginName' not found.</error>");
            return Command::FAILURE;
        }

        $enabledList = !empty($CFG->auth) ? explode(',', $CFG->auth) : [];
        $isEnabled = in_array($pluginName, $enabledList, true);

        if (!$runMode) {
            $output->writeln("<info>Dry run — would $action auth plugin '$pluginName' (use --run to execute).</info>");
            $output->writeln('  Current enabled: ' . ($enabledList ? implode(', ', $enabledList) : 'none'));
            return Command::SUCCESS;
        }

        $verbose->step("Performing '$action' on auth plugin '$pluginName'");

        switch ($action) {
            case 'enable':
                if ($isEnabled) {
                    $output->writeln("Auth plugin '$pluginName' is already enabled.");
                    return Command::SUCCESS;
                }
                $enabledList[] = $pluginName;
                break;

            case 'disable':
                if (!$isEnabled) {
                    $output->writeln("Auth plugin '$pluginName' is already disabled.");
                    return Command::SUCCESS;
                }
                $enabledList = array_values(array_filter($enabledList, fn($a) => $a !== $pluginName));
                break;

            case 'up':
                if (!$isEnabled) {
                    $output->writeln("<error>Cannot move '$pluginName' — it is not enabled.</error>");
                    return Command::FAILURE;
                }
                $pos = array_search($pluginName, $enabledList, true);
                if ($pos === 0) {
                    $output->writeln("Auth plugin '$pluginName' is already at the top.");
                    return Command::SUCCESS;
                }
                // Swap with previous.
                [$enabledList[$pos - 1], $enabledList[$pos]] = [$enabledList[$pos], $enabledList[$pos - 1]];
                break;

            case 'down':
                if (!$isEnabled) {
                    $output->writeln("<error>Cannot move '$pluginName' — it is not enabled.</error>");
                    return Command::FAILURE;
                }
                $pos = array_search($pluginName, $enabledList, true);
                if ($pos === count($enabledList) - 1) {
                    $output->writeln("Auth plugin '$pluginName' is already at the bottom.");
                    return Command::SUCCESS;
                }
                // Swap with next.
                [$enabledList[$pos], $enabledList[$pos + 1]] = [$enabledList[$pos + 1], $enabledList[$pos]];
                break;
        }

        set_config('auth', implode(',', $enabledList));
        $verbose->done("Auth config updated");

        $output->writeln("Action '$action' applied to '$pluginName'.");
        $output->writeln('Enabled auth plugins: ' . (implode(', ', $enabledList) ?: 'none'));

        return Command::SUCCESS;
    }
}
