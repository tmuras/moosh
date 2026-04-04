<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Plugin;

use Moosh2\Bootstrap\MoodleVersion;

// TODO: Add Moodle 5.2-specific logic if needed.
class PluginDownload52Handler extends PluginDownload51Handler
{
    public function __construct(?MoodleVersion $moodleVersion)
    {
        parent::__construct($moodleVersion);
    }
}
