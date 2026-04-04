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

/**
 * user:create implementation for Moodle 5.1.
 */
class UserCreate51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('username', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Username(s) to create')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'User password', 'Abc123!@')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Email address')
            ->addOption('firstname', null, InputOption::VALUE_REQUIRED, 'First name')
            ->addOption('lastname', null, InputOption::VALUE_REQUIRED, 'Last name')
            ->addOption('auth', null, InputOption::VALUE_REQUIRED, 'Authentication method', 'manual')
            ->addOption('city', null, InputOption::VALUE_REQUIRED, 'City')
            ->addOption('country', null, InputOption::VALUE_REQUIRED, 'Country code')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'ID number');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $runMode = $input->getOption('run');
        $usernames = $input->getArgument('username');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->dirroot . '/user/lib.php';

        $password = $input->getOption('password');
        $email = $input->getOption('email');
        $firstname = $input->getOption('firstname');
        $lastname = $input->getOption('lastname');
        $auth = $input->getOption('auth');
        $city = $input->getOption('city');
        $country = $input->getOption('country');
        $idnumber = $input->getOption('idnumber');

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following users would be created (use --run to execute):</info>');
            foreach ($usernames as $username) {
                $output->writeln("  $username");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Creating ' . count($usernames) . ' user(s)');

        $headers = ['id', 'username', 'email'];
        $rows = [];

        foreach ($usernames as $username) {
            $user = new \stdClass();
            $user->username = $username;
            $user->password = $password;
            $user->auth = $auth;
            $user->confirmed = 1;
            $user->mnethostid = $CFG->mnet_localhost_id;
            $user->email = $email ?? $username . '@example.com';
            $user->firstname = $firstname ?? $username;
            $user->lastname = $lastname ?? $username;

            if ($city !== null) {
                $user->city = $city;
            }
            if ($country !== null) {
                $user->country = $country;
            }
            if ($idnumber !== null) {
                $user->idnumber = $idnumber;
            }

            $verbose->info("Creating user: $username");
            $id = user_create_user($user);
            $verbose->done("Created user $username with ID $id");

            $rows[] = [$id, $username, $user->email];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
