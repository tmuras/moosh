<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Fontawesome;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FontawesomeRefreshCache52Handler extends BaseHandler
{
    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        if (!$runMode) {
            $output->writeln('<info>Dry run — would purge and regenerate Font Awesome icon mapping cache (use --run to execute).</info>');
            return Command::SUCCESS;
        }

        $verbose->step('Purging Font Awesome icon mapping cache');

        $cache = \cache::make('core', 'fontawesomeiconmapping');
        $cache->purge();

        $iconSystem = \core\output\icon_system::instance(\core\output\icon_system::FONTAWESOME);

        $verbose->step('Regenerating icon map');
        $map = $iconSystem->get_icon_name_map();
        $count = count($map);

        $verbose->done("Cache refreshed with $count icon mapping(s)");
        $output->writeln("Font Awesome icon cache refreshed. $count mappings loaded.");

        return Command::SUCCESS;
    }
}
