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

class ThemeSettingsImport51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the .tar.gz settings archive')
            ->addOption('target-theme', null, InputOption::VALUE_REQUIRED, 'Import settings into a different theme');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $filePath = $input->getArgument('file');
        $targetTheme = $input->getOption('target-theme');

        if (!file_exists($filePath)) {
            $output->writeln("<error>File not found: $filePath</error>");
            return Command::FAILURE;
        }

        // Extract archive
        $verbose->step('Extracting archive');
        $extractDir = sys_get_temp_dir() . '/moosh_theme_import_' . uniqid();
        mkdir($extractDir, 0755, true);

        try {
            $this->extractArchive($filePath, $extractDir);
        } catch (\Exception $e) {
            $this->cleanup($extractDir);
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        // Find and parse XML
        $xmlFile = $this->findXmlFile($extractDir);
        if ($xmlFile === null) {
            $this->cleanup($extractDir);
            $output->writeln('<error>No settings XML file found in the archive.</error>');
            return Command::FAILURE;
        }

        $verbose->step('Parsing settings');
        $dom = new \DOMDocument();
        if (!$dom->load($xmlFile)) {
            $this->cleanup($extractDir);
            $output->writeln('<error>Failed to parse XML settings file.</error>');
            return Command::FAILURE;
        }

        $themeElement = $dom->documentElement;
        $themeName = $targetTheme ?? $themeElement->getAttribute('name');
        $themeComponent = $targetTheme !== null ? "theme_{$targetTheme}" : $themeElement->getAttribute('component');

        // Validate theme is installed
        $availableThemes = \core_plugin_manager::instance()->get_plugins_of_type('theme');
        if (!isset($availableThemes[$themeName])) {
            $this->cleanup($extractDir);
            $output->writeln("<error>Theme '$themeName' is not installed.</error>");
            $output->writeln('Available themes: ' . implode(', ', array_keys($availableThemes)));
            return Command::FAILURE;
        }

        $settings = $themeElement->getElementsByTagName('setting');

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following settings would be imported (use --run to execute):</info>');
            $output->writeln("  Theme:     $themeName");
            $output->writeln("  Component: $themeComponent");
            $output->writeln("  Settings:  {$settings->length}");
            $output->writeln('');
            foreach ($settings as $setting) {
                $name = $setting->getAttribute('name');
                $hasFile = $setting->hasAttribute('file') ? ' [file]' : '';
                $value = mb_substr($setting->nodeValue, 0, 60);
                $output->writeln("  $name = $value$hasFile");
            }
            $this->cleanup($extractDir);
            return Command::SUCCESS;
        }

        // Import settings
        $verbose->step("Importing settings into $themeComponent");
        $context = \context_system::instance();
        $fs = get_file_storage();
        $imported = 0;

        foreach ($settings as $setting) {
            $settingName = $setting->getAttribute('name');
            $settingValue = $setting->nodeValue;

            // Handle files
            if ($setting->hasAttribute('file')) {
                $fileHash = $setting->getAttribute('file');
                $fileSrc = $extractDir . '/' . $fileHash;

                if (file_exists($fileSrc)) {
                    $filename = ltrim($settingValue, '/');
                    $fileInfo = [
                        'contextid' => $context->id,
                        'component' => $themeComponent,
                        'filearea' => $settingName,
                        'itemid' => 0,
                        'filepath' => '/',
                        'filename' => $filename,
                    ];

                    // Remove existing file if present
                    $fs->delete_area_files($context->id, $themeComponent, $settingName, 0);

                    // Create new file
                    $fs->create_file_from_pathname($fileInfo, $fileSrc);
                    $verbose->info("Imported file for setting: $settingName");
                }
            }

            // Save setting to config_plugins
            $record = new \stdClass();
            $record->plugin = $themeComponent;
            $record->name = $settingName;
            $record->value = $settingValue;

            $existing = $DB->get_record('config_plugins', [
                'plugin' => $record->plugin,
                'name' => $record->name,
            ]);

            if ($existing) {
                $record->id = $existing->id;
                $DB->update_record('config_plugins', $record);
            } else {
                $DB->insert_record('config_plugins', $record);
            }

            $imported++;
        }

        $this->cleanup($extractDir);

        $verbose->done("Imported $imported setting(s)");
        $output->writeln("$imported settings imported to $themeComponent.");

        return Command::SUCCESS;
    }

    private function extractArchive(string $filePath, string $extractDir): void
    {
        // Decompress .tar.gz to .tar
        $phar = new \PharData($filePath);
        $tarPath = rtrim($filePath, '.gz');

        // If the file ends with .tar.gz, decompress first
        if (str_ends_with($filePath, '.tar.gz') || str_ends_with($filePath, '.tgz')) {
            // Remove stale .tar from a previous run to avoid PharData error
            $baseName = basename($filePath);
            $tarPath = dirname($filePath) . '/' . preg_replace('/\.(tar\.gz|tgz)$/', '.tar', $baseName);
            if (file_exists($tarPath)) {
                unlink($tarPath);
            }
            $phar->decompress();
            // The decompressed tar lands next to the original
            $baseName = basename($filePath);
            $tarPath = dirname($filePath) . '/' . preg_replace('/\.(tar\.gz|tgz)$/', '.tar', $baseName);
        }

        if (!file_exists($tarPath)) {
            throw new \RuntimeException("Failed to decompress archive: $tarPath not found.");
        }

        // Extract tar contents
        $tar = new \PharData($tarPath);
        $tar->extractTo($extractDir, null, true);

        // Cleanup the decompressed tar if it's different from original
        if ($tarPath !== $filePath) {
            @unlink($tarPath);
        }
    }

    private function findXmlFile(string $dir): ?string
    {
        $files = glob($dir . '/*_settings.xml');
        if ($files && count($files) > 0) {
            return $files[0];
        }

        // Also check one level deep (PharData might create a subdirectory)
        $files = glob($dir . '/*/*_settings.xml');
        if ($files && count($files) > 0) {
            return $files[0];
        }

        return null;
    }

    private function cleanup(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($dir);
    }
}
