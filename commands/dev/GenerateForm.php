<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class GenerateForm extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('form', 'generate');

        $this->addOption('f|full:', 'full example');

        $this->addArgument('form_name');
    }

    public function execute()
    {
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../../templates');
        $twig = new Twig_Environment($loader);

        $formName = '';
        if ($this->pluginInfo['type'] != 'unknown') {
            $formName = $this->pluginInfo['type'] . '_' . $this->pluginInfo['name'] . '_';

        }
        $formName .= $this->arguments[0];
        $fileName = $this->arguments[0] . '_form.php';
        $filePath = $this->cwd . '/' .$fileName;

        $content = $twig->render('form.twig', array('formName' => $formName));
        if (file_exists($filePath)) {
            cli_problem("File $fileName exists");
            echo $content;
            exit(0);
        } else {
            file_put_contents($filePath, $content);
        }

        //also generate a client code
    }
}
