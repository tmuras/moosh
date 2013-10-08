<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Dev;
use Moosh\MooshCommand;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension_Debug;

class GenerateMoosh extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('moosh', 'generate');

        $this->addArgument('category-command');
    }

    public function bootstrapLevel()
    {
        return self::$BOOTSTRAP_NONE;
    }

    public function execute()
    {
        $loader = new Twig_Loader_Filesystem($this->mooshDir . '/templates');
        $twig = new Twig_Environment($loader);

        $command = explode('-', $this->arguments[0], 2);
        if (count($command) != 2) {
            cli_error("As argument provide category and command in format: category-command");
        }


        $fileName = ucfirst($command[0]) . ucfirst($command[1]) . '.php';
        $dirPath = $this->cwd . '/Moosh/Command/Moodle23/' . ucfirst($command[0]);
        $filePath = $dirPath . '/' . $fileName;

        $content = $twig->render('moosh/command.twig', array('category' => $command[0], 'command' => $command[1]));
        if (file_exists($filePath)) {
            cli_problem("File $fileName exists, dumping output instead of saving as a new file");
            echo $content;
            cli_problem("\n----------------------");
        } elseif (!is_dir($dirPath)) {
            cli_problem("Directory '$dirPath' does not exist, dumping output instead of saving as a new file");
            echo $content;
            cli_problem( "\n---------------------");
        } else {
            file_put_contents($filePath, $content);
        }
    }
}
