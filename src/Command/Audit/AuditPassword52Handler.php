<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Audit;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * audit:password implementation for Moodle 5.1.
 */
class AuditPassword52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('reveal', 'r', InputOption::VALUE_NONE, 'Show the matched password')
            ->addOption('userid', null, InputOption::VALUE_REQUIRED, 'Only check this user ID');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $reveal = $input->getOption('reveal');
        $userId = $input->getOption('userid');

        $verbose->step('Loading password list');
        $passwordsFile = dirname(__DIR__, 3) . '/includes/passwords.php';
        if (!file_exists($passwordsFile)) {
            $output->writeln('<error>Password list file not found: ' . $passwordsFile . '</error>');
            return Command::FAILURE;
        }
        require $passwordsFile;
        /** @var array $passwords */

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/authlib.php';
        require_once $CFG->libdir . '/moodlelib.php';

        // Get users to check.
        if ($userId !== null) {
            $users = $DB->get_records('user', ['id' => (int) $userId]);
            if (empty($users)) {
                $output->writeln("<error>User with ID $userId not found.</error>");
                return Command::FAILURE;
            }
        } else {
            $users = $DB->get_records('user', ['deleted' => 0]);
        }

        $verbose->step('Auditing ' . count($users) . ' user(s) against ' . count($passwords) . ' passwords');

        $headers = $reveal ? ['id', 'username', 'password'] : ['id', 'username'];
        $rows = [];
        $checked = 0;

        foreach ($users as $user) {
            if ($user->username === 'guest') {
                continue;
            }

            $checked++;
            if ($checked % 10 === 0) {
                $verbose->info("Checked $checked users...");
            }

            foreach ($passwords as $password) {
                if (validate_internal_user_password($user, $password)) {
                    $rows[] = $reveal
                        ? [$user->id, $user->username, $password]
                        : [$user->id, $user->username];
                    break;
                }
            }
        }

        $verbose->done("Checked $checked user(s), found " . count($rows) . ' with weak password(s)');

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
