<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class DebugOn extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('on', 'debug');
    }

    public function execute()
    {
        set_config('debug','32767');
        set_config('debugdisplay','1');
        set_config('debugsmtp','1');
        set_config('perfdebug','15');
        set_config('debugstringids','1');
        set_config('debugvalidators','1');
        set_config('debugpageinfo','1');
        set_config('themedesignermode','1');
    }
}
