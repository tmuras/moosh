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

class ToolsCodeCheck extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('code-check');
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_NONE;
    }

    public function execute()
    {

        // Get all cli arguments provided
        global $argv;

        // Find entry in $argv equal to 'code-check'
        $index = array_search('code-check', $argv);

        // remove all elements before 'code-check' in $argv
        $arguments = array_slice($argv, $index +1);

        // Run vendon/bin/phpcs
        $command = $this->mooshDir . "/external/moodle-cs/vendor/bin/phpcs";
        $command .= " " . implode(" ", $arguments);
        passthru($command,  $return);
    }

    public function parseArguments() {
        return false;
    }
}

