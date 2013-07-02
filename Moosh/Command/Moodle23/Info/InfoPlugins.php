<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Info;
use Moosh\MooshCommand;

class InfoPlugins extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('plugins','info');
    }

    public function execute()
    {
        print_object(get_plugin_types());
    }
}
