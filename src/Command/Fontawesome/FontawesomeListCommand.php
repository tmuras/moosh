<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Fontawesome;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List all Font Awesome icons available in Moodle's bundled FA library.
 *
 * Canonical name: fontawesome:list  |  Alias: fontawesome-list
 */
class FontawesomeListCommand extends BaseCommand
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
            ->setName('fontawesome:list')
            ->setDescription('List all Font Awesome icons available in Moodle')
            ->setHelp(
                "Lists all Font Awesome icons from Moodle's bundled FA library by parsing\n" .
                "the SCSS variables file. Shows icon name, Unicode codepoint, style (solid/brands),\n" .
                "and the HTML needed to use the icon.\n\n" .
                "Examples:\n" .
                "  moosh fontawesome:list                    # list all icons\n" .
                "  moosh fontawesome:list house               # search for 'house'\n" .
                "  moosh fontawesome:list --style brands      # only brand icons"
            );
        $this->handler->configureCommand($this);
    }

    protected function getActiveHandler(): BaseHandler { return $this->handler; }

    protected function handle(InputInterface $input, OutputInterface $output): int
    {
        return $this->handler->handle($input, $output);
    }

    private function resolveHandler(?MoodleVersion $v): BaseHandler
    {
        return new FontawesomeList52Handler();
    }
}
