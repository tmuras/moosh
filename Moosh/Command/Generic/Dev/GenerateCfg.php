<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Dev;

use Moosh\MooshCommand;
use Symfony\Component\Finder\Finder;

class GenerateCfg extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('cfg', 'generate');

    }


    public function execute()
    {
        //some variables you may want to use
        //$this->cwd - the directory where moosh command was executed
        //$this->mooshDir - moosh installation directory
        //$this->expandedOptions - commandline provided options, merged with defaults
        //$this->topDir - top Moodle directory
        //$this->arguments[0] - first argument passed
        //$this->pluginInfo - array with information about the current plugin (based on cwd), keys:'type','name','dir'

        $options = $this->expandedOptions;
        $finder = new Finder();
        $iterator = $finder
            ->files()
            ->name('*.php')
            ->in($this->topDir);
        $cfg = array();
        foreach ($iterator as $file) {
            //print $file->getRealpath() . "\n";
            $content = file_get_contents($file->getRealpath());
            $matches = NULL;
            preg_match_all('/\$CFG->(\w+)/', $content, $matches);
            foreach ($matches[1] as $match) {
                if (!isset($cfg[$match])) {
                    $cfg[$match] = array('count' => 0);
                }
                $cfg[$match]['count']++;
            }
        }


        // Find help strings.
        $finder = new Finder();
        $iterator = $finder
            ->files()
            ->path('lang/en/')
            ->name('*.php')
            ->in($this->topDir);

        $this->langfiles = '';
        foreach ($iterator as $file) {
            if($this->verbose) {
                 print $file->getRealpath() . "\n";
            }
            $this->langfiles .= file_get_contents($file->getRealpath());

        }

        foreach ($cfg as $name => $values) {
            $values['short'] = '';
            $values['long'] = '';
            $found = false;
            if (strpos($this->langfiles, "\$string['$name']") !== false && strpos($this->langfiles, "\$string['{$name}_help']")) {
                $values['short'] = $this->extract_help($name);
                $values['long'] = $this->extract_help($name . '_help');
                $found = true;
            }

            if (!$found && strpos($this->langfiles, "\$string['$name']") !== false && strpos($this->langfiles, "\$string['config{$name}']")) {
                $values['short'] = $this->extract_help($name);
                $values['long'] = $this->extract_help("config$name");
                $found = true;
            }

            if (!$found && strpos($this->langfiles, "\$string['$name']") !== false && strpos($this->langfiles, "\$string['{$name}_desc']")) {
                $values['short'] = $this->extract_help($name);
                $values['long'] = $this->extract_help($name . '_desc');
                $found = true;
            }

            if (!$found && strpos($name, '_') !== false) {
                $exploded = explode('_',$name);
                array_shift($exploded);
                $name2 = implode('_',$exploded);

                if (strpos($this->langfiles, "\$string['$name2']") !== false && strpos($this->langfiles, "\$string['config{$name2}']")) {
                    $values['short'] = $this->extract_help($name2);
                    $values['long'] = $this->extract_help("config$name2");
                    $found = true;
                }

                if (!$found && strpos($this->langfiles, "\$string['$name2']") !== false && strpos($this->langfiles, "\$string['{$name2}_help']")) {
                    $values['short'] = $this->extract_help($name2);
                    $values['long'] = $this->extract_help($name2 . '_help');
                    $found = true;
                }

                if (!$found && strpos($this->langfiles, "\$string['$name2']") !== false && strpos($this->langfiles, "\$string['{$name2}_desc']")) {
                    $values['short'] = $this->extract_help($name2);
                    $values['long'] = $this->extract_help($name2 . '_desc');
                    $found = true;
                }

            }

            $cfg[$name] = $values;
        }

        $cfg['usecomments']['short_help'] = array('enablecomments', 'admin');
        $cfg['usecomments']['long_help'] = array('configenablecomments', 'admin');
/*
        $localcfg = get_config('core');
        foreach ($localcfg as $name => $value) {
            if(!isset($cfg[$name])) {
                echo $name . "\n";
            }
        }
  */
        echo "<?php\n";
        var_export($cfg);
        echo ';';
        /* if verbose mode was requested, show some more information/debug messages
        if($this->verbose) {
            echo "Say what you're doing now";
        }
        */
    }

    private function extract_help($key)
    {
        $matches = NULL;
        preg_match("/\\\$string\['$key'\] =\s*'(.*)';/sU", $this->langfiles, $matches);
        if (!$matches[1]) {
            cli_problem("Couldn't parse string for $key");
        }

        return $matches[1];
    }
}
