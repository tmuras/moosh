<?php
/**
 * moosh - Moodle Shell
 *
 * @author     Soar Technology (soartech.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh\Command\Generic\Framework;

use Moosh\MooshCommand;
use tool_lpimportcsv\framework_importer;

class FrameworkImport extends MooshCommand {
    public function __construct() {
        parent::__construct('import', 'framework');

        $this->addArgument('framework_file');
    }

    public function execute() {        
        $arguments = $this->arguments;
        
        $filepath = $arguments[0];

        if ($filepath[0] != '/') {
            $filepath = $this->cwd . DIRECTORY_SEPARATOR . $filepath;
        }

        if (!file_exists($filepath)) {
            cli_error("framework file '" . $filepath . "' does not exist.");
        }

        if (!is_readable($filepath)) {
            cli_error("framework file '" . $filepath . "' is not readable.");
        }

        $importer = new framework_importer(file_get_contents($filepath));
        $framework = $importer->import();

        echo "Created Framework with ID: " . $framework->get('id') . "\n";
    }
}
