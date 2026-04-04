<?php
namespace Moosh2\Command\RecycleBin;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecycleBinRestore52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('itemid', InputArgument::REQUIRED, 'Recycle bin item ID')
            ->addArgument('courseid', InputArgument::REQUIRED, 'Course ID');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $itemId = (int) $input->getArgument('itemid');
        $courseId = (int) $input->getArgument('courseid');

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course $courseId not found.</error>");
            return Command::FAILURE;
        }

        $bin = new \tool_recyclebin\course_bin($courseId);
        $items = $bin->get_items();

        $item = null;
        foreach ($items as $i) {
            if ($i->id == $itemId) {
                $item = $i;
                break;
            }
        }

        if (!$item) {
            $output->writeln("<error>Recycle bin item $itemId not found in course $courseId.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would restore '{$item->name}' (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step("Restoring '{$item->name}'");
        $bin->restore_item($item);
        $output->writeln("Restored '{$item->name}' from recycle bin.");

        return Command::SUCCESS;
    }
}
