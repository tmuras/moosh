<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Search;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Search for a question ID across all database tables.
 *
 * Replaces Moosh\Command\Moodle45\Question\QuestionIdSearch.
 * Canonical name: search:questionid  |  Alias: search-questionid
 */
class SearchQuestionIdCommand extends BaseCommand
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
            ->setName('search:questionid')
            ->setDescription('Search for a question ID across all database tables')
            ->setHelp(
                "Finds all references to a question ID in the database: columns named 'questionid',\n" .
                "child questions (parent field), question bank entries, versions, references, and files."
            );

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
        if ($moodleVersion !== null && $moodleVersion->isAtLeast('5.2')) {
            return new SearchQuestionId52Handler();
        }
        return new SearchQuestionId51Handler();
    }
}
