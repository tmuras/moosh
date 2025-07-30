<?php
/**
 * This commands imports Moodle configuration settings from a JSON file.
 *
 * Usage:
 *   moosh config import --input-file=path/to/file.json [--skip-existing]
 *
 * Options:
 *   --input-file: Path to the input JSON file. (required)
 *   --skip-existing: Skip existing config values during import. (optional)
 *
 * ⚠️ WARNING: This command modifies the database directly and can corrupt your Moodle config if not used carefully.
 * Always backup your database before running this command.
 * It is recommended to validate the JSON input before importing.
 *
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Config;

use InvalidArgumentException;
use Moosh\MooshCommand;
use RuntimeException;
use Throwable;

/**
 * Class ConfigImport
 *
 * @package Moosh\Command\Moodle39\Config
 */
class ConfigImport extends MooshCommand {

    public function __construct() {
        parent::__construct('import', 'config');
        $this->addOption(
            'i|input-file:',
            'Input JSON file for import.',
        );
        $this->addOption(
            's|skip-existing:',
            'Skip existing config values during import.',
        );
    }

    public function execute() {
        global $DB;
        $this->db = $DB;
        $inputfile = $this->expandedOptions['input-file'];

        if (!$inputfile || !file_exists($inputfile) || !is_readable($inputfile)) {
            $this->error(sprintf("Input file '%s' does not exist or is not readable.", $inputfile));
            exit(1);
        }

        $choice = cli_input(
            "⚠️ ⚠️ ⚠️  DANGER ⚠️ ⚠️ ⚠️\n"
            . "Running this command will modify your database and potentially corrupt your \n"
            . "Moodle config if import payload is not validated carefully.\n"
            . "Make sure you backup your database first!\n\n"
            . "Are you sure you want to proceed? (y/n)",
            'n',
            ['y', 'n']
        );

        if ($choice !== 'y') {
            $this->message('Command canceled.');
            exit(1);
        }

        try {
            $this->import_config($inputfile);
        } catch (Throwable $exception) {
            $this->error(sprintf("Error: %s\nTerminating due to error.", $exception->getMessage()));
        }
    }

    private function error(string $message): void {
        fwrite(STDERR, $message . PHP_EOL);
    }

    private function message(string $message): void {
        fwrite(STDERR, $message . PHP_EOL);
    }

    private function import_config(string $inputfile): void {
        $overwrite = !$this->expandedOptions['skip-existing'];

        if ($this->verbose) {
            $this->message(sprintf('Importing config from %s with overwrite set to %s', $inputfile, $overwrite ? 'true' : 'false'));
        }

        if (false === ($json = file_get_contents($inputfile))) {
            throw new RuntimeException('Failed to read input file: ' . $inputfile);
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new InvalidArgumentException('Invalid JSON input.');
        }

        $count = 0;
        foreach ($data as $name => $value) {
            $record_exists = $this->db->record_exists('config', ['name' => $name]);
            if ($record_exists && $overwrite) {
                $this->db->set_field('config', 'value', $value, ['name' => $name]);
            } else if (!$record_exists) {
                $this->db->insert_record('config', ['name' => $name, 'value' => $value]);
            }
            $count++;
        }

        purge_all_caches();
        $this->message(sprintf('Imported %d config values and purged all caches.', $count));
    }
}
