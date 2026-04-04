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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProfileFieldExport51Handler extends BaseHandler
{
    public function configureCommand(Command $command): void
    {
        $command->addOption('file', null, InputOption::VALUE_REQUIRED, 'Write output to file instead of stdout');
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        global $DB;

        $verbose = new VerboseLogger($output);
        $format = $input->getOption('output');
        $filePath = $input->getOption('file');

        $verbose->step('Exporting profile fields');

        $sql = "SELECT f.*, c.name AS categoryname, c.sortorder AS categorysortorder
                  FROM {user_info_field} f
                  JOIN {user_info_category} c ON c.id = f.categoryid
                 ORDER BY c.sortorder, f.sortorder";
        $fields = $DB->get_records_sql($sql);

        $verbose->done('Found ' . count($fields) . ' field(s)');

        $headers = [
            'id', 'shortname', 'name', 'datatype', 'description', 'descriptionformat',
            'categoryid', 'sortorder', 'required', 'locked', 'visible', 'forceunique',
            'signup', 'defaultdata', 'defaultdataformat', 'param1', 'param2', 'param3',
            'param4', 'param5', 'categoryname', 'categorysortorder',
        ];

        $rows = [];
        foreach ($fields as $field) {
            $rows[] = [
                $field->id, $field->shortname, $field->name, $field->datatype,
                $field->description, $field->descriptionformat, $field->categoryid,
                $field->sortorder, $field->required, $field->locked, $field->visible,
                $field->forceunique, $field->signup, $field->defaultdata,
                $field->defaultdataformat, $field->param1 ?? '', $field->param2 ?? '',
                $field->param3 ?? '', $field->param4 ?? '', $field->param5 ?? '',
                $field->categoryname, $field->categorysortorder,
            ];
        }

        if ($filePath !== null) {
            $fh = fopen($filePath, 'w');
            if (!$fh) {
                $output->writeln("<error>Cannot open file for writing: $filePath</error>");
                return Command::FAILURE;
            }
            fputcsv($fh, $headers);
            foreach ($rows as $row) {
                fputcsv($fh, $row);
            }
            fclose($fh);
            $output->writeln("Exported " . count($rows) . " field(s) to $filePath");
            return Command::SUCCESS;
        }

        $formatter = new ResultFormatter($output, $format);
        $formatter->display($headers, $rows);

        return Command::SUCCESS;
    }
}
