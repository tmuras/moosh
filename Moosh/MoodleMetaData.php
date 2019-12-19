<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh;

/**
 * Class MoodleMetaData keeps current Moodle meta information, e.g. available modules.
 * @package Moosh
 */
class MoodleMetaData
{
    private static $modules;
    private static $roles;

    public function __construct()
    {
        global $DB;

        self::$modules = $DB->get_records('modules');
        self::$roles = $DB->get_records('role');

    }

    public function moduleName($id) {
        return self::$modules[$id]->name;
    }

    public function roleName($id) {
        return self::$roles[$id]->shortname;
    }

}