<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\User;

use Moosh2\Bootstrap\BootstrapLevel;
use Moosh2\Bootstrap\MoodleVersion;
use Moosh2\Command\BaseCommand;
use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Import user profile pictures from a directory.
 *
 * Canonical name: user:import-pictures  |  Alias: user-import-pictures
 */
class UserImportPicturesCommand extends BaseCommand
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
            ->setName('user:import-pictures')
            ->setDescription('Import user profile pictures from a directory')
            ->setHelp(<<<'HELP'
                Imports profile pictures from a directory, matching image filenames to users.

                Import mode:
                  user:import-pictures /path/to/photos --run
                  user:import-pictures /path/to/photos --match=idnumber --run
                  user:import-pictures /path/to/photos --csv=mapping.csv --run
                  user:import-pictures /path/to/photos --overwrite --run

                Report mode (no directory needed):
                  user:import-pictures --report -o csv
                  user:import-pictures --report-missing -o csv

                Supported image formats: jpg, jpeg, gif, png, webp
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
        return new UserImportPictures51Handler();
    }
}
