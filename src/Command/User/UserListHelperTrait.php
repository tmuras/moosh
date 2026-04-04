<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\User;

use Moosh2\Output\ResultFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Shared helpers for version-specific user:list handlers.
 */
trait UserListHelperTrait
{
    /**
     * Read space-separated user IDs from stdin.
     *
     * @return int[]|null  Array of IDs when --stdin is active, null otherwise.
     */
    private function readStdinIds(InputInterface $input): ?array
    {
        if (!$input->getOption('stdin')) {
            return null;
        }

        $raw = file_get_contents('php://stdin');
        $ids = array_filter(
            array_map('intval', preg_split('/\s+/', trim($raw))),
            fn(int $id) => $id > 0,
        );

        return $ids;
    }

    /**
     * Filter users to only those whose IDs appear in the given list.
     *
     * @param int[]|null $stdinIds
     */
    private function filterByStdinIds(array $users, ?array $stdinIds): array
    {
        if ($stdinIds === null) {
            return $users;
        }

        $allowed = array_flip($stdinIds);

        return array_filter(
            $users,
            fn(object $user) => isset($allowed[(int) $user->id]),
        );
    }

    /**
     * Render the user list to the console.
     */
    private function displayUsers(
        array $users,
        InputInterface $input,
        OutputInterface $output,
        bool $idOnly,
        ?array $fields,
    ): void {
        if ($idOnly) {
            $fields = ['id'];
        }

        $headers = [];
        $rows = [];
        $headersBuilt = false;

        foreach ($users as $user) {
            $row = [];
            foreach ($user as $field => $value) {
                if ($fields !== null && !in_array($field, $fields, true)) {
                    continue;
                }
                if (!$headersBuilt) {
                    $headers[] = $field;
                }
                $row[] = $value;
            }
            $rows[] = $row;
            $headersBuilt = true;
        }

        $format = $idOnly ? 'oneline' : $input->getOption('output');
        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);
    }
}
