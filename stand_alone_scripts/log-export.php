#!/usr/bin/env php
<?php
/**
 * Standalone log export script for Moodle (MySQL/MariaDB only).
 *
 * This script replicates the functionality of the moosh2 log:export command
 * (src/Command/Log/LogExport51Handler.php) as a single self-contained file
 * with no external dependencies. It parses Moodle's config.php to extract
 * database connection settings and connects directly via mysqli.
 *
 * Derived from moosh2 — Moodle Shell (https://github.com/tmuras/moosh)
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// ── Maps ─────────────────────────────────────────────────────────

const ORIGIN_MAP = [
    'web' => 1,
    'cli' => 2,
    'ws' => 3,
    'cron' => 4,
    'restore' => 5,
    '' => 6,
];

const ACTION_MAP = [
    'abandoned' => 1, 'accepted' => 2, 'added' => 3, 'answered' => 4,
    'approved' => 5, 'archived' => 6, 'assessed' => 7, 'assigned' => 8,
    'autosaved' => 9, 'awarded' => 10, 'blocked' => 11, 'called' => 12,
    'cancelled' => 13, 'completed' => 14, 'created' => 15, 'deleted' => 16,
    'detected' => 17, 'disabled' => 18, 'disapproved' => 19, 'downloaded' => 20,
    'duplicated' => 21, 'edited' => 22, 'enabled' => 23, 'ended' => 24,
    'evaluated' => 25, 'exported' => 26, 'failed' => 27, 'flagged' => 28,
    'graded' => 29, 'granted' => 30, 'imported' => 31, 'indexed' => 32,
    'joined' => 33, 'launched' => 34, 'left' => 35, 'listed' => 36,
    'loaded' => 37, 'locked' => 38, 'logged' => 39, 'loggedin' => 40,
    'loggedinas' => 41, 'loggedout' => 42, 'met' => 43, 'moved' => 44,
    'pinned' => 45, 'prevented' => 46, 'previewed' => 47, 'printed' => 48,
    'protected' => 49, 'published' => 50, 'rated' => 51, 'reassessed' => 52,
    'received' => 53, 'reevaluated' => 54, 'regraded' => 55, 'removed' => 56,
    'reopened' => 57, 'reordered' => 58, 'repaginated' => 59, 'replaced' => 60,
    'requested' => 61, 'reset' => 62, 'restarted' => 63, 'restored' => 64,
    'resumed' => 65, 'revealed' => 66, 'reverted' => 67, 'reviewed' => 68,
    'revoked' => 69, 'searched' => 70, 'sent' => 71, 'shown' => 72,
    'started' => 73, 'stopped' => 74, 'submitted' => 75, 'switched' => 76,
    'unapproved' => 77, 'unassigned' => 78, 'unblocked' => 79, 'unflagged' => 80,
    'unlinked' => 81, 'unlocked' => 82, 'unpinned' => 83, 'unprotected' => 84,
    'unpublished' => 85, 'updated' => 86, 'uploaded' => 87, 'viewed' => 88,
    'becameoverdue' => 89, 'remind' => 90, 'error' => 91, 'give' => 92,
];

// ── Usage ────────────────────────────────────────────────────────

function usage(): void {
    $script = basename(__FILE__);
    fwrite(STDERR, <<<USAGE
Usage: php $script <moodle-path> --from=DATE --to=DATE <output-file> [--compact] [--event-map=FILE]

Export entries from the Moodle standard log table into a CSV file.
Connects directly to MySQL/MariaDB using credentials from config.php.

Arguments:
  moodle-path           Path to the Moodle installation directory
  output-file           Path to the output CSV file

Options:
  --from=DATE           From date (YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)
  --to=DATE             To date (YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)
  --compact             Omit id column if consecutive; compress event names,
                        origin, action, and timecreated; write metadata.json
  --event-map=FILE      Path to event_map.php (for --compact event name compression)

Examples:
  php $script /var/www/html/moodle --from=2025-01-01 --to=2025-01-31 /tmp/logs.csv
  php $script /var/www/html/moodle --compact --from=2025-01-01 --to=2025-12-31 /tmp/logs.csv
  php $script /var/www/html/moodle --compact --event-map=src/Data/event_map.php --from=2025-01-01 --to=2025-12-31 /tmp/logs.csv

USAGE);
    exit(1);
}

// ── Helpers ──────────────────────────────────────────────────────

function fatal(string $msg): void {
    fwrite(STDERR, "ERROR: $msg\n");
    exit(1);
}

function info(string $msg): void {
    fwrite(STDERR, "$msg\n");
}

function parseDate(string $value, bool $isFrom): ?DateTime {
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
    if ($date !== false) {
        return $date;
    }

    $date = DateTime::createFromFormat('Y-m-d', $value);
    if ($date !== false) {
        $isFrom ? $date->setTime(0, 0, 0) : $date->setTime(23, 59, 59);
        return $date;
    }

    return null;
}

/**
 * Parse Moodle config.php to extract DB connection settings.
 *
 * Reads the file as text and extracts $CFG->dbtype, dbhost, dbname,
 * dbuser, dbpass, and prefix using regex — does not execute the file.
 */
function parseMoodleConfig(string $moodlePath): array {
    // Moodle 5.x: public/config.php may redirect to ../config.php
    $configPath = $moodlePath . '/config.php';
    if (!file_exists($configPath)) {
        fatal("config.php not found at $configPath");
    }

    $content = file_get_contents($configPath);

    // Check if this is a redirector (Moodle 5.x public/ layout).
    if (preg_match("/require_once\s*\(\s*.*?'([^']+)'\s*\)/", $content, $m)) {
        $redirectTarget = $m[1];
        // Resolve __DIR__ relative path.
        if (strpos($redirectTarget, '__DIR__') !== false) {
            // Already handled below via the configfile pattern.
        }
    }
    if (preg_match('/\$configfile\s*=\s*__DIR__\s*\.\s*[\'"]([^\'"]+)[\'"]\s*;/', $content, $m)) {
        $resolvedPath = realpath($moodlePath . '/' . $m[1]);
        if ($resolvedPath && file_exists($resolvedPath)) {
            $content = file_get_contents($resolvedPath);
        }
    }

    $cfg = [];
    $fields = ['dbtype', 'dblibrary', 'dbhost', 'dbname', 'dbuser', 'dbpass', 'prefix'];
    foreach ($fields as $field) {
        if (preg_match('/\$CFG->' . $field . '\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $m)) {
            $cfg[$field] = $m[1];
        }
    }

    // Also check for port in dboptions or dbhost.
    $cfg['dbport'] = null;
    if (preg_match("/['\"]dbport['\"]\s*=>\s*(\d+)/", $content, $m)) {
        $cfg['dbport'] = (int) $m[1];
    }

    foreach (['dbtype', 'dbhost', 'dbname', 'dbuser', 'prefix'] as $required) {
        if (!isset($cfg[$required])) {
            fatal("Could not extract \$CFG->$required from config.php");
        }
    }

    if (!in_array($cfg['dbtype'], ['mariadb', 'mysqli', 'auroramysql'], true)) {
        fatal("Unsupported database type: {$cfg['dbtype']}. This script supports MySQL/MariaDB only.");
    }

    // dbpass may legitimately be empty.
    if (!isset($cfg['dbpass'])) {
        $cfg['dbpass'] = '';
    }

    return $cfg;
}

function connectDb(array $cfg): mysqli {
    $host = $cfg['dbhost'];
    $port = $cfg['dbport'] ?? 3306;
    $socket = null;

    // Handle socket connections (host contains /).
    if (strpos($host, '/') !== false) {
        $socket = $host;
        $host = 'localhost';
    }

    // Handle host:port format.
    if (strpos($host, ':') !== false && strpos($host, '/') === false) {
        [$host, $port] = explode(':', $host, 2);
        $port = (int) $port;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli($host, $cfg['dbuser'], $cfg['dbpass'], $cfg['dbname'], $port, $socket);
    } catch (mysqli_sql_exception $e) {
        fatal("Database connection failed: " . $e->getMessage());
    }

    $conn->set_charset('utf8mb4');

    return $conn;
}

// ── Argument parsing ─────────────────────────────────────────────

$args = $argv;
array_shift($args); // Remove script name.

$moodlePath = null;
$outputFile = null;
$fromOpt = null;
$toOpt = null;
$compact = false;
$eventMapPath = null;
$positional = [];

foreach ($args as $arg) {
    if ($arg === '--help' || $arg === '-h') {
        usage();
    } elseif ($arg === '--compact') {
        $compact = true;
    } elseif (substr($arg, 0, 7) === '--from=') {
        $fromOpt = substr($arg, 7);
    } elseif (substr($arg, 0, 5) === '--to=') {
        $toOpt = substr($arg, 5);
    } elseif (substr($arg, 0, 12) === '--event-map=') {
        $eventMapPath = substr($arg, 12);
    } elseif (substr($arg, 0, 1) !== '-') {
        $positional[] = $arg;
    } else {
        fatal("Unknown option: $arg");
    }
}

if (count($positional) < 2) {
    usage();
}

$moodlePath = rtrim($positional[0], '/');
$outputFile = $positional[1];

if ($fromOpt === null || $toOpt === null) {
    fatal("Both --from and --to options are required.");
}

$fromDate = parseDate($fromOpt, true);
$toDate = parseDate($toOpt, false);

if ($fromDate === null) {
    fatal("Invalid --from date format. Use YYYY-MM-DD or YYYY-MM-DD HH:MM:SS.");
}
if ($toDate === null) {
    fatal("Invalid --to date format. Use YYYY-MM-DD or YYYY-MM-DD HH:MM:SS.");
}

$tsFrom = $fromDate->getTimestamp();
$tsTo = $toDate->getTimestamp();

if ($tsTo < $tsFrom) {
    fatal('"to" date must be later than "from" date.');
}

if (!is_dir($moodlePath)) {
    fatal("Moodle directory not found: $moodlePath");
}

// ── Connect to database ──────────────────────────────────────────

info("Parsing config.php...");
$cfg = parseMoodleConfig($moodlePath);
$prefix = $cfg['prefix'];
$table = $prefix . 'logstore_standard_log';

info("Connecting to {$cfg['dbtype']} database '{$cfg['dbname']}' on {$cfg['dbhost']}...");
$db = connectDb($cfg);

info("From: " . $fromDate->format('Y-m-d H:i:s') . " (timestamp: $tsFrom)");
info("To:   " . $toDate->format('Y-m-d H:i:s') . " (timestamp: $tsTo)");

// ── Compact mode checks ─────────────────────────────────────────

$compactApplied = false;
$firstId = null;
$defaultOrigin = null;
$eventMap = null;

if ($compact) {
    // Check if IDs are consecutive.
    info("Checking if IDs are consecutive...");
    $stmt = $db->prepare(
        "SELECT MIN(id) AS minid, MAX(id) AS maxid, COUNT(id) AS cnt
           FROM `$table`
          WHERE timecreated >= ? AND timecreated <= ?"
    );
    $stmt->bind_param('ii', $tsFrom, $tsTo);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    $stmt->close();

    $cnt = (int) $stats['cnt'];
    if ($cnt === 0) {
        info("No log entries found in the specified date range.");
        $db->close();
        exit(0);
    }

    $firstId = (int) $stats['minid'];
    $consecutive = ((int) $stats['maxid'] - $firstId + 1) === $cnt;

    if ($consecutive) {
        $compactApplied = true;
        info("IDs are consecutive — compact mode enabled ($cnt entries)");
    } else {
        info("WARNING: IDs are not consecutive — falling back to normal export");
    }
}

if ($compactApplied) {
    // Analyze origin values.
    info("Analyzing origin values...");
    $stmt = $db->prepare(
        "SELECT COALESCE(origin, '') AS origin, COUNT(*) AS cnt
           FROM `$table`
          WHERE timecreated >= ? AND timecreated <= ?
          GROUP BY origin
          ORDER BY cnt DESC"
    );
    $stmt->bind_param('ii', $tsFrom, $tsTo);
    $stmt->execute();
    $result = $stmt->get_result();
    $topOrigin = $result->fetch_assoc();
    $stmt->close();

    if ($topOrigin !== null) {
        $defaultOrigin = $topOrigin['origin'];
        info("Default origin: \"$defaultOrigin\" ({$topOrigin['cnt']} entries)");
    }

    // Load event map.
    if ($eventMapPath !== null) {
        if (!file_exists($eventMapPath)) {
            info("WARNING: Event map file not found at $eventMapPath — event names will not be compressed");
        } else {
            $eventMap = require $eventMapPath;
            info("Event map loaded: " . count($eventMap) . " entries");
        }
    }
}

// ── Stream records to CSV ────────────────────────────────────────

info($compactApplied ? "Writing compact CSV..." : "Writing CSV...");

$fp = fopen($outputFile, 'w');
if ($fp === false) {
    fatal("Could not open file for writing: $outputFile");
}

$stmt = $db->prepare(
    "SELECT * FROM `$table`
      WHERE timecreated >= ? AND timecreated <= ?
      ORDER BY id ASC"
);
$stmt->bind_param('ii', $tsFrom, $tsTo);
$stmt->execute();
$result = $stmt->get_result();

$headersWritten = false;
$count = 0;
$headerIndex = [];
$eventNameIndex = false;
$originIndex = false;
$actionIndex = false;
$timecreatedIndex = false;
$prevTimecreated = null;

while ($record = $result->fetch_assoc()) {
    if (!$headersWritten) {
        $headers = array_keys($record);
        if ($compactApplied) {
            array_shift($headers); // Remove 'id'.
        }

        if ($compactApplied) {
            $headerIndex = array_flip($headers);
            if ($eventMap !== null) {
                $eventNameIndex = $headerIndex['eventname'] ?? false;
            }
            $originIndex = $headerIndex['origin'] ?? false;
            $actionIndex = $headerIndex['action'] ?? false;
            $timecreatedIndex = $headerIndex['timecreated'] ?? false;
        }

        fputcsv($fp, $headers);
        $headersWritten = true;
    }

    $row = array_values($record);
    if ($compactApplied) {
        array_shift($row); // Remove id value.
    }

    // Replace eventname with numeric ID.
    if ($eventNameIndex !== false && isset($row[$eventNameIndex])) {
        $eventName = $row[$eventNameIndex];
        if (isset($eventMap[$eventName])) {
            $row[$eventNameIndex] = $eventMap[$eventName];
        }
    }

    // Replace origin: default → empty, others → numeric ID.
    if ($compactApplied && $originIndex !== false && $defaultOrigin !== null) {
        $originValue = $row[$originIndex] ?? '';
        if ($originValue === $defaultOrigin) {
            $row[$originIndex] = '';
        } else {
            $row[$originIndex] = ORIGIN_MAP[$originValue] ?? $originValue;
        }
    }

    // Replace action with numeric ID.
    if ($compactApplied && $actionIndex !== false) {
        $actionValue = $row[$actionIndex] ?? '';
        $row[$actionIndex] = ACTION_MAP[$actionValue] ?? $actionValue;
    }

    // Replace timecreated with delta from previous record.
    if ($compactApplied && $timecreatedIndex !== false) {
        $currentTime = (int) $row[$timecreatedIndex];
        if ($prevTimecreated === null) {
            $prevTimecreated = $currentTime;
        } else {
            $row[$timecreatedIndex] = $currentTime - $prevTimecreated;
            $prevTimecreated = $currentTime;
        }
    }

    fputcsv($fp, $row);
    $count++;
}

$stmt->close();
fclose($fp);

if ($count === 0) {
    unlink($outputFile);
    info("No log entries found in the specified date range.");
    $db->close();
    exit(0);
}

// ── Write metadata.json for compact mode ─────────────────────────

if ($compactApplied) {
    $metadataPath = dirname($outputFile) . '/metadata.json';
    $metadata = ['first_id' => $firstId];
    if ($eventMap !== null) {
        $metadata['event_map'] = true;
    }
    if ($defaultOrigin !== null) {
        $metadata['default_origin'] = $defaultOrigin;
        $metadata['origin_map'] = ORIGIN_MAP;
    }
    $metadata['action_map'] = ACTION_MAP;
    $metadata['timecreated_delta'] = true;
    file_put_contents($metadataPath, json_encode($metadata, JSON_PRETTY_PRINT) . "\n");
    info("Wrote metadata.json with first_id=$firstId");
}

info("Exported $count log entries to $outputFile");

$db->close();
