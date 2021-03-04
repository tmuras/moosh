<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle23\Dev;
use Moosh\MooshCommand;

class DevVersionbump extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('versionbump', 'dev');

        //$this->addArgument('name');

        //$this->addOption('t|test', 'option with no value');
        //$this->addOption('o|option:', 'option with value and default', 'default');

    }

    public function execute()
    {
        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed

        $options = $this->expandedOptions;

        //find main version.php
        $path = $this->topDir . '/' . $this->pluginInfo['dir'] . '/' . $this->pluginInfo['name'] . '/version.php';
        if (!file_exists($path)) {
            cli_error("File does not exist: $path");
        }

        if (!is_writeable($path)) {
            cli_error("Can't write to the file: $path");
        }

        if ($this->verbose) {
            echo "Updating version.php: $path\n";
        }

        //find line like $module->version   = 2010032200
        //YYYYMMDDXX
        $curdate = date('Ymd');
        $content = file($path);
        foreach ($content as $k => $line) {
            if (preg_match('/^\s*\$\w+->version\s*=\s*(\d+)/', $line, $matches)) {
                if (strlen($matches[1]) > 10) {
                    cli_error('Your version is too big, go and bump it yourself');
                }

                if (substr($matches[1], 0, 8) == $curdate) {
                    //bump final XX
                    $xx = substr($matches[1], 8, 2);
                    $xx++;
                    if ($xx < 10) {
                        $xx = '0' . $xx;
                    }
                } else {
                    $xx = '00';
                }

                $version = $curdate . $xx;

                echo "Bumped from " . $matches[1] . " to $version\n";
                $content[$k] = preg_replace('/\d+/', $version, $line, 1);

            }
        }

        file_put_contents($path,$content);
    }
}
