<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Sql;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Execute arbitrary SQL against the Moodle database.
 *
 * Canonical name: sql:run  |  Alias: sql-run
 */
class SqlRunCommand extends BaseCommand
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
            ->setName('sql:run')
            ->setDescription('Execute arbitrary SQL against the Moodle database')
            ->setHelp(<<<'HELP'
                Executes a SQL query using Moodle's database API.
                SELECT queries display results in the chosen format.
                Write queries (INSERT, UPDATE, DELETE) require --run.

                Uses Moodle table placeholders: {user}, {course}, etc.

                Examples:
                  sql:run "SELECT id, username FROM {user} LIMIT 5"
                  sql:run "SELECT id, username FROM {user}" --limit=5 -o json
                  sql:run "UPDATE {user} SET city='London' WHERE id=2" --run
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
        return new SqlRun52Handler();
    }
}
