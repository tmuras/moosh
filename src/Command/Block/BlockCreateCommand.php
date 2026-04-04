<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Block;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Add a block instance to a course, category, or all courses in a category.
 *
 * Canonical name: block:create  |  Aliases: block-create, block:add, block-add
 */
class BlockCreateCommand extends BaseCommand
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
            ->setName('block:create')
            ->setDescription('Add a block instance to a course, category, or site')
            ->setHelp(<<<'HELP'
                Adds a block instance to a course, category, all courses in a category, or the site front page.

                Examples:
                  block:create calendar_month 2 --run
                  block:create online_users 2 --region side-post --weight 5 --run
                  block:create html 1 --mode category --run
                  block:create calendar_month 1 --mode categorycourses --run
                  block:create calendar_month 1 --mode site --run
                  block:create html 2 --pagetypepattern course-view-* --showinsubcontexts 1 --run

                Modes:
                  course           Add to a single course (default)
                  category         Add to a course category page
                  categorycourses  Add to all courses in a category
                  site             Add to the site front page (target argument is ignored)
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
        return new BlockCreate52Handler();
    }
}
