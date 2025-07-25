<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2025 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Report;

use Moosh\MooshCommand;
use xmldb_table;


/**
 * Class TimestampSearch
 *
 * Search for given timestamp in all tables and columns.
 *
 * @package Moosh\Command\Generic\Report
 */
class TimestampSearch extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('search', 'timestamp');

        $this->addOption('f|from:',
            'timestamp from'
        );
        $this->addOption('t|to:',
            'timestamp to');

        $this->addArgument('timestamp');
        $this->minArguments = 0;
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_FULL_NO_ADMIN_CHECK;
    }

    public function execute()
    {
        global $DB;
        $timefrom = false;
        $timeto = false;
        $timeexact = false;

        if (isset($this->expandedOptions['from'])) {
            $timefrom = $this->expandedOptions['from'];
        }
        if (isset($this->expandedOptions['to'])) {
            $timeto = $this->expandedOptions['to'];
        }
        if (isset($this->arguments[0])) {
            $timeexact = $this->arguments[0];
        }
        if (!$timeexact && !$timefrom && !$timeto) {
            echo "You must provide either --from and --to or timestamp argument.\n";
            return 1;
        }
        if ($timeexact && ($timefrom || $timeto)) {
            echo "You cannot provide both timestamp argument and --from/--to options.\n";
            return 1;
        }
        if ($timefrom && !$timeto) {
            echo "You must provide --to option when using --from option.\n";
            return 1;
        }
        if ($timeto && !$timefrom) {
            echo "You must provide --from option when using --to option.\n";
            return 1;
        }

        $manager = $DB->get_manager();
        $schema = $manager->get_install_xml_schema();
        $tables = $schema->getTables();
        $matches = [];
        foreach ($tables as $table) {
            /** @var xmldb_table $table */
            $columns = $table->getFields();

            foreach ($columns as $column) {
                // XMLDB_TYPE_INTEGER, XMLDB_TYPE_NUMBER, XMLDB_TYPE_CHAR, XMLDB_TYPE_TEXT, XMLDB_TYPE_BINARY
                $type = $column->getType();
                if ($type != XMLDB_TYPE_INTEGER) {
                    continue;
                }
                // check if column name contains string 'time'
                $name = $column->getName();
                if (strpos($name, 'time') === false) {
                    continue;
                }
                if (!isset($matches[$table->getName()])) {
                    $matches[$table->getName()] = [];
                }
                $matches[$table->getName()][] = $name;
//                echo "Table: {$table->getName()} Column: $name Type: $type\n";
                if (!$table->getField('id')) {
                    echo "Table: {$table->getName()} does not have 'id' column!\n";
                }
            }
        }

        foreach ($matches as $table => $columns) {
            $sql = "SELECT id," . implode(',', $columns) . " FROM {{$table}} WHERE";
            $values = [];
            foreach ($columns as $column) {
                if ($timeexact) {
                    $sql .= " $column = ? OR";
                    $values[] = $timeexact;
                } else {
                    $sql .= " ($column >= ? AND $column <= ?) OR";
                    $values[] = $timefrom;
                    $values[] = $timeto;
                }
            }

            $sql = rtrim($sql, 'OR');
//            $DB->set_debug(true);
            $rs = $DB->get_recordset_sql($sql, $values);
            if ($rs->valid()) {
                $helpersql = "SELECT * FROM mdl_$table WHERE id IN (";
                echo "Table: $table\n";
                echo "Columns: id, " . implode(', ', $columns) . "\n";
                echo "Records:\n";
                foreach ($rs as $record) {
                    echo $record->id . ",";
                    $helpersql .= $record->id . ',';
                    foreach ($columns as $column) {
                        echo "{$record->$column},";
                    }
                    echo "\n";
                }
                $helpersql = rtrim($helpersql, ',') . ")";
                echo "SQL: $helpersql\n";
            }

        }

    }
}