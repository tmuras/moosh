<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Dev;
use Moosh\MooshCommand;

class GenerateMoosh extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('moosh', 'generate');

        $this->addArgument('name');
    }

    public function execute()
    {
        //exactly one -
    }
}
