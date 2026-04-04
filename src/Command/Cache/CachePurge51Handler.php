<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Cache;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * cache:purge implementation for Moodle 5.1.
 */
class CachePurge51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('store', null, InputOption::VALUE_REQUIRED, 'Purge only this cache store')
            ->addOption('definition', null, InputOption::VALUE_REQUIRED, 'Purge only this definition (component/area)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $storeName = $input->getOption('store');
        $definition = $input->getOption('definition');



        if ($storeName !== null && $definition !== null) {
            $output->writeln('<error>Use --store or --definition, not both.</error>');
            return Command::FAILURE;
        }

        if ($storeName !== null) {
            $verbose->step("Purging cache store '$storeName'");
            $config = \cache_config::instance();
            $stores = $config->get_all_stores();
            if (!isset($stores[$storeName])) {
                $output->writeln("<error>Cache store '$storeName' not found.</error>");
                return Command::FAILURE;
            }
            \cache_helper::purge_store($storeName);
            $output->writeln("Purged cache store '$storeName'.");
            return Command::SUCCESS;
        }

        if ($definition !== null) {
            $parts = explode('/', $definition, 2);
            if (count($parts) !== 2) {
                $output->writeln('<error>Definition must be in component/area format (e.g. core/coursemodinfo).</error>');
                return Command::FAILURE;
            }
            $verbose->step("Purging cache definition '$definition'");
            \cache_helper::purge_by_definition($parts[0], $parts[1]);
            $output->writeln("Purged cache definition '$definition'.");
            return Command::SUCCESS;
        }

        $verbose->step('Purging all caches');
        purge_all_caches();
        $output->writeln('All caches purged.');

        return Command::SUCCESS;
    }
}
