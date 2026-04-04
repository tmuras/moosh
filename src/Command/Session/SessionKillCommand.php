<?php
namespace Moosh2\Command\Session;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SessionKillCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new SessionKill52Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('session:kill')
            ->setDescription('Destroy all user sessions')
            ->setHelp('Destroys all active sessions, forcing all users to re-login. Requires --run.');
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
