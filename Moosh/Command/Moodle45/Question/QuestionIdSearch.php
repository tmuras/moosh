<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2026 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle45\Question;

use Moosh\MooshCommand;
use xmldb_table;


/**
 * Class QuestionIdSearch
 *
 * Search for given questionid in all tables and columns.
 *
 * @package Moosh\Command\Moodle45\Question
 */
class QuestionIdSearch extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('id-search', 'question');

        $this->addArgument('questionid');
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_FULL_NO_ADMIN_CHECK;
    }

    public function execute()
    {
        global $DB;

        $questionid = $this->arguments[0];

        $manager = $DB->get_manager();
        $schema = $manager->get_install_xml_schema();
        $tables = $schema->getTables();
        $matches = [];
        foreach ($tables as $table) {
            /** @var xmldb_table $table */
            $columns = $table->getFields();

            foreach ($columns as $column) {
                $type = $column->getType();
                if ($type != XMLDB_TYPE_INTEGER) {
                    continue;
                }
                $name = $column->getName();
                if ($name !== 'questionid') {
                    continue;
                }
                if (!isset($matches[$table->getName()])) {
                    $matches[$table->getName()] = [];
                }
                $matches[$table->getName()][] = $name;
                if (!$table->getField('id')) {
                    echo "Table: {$table->getName()} does not have 'id' column!\n";
                }
            }
        }

        // Search for child questions where parent = questionid.
        $children = $DB->get_records('question', ['parent' => $questionid]);
        if ($children) {
            echo "Table: question\n";
            echo "Records matching parent=$questionid:\n";
            foreach ($children as $child) {
                echo "id={$child->id}, name={$child->name}, qtype={$child->qtype}\n";
            }
            echo "\n";
        }

        // Search for files associated with this question.
        $files = $DB->get_records_select('files', "component = 'question' AND itemid = ?", [$questionid]);
        if ($files) {
            echo "Table: files\n";
            echo "Records matching component='question' AND itemid=$questionid:\n";
            foreach ($files as $file) {
                echo "id={$file->id}, filearea={$file->filearea}, filename={$file->filename}, filesize={$file->filesize}\n";
            }
            echo "\n";
        }

        foreach ($matches as $table => $columns) {
            $sql = "SELECT id," . implode(',', $columns) . " FROM {{$table}} WHERE";
            $values = [];
            foreach ($columns as $column) {
                $sql .= " $column = ? OR";
                $values[] = $questionid;
            }

            $sql = rtrim($sql, 'OR');
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
