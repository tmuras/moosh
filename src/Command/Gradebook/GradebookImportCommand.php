<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Gradebook;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Import grades from a CSV file into a course gradebook.
 *
 * Canonical name: gradebook:import  |  Alias: gradebook-import
 */
class GradebookImportCommand extends BaseCommand
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
            ->setName('gradebook:import')
            ->setDescription('Import grades from a CSV file into a course gradebook')
            ->setHelp(<<<'HELP'
                Imports grades from a CSV file into a course's gradebook.
                CSV columns are auto-mapped to grade items by display name.
                Users are matched by email address (default) or ID number.

                Examples:
                  gradebook:import grades.csv 2
                  gradebook:import grades.csv 2 --run
                  gradebook:import grades.csv 2 --map-users-by idnumber --run
                  gradebook:import grades.csv COURSE01 --course-idnumber --run
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
        return new GradebookImport51Handler();
    }
}
