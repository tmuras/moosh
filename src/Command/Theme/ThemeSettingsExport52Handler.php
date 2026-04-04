<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Theme;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ThemeSettingsExport52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('theme', InputArgument::REQUIRED, 'Theme name to export (e.g. boost)')
            ->addOption('outputdir', null, InputOption::VALUE_REQUIRED, 'Directory for the output archive (default: current directory)');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $themeName = $input->getArgument('theme');
        $outputDir = $input->getOption('outputdir') ?? getcwd();
        $outputDir = rtrim($outputDir, '/');

        // Validate theme
        $availableThemes = \core_plugin_manager::instance()->get_plugins_of_type('theme');
        if (!isset($availableThemes[$themeName])) {
            $output->writeln("<error>Theme '$themeName' not found.</error>");
            $output->writeln('Available themes: ' . implode(', ', array_keys($availableThemes)));
            return Command::FAILURE;
        }

        if (!is_writable($outputDir)) {
            $output->writeln("<error>Output directory is not writable: $outputDir</error>");
            return Command::FAILURE;
        }

        $themeComponent = $availableThemes[$themeName]->component;

        $verbose->step("Loading theme settings for $themeName");
        $themeConfig = \theme_config::load($themeName);
        $themeSettings = $themeConfig->settings;

        if (empty($themeSettings)) {
            $output->writeln('<info>No settings to export.</info>');
            return Command::SUCCESS;
        }

        $time = time();
        $tarName = "{$outputDir}/{$themeName}_settings_{$time}.tar";

        $verbose->step('Creating archive');
        $phar = new \PharData($tarName);

        // Build XML
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $root = $dom->createElement('theme');
        $root->setAttribute('name', $themeName);
        $root->setAttribute('component', $themeComponent);
        if (isset($themeSettings->version)) {
            $root->setAttribute('version', (string) $themeSettings->version);
        }
        $dom->appendChild($root);

        $fileCount = 0;
        $settingCount = 0;

        foreach ($themeSettings as $settingName => $settingValue) {
            if ($settingName === 'version') {
                continue;
            }

            $element = $dom->createElement('setting');
            $element->appendChild($dom->createTextNode((string) $settingValue));
            $element->setAttribute('name', $settingName);

            // Check if this setting references a file
            if ($settingValue && is_string($settingValue) && $settingValue[0] === '/' && strpos($settingValue, '.') !== false) {
                $fs = get_file_storage();
                $files = $fs->get_area_files(
                    \context_system::instance()->id,
                    $themeComponent,
                    $settingName,
                    false,
                    'id',
                    false,
                );

                foreach ($files as $f) {
                    if (!$f->is_directory()) {
                        $fh = $f->get_content_file_handle();
                        $meta = stream_get_meta_data($fh);
                        $uriParts = explode('/', $meta['uri']);
                        $hash = array_pop($uriParts);

                        $phar->addFile($meta['uri'], $hash);
                        $element->setAttribute('file', $hash);
                        $fileCount++;
                        fclose($fh);
                        break;
                    }
                }
            }

            $root->appendChild($element);
            $settingCount++;
        }

        // Write XML to archive
        $xmlFilename = "{$themeName}_settings.xml";
        $xmlTempPath = "{$outputDir}/{$xmlFilename}";
        $dom->save($xmlTempPath);
        $phar->addFile($xmlTempPath, $xmlFilename);

        // Compress
        $verbose->step('Compressing archive');
        $archivePath = "{$tarName}.gz";
        if (file_exists($archivePath)) {
            unlink($archivePath);
        }
        $phar->compress(\Phar::GZ);

        // Cleanup temp files
        @unlink($xmlTempPath);
        @unlink($tarName);

        $verbose->done("Exported $settingCount setting(s) and $fileCount file(s)");
        $output->writeln("Settings exported to $archivePath");

        return Command::SUCCESS;
    }
}
