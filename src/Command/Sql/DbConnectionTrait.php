<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Sql;

/**
 * Shared helpers for building database connection strings from Moodle config.
 *
 * All shell arguments are escaped with escapeshellarg() for safety.
 */
trait DbConnectionTrait
{
    /**
     * Return the normalized database type: 'mysql' or 'pgsql'.
     *
     * @throws \RuntimeException For unsupported database types.
     */
    private function getDbType(): string
    {
        global $CFG;

        return match ($CFG->dbtype) {
            'mysqli', 'mariadb', 'auroramysql' => 'mysql',
            'pgsql' => 'pgsql',
            default => throw new \RuntimeException(
                "Unsupported database type '{$CFG->dbtype}'. Supported: mysqli, mariadb, auroramysql, pgsql.",
            ),
        };
    }

    /**
     * Return the port option string if a custom port is configured.
     */
    private function getPortOption(string $flag): string
    {
        global $CFG;

        if (!empty($CFG->dboptions['dbport'])) {
            return $flag . ' ' . escapeshellarg($CFG->dboptions['dbport']);
        }

        return '';
    }

    /**
     * Build the mysql client command string.
     */
    private function getMysqlCliCommand(): string
    {
        global $CFG;

        $port = $this->getPortOption('-P');

        return sprintf(
            'mysql -h %s -u %s -p%s %s %s',
            escapeshellarg($CFG->dbhost),
            escapeshellarg($CFG->dbuser),
            escapeshellarg($CFG->dbpass),
            $port,
            escapeshellarg($CFG->dbname),
        );
    }

    /**
     * Build the psql client command string.
     * Caller must set PGPASSWORD env var first.
     */
    private function getPgsqlCliCommand(): string
    {
        global $CFG;

        $port = $this->getPortOption('-p');

        return sprintf(
            'psql -h %s -U %s %s %s',
            escapeshellarg($CFG->dbhost),
            escapeshellarg($CFG->dbuser),
            $port,
            escapeshellarg($CFG->dbname),
        );
    }

    /**
     * Build the mysqldump command string.
     */
    private function getMysqldumpCommand(array $tables = []): string
    {
        global $CFG;

        $port = $this->getPortOption('-P');
        $tableArgs = implode(' ', array_map('escapeshellarg', $tables));

        return sprintf(
            'mysqldump --lock-tables=false -h %s -u %s --password=%s %s %s %s',
            escapeshellarg($CFG->dbhost),
            escapeshellarg($CFG->dbuser),
            escapeshellarg($CFG->dbpass),
            $port,
            escapeshellarg($CFG->dbname),
            $tableArgs,
        );
    }

    /**
     * Build the pg_dump command string.
     * Caller must set PGPASSWORD env var first.
     */
    private function getPgDumpCommand(array $tables = []): string
    {
        global $CFG;

        $port = $this->getPortOption('-p');
        $tableArgs = '';
        foreach ($tables as $table) {
            $tableArgs .= ' -t ' . escapeshellarg($table);
        }

        return sprintf(
            'pg_dump -h %s -U %s %s %s%s',
            escapeshellarg($CFG->dbhost),
            escapeshellarg($CFG->dbuser),
            $port,
            escapeshellarg($CFG->dbname),
            $tableArgs,
        );
    }

    /**
     * Set the PGPASSWORD environment variable for PostgreSQL connections.
     */
    private function setPgPassword(): void
    {
        global $CFG;

        putenv('PGPASSWORD=' . $CFG->dbpass);
    }

    /**
     * Get the table prefix from Moodle config.
     */
    private function getTablePrefix(): string
    {
        global $CFG;

        return $CFG->prefix ?? 'mdl_';
    }
}
