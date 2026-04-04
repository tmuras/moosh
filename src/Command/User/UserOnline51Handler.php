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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserOnline51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('time', null, InputOption::VALUE_REQUIRED, 'Time window in seconds (default: 300)', '300')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum users to show (0 = all)', '0');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $timeWindow = (int) $input->getOption('time');
        $limit = (int) $input->getOption('limit');

        $cutoff = time() - $timeWindow;

        $verbose->step("Finding users active in the last {$timeWindow}s");

        $sql = 'SELECT id, username, firstname, lastname, email, lastaccess
                FROM {user}
                WHERE lastaccess > ? AND deleted = 0
                ORDER BY lastaccess DESC';

        $params = [$cutoff];

        $users = $DB->get_records_sql($sql, $params, 0, $limit > 0 ? $limit : 0);

        if (empty($users)) {
            $output->writeln('No users online.');
            return Command::SUCCESS;
        }

        $verbose->done('Found ' . count($users) . ' online user(s)');

        $headers = ['id', 'username', 'firstname', 'lastname', 'email', 'lastaccess'];
        $rows = [];

        foreach ($users as $user) {
            $rows[] = [
                $user->id,
                $user->username,
                $user->firstname,
                $user->lastname,
                $user->email,
                date('Y-m-d H:i:s', (int) $user->lastaccess),
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
