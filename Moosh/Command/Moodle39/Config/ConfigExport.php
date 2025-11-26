<?php
/**
 * This command exports Moodle configuration settings to a JSON file.
 *
 * Usage:
 *   moosh config export --output-file=path/to/file.json
 *
 * Options:
 *   --output-file: Path to the output file (default: stdout).
 *
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Config;

use moodle_database;
use Moosh\MooshCommand;
use RuntimeException;
use Throwable;

/**
 * Class ConfigExport
 *
 * @package Moosh\Command\Moodle39\Config
 */
class ConfigExport extends MooshCommand {
    private $db = null;

    public function __construct() {
        parent::__construct('export', 'config');
        $this->addOption(
            'o|output-file:',
            'Output file for export (default: stdout).',
            'php://stdout'
        );
    }

    public function execute() {
        global $DB;
        $this->db = $DB;
        $outputfile = $this->expandedOptions['output-file'];

        try {
            $this->export_config($outputfile);
        } catch (Throwable $exception) {
            $this->error(sprintf("Error: %s\nTerminating due to error.", $exception->getMessage()));
        }
    }

    private function export_config(string $outputfile): void {
        $configs = $this->db->get_records('config', [], '', 'name,value');
        $data = [];
        foreach ($configs as $config) {
            $data[$config->name] = $config->value;
        }
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents($outputfile, $json . PHP_EOL) === false) {
            throw new  RuntimeException(sprintf('Failed to write to output file: %s', $outputfile));
        }
        if ($this->verbose) {
            $this->message(sprintf('Exported %d config values to %s', count($data), $outputfile));
        }
    }

    private function message(string $message): void {
        fwrite(STDERR, $message . PHP_EOL);
    }

    private function error(string $message): void {
        fwrite(STDERR, $message . PHP_EOL);
    }
}
