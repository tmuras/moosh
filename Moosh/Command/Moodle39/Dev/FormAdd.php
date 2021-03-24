<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Dev;
use Moosh\MooshCommand;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension_Debug;

class FormAdd extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('add', 'form');

        $this->addArgument('type');
        $this->addArgument('name');
        //$this->maxArguments = 255;
    }

    public function execute()
    {

        $loader = new Twig_Loader_Filesystem($this->mooshDir.'/templates');
        $twig = new Twig_Environment($loader,array('debug' => true));
        $twig->addExtension(new Twig_Extension_Debug());

        $template = 'form/form-element-' . $this->arguments[0] . '.twig';
        if (!$loader->exists($template)) {
            cli_problem("Template $template does not exist");
            exit(1);
        }

        $content = $twig->render($template, array('id' => $this->arguments[1], 'langCategory' => $this->getLangCategory()));

        //do I know where to add the new code?
        $this->loadSession();

        if(isset($this->session['generator.last-file'][$this->cwd]) && file_exists($this->cwd . '/' .$this->session['generator.last-file'][$this->cwd])) {
            $fileName = $this->cwd . '/' . $this->session['generator.last-file'][$this->cwd];
            $fileContent = file_get_contents($fileName);
            //replace /** MOOSH AUTO-GENERATED */ with new code
            $fileContent = str_replace(MOOSH_CODE_MARKER,$content . "\n".MOOSH_CODE_MARKER,$fileContent);
            file_put_contents($fileName,$fileContent);

            if ($this->defaults['global']['xdotool']) {
                $browser = $this->defaults['global']['browser_string'];

                $active_window = run_external_command("xdotool getwindowfocus", "Couldn't find active window");
                $browser_window  = run_external_command("xdotool search --name '$browser'", "Couldn't find $browser");
                run_external_command("xdotool windowactivate $browser_window[0] ", "Couldn't activate window: $browser");
                run_external_command("xdotool key F5", "Couldn't refresh browser");
                run_external_command("xdotool windowactivate $active_window[0]", "Couldn't activate window: $active_window[0]");
            }
        } else {
            echo $content;
        }
    }

    protected function onErrorHelp()
    {
        $elements = array();
        foreach(glob($this->mooshDir . "/templates/form/form-element-*.twig") as $file) {
            $base = basename($file);
            $matches = null;
            if(preg_match('/form-element-(.*).twig/',$file,$matches)) {
                $elements[] = $matches[1];
            }
        }

        $help = "\nAvailable element templates:\n";
        foreach($elements as $element) {
            $help .= "\t".$element . "\n";
        }
        return $help;
    }

    protected function getArgumentsHelp()
    {
        $help = parent::getArgumentsHelp();
        $help .= "\n\n";
        $help .= $this->onErrorHelp();

        return $help;
    }
}
