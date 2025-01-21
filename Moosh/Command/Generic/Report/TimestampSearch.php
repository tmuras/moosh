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
        // Get Moodle database manager.
        global $DB;

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
                $sql .= " $column = ? OR";
                $values[] = $this->arguments[0];
            }
            $sql = rtrim($sql, 'OR');
//            $DB->set_debug(true);
            $records = $DB->get_records_sql($sql, $values);
            if ($records) {
                echo "$sql\n";
                print_r($records);
            }

        }

    }
}