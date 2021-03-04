<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Dev;
use Moosh\MooshCommand;

class DebugOn extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('on', 'debug');
    }

    public function execute()
    {
        set_config('debug',DEBUG_DEVELOPER);
        set_config('debugdisplay','1');
        set_config('debugsmtp','1');
        set_config('perfdebug','15');
        set_config('debugstringids','1');
        set_config('debugvalidators','1');
        set_config('debugpageinfo','1');
        set_config('themedesignermode','1');
        set_config('passwordpolicy', 0);
        set_config('allowthemechangeonurl', 1);
        set_config('cachejs', 0);
        set_config('yuicomboloading', 0);
    }
}
