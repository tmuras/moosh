<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Auth;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * auth:list implementation for Moodle 5.1.
 */
class AuthList52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('enabled-only', null, InputOption::VALUE_NONE, 'Show only enabled auth plugins');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $enabledOnly = $input->getOption('enabled-only');

        $verbose->step('Loading auth plugins');

        $available = \core_component::get_plugin_list('auth');
        $enabledList = !empty($CFG->auth) ? explode(',', $CFG->auth) : [];

        $headers = ['plugin', 'name', 'enabled', 'users', 'can_signup', 'can_change_password', 'is_internal'];
        $rows = [];

        foreach (array_keys($available) as $auth) {
            $isEnabled = in_array($auth, $enabledList, true) || $auth === 'manual';

            if ($enabledOnly && !$isEnabled) {
                continue;
            }

            $authPlugin = get_auth_plugin($auth);
            $userCount = $DB->count_records('user', ['auth' => $auth, 'deleted' => 0]);

            $rows[] = [
                $auth,
                $authPlugin->get_title(),
                $isEnabled ? 1 : 0,
                $userCount,
                $authPlugin->can_signup() ? 1 : 0,
                $authPlugin->can_change_password() ? 1 : 0,
                $authPlugin->is_internal() ? 1 : 0,
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
