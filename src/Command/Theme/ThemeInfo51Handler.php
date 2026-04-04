<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Theme;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThemeInfo51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument(
            'theme',
            InputArgument::OPTIONAL,
            'Theme name to show detailed info for (e.g. boost). If omitted, shows an overview.',
        );
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $themeName = $input->getArgument('theme');

        if ($themeName !== null) {
            return $this->showThemeDetail($themeName, $format, $verbose, $output);
        }

        return $this->showOverview($format, $verbose, $output);
    }

    private function showOverview(string $format, VerboseLogger $verbose, OutputInterface $output): int
    {
        global $CFG, $DB;

        $data = [];

        // Site theme
        $verbose->step('Checking site theme');
        $data['Site theme'] = $CFG->theme ?? '<not set>';

        // Installed themes
        $availableThemes = \core_plugin_manager::instance()->get_plugins_of_type('theme');
        $data['Installed themes'] = count($availableThemes);

        // Course theme overrides
        $verbose->step('Checking course theme overrides');
        $data['Course theme overrides'] = !empty($CFG->allowcoursethemes) ? 'enabled' : 'disabled';
        $courseThemes = $DB->get_records_sql(
            "SELECT theme, COUNT(*) AS n FROM {course} WHERE theme <> '' GROUP BY theme",
        );
        if ($courseThemes) {
            $parts = [];
            foreach ($courseThemes as $ct) {
                $parts[] = "{$ct->theme} ({$ct->n})";
            }
            $data['Course themes in use'] = implode(', ', $parts);
        } else {
            $data['Course themes in use'] = 'none';
        }

        // Category theme overrides
        $verbose->step('Checking category theme overrides');
        $data['Category theme overrides'] = !empty($CFG->allowcategorythemes) ? 'enabled' : 'disabled';
        $catThemes = $DB->get_records_sql(
            "SELECT theme, COUNT(*) AS n FROM {course_categories} WHERE theme <> '' GROUP BY theme",
        );
        if ($catThemes) {
            $parts = [];
            foreach ($catThemes as $ct) {
                $parts[] = "{$ct->theme} ({$ct->n})";
            }
            $data['Category themes in use'] = implode(', ', $parts);
        } else {
            $data['Category themes in use'] = 'none';
        }

        // User theme overrides
        $verbose->step('Checking user theme overrides');
        $data['User theme overrides'] = !empty($CFG->allowuserthemes) ? 'enabled' : 'disabled';
        $userThemes = $DB->get_records_sql(
            "SELECT theme, COUNT(*) AS n FROM {user} WHERE theme <> '' AND deleted = 0 GROUP BY theme",
        );
        if ($userThemes) {
            $parts = [];
            foreach ($userThemes as $ut) {
                $parts[] = "{$ut->theme} ({$ut->n})";
            }
            $data['User themes in use'] = implode(', ', $parts);
        } else {
            $data['User themes in use'] = 'none';
        }

        // Render
        if ($format === 'table') {
            $table = new Table($output);
            $table->setHeaders(['Metric', 'Value']);
            foreach ($data as $key => $value) {
                $table->addRow([$key, $value]);
            }
            $table->render();
        } else {
            $formatter = new ResultFormatter($output, $format);
            $formatter->display(array_keys($data), [array_values($data)]);
        }

        return Command::SUCCESS;
    }

    private function showThemeDetail(string $themeName, string $format, VerboseLogger $verbose, OutputInterface $output): int
    {
        global $CFG, $DB;

        $availableThemes = \core_plugin_manager::instance()->get_plugins_of_type('theme');
        if (!isset($availableThemes[$themeName])) {
            $output->writeln("<error>Theme '$themeName' not found.</error>");
            $output->writeln('Available themes: ' . implode(', ', array_keys($availableThemes)));
            return Command::FAILURE;
        }

        $themePlugin = $availableThemes[$themeName];

        $verbose->step("Loading theme $themeName");

        $data = [];
        $data['Name'] = $themeName;
        $data['Component'] = $themePlugin->component;
        $data['Version (disk)'] = $themePlugin->versiondisk ?? 'unknown';
        $data['Version (DB)'] = $themePlugin->versiondb ?? 'not installed';
        $data['Status'] = $themePlugin->get_status();
        $data['Directory'] = $themePlugin->rootdir;

        // Check if it's the active site theme
        $data['Active site theme'] = ($CFG->theme === $themeName) ? 'yes' : 'no';

        // Parent themes
        try {
            $themeConfig = \theme_config::load($themeName);
            $parents = $themeConfig->parents ?? [];
            $data['Parent themes'] = $parents ? implode(', ', $parents) : 'none';
        } catch (\Exception $e) {
            $data['Parent themes'] = 'error loading theme';
        }

        // Usage counts
        $verbose->step('Counting usage');
        $courseCount = $DB->count_records('course', ['theme' => $themeName]);
        $data['Courses using theme'] = $courseCount;

        $categoryCount = $DB->count_records('course_categories', ['theme' => $themeName]);
        $data['Categories using theme'] = $categoryCount;

        $userCount = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {user} WHERE theme = ? AND deleted = 0",
            [$themeName],
        );
        $data['Users using theme'] = $userCount;

        // Settings count
        $verbose->step('Reading settings');
        $settingsCount = $DB->count_records('config_plugins', ['plugin' => 'theme_' . $themeName]);
        $data['Configuration settings'] = $settingsCount;

        // Render
        if ($format === 'table') {
            $table = new Table($output);
            $table->setHeaders(['Metric', 'Value']);
            foreach ($data as $key => $value) {
                $table->addRow([$key, $value]);
            }
            $table->render();
        } else {
            $formatter = new ResultFormatter($output, $format);
            $formatter->display(array_keys($data), [array_values($data)]);
        }

        return Command::SUCCESS;
    }
}
