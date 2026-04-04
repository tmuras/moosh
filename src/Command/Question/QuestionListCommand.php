<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Question;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QuestionListCommand extends BaseCommand
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
            ->setName('question:list')
            ->setDescription('List questions in a course')
            ->setHelp(<<<'HELP'
                Lists questions in a course context, optionally filtered by category or type.

                Examples:
                  question:list 2
                  question:list 2 --category 5
                  question:list 2 --qtype multichoice
                  question:list 2 -o csv
                HELP);
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }

    protected function handle(InputInterface $input, OutputInterface $output): int
    {
        return $this->handler->handle($input, $output);
    }

    private function resolveHandler(?MoodleVersion $moodleVersion): BaseHandler
    {
        return new QuestionList52Handler();
    }
}
