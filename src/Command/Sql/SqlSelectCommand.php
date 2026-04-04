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
 * Run a SELECT query against the Moodle database.
 *
 * Replaces Moosh\Command\Generic\Request\RequestSelect.
 * Canonical name: sql:select  |  Alias: sql-select
 */
class SqlSelectCommand extends BaseCommand
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
            ->setName('sql:select')
            ->setDescription('Run a SELECT query against the Moodle database')
            ->setHelp(
                "Executes a SQL SELECT query using Moodle's database API and outputs results.\n" .
                "Uses Moodle table placeholders: {user}, {course}, {logstore_standard_log}, etc.\n\n" .
                "Examples:\n" .
                "  moosh sql:select \"SELECT username, email FROM {user} WHERE deleted = 0\"\n" .
                "  moosh sql:select -o json \"SELECT id, shortname FROM {course} WHERE id > 1\""
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
        return new SqlSelect52Handler();
    }
}
