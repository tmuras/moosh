<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Enrol;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Modify an enrolment method instance.
 *
 * Canonical name: enrol:mod  |  Aliases: enrol-mod, course-enrol-change-status, course-disable-enrolment
 */
class EnrolModCommand extends BaseCommand
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
            ->setName('enrol:mod')
            ->setDescription('Modify an enrolment method instance')
            ->setHelp(<<<'HELP'
                Modifies enrolment instance properties (status, role, name).

                Examples:
                  enrol:mod 5 --enabled 0 --run
                  enrol:mod 5 --enabled 1 --run
                  enrol:mod 5 --roleid 3 --run
                  enrol:mod 5 --name "Custom Name" --run
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
        return new EnrolMod52Handler();
    }
}
