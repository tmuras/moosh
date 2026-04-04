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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProfileFieldInfo52Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addArgument('fieldid', InputArgument::REQUIRED, 'Profile field ID');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $fieldId = (int) $input->getArgument('fieldid');

        $field = $DB->get_record('user_info_field', ['id' => $fieldId]);
        if (!$field) {
            $output->writeln("<error>Profile field with ID $fieldId not found.</error>");
            return Command::FAILURE;
        }

        $category = $DB->get_record('user_info_category', ['id' => $field->categoryid]);

        $data = [];
        $data['Field ID'] = $field->id;
        $data['Shortname'] = $field->shortname;
        $data['Name'] = $field->name;
        $data['Data type'] = $field->datatype;
        $data['Description'] = $field->description;
        $data['Category'] = $category ? $category->name : "(ID {$field->categoryid})";
        $data['Sort order'] = (int) $field->sortorder;
        $data['Required'] = (int) $field->required ? 'yes' : 'no';
        $data['Locked'] = (int) $field->locked ? 'yes' : 'no';
        $data['Visible'] = match ((int) $field->visible) {
            0 => 'hidden',
            1 => 'visible to user',
            2 => 'visible to all',
            default => (string) $field->visible,
        };
        $data['Force unique'] = (int) $field->forceunique ? 'yes' : 'no';
        $data['Show on signup'] = (int) $field->signup ? 'yes' : 'no';
        $data['Default value'] = $field->defaultdata ?? '';

        // Type-specific params.
        if ($field->param1 !== null && $field->param1 !== '') {
            $data['Param1'] = $field->param1;
        }
        if ($field->param2 !== null && $field->param2 !== '') {
            $data['Param2'] = $field->param2;
        }
        if ($field->param3 !== null && $field->param3 !== '') {
            $data['Param3'] = $field->param3;
        }

        // Usage statistics.
        $verbose->step('Counting usage');
        $totalData = $DB->count_records('user_info_data', ['fieldid' => $fieldId]);
        $data['Users with data'] = $totalData;

        $nonEmpty = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {user_info_data} WHERE fieldid = ? AND data <> '' AND data IS NOT NULL",
            [$fieldId],
        );
        $data['Users with non-empty data'] = $nonEmpty;

        // Render.
        if ($format === 'table') {
            $table = new Table($output);
            $table->setHeaders(['Metric', 'Value']);
            foreach ($data as $key => $value) {
                $table->addRow([$key, $value]);
            }
            $table->render();
        } else {
            $formatter = new ResultFormatter($output, $format);
            $headers = array_keys($data);
            $formatter->display($headers, [array_values($data)]);
        }

        return Command::SUCCESS;
    }
}
