<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\Category;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * category:create implementation for Moodle 5.1.
 */
class CategoryCreate51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Category name(s) to create')
            ->addOption('parent', null, InputOption::VALUE_REQUIRED, 'Parent category ID (0 for top-level)', '0')
            ->addOption('description', null, InputOption::VALUE_REQUIRED, 'Category description')
            ->addOption('idnumber', null, InputOption::VALUE_REQUIRED, 'Category ID number')
            ->addOption('visible', null, InputOption::VALUE_REQUIRED, 'Visibility (1 or 0)', '1');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $runMode = $input->getOption('run');
        $names = $input->getArgument('name');

        $parent = (int) $input->getOption('parent');
        $description = $input->getOption('description');
        $idnumber = $input->getOption('idnumber');
        $visible = (int) $input->getOption('visible');

        if (!$runMode) {
            $output->writeln('<info>Dry run — the following categories would be created (use --run to execute):</info>');
            foreach ($names as $name) {
                $output->writeln("  $name (parent: $parent)");
            }
            return Command::SUCCESS;
        }

        $verbose->step('Creating ' . count($names) . ' category(ies)');

        $headers = ['id', 'name', 'parent'];
        $rows = [];

        foreach ($names as $name) {
            $data = new \stdClass();
            $data->name = $name;
            $data->parent = $parent;
            $data->visible = $visible;

            if ($description !== null) {
                $data->description = $description;
            }
            if ($idnumber !== null) {
                $data->idnumber = $idnumber;
            }

            $verbose->info("Creating category: $name");
            $category = \core_course_category::create($data);
            $verbose->done("Created category $name with ID {$category->id}");

            $rows[] = [$category->id, $category->name, $category->parent];
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
