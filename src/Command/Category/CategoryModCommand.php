<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Category;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CategoryModCommand extends BaseCommand
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
            ->setName('category:mod')
            ->setDescription('Modify, move, resort, or manage courses in a category')
            ->setHelp(<<<'HELP'
                Modifies category properties, moves it, reorders children/courses, or moves courses out.

                Examples:
                  category:mod 5 --name "Renamed" --run
                  category:mod 5 --visible 0 --run
                  category:mod 5 --parent 0 --run                  (move to top level)
                  category:mod 5 --parent 3 --run                  (move under category 3)
                  category:mod 5 --sortorder first --run            (move to first position)
                  category:mod 5 --resort name --run                (sort subcategories by name)
                  category:mod 5 --resort-courses fullname --run    (sort courses by name)
                  category:mod 5 --resort-courses fullname --recursive --run
                  category:mod 5 --move-courses 3 --run             (move all courses to cat 3)
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
        return new CategoryMod51Handler();
    }
}
