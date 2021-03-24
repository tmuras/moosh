<?php
/**
 * moosh - Moodle Shell
 *
 * @author     2014 Joby Harding
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Php;
use Moosh\MooshCommand;

class PhpEval extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('eval', 'php');

        $this->addArgument('command');

    }

    public function execute()
    {

        // Expose Moodle globals to user script.
        global $CFG, $DB, $SESSION, $USER, $SITE,
               $PAGE, $COURSE, $OUTPUT, $FULLME,
               $ME, $FULLSCRIPT, $SCRIPT, $PERF,
               $GLOBALS;

        foreach ($this->arguments as $argument) {
            $command = $argument;
        }

        if($this->verbose) {
            echo "Evaluating PHP: {$command}";
        }

        chdir($this->cwd);

        return eval($command . ';');

    }
}
