<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace Moosh\Command\Moodle39\Report;

use Moosh\MooshCommand;

const TABLE_NAMES = [
    "log" => "mdl_log",
    "grades" => "mdl_grade_grades",
    "grades_history" => "mdl_grade_grades_history",
];



/**
 * Command returns moodle database stats. For format check Moosh web docs.
 */
class DbStats extends MooshCommand {
    public function __construct() {
        parent::__construct('stats', 'db');

        $this->addOption('j|json', 'generate output using json format');
        $this->addOption('e|extended', 'generate extended stats for every table, always in json');
        $this->addOption('H|no-human-readable', 'Do not display sizes in human-readble strings');
    }

    public function execute() {
        $options = $this->expandedOptions;

        if(isset($options['extended'])) {
            $results = $this->fetchStats();

            // returns full json stats
            $results->tables = array_values($results->tables);

            echo json_encode($results);
        } else {
            // returns minimal stats
            $results = $this->fetchStats(3, array_values(TABLE_NAMES));

            $results->rowCount = $results->rowCount . "\u{00A0}";
            // non-breaking space prevents from formatting as size
            foreach($results->tables as $key => $table) {
                $results->tables[$key]->rowCount = $table->rowCount . "\u{00A0}";
            }

            $stats = $this->mapStatsToSimpleFormat($results);

            $this->display($stats, $options['json'], !$options['no-human-readable']);
        }
    }

    /**
     * Fetches database stats and returns plain results. Returns object containing size (int)
     * and tables (array). Additionally, method may include tables provided in $additionalTableStats argument.
     * @param non-negative-int|null $limit results limit
     * @param string[] $additionalTableStats array of table names
     * @return object
     */
    public function fetchStats($limit = null, $additionalTableStats = []) {
        global $DB, $CFG;

        if($limit < 0 || !($limit === null || is_int($limit))) {
            throw new \InvalidArgumentException("limit must be a positive integer or null.");
        }

        if(!is_array($additionalTableStats)) {
            throw new \InvalidArgumentException("additionalTableStats must be an array.");
        }

        // fetching general info
        $informationSchemaSql = "
            SELECT table_name AS 'name',
            ROUND(data_length + index_length) AS 'size'
            FROM information_schema.TABLES
            WHERE table_schema = '$CFG->dbname'
            ORDER BY (data_length + index_length) DESC 
        ";

        $results = $DB->get_records_sql($informationSchemaSql);

        // formatting query result, adding row count and calculating size
        $values = array_values($results);
        $resultsLength = count($values);
        if(is_null($limit) || $limit > $resultsLength) {
            $limit = $resultsLength;
        }

        $databaseSize = 0;
        $rowCount = 0;
        $tableData = [];
        foreach($values as $index => $result) {
            $databaseSize += $result->size;
            $rowCount += $result->row_count;

            // we only calculate size and skips other actions when limit is reached
            // we include tables from additionalTableStats
            if($index >= $limit && !in_array($result->name, $additionalTableStats)) {
                continue;
            }

            // querying row count
            $tableName = $result->name;

            // calculating precise table row count
            $countResult = $DB->get_records_sql("SELECT COUNT(*) AS 'count' from $tableName");

            $firstRow = array_values($countResult)[0];

            $result->rowCount = strval($firstRow->count);

            $tableData[$result->name] = $result;
        }

        return (object) [
            'size' => $databaseSize,
            'rowCount' => $rowCount,
            'tables' => $tableData
        ];
    }

    /**
     * Maps results to array with simple keys and values.
     * @param \stdClass $results full stats data
     * @return array stat names with values
     */
    public function mapStatsToSimpleFormat($results) {
        $tables = $results->tables;
        $tablesIndexed = array_values($tables);

        return ['Database size' => $results->size,
            'Database row count' => $results->rowCount,
            'Biggest table name' => $tablesIndexed[0]->name,
            'Biggest table size' => $tablesIndexed[0]->size,
            'Biggest table number of records' => $tablesIndexed[0]->rowCount,
            '2nd biggest table name' => $tablesIndexed[1]->name,
            '2nd biggest table size' => $tablesIndexed[1]->size,
            '2nd biggest table number of records' => $tablesIndexed[1]->rowCount,
            '3rd biggest table name' => $tablesIndexed[2]->name,
            '3rd biggest table size' => $tablesIndexed[2]->size,
            '3rd biggest table number of records' => $tablesIndexed[2]->rowCount,
            'Log table size' => $tables[TABLE_NAMES['log']]->size,
            'Log table number of records' => $tables[TABLE_NAMES['log']]->rowCount,
            'Grades table size' => $tables[TABLE_NAMES['grades']]->size,
            'Grades table number of records' => $tables[TABLE_NAMES['grades']]->rowCount,
            'Grades history table size' => $tables[TABLE_NAMES['grades_history']]->size,
            'Grades history table number of records' => $tables[TABLE_NAMES['grades_history']]->rowCount,
        ];
    }
}
