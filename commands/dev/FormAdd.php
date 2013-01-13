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
        $this->maxArguments = 255;
    }

    public function execute()
    {
        //command may need to store some information in-between runs
        $this->loadSession();

        //replace /** MOOSH AUTO-GENERATED */ with new code

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

        $content = $twig->render($template, array('id' => $this->arguments[1], 'langKey' => $this->arguments[2], 'langCategory' => $langCategory));
        echo $content;

        //add string to a lang file: discovered or found in lang/en/*.php
        $langFile = $this->topDir . '/' . $this->relativeDir . "/lang/en/$langCategory.php";
        var_dump($this->pluginInfo);
        var_dump($this->relativeDir);
        var_dump($this->topDir);
        var_dump($langFile);
/*
        if (file_exists($filePath)) {
            cli_problem("File $fileName exists");
            echo $content;
            echo "\n---------------------\n";
        } else {
            file_put_contents($filePath, $content);
        }


        $this->saveSession();*/
    }
}
