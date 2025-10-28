<?php
/**
 * Moosh command to clean up XSS code from the database that can be used to attack users.
 *
 *  This command helps administrators find and clean up XSS code from the Moodle
 *  database. It operates in three primary modes:
 *
 *  1.  **`detect` mode (default):** Finds and reports all occurrences of XSS
 *      code in the database.
 *
 *  2.  **`defuse` mode:** Finds and replaces all occurrences of XSS code in the
 *      database with their HTML encoded equivalents.
 *
 *  3.  **`clean` mode:** Finds and removes all occurrences of XSS code from the
 *      database.
 *
 *  The output can be formatted as plain text, JSON, XML, or CSV, making it
 *  easy to parse or use in other scripts.
 *
 *  If the `--output-file` option is given, all output will be redirected to the specified file.
 *  Example output files for each format:
 *    JSON:
 *    [
 *      {"id": "123", "table": "my_table", "column": "my_column", "value": "<script>alert(1)</script>"}
 *    ]
 *    XML:
 *      <scan_matches>
 *      <match>
 *      <id>123</id>
 *      <table>my_table</table>
 *      <column>my_column</column>
 *      <occurrences>
 *          <value>&lt;script&gt;alert(1)&lt;/script&gt;</value>
 *          <value>&lt;script&gt;alert(1)&lt;/script&gt;</value>
 *      </occurrences>
 *      </match>
 *      </scan_matches>
 *    CSV:
 *    123,my_table,my_column,"<script>alert(1)</script>; <script>alert(1)</script>
 *
 *
 *  You can also use the `--input-file` and `--input-format` options to process a list of records
 *  from a file (in XML, JSON, or CSV format) instead of scanning the database.
 *  For an example you can first output the results of a scan to a file and then edit it manually
 *  to filter out false positives and load that file as input for this command.
 *
 * Example input files:
 *    XML:
 *    <scan_matches>
 *      <match>
 *        <id>123</id>
 *        <table>my_table</table>
 *        <column>my_column</column>
 *      </match>
 *    </scan_matches>
 *    JSON:
 *    [
 *      {"id": "123", "table": "my_table", "column": "my_column"}
 *    ]
 *    CSV:
 *    123,my_table,my_column
 *
 *  If the `--summary` option is given, a summary of the total number of matches
 *  and records processed will be displayed at the end of the output.
 *
 *  The `--include-expression` option allows you to specify a regular expression; only matches that satisfy this
 *  expression will be processed.
 *
 *  The `--exclude-expression` option allows you to specify a regular expression; matches that satisfy this expression
 *  will be excluded from processing.
 *
 * @package    Moosh\Command\Moodle40\Security
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @example    Detect all occurrences of XSS code in the database (default mode).
 *              $ php moosh.php script-xss-cleanup
 *
 * @example    Defuse all occurrences of XSS code in the database by replacing
 *              them with their HTML encoded equivalents.
 *              $ php moosh.php script-xss-cleanup --mode=defuse
 *
 * @example    Clean all occurrences of XSS code from the database.
 *              $ php moosh.php script-xss-cleanup --mode=clean
 *
 * @example    Output the results in JSON format to a file and show a summary.
 *              $ php moosh.php script-xss-cleanup --output-format=json --output-file=file1.json --summary
 *
 * @example    Clean only records listed in an input file (XML format).
 *              $ php moosh.php script-xss-cleanup --mode=clean --input-file=matches.xml --input-format=xml
 *
 * @example    Defuse records from a CSV input file.
 *              $ php moosh.php script-xss-cleanup --mode=defuse --input-file=matches.csv --input-format=csv
 *
 * @example    Detect XSS in records from a JSON input file and output as plain text.
 *              $ php moosh.php script-xss-cleanup --input-file=matches.json --input-format=json --output-format=plain
 */

namespace Moosh\Command\Moodle40\Security;

use core_php_time_limit;
use core_text;
use database_column_info;
use Generator;
use InvalidArgumentException;
use moodle_database;
use Moosh\MooshCommand;
use SimpleXMLElement;
use Throwable;
use function file_exists;
use function is_readable;

/**
 * @package    Moosh\Command\Moodle40\Security
 * @author     Andrej Vitez <contact@andrejvitez.com>
 * @see        MooshCommand
 */
class XssCleanup extends MooshCommand {

    private const MODE_DEFUSE = 'defuse';
    private const MODE_CLEAN = 'clean';
    private const MODE_DETECT = 'detect';
    private const DEFAULT_MODE = self::MODE_DETECT;

    /**
     * @var string[] Valid modes for file comparison.
     */
    private const VALID_MODES = [
        self::MODE_CLEAN  => 'Clean (remove) all XSS occurrences from the database.',
        self::MODE_DETECT => 'Detect (list) all XSS occurrences in the database.',
        self::MODE_DEFUSE => 'Defuse (replace) all with html encoded value so the XSS can not be executed in browser.',
    ];
    private const VALID_OUTPUT_FORMATS = ['xml', 'json', 'csv', self::DEFAULT_OUTPUT_FORMAT];
    private const VALID_INPUT_FORMATS = [self::DEFAULT_INPUT_FORMAT, 'json', 'csv'];
    private const MATCH_CHAR_LIMIT = 50;
    private const MATCH_PATTERN = '~(\s*<(script|iframe)[^>]*>.*?</\2>\s*)~ius';
    private const DEFAULT_OUTPUT_FORMAT = 'plain';
    private const DEFAULT_INPUT_FORMAT = 'xml';

    private string $output_format;

    /**
     * @var bool Flag for JSON output to handle commas correctly.
     */
    private bool $is_first_json_item = true;

    private ?moodle_database $db = null;

    /**
     * @var false|resource
     */
    private $output;

    private array $excludetables = [
        'config',
        'config_plugins',
        'filter_config',
        'sessions',
        'events_queue',
        'repository_instance_config',
        'block_instances',
        'files',
    ];
    private array $includetables = [];

    private string $mode;
    private int $totalrecords = 0;
    private int $totalmatches = 0;
    private string $include_expression = '';
    private string $exclude_expression = '';

    public function __construct() {
        parent::__construct('xss-cleanup', 'security');

        $modes = [];
        foreach (self::VALID_MODES as $mode => $description) {
            $modes[] = sprintf('"%s": %s', $mode, $description);
        }
        $this->addOption(
            'm|mode:',
            sprintf('Scan mode: [%s]. Default is "%s".', implode(', ', $modes), self::DEFAULT_MODE),
            self::DEFAULT_MODE
        );
        $this->addOption(
            'f|output-format:',
            sprintf(
                'Output format: [%s]. Default is "%s".',
                implode('|', self::VALID_OUTPUT_FORMATS),
                self::DEFAULT_OUTPUT_FORMAT
            ),
            self::DEFAULT_OUTPUT_FORMAT
        );
        $this->addOption('s|summary', 'Output a summary of the results to stdout.', false);
        $this->addOption(
            'x|exclude-tables:',
            'Specify additional tables to skip. Comma separated list of table names.',
            ''
        );
        $this->addOption(
            't|include-tables:',
            'Only include these tables. Comma separated list of table names.',
            ''
        );

        $this->addOption('o|output-file:', 'Specify output file to redirect all output. Default is stdout.', 'php://stdout');

        $this->addOption(
            'i|input-file:', 'Specify input file to read database records that needs cleaning. Format is the same as for export.',
            false
        );
        $this->addOption(
            'n|input-format:',
            sprintf(
                'Input format: [%s]. Default is "%s".',
                implode('|', self::VALID_INPUT_FORMATS),
                self::DEFAULT_INPUT_FORMAT
            ),
            self::DEFAULT_INPUT_FORMAT
        );
        $this->addOption(
            'l|include-expression:',
            'Only process matches that match this regex expression. "~" char is used as regex delimiter and it must be escaped.',
            ''
        );
        $this->addOption(
            'c|exclude-expression:',
            'Exclude matches that match this regex expression. "~" char is used as regex delimiter and it must be escaped.',
            ''
        );

    }

    public function execute() {
        global $DB;
        $this->db = $DB;
        $this->mode = $this->expandedOptions['mode'];
        $this->output_format = $this->expandedOptions['output-format'];
        $summary = $this->expandedOptions['summary'];
        $outputfile = $this->expandedOptions['output-file'];
        $inputfile = $this->expandedOptions['input-file'];
        $input_format = $this->expandedOptions['input-format'];
        $this->include_expression = $this->expandedOptions['include-expression'];
        $this->exclude_expression = $this->expandedOptions['exclude-expression'];
        $this->totalmatches = 0;
        $this->totalrecords = 0;

        if (!array_key_exists($this->mode, self::VALID_MODES)) {
            $this->error_exit("Invalid mode '%s'. Valid modes are: %s", $this->mode, implode(', ', self::VALID_MODES));
        }

        if (!in_array($this->output_format, self::VALID_OUTPUT_FORMATS, true)) {
            $this->error_exit(
                "Invalid output format '%s'. Valid formats are: %s", $this->output_format, implode(', ', self::VALID_OUTPUT_FORMATS)
            );
        }

        if (!in_array($input_format, self::VALID_INPUT_FORMATS, true)) {
            $this->error_exit(
                "Invalid input format '%s'. Valid formats are: %s", $input_format, implode(', ', self::VALID_INPUT_FORMATS)
            );
        }

        if ($this->mode !== self::MODE_DETECT) {
            $choice = cli_input(
                "⚠️ ⚠️ ⚠️  DANGER ⚠️ ⚠️ ⚠️\n"
                . "Running this command in mode '{$this->mode}' will modify your database.\n"
                . "Make sure you backup your database first!\n\n"
                . "Are you sure you want to proceed? (y/n)",
                'n',
                ['y', 'n']
            );

            if ($choice !== 'y') {
                $this->message('Command canceled.');
                exit(1);
            }
        }

        if ($this->verbose) {
            $this->message('Starting database scan in mode \'%s\' with output format \'%s\'', $this->mode, $this->output_format);
            if ($inputfile) {
                $this->message('Using input file \'%s\' with input format \'%s\'', $inputfile, $input_format);
            }
        }

        if ($this->expandedOptions['exclude-tables']) {
            $this->excludetables = array_merge(
                $this->excludetables,
                array_map('trim', explode(',', $this->expandedOptions['exclude-tables']))
            );
            if ($this->verbose) {
                $this->message('Skipping tables: %s', implode(', ', $this->excludetables));
            }
        }

        if ($this->expandedOptions['include-tables']) {
            $this->includetables = array_merge(
                $this->includetables,
                array_map('trim', explode(',', $this->expandedOptions['include-tables']))
            );
            if ($this->verbose) {
                $this->message('Only scanning tables: %s', implode(', ', $this->includetables));
            }
        }

        // Turn off time limits, sometimes upgrades can be slow.
        core_php_time_limit::raise();

        try {
            $this->output = fopen($outputfile, 'wb+');
            if (!is_resource($this->output)) {
                $this->error_exit("Could not open output file '%s' for writing.", $outputfile);
            }

            $this->start_output();

            if ($inputfile) {
                $inputiterator = $this->create_item_iterator($inputfile, $input_format);
                $this->clean_db($inputiterator);
            } else {
                $this->scan_db();
            }

            $this->close_output();

        } catch (Throwable $exception) {
            $this->error('Error while scanning %s mode entries: %s', $this->mode, $exception->getMessage());
            if ($this->verbose) {
                $this->error(PHP_EOL . 'Exception details: ' . $exception);
            }
            $this->error_exit('Terminating due to an error during the scan.');
        } finally {
            if (is_resource($this->output) && $outputfile !== 'php://stdout') {
                fclose($this->output);
            }
        }

        if ($this->verbose && $outputfile !== 'php://stdout') {
            $this->message('Output written to file: %s', $outputfile);
        }

        if ($this->verbose) {
            $this->message('Purging all caches');
        }
        // delete modinfo caches
        rebuild_course_cache(0, true);
        purge_all_caches();

        if ($summary) {
            $this->message(
                "\nSummary\nTotal database records checked: %s\nTotal matches: %s\n\n", $this->totalrecords, $this->totalmatches
            );
        }
    }

    private function error_exit(string $message, ...$args): void {
        $this->error($message, ...$args);
        exit(1);
    }

    private function error(string $message, ...$args): void {
        fwrite(STDERR, sprintf("Error: {$message}\n", ...$args));
    }

    private function message(string $message, ...$args): void {
        fwrite(STDERR, sprintf("{$message}\n", ...$args));
    }

    private function start_output(): void {
        switch ($this->output_format) {
            case 'json':
                fwrite($this->output, '[' . PHP_EOL);
                $this->is_first_json_item = true;
                break;
            case 'xml':
                fwrite($this->output, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
                fwrite($this->output, '<scan_matches>' . PHP_EOL);
                break;
            case 'csv':
                fwrite($this->output, '"type","path","original_filename","size_bytes"' . PHP_EOL);
                break;
        }
    }

    private function create_item_iterator(string $inputfile, string $format): Generator {
        if (!file_exists($inputfile) || !is_readable($inputfile)) {
            throw new InvalidArgumentException('Input file does not exist or is not readable.');
        }

        switch ($format) {
            case 'json':
                $json = json_decode(file_get_contents($inputfile));
                foreach ($json as $item) {
                    if (!isset($item->id, $item->table, $item->column)) {
                        throw new InvalidArgumentException(
                            'JSON file does not contain valid items, expected: {id:string, table:string, column:string}'
                        );
                    }
                    yield $item;
                }
                break;
            case 'csv':
                $handle = fopen($inputfile, 'rb');
                while ($item = fgetcsv($handle)) {
                    if (count($item) !== 3) {
                        throw new InvalidArgumentException('CSV file does not contain valid items, expected: id,table,column');
                    }
                    yield (object) [
                        'id'     => $item[0],
                        'table'  => $item[1],
                        'column' => $item[2],
                    ];
                }
                break;
            case 'xml':
                $xml = new SimpleXMLElement(file_get_contents($inputfile));
                foreach ($xml->match as $item) {
                    if (!isset($item->id, $item->table, $item->column)) {
                        throw new InvalidArgumentException(
                            'XML file does not contain valid match elements, expected: {id:string, table:string, column:string}'
                        );
                    }
                    yield $item;
                }
        }
    }

    private function clean_db(iterable $itemiterator): void {
        static $table_columns = [];

        /** @var object{id: string, table: string, column: string} $item */
        foreach ($itemiterator as $item) {
            $tablename = (string) $item->table;
            if (!$this->should_scan($tablename)) {
                continue;
            }

            $columns = $table_columns[$tablename] ?? $table_columns[$tablename] = $this->db->get_columns($tablename);
            $itemcolumn = (string) $item->column;
            $column = $columns[$itemcolumn] ?? null;

            if (!$columns) {
                $this->error('Table "%s" does not exist. Skipping.', $tablename);
                continue;
            }

            if (!$column) {
                $this->error('Table "%s" does not have a "%s" column. Skipping.', $tablename, $itemcolumn);
                continue;
            }

            if (!$this->should_scan($tablename, $column)) {
                continue;
            }

            $columnname = $this->db->get_manager()->generator->getEncQuoted($column->name);

            $itemid = (string)$item->id;
            $record = $this->db->get_record_sql(
                "SELECT id, $columnname FROM {" . $tablename . "} WHERE id = ?", [$itemid]
            );

            if (!$record) {
                $this->error('Record "%s=%s" does not exist in table "%s". Skipping.', $itemcolumn, $itemid, $tablename);
                continue;
            }

            $this->process_matched_record($record, $tablename, $column->name);
        }
    }

    private function should_scan(string $table, database_column_info $column = null): bool {
        // Don't touch anything that looks like a hash.
        // Ignore column types differing from char or text based.
        if ($column) {
            if ($column->name === 'id' || str_ends_with($column->name, 'hash')) {
                return false;
            }

            if (!in_array($column->meta_type, ['C', 'X'], true)) {
                return false;
            }
        }

        if ($this->includetables && !in_array($table, $this->includetables, true)) {
            $this->verbose_message('Skipping table "%s" since it is not exclusively included.', $table);
            return false;
        }

        if (str_ends_with($table, '_vw')) {
            $this->verbose_message('Skipping view "%s".', $table);
            return false;
        }

        // Don't process these.
        if (in_array($table, $this->excludetables, true)) {
            $this->verbose_message('Skipping excluded table "%s".', $table);
            return false;
        }

        // To be safe never replace inside a table that looks related to logging.
        if (preg_match('/(^|_)logs?($|_)/', $table)) {
            $this->verbose_message('Skipping table "%s" due to potential logging.', $table);
            return false;
        }

        return true;
    }

    private function verbose_message(string $message, ...$args): void {
        if ($this->verbose) {
            $this->message($message, ...$args);
        }
    }

    public function process_matched_record(object $record, string $table, string $column_name): void {
        $this->totalrecords++;

        if ($this->mode === self::MODE_DEFUSE || $this->mode === self::MODE_CLEAN) {
            $matches = [];
            $cleaned_value = preg_replace_callback(
                self::MATCH_PATTERN,
                function($match) use (&$matches) {
                    if (!$this->should_process_match($match[1])) {
                        return $match[1];
                    }
                    $matches[] = $match[1];
                    $this->totalmatches++;
                    return $this->mode === self::MODE_CLEAN ? '' : htmlentities($match[1]);
                },
                $record->{$column_name}
            );

            if ($matches) {
                $this->db->execute(
                    "UPDATE {" . $table . "} SET {$column_name} = ?" . " WHERE id = ?", [$cleaned_value, $record->id]
                );

                if ($this->verbose) {
                    $this->print_item(
                        $record->id,
                        $table,
                        $column_name,
                        $matches
                    );
                }
            }

            return;
        }

        if (preg_match_all(
            self::MATCH_PATTERN,
            $record->{$column_name},
            $matches
        )) {
            $matches_filtered = array_filter($matches[1], [$this, 'should_process_match']);
            if ($matches_filtered) {
                $this->totalmatches += count($matches_filtered);
                $this->print_item(
                    $record->id,
                    $table,
                    $column_name,
                    $matches_filtered
                );
            }
        }
    }

    /**
     * Filter matches based on regex.
     *
     * @return bool Return true if the match should be ignored.
     */
    private function should_process_match(string $match): bool {
        if ($this->include_expression && !preg_match("~$this->include_expression~i", $match)) {
            return false;
        }

        if ($this->exclude_expression && preg_match("~$this->exclude_expression~i", $match)) {
            return false;
        }

        return true;
    }

    private function print_item(string $identifier, string $table, string $column, array $matches): void {
        $data = [
            'id'          => $identifier,
            'table'       => $table,
            'column'      => $column,
            'occurrences' => array_map('trim', $matches),
        ];

        switch ($this->output_format) {
            case 'json':
                if (!$this->is_first_json_item) {
                    fwrite($this->output, ',' . PHP_EOL);
                }
                $jsonlines = explode(PHP_EOL, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                $linecount = count($jsonlines);
                for ($row = 0, $line = $jsonlines[$row]; $row < $linecount; $row++, $line = $jsonlines[$row] ?? null) {
                    fwrite($this->output, '    ' . $line . ($row < $linecount - 1 ? PHP_EOL : ''));
                }
                $this->is_first_json_item = false;
                break;
            case 'xml':
                fwrite($this->output, '  <match>' . PHP_EOL);
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        fwrite(
                            $this->output,
                            '    <' . $key . '>' . PHP_EOL
                        );
                        foreach ($value as $subvalue) {
                            fwrite(
                                $this->output,
                                '      <value>' . htmlspecialchars($subvalue, ENT_XML1, 'UTF-8') . '</value>' . PHP_EOL
                            );
                        }
                        fwrite($this->output, '    </' . $key . '>' . PHP_EOL);
                    } else {
                        fwrite(
                            $this->output,
                            '    <' . $key . '>' . htmlspecialchars($value, ENT_XML1, 'UTF-8') . '</' . $key . '>' . PHP_EOL
                        );
                    }
                }
                fwrite($this->output, '  </match>' . PHP_EOL);
                break;
            case 'csv':
                fputcsv(
                    $this->output,
                    array_map(
                        static function($item) {
                            return is_array($item) ? implode('; ', $item) : trim($item);
                        }, array_values($data)
                    )
                );
                break;
            case self::DEFAULT_OUTPUT_FORMAT:
            default:
                $match = implode('; ', $matches);
                $matchstr = core_text::strlen($match) > self::MATCH_CHAR_LIMIT
                    ? core_text::substr($match, 0, self::MATCH_CHAR_LIMIT) . '...' : $match;
                $matchstr = trim(str_replace(["\n", "\r", "\t"], ' ', $matchstr));
                fwrite(
                    $this->output,
                    sprintf(
                        'Found match -> table column \'%s.%s\' [id: %s, occurrences: %d, values: \'%s\']' . PHP_EOL,
                        $table,
                        $column,
                        $identifier,
                        count($matches),
                        $matchstr
                    )
                );
                break;
        }
    }

    private function scan_db(): void {
        if (!$tables = $this->db->get_tables()) {
            return;
        }
        foreach ($tables as $table) {
            if (!$this->should_scan($table)) {
                continue;
            }

            if (!($columns = $this->db->get_columns($table))) {
                continue;
            }

            if ($this->verbose) {
                $this->db->set_debug(true);
            }
            if (!isset($columns['id'])) {
                $this->error('Table "%s" does not have an "id" column. Skipping.', $table);
                continue;
            }
            foreach ($columns as $column) {
                if (!$this->should_scan($table, $column)) {
                    continue;
                }
                $this->scan_column($table, $column);
            }
            if ($this->verbose) {
                $this->db->set_debug(false);
            }
        }
    }

    public function scan_column(string $table, database_column_info $column): void {
        $columnname = $this->db->get_manager()->generator->getEncQuoted($column->name);

        $records = $this->db->get_recordset_sql(
            "SELECT id, $columnname FROM {" . $table . "} WHERE $columnname LIKE '%<script%' OR $columnname LIKE '%<iframe%'"
        );

        try {
            foreach ($records as $record) {
                $this->process_matched_record($record, $table, $column->name);
            }
        } finally {
            if ($records) {
                $records->close();
            }
        }
    }

    /**
     * Closes the output stream, printing any necessary footers or closing tags.
     */
    private function close_output(): void {
        switch ($this->output_format) {
            case 'json':
                fwrite($this->output, PHP_EOL . ']' . PHP_EOL);
                break;
            case 'xml':
                fwrite($this->output, '</scan_matches>' . PHP_EOL);
        }
    }
}