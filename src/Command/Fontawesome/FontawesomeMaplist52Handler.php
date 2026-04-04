<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Fontawesome;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FontawesomeMaplist52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('search', InputArgument::OPTIONAL, 'Search term to filter icons')
            ->addOption('component', null, InputOption::VALUE_REQUIRED, 'Filter by component (e.g. core, mod_forum, theme)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $search = $input->getArgument('search');
        $component = $input->getOption('component');

        $verbose->step('Loading Font Awesome icon map');

        $iconSystem = \core\output\icon_system::instance(\core\output\icon_system::FONTAWESOME);
        $map = $iconSystem->get_icon_name_map();

        $verbose->done('Loaded ' . count($map) . ' mapping(s)');

        $headers = ['component', 'icon', 'fontawesome'];
        $rows = [];

        foreach ($map as $moodleIcon => $faIcon) {
            // Parse component:icon format.
            $parts = explode(':', $moodleIcon, 2);
            $comp = $parts[0] ?? '';
            $icon = $parts[1] ?? $moodleIcon;

            // Filter by component.
            if ($component !== null && $comp !== $component) {
                continue;
            }

            // Filter by search term.
            if ($search !== null) {
                $haystack = strtolower($moodleIcon . ' ' . $faIcon);
                if (stripos($haystack, strtolower($search)) === false) {
                    continue;
                }
            }

            $rows[] = [$comp, $icon, $faIcon];
        }

        // Sort by component then icon.
        usort($rows, fn($a, $b) => [$a[0], $a[1]] <=> [$b[0], $b[1]]);

        $verbose->done(count($rows) . ' icon(s) after filtering');

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
