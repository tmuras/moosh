<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Kacper Golewski <k.golewski@gmail.com>
 */

namespace Moosh\Command\Generic\Tools;

use Moosh\MooshCommand;

class BasePath extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('base-path');
        $this->addArgument('path');
        $this->minArguments = 0;
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_NONE;
    }

    public function execute()
    {
        // Path given either as STDIN or 1st argument.
        if(isset($this->arguments[0])) {
            $path = $this->arguments[0];
        } else {
            $path = trim(fgets(STDIN));
        }

        $plugin = detect_plugin($path);

        // We want a path to base plugin directory + 1 more
        if($plugin['dir'] != 'unknown') {
            echo $plugin['dir'] . '/' . $plugin['name'];
        }

        echo "\n";
    }

}

