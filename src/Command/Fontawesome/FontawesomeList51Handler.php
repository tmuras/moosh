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

class FontawesomeList51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('search', InputArgument::OPTIONAL, 'Filter icons by name (substring match)')
            ->addOption('style', null, InputOption::VALUE_REQUIRED, 'Filter by style: solid, brands');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $search = $input->getArgument('search');
        $styleFilter = $input->getOption('style');

        $scssPath = $CFG->dirroot . '/theme/boost/scss/fontawesome/_variables.scss';
        if (!file_exists($scssPath)) {
            $output->writeln('<error>Font Awesome SCSS variables file not found.</error>');
            return Command::FAILURE;
        }

        $verbose->step('Parsing Font Awesome SCSS variables');
        $scss = file_get_contents($scssPath);

        // Parse all $fa-var-{name}: \{codepoint}; entries
        $allIcons = [];
        if (preg_match_all('/^\$fa-var-([\w-]+)\s*:\s*\\\\([0-9a-f]+)\s*;/m', $scss, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $allIcons[$m[1]] = $m[2];
            }
        }

        $verbose->detail('Total icon variables', (string) count($allIcons));

        // Parse $fa-brand-icons map to identify brand icons
        $brandIcons = $this->parseIconMap($scss, 'fa-brand-icons');
        $solidIcons = $this->parseIconMap($scss, 'fa-icons');

        $verbose->detail('Solid/regular icons', (string) count($solidIcons));
        $verbose->detail('Brand icons', (string) count($brandIcons));

        // Build rows
        $rows = [];
        foreach ($allIcons as $name => $codepoint) {
            $style = isset($brandIcons[$name]) ? 'brands' : 'solid';

            if ($styleFilter !== null && $style !== $styleFilter) {
                continue;
            }

            if ($search !== null && stripos($name, $search) === false) {
                continue;
            }

            $cssClass = $style === 'brands' ? 'fa-brands' : 'fa-solid';
            $html = "<i class=\"$cssClass fa-$name\"></i>";

            $rows[] = [
                'name' => $name,
                'codepoint' => $codepoint,
                'style' => $style,
                'html' => $html,
            ];
        }

        usort($rows, fn(array $a, array $b) => strcmp($a['name'], $b['name']));

        $formatter = new ResultFormatter($output, $format);
        $formatter->display(['name', 'codepoint', 'style', 'html'], $rows);

        return Command::SUCCESS;
    }

    /**
     * Parse a SCSS icon map ($fa-icons or $fa-brand-icons) and return icon names.
     *
     * @return array<string, true> icon name => true
     */
    private function parseIconMap(string $scss, string $mapName): array
    {
        $icons = [];

        // Find the map block: $fa-icons: ( ... );  or  $fa-brand-icons: ( ... );
        $pattern = '/\$' . preg_quote($mapName, '/') . '\s*:\s*\((.*?)\)\s*(!default\s*)?;/s';
        if (preg_match($pattern, $scss, $match)) {
            // Each entry looks like:  "icon-name": $fa-var-icon-name,
            if (preg_match_all('/"([\w-]+)"/', $match[1], $names)) {
                foreach ($names[1] as $name) {
                    $icons[$name] = true;
                }
            }
        }

        return $icons;
    }
}
