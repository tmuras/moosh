<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Context;

/**
 * Shared helpers for resolving Moodle contexts from named levels and IDs.
 */
trait ContextLevelTrait
{
    private const LEVEL_MAP = [
        'system'    => 10, // CONTEXT_SYSTEM
        'user'      => 30, // CONTEXT_USER
        'coursecat'  => 40, // CONTEXT_COURSECAT
        'course'    => 50, // CONTEXT_COURSE
        'module'    => 70, // CONTEXT_MODULE
        'block'     => 80, // CONTEXT_BLOCK
    ];

    private const LEVEL_NAMES = [
        10 => 'System',
        30 => 'User',
        40 => 'Course category',
        50 => 'Course',
        70 => 'Module',
        80 => 'Block',
    ];

    /**
     * Resolve a context from an ID and optional named level.
     *
     * When $level is provided, $id is treated as an instance ID and the
     * appropriate context_*::instance() factory is used.  Without $level,
     * $id is treated as a context table ID.
     */
    private function resolveContext(int $id, ?string $level): \context
    {
        if ($level === null) {
            return \context::instance_by_id($id, MUST_EXIST);
        }

        $constant = $this->getLevelConstant($level);

        return match ($constant) {
            CONTEXT_SYSTEM    => \context_system::instance(),
            CONTEXT_USER      => \context_user::instance($id),
            CONTEXT_COURSECAT  => \context_coursecat::instance($id),
            CONTEXT_COURSE    => \context_course::instance($id),
            CONTEXT_MODULE    => \context_module::instance($id),
            CONTEXT_BLOCK     => \context_block::instance($id),
        };
    }

    /**
     * Return all child context IDs for a given context.
     *
     * @return int[]
     */
    private function getChildContextIds(\context $context): array
    {
        global $DB;

        return array_map(
            'intval',
            $DB->get_fieldset_sql(
                "SELECT id FROM {context} WHERE path LIKE ?",
                [$context->path . '/%'],
            ),
        );
    }

    /**
     * Map a named level to its Moodle constant value.
     */
    private function getLevelConstant(string $name): int
    {
        $name = strtolower(trim($name));

        if (!isset(self::LEVEL_MAP[$name])) {
            $valid = implode(', ', array_keys(self::LEVEL_MAP));
            throw new \InvalidArgumentException(
                "Unknown context level '$name'. Valid levels: $valid",
            );
        }

        return self::LEVEL_MAP[$name];
    }

    /**
     * Human-readable name for a context level constant.
     */
    private function getLevelName(int $contextLevel): string
    {
        return self::LEVEL_NAMES[$contextLevel] ?? "Level $contextLevel";
    }
}
