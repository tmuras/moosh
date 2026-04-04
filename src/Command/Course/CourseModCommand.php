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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Modify course properties, move to category, or toggle enrolment methods.
 *
 * Canonical name: course:mod  |  Aliases: course-mod, course-move, course-config-set
 */
class CourseModCommand extends BaseCommand
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
            ->setName('course:mod')
            ->setDescription('Modify course properties, move to category, or toggle enrolment methods')
            ->setHelp(<<<'HELP'
                Modifies course settings, moves a course to a different category,
                or enables/disables guest and self-enrolment.

                Examples:
                  course:mod 2 --fullname "New Course Name" --run
                  course:mod 2 --visible 0 --run
                  course:mod 2 --category 3 --run
                  course:mod 2 --startdate "2025-09-01" --enddate "2026-06-30" --run
                  course:mod 2 --guest 1 --run
                  course:mod 2 --selfenrol 1 --run
                  course:mod 2 --format weeks --run
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
        return new CourseMod51Handler();
    }
}
