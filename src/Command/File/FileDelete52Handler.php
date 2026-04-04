<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\File;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FileDelete52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('fileid', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'File ID(s) to delete')
            ->addOption('hash', null, InputOption::VALUE_REQUIRED, 'Delete all files with this content hash');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $fileIds = $input->getArgument('fileid');
        $hash = $input->getOption('hash');

        if (empty($fileIds) && $hash === null) {
            $output->writeln('<error>Specify file ID(s) or --hash.</error>');
            return Command::FAILURE;
        }

        $fs = get_file_storage();

        if ($hash !== null) {
            return $this->deleteByHash($hash, $runMode, $fs, $output, $verbose);
        }

        return $this->deleteByIds($fileIds, $runMode, $fs, $output, $verbose);
    }

    private function deleteByIds(array $fileIds, bool $runMode, \file_storage $fs, OutputInterface $output, VerboseLogger $verbose): int
    {
        // Validate.
        $files = [];
        foreach ($fileIds as $id) {
            $storedFile = $fs->get_file_by_id((int) $id);
            if (!$storedFile) {
                $output->writeln("<error>File with ID $id not found.</error>");
                return Command::FAILURE;
            }
            if ($storedFile->is_directory()) {
                $output->writeln("<error>File ID $id is a directory, skipping.</error>");
                continue;
            }
            $files[] = $storedFile;
        }

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following file(s) would be deleted (use --run to execute):</info>');
            foreach ($files as $f) {
                $output->writeln("  ID={$f->get_id()} {$f->get_filename()} ({$f->get_component()}/{$f->get_filearea()})");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Deleting ' . count($files) . ' file(s)');
        foreach ($files as $f) {
            $name = $f->get_filename();
            $f->delete();
            $output->writeln("Deleted file $name.");
        }

        return Command::SUCCESS;
    }

    private function deleteByHash(string $hash, bool $runMode, \file_storage $fs, OutputInterface $output, VerboseLogger $verbose): int
    {
        global $DB;

        $records = $DB->get_records_select('files', "contenthash = ? AND filename != '.'", [$hash]);

        if (empty($records)) {
            $output->writeln("<error>No files found with hash '$hash'.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would delete " . count($records) . " file(s) with hash '$hash' (use --run to execute):</info>");
            foreach ($records as $r) {
                $output->writeln("  ID={$r->id} {$r->filename} ({$r->component}/{$r->filearea})");
            }
            return Command::SUCCESS;
        }

        $verbose->step("Deleting " . count($records) . " file(s) with hash '$hash'");
        $deleted = 0;
        foreach ($records as $r) {
            $storedFile = $fs->get_file_by_id($r->id);
            if ($storedFile && !$storedFile->is_directory()) {
                $storedFile->delete();
                $deleted++;
            }
        }

        $output->writeln("Deleted $deleted file(s) with hash '$hash'.");

        return Command::SUCCESS;
    }
}
