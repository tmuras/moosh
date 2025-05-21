<?php
/**
 * moosh - Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Moodle39\Dev;
use Moosh\MooshCommand;
use \Twig\Loader\Filesystem;
use \Twig\Environment;
use \Twig\Extension\Debug;

class GenerateFilemanager extends MooshCommand
{
    public function __construct()
    {
        parent::__construct('filemanager', 'generate');
    }


    public function execute()
    {
        $loader = new \Twig\Loader\Filesystem($this->mooshDir.'/templates');
        $twig = new \Twig\Environment($loader,array('debug' => true));
        $twig->addExtension(new \Twig\Extension\Debug());

        foreach(array('filemanager/form-handler.twig','filemanager/display.twig','filemanager/lib.twig') as $template) {
            echo $twig->render($template, array('id' =>  $this->pluginInfo['type'] .'_'. $this->pluginInfo['name']));
        }
    }
}
