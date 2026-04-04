<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Role;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * role:export implementation for Moodle 5.1.
 */
class RoleExport51Handler extends BaseHandler
{
    use RoleLookupTrait;

    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('role', InputArgument::REQUIRED, 'Role shortname or ID to export')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Write XML to file instead of stdout')
            ->addOption('pretty', null, InputOption::VALUE_NONE, 'Format XML with indentation');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG;

        $verbose = new VerboseLogger($output);
        $identifier = $input->getArgument('role');
        $filePath = $input->getOption('file');
        $pretty = $input->getOption('pretty');

        $verbose->step('Loading Moodle libraries');
        require_once $CFG->libdir . '/filelib.php';

        $role = $this->findRole($identifier);
        if (!$role) {
            $output->writeln("<error>Role '$identifier' not found.</error>");
            return Command::FAILURE;
        }

        $verbose->step("Exporting role '{$role->shortname}' (ID={$role->id})");
        $xml = \core_role_preset::get_export_xml($role->id);

        if ($pretty) {
            $dom = new \DOMDocument('1.0');
            $dom->preserveWhiteSpace = true;
            $dom->formatOutput = true;
            $dom->loadXML($xml);
            $xml = $dom->saveXML();
        }

        if ($filePath) {
            if (file_put_contents($filePath, $xml) === false) {
                $output->writeln("<error>Could not write to file '$filePath'.</error>");
                return Command::FAILURE;
            }
            $output->writeln("Exported role '{$role->shortname}' to $filePath");
            return Command::SUCCESS;
        }

        // Write raw XML to stdout (not via $output to avoid decoration).
        echo $xml . PHP_EOL;

        return Command::SUCCESS;
    }
}
