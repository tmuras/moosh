<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Event;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EventList51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addOption('component', null, InputOption::VALUE_REQUIRED, 'Filter by component (core, mod_forum, etc.)')
            ->addOption('crud', null, InputOption::VALUE_REQUIRED, 'Filter by CRUD: c, r, u, d')
            ->addOption('search', null, InputOption::VALUE_REQUIRED, 'Search event names')
            ->addOption('classname-only', null, InputOption::VALUE_NONE, 'Show classnames only');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $idOnly = $input->getOption('classname-only');

        $filterComponent = $input->getOption('component');
        $filterCrud = $input->getOption('crud');
        $filterSearch = $input->getOption('search');

        $verbose->step('Discovering events');

        // Suppress deprecation warnings during event class discovery.
        global $CFG;
        $prevDebug = $CFG->debug;
        $CFG->debug = 0;
        $classes = \core_component::get_component_classes_in_namespace(null, 'event');
        $CFG->debug = $prevDebug;
        $events = [];

        foreach (array_keys($classes) as $classname) {
            if (!is_subclass_of($classname, \core\event\base::class)) {
                continue;
            }

            try {
                $ref = new \ReflectionClass($classname);
                if ($ref->isAbstract()) {
                    continue;
                }

                // Suppress deprecation warnings from deprecated event classes.
                $CFG->debug = 0;
                $info = $classname::get_static_info();
                $CFG->debug = $prevDebug;
            } catch (\Throwable $e) {
                $CFG->debug = $prevDebug;
                continue;
            }

            $component = $info['component'] ?? '';
            $crud = $info['crud'] ?? '';
            $edulevel = $info['edulevel'] ?? 0;

            if ($filterComponent !== null && $component !== $filterComponent) {
                continue;
            }
            if ($filterCrud !== null && $crud !== $filterCrud) {
                continue;
            }
            if ($filterSearch !== null && stripos($classname, $filterSearch) === false) {
                continue;
            }

            $events[] = [
                'classname' => $classname,
                'component' => $component,
                'target' => $info['target'] ?? '',
                'action' => $info['action'] ?? '',
                'crud' => $crud,
                'edulevel' => $edulevel,
            ];
        }

        if (empty($events)) {
            $output->writeln('No events found matching criteria.');
            return Command::SUCCESS;
        }

        // Sort by classname.
        usort($events, fn($a, $b) => strcmp($a['classname'], $b['classname']));

        if ($idOnly) {
            foreach ($events as $event) {
                $output->writeln($event['classname']);
            }
            return Command::SUCCESS;
        }

        $edulevelNames = [0 => 'other', 1 => 'teaching', 2 => 'participating'];
        $headers = ['classname', 'component', 'action', 'target', 'crud', 'edulevel'];
        $rows = [];
        foreach ($events as $e) {
            $rows[] = [$e['classname'], $e['component'], $e['action'], $e['target'], $e['crud'], $edulevelNames[$e['edulevel']] ?? $e['edulevel']];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
