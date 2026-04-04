<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\User;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Modify user properties, system roles, or admin status.
 *
 * Merges moosh1's user-mod, user-assign-system-role, and user-unassign-system-role.
 * Canonical name: user:mod  |  Aliases: user-mod, user-assign-system-role, user-unassign-system-role
 */
class UserModCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::FullNoAdminCheck;

    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = $this->resolveHandler($moodleVersion);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:mod')
            ->setDescription('Modify user properties, system roles, or admin status')
            ->setHelp(<<<'HELP'
                Modify one or more users. Accepts usernames or IDs.

                Property changes:
                  user:mod admin --email=new@example.com --run
                  user:mod admin --password=NewPass123! --run
                  user:mod admin --auth=ldap --run
                  user:mod admin --firstname=John --lastname=Doe --run
                  user:mod student01 --suspended=1 --run

                System role assignment:
                  user:mod admin --assign-role=manager --run
                  user:mod admin --unassign-role=manager --run

                Admin status:
                  user:mod student01 --global-admin --run
                  user:mod student01 --remove-global-admin --run
                HELP);

        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler
    {
        return $this->handler;
    }

    protected function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $verbose->step('Delegating to handler: ' . get_class($this->handler));
        return $this->handler->handle($input, $output);
    }

    private function resolveHandler(?MoodleVersion $moodleVersion): BaseHandler
    {
        return new UserMod51Handler();
    }
}
