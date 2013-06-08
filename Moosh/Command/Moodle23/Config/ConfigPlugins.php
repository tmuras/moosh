<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Config;
use Moosh\MooshCommand;

class ConfigPlugins extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('plugins', 'config');
        $this->maxArguments = 1;
    }

    public function execute()
    {
        global $CFG, $DB;

        if (isset($this->arguments[0])) {
            $rows = $DB->get_records_sql(
                'SELECT plugin FROM {config_plugins}
                  WHERE ' . $DB->sql_like('plugin', ':plugin', false) . '
                  GROUP BY plugin
                  ORDER BY plugin ASC',
                  array('plugin' => '%'.$this->arguments[0].'%'));
        } else {
            $rows = $DB->get_records_sql('SELECT plugin FROM {config_plugins} GROUP BY plugin ORDER BY plugin ASC');
        }
        foreach($rows as $row) {
            echo $row->plugin . "\n";
        }
    }

    protected function getArgumentsHelp()
    {
        $ret = "\n\nARGUMENTS:";
        $ret .= "\n\t";
        $ret .= "<plugin_name_fragment>\n";

        return $ret;
    }
}
