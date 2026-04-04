<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Course;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Moosh2\Service\ClockInterface;
use Moosh2\Service\SystemClock;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List courses matching optional search criteria.
 *
 * Replaces Moosh\Command\Moodle39\Course\CourseList.
 * Canonical name: course:list  |  Alias: course-list
 *
 * Delegates argument/option definition and execution to a
 * version-specific handler selected at construction time.
 */
class CourseListCommand extends BaseCommand
{
    protected BootstrapLevel $bootstrapLevel = BootstrapLevel::Full;

    private BaseHandler $handler;

    public function __construct(?MoodleVersion $moodleVersion, ?ClockInterface $clock = null)
    {
        $this->handler = $this->resolveHandler($moodleVersion, $clock ?? new SystemClock());
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('course:list')
            ->setDescription('List Moodle courses')
            ->setHelp('Lists courses matching optional search criteria with configurable output fields and format.');

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

    private function resolveHandler(?MoodleVersion $moodleVersion, ClockInterface $clock): BaseHandler
    {
        return new CourseList52Handler($clock);
    }
}
