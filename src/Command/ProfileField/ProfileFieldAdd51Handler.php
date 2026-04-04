<?php
/**
 * moosh2 — Moodle Shell
 *
 * @copyright  2012 onwards Tomasz Muras
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moosh2\Command\ProfileField;

use Moosh2\Command\BaseHandler;
use Moosh2\Output\ResultFormatter;
use Moosh2\Output\VerboseLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProfileFieldAdd51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command
            ->addArgument('shortname', InputArgument::REQUIRED, 'Field shortname (unique identifier)')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Display name (defaults to shortname)')
            ->addOption('datatype', null, InputOption::VALUE_REQUIRED, 'Field type: text, textarea, checkbox, menu, datetime', 'text')
            ->addOption('description', 'd', InputOption::VALUE_REQUIRED, 'Field description', '')
            ->addOption('category', null, InputOption::VALUE_REQUIRED, 'Category name (created if not exists)', 'Other fields')
            ->addOption('required', null, InputOption::VALUE_REQUIRED, 'Required (1 or 0)', '0')
            ->addOption('locked', null, InputOption::VALUE_REQUIRED, 'Locked (1 or 0)', '0')
            ->addOption('visible', null, InputOption::VALUE_REQUIRED, 'Visible: 0=hidden, 1=visible to user, 2=visible to all', '2')
            ->addOption('forceunique', null, InputOption::VALUE_REQUIRED, 'Force unique (1 or 0)', '0')
            ->addOption('signup', null, InputOption::VALUE_REQUIRED, 'Show on signup (1 or 0)', '0')
            ->addOption('defaultdata', null, InputOption::VALUE_REQUIRED, 'Default value', '')
            ->addOption('param1', null, InputOption::VALUE_REQUIRED, 'Type-specific param1 (e.g. max length for text, menu options for menu)')
            ->addOption('param2', null, InputOption::VALUE_REQUIRED, 'Type-specific param2');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $runMode = $input->getOption('run');

        $shortname = $input->getArgument('shortname');
        $name = $input->getOption('name') ?? $shortname;
        $datatype = $input->getOption('datatype');
        $categoryName = $input->getOption('category');

        // Check if field already exists.
        if ($DB->record_exists('user_info_field', ['shortname' => $shortname])) {
            $output->writeln("<error>Profile field '$shortname' already exists.</error>");
            return Command::FAILURE;
        }

        if (!$runMode) {
            $output->writeln("<info>Dry run — would create profile field \"$shortname\" (type: $datatype, category: $categoryName). Use --run to execute.</info>");
            return Command::SUCCESS;
        }

        $verbose->step('Creating profile field');

        // Get or create category.
        $category = $DB->get_record('user_info_category', ['name' => $categoryName]);
        if (!$category) {
            $verbose->info("Creating category: $categoryName");
            $catData = new \stdClass();
            $catData->name = $categoryName;
            $catData->sortorder = $DB->count_records('user_info_category') + 1;
            $catData->id = $DB->insert_record('user_info_category', $catData);
            $category = $catData;
        }

        $field = new \stdClass();
        $field->shortname = $shortname;
        $field->name = $name;
        $field->datatype = $datatype;
        $field->description = $input->getOption('description');
        $field->descriptionformat = 1;
        $field->categoryid = $category->id;
        $field->sortorder = $DB->count_records('user_info_field', ['categoryid' => $category->id]) + 1;
        $field->required = (int) $input->getOption('required');
        $field->locked = (int) $input->getOption('locked');
        $field->visible = (int) $input->getOption('visible');
        $field->forceunique = (int) $input->getOption('forceunique');
        $field->signup = (int) $input->getOption('signup');
        $field->defaultdata = $input->getOption('defaultdata');
        $field->defaultdataformat = 0;
        $field->param1 = $input->getOption('param1') ?? ($datatype === 'text' ? '30' : '');
        $field->param2 = $input->getOption('param2') ?? ($datatype === 'text' ? '2048' : '');
        $field->param3 = '';
        $field->param4 = '';
        $field->param5 = '';

        $newId = $DB->insert_record('user_info_field', $field);
        $verbose->done("Created profile field with ID $newId");

        $headers = ['id', 'shortname', 'name', 'datatype', 'category'];
        $rows = [[$newId, $shortname, $name, $datatype, $categoryName]];

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
