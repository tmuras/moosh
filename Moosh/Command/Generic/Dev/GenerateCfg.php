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
        $this->addOption('d|nodoc', 'List attributes with no documentation in the template.');

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

        // Simply iterate $CFG.

        // Find in source code to find usages.
        $cfg = $this->find_cfg_in_code();

        // Get the newest documentation from the lang files.
        $cfg = $this->find_help_strings($cfg);

        // Fill in blanks from the manual entries.
        require_once($this->mooshDir . '/includes/cfg_documentation.php');

        foreach ($cfg as $name => $values) {
            if (!$values['short'] && isset($template[$name])) {
                $cfg[$name]['short'] = $template[$name]['short'];
                $longraw = $template[$name]['long'];
                $longraw = explode("\n", $longraw);
                $long = array();
                foreach ($longraw as $line) {
                    $line = str_replace('*/', '* /', $line);
                    $long[] = "     * $line";
                }
                $cfg[$name]['long'] = implode("\n", $long);
            }
        }

        if ($options['nodoc']) {
            foreach ($cfg as $name => $values) {
                if (!$values['short']) {
                    echo "$name\n";
                }
            }
            die();
        }

        echo <<<HEREDOC
<?php
class moodle_config {
HEREDOC;

        foreach ($cfg as $name => $values) {
            $long = $values['long'];
            $short = $values['short'];
            echo <<<HEREDOC

    /**
$long
     *
     * @var string $name $short
     */
    public \$$name;

HEREDOC;

        }

        echo "}\n\$CFG = new moodle_config();";

    }

    private function extract_help($key)
    {
        $matches = null;
        preg_match("/\\\$string\['$key'\] =\s*'(.*)';/sU", $this->langfiles, $matches);
        if (!$matches[1]) {
            cli_problem("Couldn't parse string for $key");
        }

        return $matches[1];
    }

    private function find_cfg_in_code()
    {
        $finder = new Finder();
        $iterator = $finder
                ->files()
                ->name('*.php')
                ->in($this->topDir);
        $cfg = array();
        foreach ($iterator as $file) {
            // Source code with comments and whitespaces removed.
            $content = php_strip_whitespace($file->getRealpath());
            $matches = null;
            preg_match_all('/\$CFG->(\w+)/', $content, $matches);
            foreach ($matches[1] as $match) {
                if (!isset($cfg[$match])) {
                    $cfg[$match] = array('count' => 0);
                }
                $cfg[$match]['count']++;
            }
        }
        ksort($cfg);
        return $cfg;
    }

    private function find_cfg_in_db()
    {
        return get_config('core');
    }

    private function find_help_strings($cfg)
    {
        $finder = new Finder();
        $iterator = $finder
                ->files()
                ->path('lang/en/')
                ->name('*.php')
                ->in($this->topDir);

        $this->langfiles = '';
        foreach ($iterator as $file) {
            if ($this->verbose) {
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

            if (!$found && strpos($this->langfiles, "\$string['$name']") !== false &&
                    strpos($this->langfiles, "\$string['config{$name}']")) {
                $values['short'] = $this->extract_help($name);
                $values['long'] = $this->extract_help("config$name");
                $found = true;
            }

            if (!$found && strpos($this->langfiles, "\$string['$name']") !== false &&
                    strpos($this->langfiles, "\$string['{$name}_desc']")) {
                $values['short'] = $this->extract_help($name);
                $values['long'] = $this->extract_help($name . '_desc');
                $found = true;
            }

            if (!$found && strpos($name, '_') !== false) {
                $exploded = explode('_', $name);
                array_shift($exploded);
                $name2 = implode('_', $exploded);

                if (strpos($this->langfiles, "\$string['$name2']") !== false &&
                        strpos($this->langfiles, "\$string['config{$name2}']")) {
                    $values['short'] = $this->extract_help($name2);
                    $values['long'] = $this->extract_help("config$name2");
                    $found = true;
                }

                if (!$found && strpos($this->langfiles, "\$string['$name2']") !== false &&
                        strpos($this->langfiles, "\$string['{$name2}_help']")) {
                    $values['short'] = $this->extract_help($name2);
                    $values['long'] = $this->extract_help($name2 . '_help');
                    $found = true;
                }

                if (!$found && strpos($this->langfiles, "\$string['$name2']") !== false &&
                        strpos($this->langfiles, "\$string['{$name2}_desc']")) {
                    $values['short'] = $this->extract_help($name2);
                    $values['long'] = $this->extract_help($name2 . '_desc');
                    $found = true;
                }

            }

            // Maybe only a short name is in the lang file.
            if (!$found && strpos($this->langfiles, "\$string['$name']") !== false) {
                $values['short'] = $this->extract_help($name);
                $values['long'] = '';
                $found = true;
            }

            $cfg[$name] = $values;
        }

        return $cfg;
    }

    private function export_template($cfg)
    {
        echo "<?php\n";
        echo "\$template = ";
        var_export($cfg);
        echo ';';
    }

    /**
     * @param $template
     * @throws \coding_exception
     */
    private function fill_help($template)
    {
        foreach ($template as $k => $v) {
            if (!@$v['short'] && @$v['short_help']) {
                $template[$k]['short'] = get_string($v['short_help'][0], $v['short_help'][1]);
            }
            if (!@$v['long'] && @$v['long_help']) {
                $template[$k]['long'] = get_string($v['long_help'][0], $v['long_help'][1]);
            }
        }
        ksort($template);

        return $template;
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_CONFIG;
    }
}
