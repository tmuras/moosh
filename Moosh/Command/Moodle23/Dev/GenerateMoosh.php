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

        $this->addArgument('category-command');
    }

    public function execute()
    {
        $loader = new Twig_Loader_Filesystem($this->mooshDir.'/templates');
        $twig = new Twig_Environment($loader);

        $command = explode('-',$this->arguments[0],2);


        $fileName = ucfirst($command[0]).ucfirst($command[1]).'.php';
        $filePath = $this->cwd . '/' .$fileName;
        $content = $twig->render('moosh/command.twig', array('formName' => $formName));
        if (file_exists($filePath)) {
            cli_problem("File $fileName exists");
            echo $content;
            echo "\n---------------------\n";
        } else {
            file_put_contents($filePath, $content);
        }
    }
}
