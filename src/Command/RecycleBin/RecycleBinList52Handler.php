<?php
namespace Moosh2\Command\RecycleBin;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecycleBinList52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument('courseid', InputArgument::REQUIRED, 'Course ID');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $courseId = (int) $input->getArgument('courseid');
        $format = $input->getOption('output');

        $course = $DB->get_record('course', ['id' => $courseId]);
        if (!$course) {
            $output->writeln("<error>Course $courseId not found.</error>");
            return Command::FAILURE;
        }

        $bin = new \tool_recyclebin\course_bin($courseId);
        $items = $bin->get_items();

        if (empty($items)) {
            $output->writeln('Recycle bin is empty.');
            return Command::SUCCESS;
        }

        $headers = ['id', 'name', 'module', 'section', 'deleted'];
        $rows = [];
        foreach ($items as $item) {
            $rows[] = [
                $item->id,
                $item->name,
                $item->module ?? '',
                $item->section ?? '',
                date('Y-m-d H:i', $item->timecreated),
            ];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
