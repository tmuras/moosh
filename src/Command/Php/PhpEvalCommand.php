<?php
namespace Moosh2\Command\Php;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpEvalCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;
    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion)
    {
        $this->handler = new PhpEval52Handler();
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('php:eval')
            ->setDescription('Evaluate PHP code in Moodle context')
            ->setHelp("Evaluates PHP code with access to all Moodle globals (\$CFG, \$DB, \$USER, etc.).\n\nExamples:\n  php:eval 'echo \$CFG->wwwroot;'\n  php:eval 'echo \$DB->count_records(\"user\");'");
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }
    protected function handle(InputInterface $input, OutputInterface $output): int { return $this->handler->handle($input, $output); }
}
