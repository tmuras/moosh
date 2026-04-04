<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\File;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FileUpload52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('filepath', InputArgument::REQUIRED, 'Path to file to upload')
            ->addOption('contextid', null, InputOption::VALUE_REQUIRED, 'Target context ID')
            ->addOption('component', null, InputOption::VALUE_REQUIRED, 'Component name (e.g. mod_resource)')
            ->addOption('filearea', null, InputOption::VALUE_REQUIRED, 'File area (e.g. content)')
            ->addOption('itemid', null, InputOption::VALUE_REQUIRED, 'Item ID', '0')
            ->addOption('storedpath', null, InputOption::VALUE_REQUIRED, 'Stored file path', '/')
            ->addOption('filename', null, InputOption::VALUE_REQUIRED, 'Override filename');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $CFG, $USER;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');
        $format = $input->getOption('output');

        $filePath = $input->getArgument('filepath');
        $contextId = $input->getOption('contextid');
        $component = $input->getOption('component');
        $filearea = $input->getOption('filearea');
        $itemId = (int) $input->getOption('itemid');
        $storedPath = $input->getOption('storedpath');
        $filename = $input->getOption('filename');

        if (!file_exists($filePath)) {
            $output->writeln("<error>File not found: $filePath</error>");
            return Command::FAILURE;
        }

        if ($contextId === null || $component === null || $filearea === null) {
            $output->writeln('<error>Required: --contextid, --component, and --filearea.</error>');
            return Command::FAILURE;
        }

        if ($filename === null) {
            $filename = basename($filePath);
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would upload '$filename' to context=$contextId, component=$component, filearea=$filearea (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Uploading '$filename'");

        $fs = get_file_storage();

        $fileRecord = new \stdClass();
        $fileRecord->contextid = (int) $contextId;
        $fileRecord->component = $component;
        $fileRecord->filearea = $filearea;
        $fileRecord->itemid = $itemId;
        $fileRecord->filepath = $storedPath;
        $fileRecord->filename = $filename;
        $fileRecord->userid = $USER->id;
        $fileRecord->timecreated = time();
        $fileRecord->timemodified = time();

        $storedFile = $fs->create_file_from_pathname($fileRecord, $filePath);

        $headers = ['id', 'filename', 'component', 'filearea', 'filesize', 'contenthash'];
        $rows = [[
            $storedFile->get_id(),
            $storedFile->get_filename(),
            $storedFile->get_component(),
            $storedFile->get_filearea(),
            $storedFile->get_filesize(),
            $storedFile->get_contenthash(),
        ]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
