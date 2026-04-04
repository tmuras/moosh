<?php
namespace Moosh2\Command\RecycleBin;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecycleBinPurge52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument('courseid', InputArgument::REQUIRED, 'Course ID');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $runMode = $input->getOption('run');

        $courseId = (int) $input->getArgument('courseid');

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course $courseId not found.</error>");
            return Command::FAILURE;
        }

        $bin = new \tool_recyclebin\course_bin($courseId);
        $items = $bin->get_items();

        if (empty($items)) {
            $output->writeln('Recycle bin is already empty.');
            return Command::SUCCESS;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would permanently delete " . count($items) . " item(s) from recycle bin (use --run to execute).</info>");
            return Command::SUCCESS;
        }

        $verbose->step('Purging recycle bin');
        foreach ($items as $item) {
            $bin->delete_item($item);
            $verbose->info("Deleted '{$item->name}'");
        }

        $output->writeln("Purged " . count($items) . " item(s) from recycle bin.");

        return Command::SUCCESS;
    }
}
