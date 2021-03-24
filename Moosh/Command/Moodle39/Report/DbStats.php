<?php

namespace Moosh\Command\Moodle39\Report;

use Moosh\MooshCommand;

class DbStats extends MooshCommand {
    public function __construct() {
        parent::__construct('stats', 'db');

        $this->addOption('j|json', 'generate output using json format');
        $this->addOption('H|no-human-readable', 'Do not display sizes in human-readble strings');
    }

    public function execute() {
        global $DB, $CFG;

        $options = $this->expandedOptions;

        $sql = "SELECT table_name AS 'table',
                  ROUND(((data_length + index_length))) AS 'size'
                  FROM information_schema.TABLES
                  WHERE table_schema = '" . $CFG->dbname . "'
                  ORDER BY (data_length + index_length) DESC";
        $results = $DB->get_records_sql($sql);

        $databasesize = 0;
        $topsizes = [];
        $topnames = [];
        foreach ($results as $result) {
            if (!isset($topsizes[0])) {
                $topsizes[0] = $result->size;
                $topnames[0] = $result->table;
            } else if (!isset($topsizes[1])) {
                $topsizes[1] = $result->size;
                $topnames[1] = $result->table;
            } else if (!isset($topsizes[2])) {
                $topsizes[2] = $result->size;
                $topnames[2] = $result->table;
            }
            $databasesize += $result->size;
        }

        $data = ['Database size' => $databasesize,
                'Biggest table name' => $topnames[0],
                'Biggest table size' => $topsizes[0],
                '2nd biggest table name' => $topnames[1],
                '2nd biggest table size' => $topsizes[1],
                '3rd biggest table name' => $topnames[2],
                '3rd biggest table size' => $topsizes[2],
        ];

        $this->display($data, $options['json'], !$options['no-human-readable']);
    }
}
