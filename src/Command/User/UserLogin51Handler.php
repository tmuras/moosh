<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\User;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserLogin51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('username', InputArgument::REQUIRED, 'Username or user ID (with --id)')
            ->addOption('id', null, InputOption::VALUE_NONE, 'Treat argument as numeric user ID');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $identifier = $input->getArgument('username');
        $byId = $input->getOption('id');

        require_once $CFG->libdir . '/datalib.php';

        $verbose->step('Looking up user');

        if ($byId) {
            $user = $DB->get_record('user', ['id' => (int) $identifier]);
        } else {
            $user = $DB->get_record('user', ['username' => $identifier]);
        }

        if (!$user) {
            $output->writeln("<error>User '$identifier' not found.</error>");
            return Command::FAILURE;
        }

        if ($user->deleted) {
            $output->writeln("<error>User '{$user->username}' is deleted.</error>");
            return Command::FAILURE;
        }

        if ($user->auth === 'nologin') {
            $output->writeln("<error>User '{$user->username}' has auth method 'nologin'.</error>");
            return Command::FAILURE;
        }

        $verbose->step("Logging in as '{$user->username}'");

        $auth = get_auth_plugin($user->auth);
        $auth->sync_roles($user);
        login_attempt_valid($user);
        complete_user_login($user);

        $sessionName = session_name();
        $sessionId = session_id();

        $verbose->done("Session created for '{$user->username}'");

        if ($format === 'table') {
            $output->writeln("$sessionName:$sessionId");
        } else {
            $formatter = new ResultFormatter($output, $format);
            $formatter->display(
                ['cookie_name', 'cookie_value'],
                [[$sessionName, $sessionId]],
            );
        }

        return Command::SUCCESS;
    }
}
