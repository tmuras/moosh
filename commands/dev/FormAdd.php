<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class FormAdd extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('add', 'form');

        $this->addOption('f|full:', 'full example');

        $this->addArgument('type');
        $this->addArgument('name');
        //$this->maxArguments = 255;
    }

    public function execute()
    {

        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../../templates');
        $twig = new Twig_Environment($loader,array('debug' => true));
        $twig->addExtension(new Twig_Extension_Debug());

        $template = 'form-element-' . $this->arguments[0] . '.twig';
        if (!$loader->exists($template)) {
            cli_problem("Template $template does not exist");
            exit(1);
        }

        if($this->pluginInfo['type'] == 'mod' || $this->pluginInfo['type'] == 'unknown') {
            $langCategory = $this->pluginInfo['name'];
        }  else {
            $langCategory = $this->pluginInfo['type'] .'_'. $this->pluginInfo['name'];
        }

        $content = $twig->render($template, array('id' => $this->arguments[1], 'langCategory' => $langCategory));

        //do I know where to add the new code?
        $this->loadSession();

        if(isset($this->session['generator.last-file'][$this->cwd]) && file_exists($this->cwd . '/' .$this->session['generator.last-file'][$this->cwd])) {
            $fileName = $this->cwd . '/' . $this->session['generator.last-file'][$this->cwd];
            $fileContent = file_get_contents($fileName);
            //replace /** MOOSH AUTO-GENERATED */ with new code
            $fileContent = str_replace(MOOSH_CODE_MARKER,$content . "\n".MOOSH_CODE_MARKER."\n",$fileContent);
            file_put_contents($fileName,$fileContent);
        } else {
            echo $content;
        }
    }

    protected function onErrorHelp()
    {
        $help = $this->mooshDir . "/templates/";
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
