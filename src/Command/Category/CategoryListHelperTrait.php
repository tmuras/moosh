<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Category;

use Moosh2\Output\ResultFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Shared helpers for version-specific category:list handlers.
 */
trait CategoryListHelperTrait
{
    /**
     * Read space-separated category IDs from stdin.
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
     * Filter categories to only those whose IDs appear in the given list.
     *
     * @param int[]|null $stdinIds
     */
    private function filterByStdinIds(array $categories, ?array $stdinIds): array
    {
        if ($stdinIds === null) {
            return $categories;
        }

        $allowed = array_flip($stdinIds);

        return array_filter(
            $categories,
            fn(object $cat) => isset($allowed[(int) $cat->id]),
        );
    }

    /**
     * Resolve a category's path IDs to human-readable names.
     */
    private function resolvePathNames(string $path): string
    {
        global $DB;

        $ids = array_filter(array_map('intval', explode('/', $path)));
        if (!$ids) {
            return '';
        }

        $names = [];
        foreach ($ids as $id) {
            $cat = $DB->get_record('course_categories', ['id' => $id], 'name');
            $names[] = $cat ? $cat->name : "($id)";
        }

        return implode(' / ', $names);
    }

    /**
     * Render the category list to the console.
     */
    private function displayCategories(
        array $categories,
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

        foreach ($categories as $cat) {
            $row = [];
            foreach ($cat as $field => $value) {
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
