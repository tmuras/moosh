<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Role;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Modify a role's properties, capabilities, or context levels.
 *
 * Merges moosh1's role-update-capability and role-update-contextlevel
 * with the ability to change name, description, and archetype.
 *
 * Canonical name: role:mod  |  Aliases: role-mod, role-update-capability, role-update-contextlevel
 */
class RoleModCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;

    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = $this->resolveHandler($moodleVersion);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('role:mod')
            ->setDescription('Modify a role: properties, capabilities, or context levels')
            ->setHelp(<<<'HELP'
                Modify a role's name, description, capabilities, or context levels.

                Property changes:
                  role:mod teacher --name="New Teacher" --run
                  role:mod teacher --description="Updated description" --run

                Capability changes (repeatable):
                  role:mod teacher --capability "mod/forum:addnews=allow" --run
                  role:mod teacher --capability "mod/forum:addnews=prohibit" --capability "block/calendar_month:myaddinstance=prevent" --run

                Context level changes:
                  role:mod teacher --context-on=module,block --run
                  role:mod teacher --context-off=system --run

                Valid permission values: inherit, allow, prevent, prohibit
                Valid context levels: system, user, coursecat, course, module, block
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
        return new RoleMod52Handler();
    }
}
