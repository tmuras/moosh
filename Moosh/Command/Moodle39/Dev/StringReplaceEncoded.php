<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Dev;

use Moosh\MooshCommand;

class StringReplaceEncoded extends MooshCommand
{
    public function __construct()
    {
        // moosh string-replace-encoded from to table column
        parent::__construct('replace-encoded', 'string');

        $this->addArgument('from');
        $this->addArgument('to');
        $this->addArgument('table');
        $this->addArgument('column');

        $this->addOption('d|dry-run', "don't perform the UPDATE", false);

    }


    public function execute()
    {
        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory

        global $CFG, $DB, $USER;

        $from = $this->arguments[0];
        $to = $this->arguments[1];
        $table = $this->arguments[2];
        $column = $this->arguments[3];
        $dryrun = $this->expandedOptions['dry-run'];

        $possiblerecords = $DB->get_records_sql("SELECT id, $column FROM {{$table}} WHERE $column != ''");
        $recordsupdated = 0;
        foreach ($possiblerecords as $possiblerecord) {
            $decoded = base64_decode($possiblerecord->$column);
            if (strpos($decoded, $from) === false) {
                continue;
            }
            $unserialized = unserialize_object($decoded);

            // Iterate over all public properties of the object
            foreach ($unserialized as $key => $value) {
                // Check if $value is a type of string
                if (is_string($value)) {
                    if (strpos($value, $from) !== false) {
                        $unserialized->$key = str_replace($from, $to, $value);
                        if ($this->verbose) {
                            echo "Replacing in '$key' from\n$value\nto\n{$unserialized->$key}\n";
                        }
                    }
                } elseif (is_array($value)) {
                    foreach ($value as $k => $v) {
                        if (!is_string($v)) {
                            continue;
                        }
                        if (strpos($v, $from) !== false) {
                            $unserialized->$key[$k] = str_replace($from, $to, $v);
                            if ($this->verbose) {
                                echo "Replacing in '$key'[$k] from\n$v\nto\n{$unserialized->$key[$k]}\n";
                            }
                        }

                    }
                }
            }
            $encoded = base64_encode(serialize($unserialized));
            if (!$dryrun && $possiblerecord->$column != $encoded ) {
                $recordsupdated++;
                $DB->set_field($table, $column, $encoded, ['id' => $possiblerecord->id]);
            }
        }

        echo "Records updated: $recordsupdated\n";
    }
}
